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
     * @param TournamentLeaderboardService $leaderboardService
     * @param TournamentPlacesPaidRepositoryInterface $placesPaidRepository
     */
    public function __construct(TournamentLeaderboardService $leaderboardService, TournamentPlacesPaidRepositoryInterface $placesPaidRepository)
    {
        $this->placesPaidRepository = $placesPaidRepository;
        $this->leaderboardService = $leaderboardService;
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

        $leaderboard = $this->leaderboardService->getLeaderboard($tournament->id, null, true);

        $rank = 1;

        while($rank <= $percentages->places_paid) {
            $percs = $percentages->pay_perc;

            $usersAtRank = array_filter($leaderboard, function ($v) use ($rank) {return $v['rank'] == $rank;});

            $amount = array_sum(array_splice($percs, $rank - 1, count($usersAtRank)))/100) * $tournament->prizePool()/count($usersAtRank);

            foreach ($usersAtRank as $ticket) {
                $results->push(new TournamentResult($this->ticketRepository))
            }
        }

        $leaderboard = $this->leaderboardService->getLeaderboard($tournament->id, null, true);


    }

    public function getJackpotTournamentResults($tournament)
    {}

    public function getPayoutPercentages($tournament)
    {
        $percentages = null;

        //get number of places paid
        if ($tournament->prizeFormat->keyword == TournamentPrizeFormatRepositoryInterface::PRIZE_FORMAT_ALL) {
            $placesPaid = 1;
        } else if ($tournament->prize->format->keyword == TournamentPrizeFormatRepositoryInterface::PRIZE_FORMAT_TOP3) {
            $placesPaid = 3;
        } else {
            $percentages = $this->placesPaidRepository->getByEntrants($tournament->tickets->count());
            $placesPaid = $percentages->places_paid;
        }

        //check we have enough qualifiers
        if ($placesPaid < $tournament->qualifiers->count()) {
            $percentages = $this->placesPaidRepository->getByPlacesPaid($tournament->qualifiers->count());
        }

        //get percentages
        if (! $percentages) {
            $percentages = $this->placesPaidRepository->getByPlacesPaid($placesPaid);
        }

        return $percentages;
    }
}