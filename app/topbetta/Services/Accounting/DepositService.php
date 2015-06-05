<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 10:31 AM
 */

namespace TopBetta\Services\Accounting;


use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Services\Accounting\Payments\EwayPaymentService;
use TopBetta\Services\DashboardNotification\UserDashboardNotificationService;
use TopBetta\Services\ExternalSourceNotifications\DepositExternalSourceNotificationService;
use TopBetta\Services\Validation\DepositValidator;

class DepositService {

    /**
     * @var EwayPaymentService
     */
    private $creditCardDepositService;
    /**
     * @var DepositValidator
     */
    private $depositValidator;
    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;
    /**
     * @var BetSourceRepositoryInterface
     */
    private $betSourceRepository;
    /**
     * @var DepositExternalSourceNotificationService
     */
    private $externalSourceNotificationService;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var UserDashboardNotificationService
     */
    private $dashboardNotificationService;

    public function __construct(EwayPaymentService $creditCardDepositService,
                                DepositValidator $depositValidator,
                                AccountTransactionService $accountTransactionService,
                                BetSourceRepositoryInterface $betSourceRepository,
                                DepositExternalSourceNotificationService $externalSourceNotificationService,
                                UserDashboardNotificationService $dashboardNotificationService,
                                UserRepositoryInterface $userRepository)
    {
        $this->creditCardDepositService = $creditCardDepositService;
        $this->depositValidator = $depositValidator;
        $this->accountTransactionService = $accountTransactionService;
        $this->betSourceRepository = $betSourceRepository;
        $this->externalSourceNotificationService = $externalSourceNotificationService;
        $this->userRepository = $userRepository;
        $this->dashboardNotificationService = $dashboardNotificationService;
    }

    /**
     * Does a credit card deposit
     * @param $user
     * @param $input
     * @return bool
     * @throws \TopBetta\Services\Validation\Exceptions\ValidationException
     */
    public function creditCardDeposit($user, $input)
    {
        //validate input
        $this->depositValidator->validate(
            $input,
            array_merge($this->depositValidator->rules, $this->creditCardDepositService->getCardValidationRules($user))
        );

        //check if a child user is the target for the deposit
        if( $childUser = array_get($input, 'child_username', null) ) {
            $depositUser = $this->userRepository->getUserByUsername($childUser);
        } else {
            $depositUser = $user;
        }

        //create deposit with gateway
        $response = $this->creditCardDepositService->deposit($depositUser, $input['amount'], array_except($input, array('source', 'amount')));

        //create transaction
        $transaction = $this->createDepositTransaction(
            $depositUser,
            AccountTransactionTypeRepositoryInterface::TYPE_EWAY_DEPOSIT,
            array_get($input, 'amount', 0),
            array_get($input, 'source', null)
        );

        //notifications
        $this->sendNotifications($depositUser->parent_user_id ? $depositUser->parent_user_id : $depositUser->id, $transaction['id'], array_get($input, 'source', null));

        return $transaction;
    }

    /**
     * Does a scheduled credit card deposit, no validation is required
     * @param $user
     * @param $token
     * @param $amount
     * @param null $source
     * @return bool
     */
    public function scheduledCreditCardDeposit($user, $token, $amount, $source = null)
    {
        //create deposit with gateway
        $response = $this->creditCardDepositService->deposit($user, $amount, array("token" => $token), true);

        //create deposit transaction
        $transaction = $this->createDepositTransaction($user, AccountTransactionTypeRepositoryInterface::TYPE_EWAY_RECURRING_DEPOSIT, $amount, $source);

        //notifications
        $this->sendNotifications($user->parent_user_id ? $user->parent_user_id : $user->id, $transaction['id'], $source);

        return $transaction;
    }

    /**
     * Creates a deposit transaction
     * @param $user
     * @param $transactionType
     * @param $amount
     * @param null $source
     * @return bool
     */
    public function createDepositTransaction($user, $transactionType, $amount, $source = null)
    {
        //apply deposit to parent
        $userId = $user->parent_user_id ? $user->parent_user_id : $user->id;

        //get the source
        if( $source ) {
            $source = $this->betSourceRepository->getSourceByKeyword($source);
        }

        //create transaction
        return $this->accountTransactionService->increaseAccountBalance(
            $userId,
            $amount,
            $transactionType,
            $user->id,
            null,
            $source ? $source['id'] : null
        );
    }

    public function sendNotifications($userId, $transactionId, $source = null)
    {
        //notify external source if necessary
        $this->externalSourceNotificationService->notify(array(
            "id" => $userId,
            "transactions" => array(
                $transactionId,
            ),
            "source" => $source
        ));

        //notify dashboard
        $this->dashboardNotificationService->notify(array(
            "id" => $userId,
            "transactions" => array(
                $transactionId
            )
        ));
    }
}