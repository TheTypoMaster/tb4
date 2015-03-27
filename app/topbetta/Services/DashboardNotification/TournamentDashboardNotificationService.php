<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/03/2015
 * Time: 3:16 PM
 */

namespace TopBetta\Services\DashboardNotification;


use TopBetta\Repositories\DbTournamentTicketRepository;

class TournamentDashboardNotificationService extends AbstractTransactionDashboardNotificationService {

    /**
     * @var DbTournamentTicketRepository
     */
    private $tournamentTicketRepository;

    public function __construct(DbTournamentTicketRepository $tournamentTicketRepository) {

        $this->tournamentTicketRepository = $tournamentTicketRepository;
    }

    public function getHttpMethod()
    {
        return "POST";
    }

    public function getEndpoint()
    {
        return "tickets";
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

        \Log::info($tournamentTicket);
        //format transactions
        if( array_get($tournamentTicket,'entry_fee_transaction_id', null) ) {
            $payload['transactions'][] = $this->formatTransaction($tournamentTicket['entry_fee_transaction_id']);
        }

        if( array_get($tournamentTicket, 'buy_in_transaction', null) ) {
            $payload['transactions'][] = $this->formatTransaction($tournamentTicket['buy_in_transaction_id']);
        }

        if( array_get($tournamentTicket, 'result_transaction_id', null) ) {
            $payload['transactions'][] = $this->formatTransaction($tournamentTicket['result_transaction_id']);
        }

        return $payload;
    }

}