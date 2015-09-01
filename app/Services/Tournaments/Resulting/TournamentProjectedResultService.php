<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 1/09/2015
 * Time: 1:50 PM
 */

namespace TopBetta\Services\Tournaments\Resulting;


use Illuminate\Support\Collection;
use TopBetta\Repositories\Contracts\TournamentPrizeFormatRepositoryInterface;
use TopBetta\Services\Tournaments\TournamentPlacesPaidService;

class TournamentProjectedResultService {

    private $results;
    /**
     * @var TournamentPlacesPaidService
     */
    private $placesPaidService;

    public function __construct(TournamentPlacesPaidService $placesPaidService)
    {
        $this->placesPaidService = $placesPaidService;
    }

    /**
     * Get Tournament results
     * @param $tournament
     * @return Collection
     */
    public function getTournamentResults($tournament)
    {
        $this->results = new Collection;

        if ($tournament->jackpot_flag && $tournament->parent_tournament_id > 0) {
            return $this->getJackpotTournamentResults($tournament);
        }

        return $this->getCashTournamentResults($tournament, $tournament->prizePool());
    }

    public function getCashTournamentResults($tournament, $prizePool)
    {
        $percentages = $this->getPayoutPercentages($tournament);

        for ($i=0; $i < count($percentages); $i++) {
            $result = $tournament->free_credit_flag ?
                $this->createFreeCreditResult($i+1, $percentages * $prizePool) :
                $this->createCashResult($i+1, $percentages * $prizePool);
        }

        return $this->results;
    }

    public function getJackpotTournamentResults($tournament)
    {
        $ticketCost = $tournament->parentTournament->buy_in + $tournament->parentTournament->entry_fee;

        $prizePool = $tournament->prizePool();

        $placesPaid = $this->getJackpotTournamentPlacesPaid( $tournament, $ticketCost, $prizePool);

        for ($i=0; $i<$placesPaid; $i++) {
            $result = $this->createJackpotTicketResult($i+1, $tournament);
            $prizePool -= $ticketCost;
        }

        if ($prizePool > 0 && !$tournament->buy_in > 0) {
            $this->getRemainderResults($tournament, $prizePool, $placesPaid+1);
        }

        return $this->results;
    }

    public function getRemainderResults($tournament, $remainder, $nextRank)
    {
        if ($nextRank > $tournament->tickets->count()) {
           return  $this->getCashTournamentResults($tournament, $remainder);
        }

        return $this->createCashResult($nextRank, $remainder);
    }

    /**
     * Get no of tickets paid for a jackpot tournament
     * @param $tournament
     * @return int
     */
    public function getJackpotTournamentPlacesPaid($tournament, $jackpotTournamentCost, $prizePool)
    {

        $placesPaid = floor($prizePool / $jackpotTournamentCost);

        if ($placesPaid > $tournament->tickets->count()) {
            $placesPaid = $tournament->tickets->count();
        }

        return (int) $placesPaid;
    }

    public function createCashResult($position, $amount)
    {
        if (! $result = $this->results->get($position) ) {
            $result = new TournamentResult();
            $result->setAmount($amount);
            $this->results->put($position, $result);
        }

        return $result;
    }

    public function createFreeCreditResult($position, $amount)
    {
        if (! $result = $this->results->get($position) ) {
            $result = new TournamentResult();
            $result->setFreeCreditAmount($amount);
            $this->results->put($position, $result);
        }

        return $result;
    }

    public function createJackpotTicketResult($position, $tournament)
    {
        if (! $result = $this->results->get($position) ) {
            $result = new TournamentResult();
            $result->setJackpotTicket($tournament->parentTournament);
            $this->results->put($position, $result);
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