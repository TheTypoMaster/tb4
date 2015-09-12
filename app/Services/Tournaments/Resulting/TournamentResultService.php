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
     * @var Collection;
     */
    private $results;

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
        $this->results = new Collection;
    }

    /**
     * Get Tournament results
     * @param $tournament
     * @return Collection
     */
    public function getTournamentResults($tournament)
    {
        if ($tournament->jackpot_flag && $tournament->parent_tournament_id > 0) {
            return $this->getJackpotTournamentResults($tournament);
        }

        return $this->getCashTournamentResults($tournament);
    }

    /**
     * Get results for a cash tournament
     * @param $tournament
     * @return Collection
     */
    public function getCashTournamentResults($tournament)
    {
        $this->results = new Collection;

        $percentages = $this->getPayoutPercentages($tournament);

        //get the tournament leaderboard
        $leaderboard = $this->leaderboardService->getLeaderboard($tournament->id, null, true);

        $this->createCashResults($tournament, $percentages, $leaderboard, $tournament->prizePool());

        return $this->results;
    }

    /**
     * Get result for a jackpot tournament
     * @param $tournament
     * @return Collection
     */
    public function getJackpotTournamentResults($tournament)
    {
        $this->results = new Collection;

        //get the tournament leaderboard
        $leaderboard = $this->leaderboardService->getLeaderboard($tournament->id, null, true);

        $placesPaid = $this->getJackpotTournamentPlacesPaid($tournament);

        $nextRank = $this->createJackpotResults($tournament, $leaderboard, $placesPaid);

        $this->createRemainderResults($tournament, $nextRank, $leaderboard);

        return $this->results;
    }

    /**
     * Create results for remainder in jackpot tournaments
     * @param $tournament
     * @param $nextRank
     * @param $leaderboard
     */
    public function createRemainderResults($tournament, $nextRank, $leaderboard)
    {
        $jackpotTournamentCost = $tournament->parentTournament->buy_in + $tournament->parentTournament->entry_fee;

        $remainder = $tournament->prizePool() - ($nextRank - 1) * $jackpotTournamentCost;

        if ($remainder && $tournament->buy_in > 0) {
            if ($tournament->qualifiers->count() < $nextRank) {
                $percentages = $this->getPayoutPercentages($tournament);
                $this->createCashResults($tournament, $percentages, $leaderboard, $remainder);
            } else {
                $usersAtRank =  $usersAtRank = array_filter($leaderboard, function ($v) use ($nextRank) { return $v['rank'] == $nextRank; });
                foreach ($usersAtRank as $leaderboardRecord) {
                    $this->createCashTournamentPrizeForTournamentUser($leaderboardRecord['id'], $tournament, floor($remainder/count($usersAtRank)));
                }
            }
        }
    }

    /**
     * Get no of tickets paid for a jackpot tournament
     * @param $tournament
     * @return int
     */
    public function getJackpotTournamentPlacesPaid($tournament)
    {
        $jackpotTournamentCost = $tournament->parentTournament->buy_in + $tournament->parentTournament->entry_fee;

        $placesPaid = floor($tournament->prizePool() / $jackpotTournamentCost);

        if ($placesPaid > $tournament->qualifiers->count()) {
            $placesPaid = $tournament->qualifiers->count();
        }

        return (int) $placesPaid;
    }

    /**
     * Build jackpot tournament ticket results
     * @param $tournament
     * @param $leaderboard
     * @param $noTickets
     * @return int
     */
    public function createJackpotResults($tournament, $leaderboard, $noTickets)
    {
        for ($rank = 1; $rank <= $noTickets; $rank += count($usersAtRank)) {
            //get the users at rank $rank
            $usersAtRank = array_filter($leaderboard, function ($v) use ($rank) { return $v['rank'] == $rank; });

            if ($rank - 1 + count($usersAtRank) > $noTickets) {
                break;
            }

            foreach ($usersAtRank as $leaderboardRecord) {
                $result = $this->createJackpotTournamentPrizeForTournamentUser($leaderboardRecord['id'], $tournament);
                $result->setPosition($rank);
            }
        }

        return $rank;
    }

    /**
     * Build cash prize results
     * @param $tournament
     * @param $percentages
     * @param $leaderboard
     * @param $prizePool
     * @return Collection
     */
    public function createCashResults($tournament, $percentages, $leaderboard, $prizePool)
    {
        for ($rank = 1; $rank <= $percentages->places_paid; $rank += count($usersAtRank)) {
            $percs = $percentages->pay_perc;

            //get the users at rank $rank
            $usersAtRank = array_filter($leaderboard, function ($v) use ($rank) { return $v['rank'] == $rank; });

            //get the payout amount for each user at $rank
            $amount = floor((array_sum(array_splice($percs, $rank - 1, count($usersAtRank)))/100) * $prizePool/count($usersAtRank));

            //create the results for each user
            foreach ($usersAtRank as $leaderboardRecord) {
                $result = $this->createCashTournamentPrizeForTournamentUser($leaderboardRecord['id'], $tournament, $amount);
                $result->setPosition($rank);
            }
        }

        return $this->results;
    }

    /**
     * Get result if exists or create and add cash amount
     * @param $user
     * @param $tournament
     * @param $amount
     * @return mixed|TournamentResult
     */
    public function createCashTournamentPrizeForTournamentUser($user, $tournament, $amount)
    {
        $result = $this->getResult($user, $tournament);

        if ($tournament->free_credit_flag || $tournament->jackpot_flag) {
            $result->setFreeCreditAmount($amount);
        } else {
            $result->setAmount($result->getAmount() + $amount);
        }

        return $result;
    }

    /**
     * Get result if exists or create and set jackpot tournament
     * @param $user
     * @param $tournament
     * @return mixed|TournamentResult
     */
    public function createJackpotTournamentPrizeForTournamentUser($user, $tournament)
    {
        $result = $this->getResult($user ,$tournament);

        $result->setJackpotTicket($tournament->parentTournament);

        if( $this->ticketRepository->getTicketByUserAndTournament($user, $tournament->id) ) {
            $result->setJackpotTicketExists(true);
        }

        return $result;
    }

    /**
     * Get or create result from collection
     * @param $user
     * @param $tournament
     * @return mixed|TournamentResult
     */
    public function getResult($user, $tournament)
    {
        if (!$result = $this->results->get($user)) {
            $result = new TournamentResult($this->ticketRepository->getTicketByUserAndTournament($user, $tournament->id));
            $this->results->put($user, $result);
        }

        return $result;
    }

    /**
     * Get the percentages for cash tournament multiple payout
     * @param $tournament
     * @return mixed
     */
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