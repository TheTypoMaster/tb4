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
        $tournamentTicket = $this->tournamentTicketRepository->getWithUserAndTournament($data['id']);

        //create payload
        $payload = array(
            "ticket_username" => array_get($tournamentTicket, "user.username", null),
            "ticket_extra_starting_currency" => array_get($tournamentTicket, 'extra_starting_currency', null),
            "ticket_entry_fee" => array_get($tournamentTicket, "tournament.entry_fee", 0),
            "ticket_buy_in" => array_get($tournamentTicket, "tournament.buy_in", 0),
            "external_id" => array_get($tournamentTicket, "id", 0),
            "transactions" => array(),
            "tournament" => null,
            "user" => null,
        );

        //format nested resources
        if( $tournament = array_get($tournamentTicket, 'tournament', null) ) {
            $payload['tournament'] = $this->formatTournament($tournament);
        }

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

    /**
     * Formats the tournament resource
     * @param $tournament
     * @return array
     */
    private function formatTournament($tournament)
    {
        return array(
            "external_id"                                   => array_get($tournament, "id", 0),
            "tournament_start_date"                         => array_get($tournament, "start_date", null),
            "tournament_end_date"                           => array_get($tournament, "end_date", null),
            "tournament_name"                               => array_get($tournament, 'name', null),
            "tournament_description"                        => array_get($tournament, "description", null),
            "tournament_start_currency"                     => array_get($tournament, "start_currency", null),
            "tournament_jackpot_flag"                       => array_get($tournament, "jackpot_flag", null),
            "tournament_buy_in"                             => array_get($tournament, "buy_in", null),
            "tournament_entry_fee"                          => array_get($tournament, "entry_fee", null),
            "tournament_minimum_prize_pool"                 => array_get($tournament, "minimum_prize_pool", null),
            "tournament_paid_flag"                          => (bool)array_get($tournament, "paid_flag", false),
            "tournament_auto_create_flag"                   => (bool)array_get($tournament, "auto_create_flag", false),
            "tournament_cancelled_flag"                     => (bool)array_get($tournament, "cancelled_flag", false),
            "tournament_cancelled_reason"                   => array_get($tournament, "cancelled_reason", null),
            "tournament_status_flag"                        => (bool)array_get($tournament, "status_flag", false),
            "tournament_closed_betting_on_first_match_flag" => (bool)array_get($tournament, "closed_betting_on_first_match_flag", false),
            "tournament_betting_closed_date"                => array_get($tournament, "betting_closed_date", null),
            "tournament_reinvest_winnings_flag"             => (bool)array_get($tournament, "reinvest_winnings_flag", false),
            "tournament_bet_limit_flag"                     => (bool)array_get($tournament, "bet_limit_flag", false),
            "tournament_bet_limit_per_event"                => array_get($tournament, "bet_limit_per_event", null),
            "tournament_created_date"                       => array_get($tournament, "created_date", null),
            "tournament_updated_date"                       => array_get($tournament, "updated_date", null),
            "tournament_private_flag"                       => (bool)array_get($tournament, "private_flag", false),
            "tournament_tod_flag"                           => array_get($tournament, "tod_flag", null),
            "tournament_free_credit_flag"                   => (bool)array_get($tournament, 'free_credit_flag', false),
            "tournament_tournament_sponsor_name"            => array_get($tournament, "tournament_sponsor_name", null),
            "tournament_tournament_sponsor_logo"            => array_get($tournament, "tournament_sponsor_logo", null),
            "tournament_tournament_sponsor_logo_link"       => array_get($tournament, "tournament_sponsor_logo_link", null),
            "tournament_tournament_prize_format"            => array_get($tournament, "prize_format", null),
            "tournament_entries_close"                      => array_get($tournament, "entries_close", null),
        );
    }


}