<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 1:54 PM
 */

namespace TopBetta\Services\Betting\BetTransaction;


use TopBetta\Repositories\BetRepo;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
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
    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;

    public function __construct(AccountTransactionService $accountTransactionService, FreeCreditTransactionService $freeCreditTransactionService, BetRepositoryInterface $betRepository)
    {
        $this->accountTransactionService = $accountTransactionService;
        $this->freeCreditTransactionService = $freeCreditTransactionService;
        $this->betRepository = $betRepository;
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

    public function refund($user, $amount, $freeCreditAmount)
    {
        $transactions = array();

        if( $amount > 0 ) {
            $transactions['account'] = $this->accountTransactionService->increaseAccountBalance($user->id, $amount, 'betrefund', $user->id);
        }

        if( $freeCreditAmount > 0 ) {
            $transactions['free_credit'] = $this->freeCreditTransactionService->increaseFreeCreditBalance($user->id, $user->id, $freeCreditAmount, 'freebetrefund');
        }

        return $transactions;
    }

    public function refundBet($betId)
    {
        $bet = $this->betRepository->find($betId);

        $transactions = $this->refund($bet->user, $bet->bet_amount - $bet->bet_freebet_amount, $bet->bet_freebet_amount);

        $this->betRepository->updateWithId($bet['id'], array(
            'refund_transaction_id' => array_get($transactions, 'account.id', 0),
            'refund_freebet_transaction_id' => array_get($transactions, 'free_credit.id', 0),
        ));

        return $transactions;
    }
}