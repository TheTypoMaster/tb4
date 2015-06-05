<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 11:27 AM
 */

namespace TopBetta\Services\Accounting;


use Carbon\Carbon;
use Log;
use Config;
use TopBetta\PaymentEwayTokens;
use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;
use TopBetta\Repositories\Contracts\PaymentEwayTokenRepositoryInterface;
use TopBetta\Repositories\Contracts\ScheduledPaymentRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Services\Accounting\Payments\EwayPaymentService;
use TopBetta\Services\Accounting\Payments\Exceptions\PaymentException;

class ScheduledDepositService {

    /**
     * @var ScheduledPaymentRepositoryInterface
     */
    private $scheduledPaymentRepository;
    /**
     * @var DepositService
     */
    private $depositService;
    /**
     * @var EwayPaymentService
     */
    private $ewayDepositService;
    /**
     * @var PaymentEwayTokenRepositoryInterface
     */
    private $ewayTokens;
    /**
     * @var BetSourceRepositoryInterface
     */
    private $betSourceRepository;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(ScheduledPaymentRepositoryInterface $scheduledPaymentRepository,
                                DepositService $depositService,
                                EwayPaymentService $ewayDepositService,
                                PaymentEwayTokenRepositoryInterface $ewayTokens,
                                BetSourceRepositoryInterface $betSourceRepository,
                                UserRepositoryInterface $userRepository)
    {
        $this->scheduledPaymentRepository = $scheduledPaymentRepository;
        $this->depositService = $depositService;
        $this->ewayDepositService = $ewayDepositService;
        $this->ewayTokens = $ewayTokens;
        $this->betSourceRepository = $betSourceRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Create a scheduled payment
     * @param $user
     * @param $input
     * @return bool
     * @throws \Exception
     */
    public function createSchedule($user, $input)
    {
        //get source
        if ( $source = array_get($input, "source", null) ) {
            $source = $this->betSourceRepository->getSourceByKeyword(array_get($input, "source", null));
        }

        //get the use the deposit is for
        if( $depositUser = array_get($input, 'child_username', null) ) {
            $depositUser = $this->userRepository->getUserByUsername($depositUser);
        } else {
            $depositUser = $user;
        }

        //create the schedule
        $payment = $this->scheduledPaymentRepository->create(array(
            "recurring_period" => array_get($input, "period"),
            "next_payment" => $this->getNextPaymentDate(Carbon::now()->startOfDay()->toDateTimeString(), array_get($input, "period")),
            "active" => true,
            'amount' => array_get($input, 'amount', 0),
            "source_id" => $source ? $source['id'] : 0,
            "user_id" => $depositUser->id
        ));

        //first deposit
        try {
            //create the token
            if( ! $token = array_get($input, 'cc_token', null) ) {
                $token = $this->ewayDepositService->createCard($user, array_only($input, array(
                    "firstName", "lastName", "number", "billingCountry", "expiryMonth", "expiryYear", "cvv",
                )));
            } else {
                $token = $this->ewayTokens->getByToken($token);
            }

            //first deposit
            $deposit = $this->depositService->creditCardDeposit($user, array(
                'amount' => array_get($input, 'amount', 0),
                'cvv' => array_get($input, 'cvv', 0),
                'cc_token' => $token->cc_token,
                "source" => array_get($input, 'source'),
                "child_username" => array_get($input,'child_username', null)
            ));

        } catch( \Exception $e ) {
            //stop payment
            //dd(\DB::getQueryLog());
            $this->scheduledPaymentRepository->updateWithId($payment['id'], array("active" => false));
            throw $e;
        }

        $token->scheduledPayments()->save($this->scheduledPaymentRepository->find($payment['id']));

        return $deposit;
    }

    /**
     * Process scheduled payments
     * @return array
     */
    public function processScheduledPayments()
    {
        //get all due payments
        $payments = $this->scheduledPaymentRepository->getPaymentsDueAfterDate(Carbon::now());

        $deposits = array();

        //process each payment
        foreach($payments as $payment) {
            Log::info("Processing scheduled payment id: " . $payment->id);
            $deposits[] = $this->scheduledPayment($payment);
        }

        return $deposits;
    }

    /**
     * Process a scheduled payment
     * @param $payment
     * @return bool|null
     */
    public function scheduledPayment($payment)
    {
        try {
            //perform deposit
            $deposit = $this->depositService->scheduledCreditCardDeposit($payment->user, $payment->paymentToken->cc_token, $payment->amount, $payment->source ? $payment->source->keyword : null);
        } catch (PaymentException $e) {
            Log::error("Scheduled Payment Error for payment " . $payment . " with message " . $e->getMessage());

            //update tries if unsucessful. If we've gone over max then give up on this payment and set up for next time.
            if( $payment->retries + 1 == Config::get('ewayrapid.max_retries')) {
                $this->scheduledPaymentRepository->updateWithId($payment->id, array(
                    "next_payment" => $this->getNextPaymentDate($payment->next_payment, $payment->recurring_period),
                    "retries" => 0,
                ));
            } else {
                $this->scheduledPaymentRepository->updateWithId($payment->id, array(
                    "retries" => $payment->retries + 1,
                ));
            }

            return null;
        }

        //update next payment date
        $this->scheduledPaymentRepository->updateWithId($payment->id, array(
            "next_payment" => $this->getNextPaymentDate($payment->next_payment, $payment->recurring_period),
            "retries" => 0,
        ));

        return $deposit;
    }

    /**
     * Cancel a scheduled payment
     * @param $payment
     * @return mixed
     */
    public function cancelPayment($payment)
    {
        return $this->scheduledPaymentRepository->updateWithId($payment->id, array("active" => false));
    }

    /**
     * Get All active payments for user
     * @param $user
     * @param null $source
     * @return array
     * @throws PaymentException
     */
    public function getActivePaymentsForUser($user, $source = null)
    {
        //check for source
        if( $source ) {
            $source = $this->betSourceRepository->getSourceByKeyword($source);
        }

        //get payments
        $payments = $this->scheduledPaymentRepository->getActivePaymentsForUser($user->id, $source ? $source['id'] : null);

        //format data
        $cardDetailsArray[] = array();
        $paymentsArray = array();
        foreach( $payments as $payment ) {
            //get card details
            $cardDetailsArray[$payment->payment_token->cc_token] = array_get(
                $cardDetailsArray,
                $payment->payment_token->cc_token,
                $this->ewayDepositService->getCard($payment->payment_token->cc_token)
            );

            //merge card details with payment details
            $paymentsArray[] = array_merge(array(
                "id" => $payment->id,
                "amount" => $payment->amount,
                "period" => $payment->recurring_period,
                "retries" => $payment->retries,
                "next_payment" => $payment->next_payment
            ), $cardDetailsArray[$payment->payment_token->cc_token]);
        }

        return $paymentsArray;
    }

    /**
     * Calculate next payment date
     * @param $paymentDate
     * @param $period
     * @return bool|static
     */
    public function getNextPaymentDate($paymentDate, $period)
    {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $paymentDate);
        switch($period)
        {
            case "daily":
                return $date->addDay();
            case "weekly":
                return $date->addWeek();
            case "fortnightly":
                return $date->addWeeks(2);
            case "monthly":
                return $date->addMonth();
        }

        return false;
    }
}