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
use TopBetta\Services\Tournaments\TournamentLeaderboardService;

class TournamentResultService {

    /**
     * @var TournamentPlacesPaidRepositoryInterface
     */
    private $placesPaidRepository;
    /**
     * @var TournamentLeaderboardService
     */
    private $leaderboardService;
    /**
     * @var TournamentLeaderboardRepositoryInterface
     */
    private $leaderboardRepository;

    /**
     * @param TournamentLeaderboardService $leaderboardService
     * @param TournamentPlacesPaidRepositoryInterface $placesPaidRepository
     * @param TournamentLeaderboardRepositoryInterface $leaderboardRepository
     */
    public function __construct(TournamentLeaderboardService $leaderboardService, TournamentPlacesPaidRepositoryInterface $placesPaidRepository, TournamentLeaderboardRepositoryInterface $leaderboardRepository)
    {
        $this->placesPaidRepository = $placesPaidRepository;
        $this->leaderboardService = $leaderboardService;
        $this->leaderboardRepository = $leaderboardRepository;
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
        $results = new Collection;

        $percentages = $this->getPayoutPercentages($tournament);

        //return empty collection if now qualifiers
        if (! $percentages) {
            return $results;
        }

        //get the tournament leaderboard
        $leaderboard = $this->leaderboardService->getLeaderboard($tournament->id, null, true);

        for($rank = 1; $rank <= $percentages->places_paid; $rank += count($usersAtRank)) {
            $percs = $percentages->pay_perc;

            //get the users at rank $rank
            $usersAtRank = array_filter($leaderboard, function ($v) use ($rank) {return $v['rank'] == $rank;});

            //get the payout amount for each user at $rank
            $amount = (array_sum(array_splice($percs, $rank - 1, count($usersAtRank)))/100) * $tournament->prizePool()/count($usersAtRank);

            //create the results for each user
            foreach ($usersAtRank as $leaderboard) {
                $results->push($this->createTournamentResult($leaderboard['id'], $amount));
            }
        }

        return $results;
    }

    public function getJackpotTournamentResults($tournament)
    {}

    public function createTournamentResult($leaderboardId, $amount = 0, $tournament = null)
    {
        return new TournamentResult(
            $this->leaderboardRepository->find($leaderboardId),
            $amount,
            $tournament
        );
    }

    public function getPayoutPercentages($tournament)
    {
        //get number of places paid
        if ($tournament->prizeFormat->keyword == TournamentPrizeFormatRepositoryInterface::PRIZE_FORMAT_ALL) {
            $placesPaid = 1;
        } else if ($tournament->prizeFormat->keyword == TournamentPrizeFormatRepositoryInterface::PRIZE_FORMAT_TOP3) {
            $placesPaid = 3;
        } else {
            $percentages = $this->placesPaidRepository->getByEntrants($tournament->tickets->count());
            $placesPaid = (int)$percentages->places_paid;
        }

        //check we have enough qualifiers
        if ($placesPaid > $tournament->qualifiers->count()) {
            return $this->placesPaidRepository->getByPlacesPaid($tournament->qualifiers->count());
        }

        //get percentages
        if (! $percentages) {
            return $this->placesPaidRepository->getByPlacesPaid($placesPaid);
        }

        return $percentages;
    }
}