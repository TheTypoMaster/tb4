<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 1:14 PM
 */

namespace TopBetta\Services\Tournaments;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\AccountTransactionTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\FreeCreditTransactionTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBuyInTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketBuyInHistoryRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Repositories\DbTournamentLeaderboardRepository;
use TopBetta\Repositories\DbTournamentTicketRepository;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Accounting\FreeCreditTransactionService;
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
     * @var AccountTransactionService
     */
    private $accountTransactionService;
    /**
     * @var FreeCreditTransactionService
     */
    private $freeCreditTransactionService;
    /**
     * @var TournamentRepositoryInterface
     */
    private $tournamentRepository;
    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $ticketRepository;

    public function __construct(TournamentBuyInTypeRepositoryInterface $buyInTypeRepository,
                                TournamentTicketBuyInHistoryRepositoryInterface $buyInHistoryRepository,
                                AccountTransactionService $accountTransactionService,
                                FreeCreditTransactionService $freeCreditTransactionService,
                                TournamentRepositoryInterface $tournamentRepository,
                                TournamentTicketRepositoryInterface $ticketRepository,
                                DbTournamentLeaderboardRepository $leaderboardRepository)
    {

        $this->buyInTypeRepository          = $buyInTypeRepository;
        $this->buyInHistoryRepository       = $buyInHistoryRepository;
        $this->accountTransactionService    = $accountTransactionService;
        $this->freeCreditTransactionService = $freeCreditTransactionService;
        $this->tournamentRepository = $tournamentRepository;
        $this->ticketRepository = $ticketRepository;
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

    public function createEntryHistoryRecord($ticketId)
    {
        $typeId = $this->buyInTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_BUYIN);

        return $this->createBuyInHistoryRecord($ticketId, $typeId);
    }

    public function createRebuyHistoryRecord($ticketId)
    {
        $typeId = $this->buyInTypeRepository->getIdByKeyword(TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_REBUY);

        return $this->createBuyInHistoryRecord($ticketId, $typeId);
    }

    private function createBuyInHistoryRecord($ticketId, $typeId) {
        return $this->buyInHistoryRepository->create(array(
            "tournament_ticket_id"     => $ticketId,
            "tournament_buyin_type_id" => $typeId,
        ));
    }

    public function rebuyIntoTournament($ticketId)
    {
        $ticket = $this->ticketRepository->find($ticketId);

        if( ! $ticket ) {
            throw new \Exception("Tournament Ticket not found");
        }
        
        $tournament = $ticket['tournament'];
        $leaderboard = $this->getLeaderboardRecordForUserInTournament($ticket['user']['id'], $tournament['id']);
        $rebuys = $this->getTotalRebuysForTicket($ticketId);
        $topups = $this->getTotalTopupsForTicket($ticketId);

        //check we haven't exceeded max rebuys        
        if($tournament['rebuys'] <= $rebuys) {
            throw new TournamentBuyInException("Cannot buyin more than : " . $tournament['rebuys'] . " times.");
        }

        //check rebuy period has not ended
        if($tournament['rebuy_end'] < Carbon::now()) {
            throw new TournamentBuyInException("Rebuy period has ended");
        }

        //check we've turned over enough
        if($this->getTotalToTurnOverForTournament($tournament, $rebuys, $topups) > $leaderboard['turned_over']) {
            throw new TournamentBuyInException("Must turn over all current BettaBucks before rebuying");
        }

        //TODO: check this is correct
        //check currency is less then starting
        if($leaderboard['currency'] >= $tournament['start_currency']) {
            throw new TournamentBuyInException("Total BettaBucks must be less than " . $tournament['start_currency'] . " to rebuy");
        }

        //create transactions
        $transactions = $this->createTournamentBuyInTransactions($ticket['user']['id'], $tournament['rebuy_buyin'], $tournament['rebuy_entry'], TournamentBuyInTypeRepositoryInterface::TOURNAMENT_BUYIN_TYPE_REBUY);

        //create history record
        return $this->createRebuyHistoryRecord($ticketId);
    }

    private function getTotalToTurnOverForTournament(array $tournament, $rebuys = 0, $topups = 0 )
    {
        return $tournament['start_currency'] + $tournament['rebuy_currency'] * $rebuys + $tournament['topup_currency'] * $topups;
    }
}