<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 1:54 PM
 */

namespace TopBetta\Services\Betting\BetTransaction;


use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Accounting\FreeCreditTransactionService;

class BetTransactionService {

    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;
    /**
     * @var FreeCreditTransactionService
     */
    private $freeCreditTransactionService;

    public function __construct(AccountTransactionService $accountTransactionService, FreeCreditTransactionService $freeCreditTransactionService)
    {
        $this->accountTransactionService = $accountTransactionService;
        $this->freeCreditTransactionService = $freeCreditTransactionService;
    }

    public function createBetPlacementTransaction($user, $amount, $freeCreditFlag = false)
    {
        $transactions = array();
        $accountAmount = $amount;

        if ( $freeCreditFlag ) {
            $accountAmount -= min($user->freeCreditBalance(), $amount);
        }

        if( $accountAmount > 0 ) {
            $transactions['account'] = $this->accountTransactionService->decreaseAccountBalance($user->id, $accountAmount, 'betentry', $user->id);
        }

        if( $amount - $accountAmount > 0 ) {
            $transactions['free_credit'] = $this->freeCreditTransactionService->decreaseFreeCreditBalance($user->id, $user->id, $amount - $accountAmount, 'freebetentry');
        }

        return $transactions;
    }
}