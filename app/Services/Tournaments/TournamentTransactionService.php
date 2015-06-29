<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 3:19 PM
 */

namespace TopBetta\Services\Tournaments;


use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBuyInTypeRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;

class TournamentTransactionService {

    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;

    public function __construct(AccountTransactionService $accountTransactionService)
    {
        $this->accountTransactionService = $accountTransactionService;
    }

    public function createTournamentBuyInTransactions($userId, $buyinAmount, $entryAmount, $type = 'buyin')
    {
        //create transactions
        $buyin = $this->accountTransactionService->decreaseAccountBalance($userId, $buyinAmount, $this->getBuyInTransactionType($type), $userId);

        $entry = $this->accountTransactionService->decreaseAccountBalance($userId, $entryAmount, $this->getEntryTransactionType($type), $userId);

        return array('buyin_transaction' => $buyin, 'entry_transaction' => $entry);
    }

    /**
     * Creates refund transaction for user
     * @param $userId
     * @param $amount
     * @return bool
     */
    public function createRefundTransaction($userId, $amount)
    {
        return $this->accountTransactionService->increaseAccountBalance($userId, $amount, $this->getRefundTransactionType());
    }

    private function getRefundTransactionType()
    {
        return AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_REFUND;
    }

    private function getBuyInTransactionType($type)
    {
        switch($type)
        {
            case TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_BUYIN:
                return AccountTransactionTypeRepositoryInterface::TYPE_BUY_IN;
            case TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_REBUY:
                return AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_REBUY_BUYIN;
            case TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_TOPUP:
                return AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_TOPUP_BUYIN;
        }
        return null;
    }

    private function getEntryTransactionType($type)
    {
        switch($type)
        {
            case TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_BUYIN:
                return AccountTransactionTypeRepositoryInterface::TYPE_ENTRY;
            case TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_REBUY:
                return AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_REBUY_ENTRY;
            case TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_TOPUP:
                return AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_TOPUP_ENTRY;
        }

        return null;
    }

}