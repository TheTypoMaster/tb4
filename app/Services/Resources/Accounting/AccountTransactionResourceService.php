<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 3:09 PM
 */

namespace TopBetta\Services\Resources\Accounting;


use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketBuyInHistoryRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Resources\Betting\BetResource;
use TopBetta\Resources\PaginatedEloquentResourceCollection;
use TopBetta\Resources\Tournaments\TicketResource;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Resources\OrderableResourceService;

class AccountTransactionResourceService extends OrderableResourceService {

    protected $orderFields = array(
        "date" => "created_date",
        "type" => "name",
        "description" => "description",
        "amount" => "amount",
        "notes" => "notes",
    );

    /**
     * @var AccountTransactionRepositoryInterface
     */
    protected $repository;
    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $ticketRepository;
    /**
     * @var TournamentTicketBuyInHistoryRepositoryInterface
     */
    private $buyInHistoryRepository;

    /**
     * @param AccountTransactionRepositoryInterface $repository
     * @param BetRepositoryInterface $betRepository
     * @param TournamentTicketRepositoryInterface $ticketRepository
     * @param TournamentTicketBuyInHistoryRepositoryInterface $buyInHistoryRepository
     */
    public function __construct(AccountTransactionRepositoryInterface $repository,
                                BetRepositoryInterface $betRepository,
                                TournamentTicketRepositoryInterface $ticketRepository,
                                TournamentTicketBuyInHistoryRepositoryInterface $buyInHistoryRepository)
    {
        $this->repository = $repository;
        $this->betRepository = $betRepository;
        $this->ticketRepository = $ticketRepository;
        $this->buyInHistoryRepository = $buyInHistoryRepository;
    }

    public function getAllTransactionsWithDetailsForUser($user)
    {
        $transactions = $this->repository->findAllPaginatedForUser($user->id);

        $transactions = new PaginatedEloquentResourceCollection($transactions, 'TopBetta\Resources\Accounting\AccountTransactionResource');

        return $this->setTransactionsDetails($transactions, $user);
    }

    public function getTransactionsForUserByTypeIn($user, $types)
    {
        $transactions = $this->repository->findForUserByTypesPaginated($user->id, $types);

        $transactions = new PaginatedEloquentResourceCollection($transactions, 'TopBetta\Resources\Accounting\AccountTransactionResource');

        return $this->setTransactionsDetails($transactions, $user);
    }

    public function getLosingBetTransactionsForUser($user)
    {
        $transactions = $this->repository->findLosingBetTransactionsForUser($user->id);

        $transactions = new PaginatedEloquentResourceCollection($transactions, 'TopBetta\Resources\Accounting\AccountTransactionResource');

        return $this->setTransactionsDetails($transactions, $user);
    }

    protected function setTransactionsDetails($transactions, $user)
    {
        foreach($transactions as $transaction) {
            $this->setTransactionDetails($transaction);
            $transaction->setRunningBalance($user->accountBalance($transaction->id));
        }

        return $transactions;
    }

    protected function setTransactionDetails($transaction)
    {
        switch ($transaction->transactionType->keyword) {
            case AccountTransactionTypeRepositoryInterface::TYPE_BET_WIN:
                $bet = $this->betRepository->getByResultTransaction($transaction->id);
                if ($bet) {
                    $transaction->setBet(
                        new BetResource($bet)
                    );
                }
                break;
            case AccountTransactionTypeRepositoryInterface::TYPE_BET_ENTRY:
                $bet = $this->betRepository->getByEntryTransaction($transaction->id);
                if ($bet) {
                    $transaction->setBet(
                        new BetResource($bet)
                    );
                }
                break;
            case AccountTransactionTypeRepositoryInterface::TYPE_BET_REFUND:
            case AccountTransactionTypeRepositoryInterface::TYPE_BET_PARTIAL_REFUND:
                $bet = $this->betRepository->getByRefundTransaction($transaction->id);
                if ($bet) {
                    $transaction->setBet(
                        new BetResource($bet)
                    );
                }
                break;
            case AccountTransactionTypeRepositoryInterface::TYPE_ENTRY:
            case AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_REBUY_ENTRY:
            case AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_TOPUP_ENTRY:
                $ticket = $this->buyInHistoryRepository->getByEntryTransaction($transaction->id);
                if( $ticket && $ticket->ticket ) {
                    $transaction->setTicket(
                        new TicketResource($ticket->ticket->load('tournament'))
                    );
                }

                break;
            case AccountTransactionTypeRepositoryInterface::TYPE_BUY_IN:
            case AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_REBUY_BUYIN:
            case AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_TOPUP_BUYIN:
                $ticket = $this->buyInHistoryRepository->getByBuyinTransaction($transaction->id);
                if( $ticket && $ticket->ticket ) {
                    $transaction->setTicket(
                        new TicketResource($ticket->ticket->load('tournament'))
                    );
                }

            break;
            case AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_REFUND:
            case AccountTransactionTypeRepositoryInterface::TYPE_TOURNAMENT_WIN:
                $ticket = $this->ticketRepository->getByResultTransaction($transaction->id);
                if( $ticket ) {
                    $transaction->setTicket(
                        new TicketResource($ticket->load('tournament'))
                    );
                }
                break;
        }

        return $transaction;
    }
}