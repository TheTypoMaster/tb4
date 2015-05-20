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

    /**
     * create bet placement transaction
     * @param $user
     * @param $amount
     * @param bool $freeCreditFlag
     * @return array ('free_credit' => free credit transaction, 'account' => account transaction)
     */
    public function createBetPlacementTransaction($user, $amount, $freeCreditFlag = false)
    {
        $transactions = array();
        $accountAmount = $amount;

        //free credit flag so calculate amount of free credit to use and amout of account balance
        if ( $freeCreditFlag ) {
            $accountAmount -= min($user->freeCreditBalance(), $amount);
        }

        //create the account transaction
        if( $accountAmount > 0 ) {
            $transactions['account'] = $this->accountTransactionService->decreaseAccountBalance($user->id, $accountAmount, 'betentry', $user->id);
        }

        //create the free credit transaction
        if( $amount - $accountAmount > 0 ) {
            $transactions['free_credit'] = $this->freeCreditTransactionService->decreaseFreeCreditBalance($user->id, $user->id, $amount - $accountAmount, 'freebetentry');
        }

        return $transactions;
    }

    /**
     * Create bet refund
     * @param $user
     * @param $amount
     * @param $freeCreditAmount
     * @return array ('free_credit' => free credit transaction, 'account' => account transaction)
     */
    public function refund($user, $amount, $freeCreditAmount)
    {
        $transactions = array();

        //refund account
        if( $amount > 0 ) {
            $transactions['account'] = $this->accountTransactionService->increaseAccountBalance($user->id, $amount, 'betrefund', $user->id);
        }

        //refund free credit
        if( $freeCreditAmount > 0 ) {
            $transactions['free_credit'] = $this->freeCreditTransactionService->increaseFreeCreditBalance($user->id, $user->id, $freeCreditAmount, 'freebetrefund');
        }

        return $transactions;
    }

    /**
     * Refunds a bet
     * Refund bet
     * @param $betId int
     * @return array ('free_credit' => free credit transaction, 'account' => account transaction)
     */
    public function refundBet($betId)
    {
        //get the bet
        $bet = $this->betRepository->find($betId);

        //refund the bet
        $transactions = $this->refund($bet->user, $bet->bet_amount - $bet->bet_freebet_amount, $bet->bet_freebet_amount);

        //add transaction ids to bet record
        $this->betRepository->updateWithId($bet['id'], array(
            'refund_transaction_id' => array_get($transactions, 'account.id', 0),
            'refund_freebet_transaction_id' => array_get($transactions, 'free_credit.id', 0),
        ));

        return $transactions;
    }

    public function createBetWinTransaction($bet, $amount)
    {
        $transaction = $this->accountTransactionService->increaseAccountBalance($bet->user_id, $amount, 'betwin');

        $this->betRepository->updateWithId($bet->id, array(
            'result_transaction_id' => $transaction['id'],
        ));

        return $transaction;
    }
}