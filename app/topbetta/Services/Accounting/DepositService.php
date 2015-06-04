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
use TopBetta\Services\Accounting\Payments\EwayPaymentService;
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

    public function __construct(EwayPaymentService $creditCardDepositService, DepositValidator $depositValidator, AccountTransactionService $accountTransactionService, BetSourceRepositoryInterface $betSourceRepository)
    {
        $this->creditCardDepositService = $creditCardDepositService;
        $this->depositValidator = $depositValidator;
        $this->accountTransactionService = $accountTransactionService;
        $this->betSourceRepository = $betSourceRepository;
    }

    public function creditCardDeposit($user, $input)
    {
        $this->depositValidator->validate(
            $input,
            array_merge($this->depositValidator->rules, $this->creditCardDepositService->getCardValidationRules($user))
        );

        $response = $this->creditCardDepositService->deposit($user, $input['amount'], array_except($input, array('source', 'amount')));

        return $this->createDepositTransaction($user, AccountTransactionTypeRepositoryInterface::TYPE_EWAY_DEPOSIT, array_get($input, 'amount', 0), array_get($input, 'source', null));
    }

    public function scheduledCreditCardDeposit($user, $token, $amount, $source = null)
    {
        $response = $this->creditCardDepositService->deposit($user, $amount, array("token" => $token), true);

        return $this->createDepositTransaction($user, AccountTransactionTypeRepositoryInterface::TYPE_EWAY_DEPOSIT, $amount, $source);
    }

    public function createDepositTransaction($user, $transactionType, $amount, $source = null)
    {
        //apply deposit to parent
        $userId = $user->parent_user_id ? $user->parent_user_id : $user->id;

        //get the source
        if( $source ) {
            $this->betSourceRepository->getSourceByKeyword($source);
        }

        //create transaction
        return $this->accountTransactionService->increaseAccountBalance(
            $userId,
            $amount,
            $transactionType,
            $userId,
            null,
            $source->id
        );
    }
}