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
use TopBetta\PaymentEwayTokens;
use TopBetta\Repositories\Contracts\PaymentEwayTokenRepositoryInterface;
use TopBetta\Repositories\Contracts\ScheduledPaymentRepositoryInterface;
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

    public function __construct(ScheduledPaymentRepositoryInterface $scheduledPaymentRepository,
                                DepositService $depositService,
                                EwayPaymentService $ewayDepositService,
                                PaymentEwayTokenRepositoryInterface $ewayTokens)
    {
        $this->scheduledPaymentRepository = $scheduledPaymentRepository;
        $this->depositService = $depositService;
        $this->ewayDepositService = $ewayDepositService;
        $this->ewayTokens = $ewayTokens;
    }

    public function createSchedule($user, $input)
    {
        //create the schedule
        $payment = $this->scheduledPaymentRepository->create(array(
            "recurring_period" => array_get($input, "period"),
            "next_payment" => $this->getNextPaymentDate(Carbon::now()->toDateTimeString(), array_get($input, "period")),
            "active" => true,
            'amount' => array_get($input, 'amount', 0),
        ));

        //create the token
        if( ! $token = array_get($input, 'token', null) ) {
            $token = $this->ewayDepositService->createCard($user, array_get($input, 'cardDetails', null));
        } else {
            $token = $this->ewayTokens->getByToken($token);
        }

        //first deposit
        try {
            $deposit = $this->depositService->creditCardDeposit($user, array(
                'amount' => array_get($input, 'amount', 0),
                'cvv' => array_get($input, 'cardDetails.cvv', 0),
                'token' => $token->cc_token
            ));
        } catch( \Exception $e ) {
            //stop payment
            $this->scheduledPaymentRepository->updateWithId($payment->id, array("active" => false));
            throw $e;
        }

        $token->scheduledPayments()->save($this->scheduledPaymentRepository->find($payment['id']));

        return $deposit;
    }

    public function processScheduledPayments()
    {
        $payments = $this->scheduledPaymentRepository->getPaymentsDueAfterDate(Carbon::now());

        $deposits = array();

        foreach($payments as $payment) {
            Log::info("Processing scheduled payment id: " . $payment->id);
            $deposits[] = $this->scheduledPayment($payment);
        }

        return $deposits;
    }

    public function scheduledPayment($payment)
    {
        try {
            $deposit = $this->depositService->scheduledCreditCardDeposit($payment->paymentToken->user, $payment->paymentToken->cc_token, $payment->amount);
        } catch (PaymentException $e) {
            Log::error("Scheduled Payment Error for payment " . $payment . " with message " . $e->getMessage());

            $this->scheduledPaymentRepository->updateWithId($payment->id, array(
                "next_payment" => $this->getNextPaymentDate($payment->next_payment, $payment->recurring_period),
                "tries" => $payment->tries + 1,
            ));

            return null;
        }

        $this->scheduledPaymentRepository->updateWithId($payment->id, array(
            "next_payment" => $this->getNextPaymentDate($payment->next_payment, $payment->recurring_period),
            "tries" => 0,
        ));

        return $deposit;
    }

    public function cancelPayment($payment)
    {
        return $this->scheduledPaymentRepository->updateWithId($payment->id, array("active" => false));
    }

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