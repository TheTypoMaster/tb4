<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 10/08/2015
 * Time: 3:42 PM
 */

namespace TopBetta\Services\Tournaments\Resulting;


use Illuminate\Support\Collection;
use TopBetta\Repositories\Contracts\TournamentLeaderboardRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentPlacesPaidRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentPrizeFormatRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Services\Tournaments\TournamentLeaderboardService;
use TopBetta\Services\Tournaments\TournamentPlacesPaidService;

class TournamentResultService {

    /**
     * @var TournamentLeaderboardService
     */
    private $leaderboardService;
    /**
     * @var TournamentPlacesPaidService
     */
    private $placesPaidService;
    /**
     * @var TournamentTicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param TournamentLeaderboardService $leaderboardService
     * @param TournamentPlacesPaidService $placesPaidService
     * @param TournamentTicketRepositoryInterface $ticketRepository
     * @internal param TournamentLeaderboardRepositoryInterface $leaderboardRepository
     */
    public function __construct(TournamentLeaderboardService $leaderboardService, TournamentPlacesPaidService $placesPaidService, TournamentTicketRepositoryInterface $ticketRepository)
    {
        $this->leaderboardService = $leaderboardService;
        $this->placesPaidService = $placesPaidService;
        $this->ticketRepository = $ticketRepository;
    }

    public function getTournamentResults($tournament)
    {
        if ($tournament->jackpot_flag && $tournament->parent_tournament_id > 0) {
            return $this->getJackpotTournamentResults($tournament);
        }

        return $this->getCashTournamentResults($tournament);
    }

    public function getCashTournamentResults($tournament)
    {
        $percentages = $this->getPayoutPercentages($tournament);

        //get the tournament leaderboard
        $leaderboard = $this->leaderboardService->getLeaderboard($tournament->id, null, true);

        return $this->createCashResults($tournament, $percentages, $leaderboard, $tournament->prizePool());
    }

    public function getJackpotTournamentResults($tournament)
    {
        //get the tournament leaderboard
        $leaderboard = $this->leaderboardService->getLeaderboard($tournament->id, null, true);

        $jackpotTournamentCost = $tournament->parentTournament->buy_in + $tournament->parentTournament->entry_fee;

        $placesPaid = floor($tournament->prizePool / $jackpotTournamentCost);

        if ($placesPaid > $tournament->qualifiers->count()) {
            $placesPaid = $tournament->qualifiers->count();
        }
    }

    public function createJackpotResults($tournament, $leaderboard, $noTickets)
    {
        $results = new Collection;


    }

    public function createCashResults($tournament, $percentages, $leaderboard, $prizePool)
    {
        $results = new Collection;

        for ($rank = 1; $rank <= $percentages->places_paid; $rank += count($usersAtRank)) {
            $percs = $percentages->pay_perc;

            //get the users at rank $rank
            $usersAtRank = array_filter($leaderboard, function ($v) use ($rank) { return $v['rank'] == $rank; });

            //get the payout amount for each user at $rank
            $amount = (array_sum(array_splice($percs, $rank - 1, count($usersAtRank)))/100) * $prizePool/count($usersAtRank);

            //create the results for each user
            foreach ($usersAtRank as $leaderboardRecord) {
                $results->push($this->createCashTournamentPrizeForTournamentUser($leaderboardRecord['id'], $tournament, $amount));
            }
        }

        return $results;
    }

    public function createCashTournamentPrizeForTournamentUser($user, $tournament, $amount)
    {
        return CashPrizeFactory::make(array(
            "ticket" => $this->ticketRepository->getTicketByUserAndTournament($user, $tournament->id),
            "amount" => $amount
        ));
    }

    public function getPayoutPercentages($tournament)
    {
        switch ($tournament->prizeFormat->keyword) {
            case TournamentPrizeFormatRepositoryInterface::PRIZE_FORMAT_ALL:
                return $this->placesPaidService->getPercentagesForTournamentByPlacesPaid($tournament, 1);
            case TournamentPrizeFormatRepositoryInterface::PRIZE_FORMAT_TOP3:
                return $this->placesPaidService->getPercentagesForTournamentByPlacesPaid($tournament, 3);
            case TournamentPrizeFormatRepositoryInterface::PRIZE_FORMAT_MULTIPLE:
                return $this->placesPaidService->getPercentagesForTournamentByEntrants($tournament);
        }

        throw new \InvalidArgumentException("Invalid tournament prize format");
    }

}