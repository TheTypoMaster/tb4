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

    public function __construct(DbTournamentTicketRepository $tournamentTicketRepository, AccountTransactionRepositoryInterface $accountTransactionRepository) {

        $this->tournamentTicketRepository = $tournamentTicketRepository;
        $this->accountTransactionRepository = $accountTransactionRepository;
    }

    public function getHttpMethod()
    {
        return "POST";
    }

    public function getEndpoint()
    {
        return "test-notify";
    }

    public function getTransaction($transactionId)
    {
        return $this->accountTransactionRepository->findWithType($transactionId);
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
        );

        if($transactions = array_get($data, 'transactions', null)) {
            $payload['transactions'] = $this->formatTransactions($transactions);
        }

        return $payload;
    }


}