<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/03/2015
 * Time: 1:53 PM
 */

namespace TopBetta\Services\DashboardNotification\Queue;

use Log;
use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface;
use TopBetta\Repositories\DbTournamentTicketRepository;
use TopBetta\Services\DashboardNotification\AbstractDashboardNotificationService;

class TournamentDashboardNotificationQueueService extends AbstractTransactionDashboardNotificationService{
    /**
     * @var DbTournamentTicketRepository
     */
    private $tournamentTicketRepository;
    /**
     * @var AccountTransactionRepositoryInterface
     */
    private $accountTransactionRepository;
    /**
     * @var FreeCreditTransactionRepositoryInterface
     */
    private $freeCreditTransactionRepository;

    public function __construct(DbTournamentTicketRepository $tournamentTicketRepository,
                                AccountTransactionRepositoryInterface $accountTransactionRepository,
                                FreeCreditTransactionRepositoryInterface $freeCreditTransactionRepository) {

        $this->tournamentTicketRepository = $tournamentTicketRepository;
        $this->accountTransactionRepository = $accountTransactionRepository;
        $this->freeCreditTransactionRepository = $freeCreditTransactionRepository;
    }

    public function getHttpMethod()
    {
        return "POST";
    }

    public function getEndpoint()
    {
        return "tickets";
    }

    public function getTransaction($transactionId)
    {
        return $transactionId ? $this->accountTransactionRepository->findWithType($transactionId) : array();
    }

    public function getFreeCreditTransaction($transactionId)
    {
        return $transactionId ? $this->freeCreditTransactionRepository->findWithType($transactionId) : array();
    }

    public function formatPayload($data)
    {
        if( ! array_get($data, 'id', false) ) {
            \Log::error("No tournament id specified for Tournament Dashboard Notification");
            return array();
        }

        //get tournament ticket
        $tournamentTicket = $this->tournamentTicketRepository->getWithUserAndTransactions($data['id']);

        //create payload
        $payload = array(
            "ticket_username" => array_get($tournamentTicket, "user.username", null),
            "ticket_extra_starting_currency" => array_get($tournamentTicket, 'extra_starting_currency', null),
            "ticket_entry_fee" => array_get($tournamentTicket, "entry_fee_transaction.amount", 0),
            "external_id" => array_get($tournamentTicket, "id", 0),
            "transactions" => array(),
            "user" => null,
        );

        if( $user = array_get($tournamentTicket, 'user', null) ) {
            $payload['user'] = $this->formatUser($user);
        }

        if($transactions = array_get($data, 'transactions', null)) {
            $payload['transactions'] = $this->formatTransactions($transactions);
        }

        if($transactions = array_get($data, 'free-credit-transactions', null)) {
            $payload['transactions'] = array_merge($payload['transactions'], $this->formatTransactions($transactions, true));
        }

        return $payload;
    }


}