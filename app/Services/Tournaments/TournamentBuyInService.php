<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 1:14 PM
 */

namespace TopBetta\Services\Tournaments;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\TournamentBuyInTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketBuyInHistoryRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Repositories\DbTournamentLeaderboardRepository;
use TopBetta\Services\Betting\BetLimitService;
use TopBetta\Services\DashboardNotification\TournamentDashboardNotificationService;
use TopBetta\Services\Tournaments\Exceptions\TournamentBuyInException;

class TournamentBuyInService
{

    /**
     * @var TournamentBuyInTypeRepositoryInterface
     */
    private $buyInTypeRepository;
    /**
     * @var TournamentTicketBuyInHistoryRepositoryInterface
     */
    private $buyInHistoryRepository;
    /**
     * @var TournamentRepositoryInterface
     */
    private $tournamentRepository;
    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $ticketRepository;
    /**
     * @var DbTournamentLeaderboardRepository
     */
    private $leaderboardRepository;
    /**
     * @var TournamentTransactionService
     */
    private $tournamentTransactionService;
    /**
     * @var TournamentLeaderboardService
     */
    private $leaderboardService;
    /**
     * @var TournamentDashboardNotificationService
     */
    private $dashboardNotificationService;
    /**
     * @var BetLimitService
     */
    private $betLimitService;

    public function __construct(TournamentBuyInTypeRepositoryInterface $buyInTypeRepository,
                                TournamentTicketBuyInHistoryRepositoryInterface $buyInHistoryRepository,
                                TournamentTransactionService $tournamentTransactionService,
                                TournamentRepositoryInterface $tournamentRepository,
                                TournamentTicketRepositoryInterface $ticketRepository,
                                DbTournamentLeaderboardRepository $leaderboardRepository,
                                TournamentLeaderboardService $leaderboardService,
                                TournamentDashboardNotificationService $dashboardNotificationService,
                                BetLimitService $betLimitService)
    {

        $this->buyInTypeRepository          = $buyInTypeRepository;
        $this->buyInHistoryRepository       = $buyInHistoryRepository;
        $this->tournamentRepository = $tournamentRepository;
        $this->ticketRepository = $ticketRepository;
        $this->leaderboardRepository = $leaderboardRepository;
        $this->tournamentTransactionService = $tournamentTransactionService;
        $this->leaderboardService = $leaderboardService;
        $this->dashboardNotificationService = $dashboardNotificationService;
        $this->betLimitService = $betLimitService;
    }

    public function ticketBelongsToUser($ticketId, $userId)
    {
        $ticket = $this->ticketRepository->find($ticketId);

        if ( ! $ticket ) {
            throw new \Exception("Tournament Ticket not found");
        }

        return $ticket->user_id == $userId;
    }

    public function getTotalRebuysForTicket($ticketId)
    {
        $typeId = $this->buyInTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_REBUY);

        return $this->buyInHistoryRepository->getTotalByTicketAndType($ticketId, $typeId);
    }

    public function getTotalTopupsForTicket($ticketId)
    {
        $typeId = $this->buyInTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_TOPUP);

        return $this->buyInHistoryRepository->getTotalByTicketAndType($ticketId, $typeId);
    }

    public function createTournamentEntryHistoryRecord($ticketId, $buyinTransaction = 0, $entryTransaction = 0)
    {
        $typeId = $this->buyInTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_BUYIN);

        return $this->createBuyInHistoryRecord($ticketId, $typeId, $buyinTransaction, $entryTransaction);
    }

    public function createRebuyHistoryRecord($ticketId, $buyinTransaction = 0, $entryTransaction = 0)
    {
        $typeId = $this->buyInTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_REBUY);

        return $this->createBuyInHistoryRecord($ticketId, $typeId, $buyinTransaction, $entryTransaction);
    }

    public function createTopupHistoryRecord($ticketId, $buyinTransaction = 0, $entryTransaction = 0)
    {
        $typeId = $this->buyInTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_TOPUP);

        return $this->createBuyInHistoryRecord($ticketId, $typeId, $buyinTransaction, $entryTransaction);
    }

    private function createBuyInHistoryRecord($ticketId, $typeId, $buyinTransaction = 0, $entryTransaction = 0) {
        return $this->buyInHistoryRepository->create(array(
            "tournament_ticket_id"     => $ticketId,
            "tournament_buyin_type_id" => $typeId,
            "buyin_transaction_id"     => $buyinTransaction,
            "entry_transaction_id"     => $entryTransaction,
        ));
    }

    public function rebuyIntoTournament($ticketId)
    {
        $ticket = $this->ticketRepository->find($ticketId);

        if( ! $ticket ) {
            throw new \Exception("Tournament Ticket not found");
        }
        
        $tournament = $ticket->tournament;
        $leaderboard = $this->leaderboardRepository->getLeaderboardRecordForUserInTournament($ticket->user->id, $tournament->id);
        $rebuys = $this->getTotalRebuysForTicket($ticketId);
        $topups = $this->getTotalTopupsForTicket($ticketId);

        //check account balance
        if($ticket->user->accountBalance() < $tournament->rebuy_buyin + $tournament->rebuy_entry) {
            throw new TournamentBuyInException("Insufficient Funds");
        }

        //check we haven't exceeded max rebuys        
        if($tournament->rebuys <= $rebuys) {
            throw new TournamentBuyInException("Cannot buyin more than : " . $tournament->rebuys . " times.");
        }

        //check rebuy period has not ended
        if($tournament->rebuy_end < Carbon::now()) {
            throw new TournamentBuyInException("Rebuy period has ended");
        }

        //check we've turned over enough
        if($this->getTotalToTurnOverForTournament($tournament, $rebuys, $topups) > $leaderboard->turned_over) {
            throw new TournamentBuyInException("Must turn over all current BettaBucks before rebuying");
        }

        //TODO: check this is correct
        //check currency is less then starting
        if($leaderboard['currency'] > 0) {
            throw new TournamentBuyInException("Must have $0.00 BettaBucks (including unresulted bets) to rebuy");
        }

        //create transactions
        $transactions = $this->tournamentTransactionService->createTournamentBuyInTransactions($ticket->user->id, $tournament->rebuy_buyin, $tournament->rebuy_entry, TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_REBUY);

        if ( ! $transactions ) {
            throw new TournamentBuyInException("Error creating transaction");
        }

        //notify dashboard
        $this->dashboardNotificationService->notify(array("id" => $ticket->id, "tranasctions" => array($transactions['buyin_transaction']['id'], $transactions['entry_transaction']['id'])));

        //create history record
        $this->createRebuyHistoryRecord($ticketId, $transactions['buyin_transaction']['id'], $transactions['entry_transaction']['id']);

        //add funds to leaderboard currency
        $this->leaderboardService->increaseCurrency($leaderboard['id'], $tournament->rebuy_currency, true);

        //increment rebuys
        $ticket = $this->ticketRepository->update($ticket, array("rebuy_count" => $ticket->rebuy_count + 1));

        return $ticket;
    }

    public function topupTournament($ticketId)
    {
        $ticket = $this->ticketRepository->find($ticketId);

        if( ! $ticket ) {
            throw new \Exception("Tournament Ticket not found");
        }

        $tournament = $ticket->tournament;
        $leaderboard = $this->leaderboardRepository->getLeaderboardRecordForUserInTournament($ticket->user->id, $tournament->id);
        $rebuys = $this->getTotalRebuysForTicket($ticketId);
        $topups = $this->getTotalTopupsForTicket($ticketId);

        //check account balance
        if($ticket->user->accountBalance() < $tournament->topup_buyin + $tournament->topup_entry) {
            throw new TournamentBuyInException("Insufficient Funds");
        }

        //check we haven't exceed max topups
        if( $tournament->topups <= $topups ) {
            throw new TournamentBuyInException("Cannot top up more than " . $tournament->topups . " times.");
        }

        //check topup dates
        if( $tournament->topup_start_date > Carbon::now() || $tournament->topup_end_date < Carbon::now()) {
            throw new TournamentBuyInException("Cannot Top up at the moment. Top up period is between " . $tournament->topup_start_date . " and "  . $tournament->topup_end_date);
        }

        //create transactions
        $transactions = $this->tournamentTransactionService->createTournamentBuyInTransactions($ticket->user->id, $tournament->rebuy_buyin, $tournament->rebuy_entry, TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_TOPUP);

        if ( ! $transactions ) {
            throw new TournamentBuyInException("Error creating transaction");
        }

        //notify dashboard
        $this->dashboardNotificationService->notify(array("id" => $ticket->id, "tranasctions" => array($transactions['buyin_transaction']['id'], $transactions['entry_transaction']['id'])));

        //create history record
        $this->createTopupHistoryRecord($ticketId, $transactions['buyin_transaction']['id'], $transactions['entry_transaction']['id']);

        //add funds to leaderboard currency
        $this->leaderboardService->increaseCurrency($leaderboard['id'], $tournament->topup_currency, true);

        //increment rebuys
        $ticket = $this->ticketRepository->update($ticket, array("topup_count" => $ticket->topup_count + 1));

        return $ticket;
    }

    /**
     * @param \TopBetta\Models\TournamentModel $tournament
     * @param \TopBetta\Models\UserModel $user
     * @return array
     * @throws TournamentBuyInException
     */
    public function buyin($tournament, $user)
    {
        //check funds
        if( $tournament->buy_in + $tournament->entry_fee > $user->accountBalance() ) {
            throw new TournamentBuyInException("Insufficient Funds");
        }

        //check daily limit
        $this->betLimitService->checkUserDailyBetLimit($user, $tournament->buy_in + $tournament->entry_fee);

        $transactions = $this->tournamentTransactionService->createTournamentBuyInTransactions($user->id, $tournament->buy_in, $tournament->entry_fee);

        return $transactions;
    }


    private function getTotalToTurnOverForTournament($tournament, $rebuys = 0, $topups = 0 )
    {
        return $tournament['start_currency'] + $tournament['rebuy_currency'] * $rebuys + $tournament['topup_currency'] * $topups;
    }

}