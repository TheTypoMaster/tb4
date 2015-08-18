<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/08/2015
 * Time: 9:18 AM
 */

namespace TopBetta\Services\Betting\BetLiability;


use Illuminate\Support\Collection;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

abstract class AbstractLiabilityCalculator implements LiabilityCalculator {

    /**
     * @var Collection
     */
    protected $bets;

    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;

    public function __construct(BetRepositoryInterface $betRepository)
    {
        $this->betRepository = $betRepository;
    }

    /**
     * Calculates liabilities for events for which bets are being placed
     * @param $betData
     * @return Collection
     */
    public function calculateLiability($betData)
    {
        $liabilities = new Collection;

        //get collection of events
        $events = new Collection(array_unique(array_map(function ($v) { return $v['selection']->market->event; }, $betData['selections'])));

        //get bets for events
        $this->bets = $this->betRepository->getBetsForUserByEvents($betData['user'], $events->lists('id')->all(), $betData['bet_type']->name == BetTypeRepositoryInterface::TYPE_SPORT ? null : $betData['bet_type']->id);

        $selections = new Collection($betData['selections']);

        //get the liability for each event
        foreach ($events as $event) {
            $liabilities->put($event->id, $this->calculateEventLiability($event, $betData, $selections->filter(function($v) use ($event) {
                return $v['selection']->market->event_id == $event->id;
            })));
        }

        return $liabilities;
    }

    /**
     * Calculates the liability of a bet on an event
     * @param $event
     * @param $betData
     * @param $selections
     * @return int
     */
    public function calculateEventLiability($event, $betData, $selections)
    {
        $liability = 0;

        //sum the liability for each market
        foreach ($event->markets as $market) {
            $marketSelections = $selections->filter(function($v) use ($market) { return $v['selection']->market_id == $market->id; });
            $liability += $this->calculateMarketLiability($market, $betData, $marketSelections);
        }

        return $liability;
    }

    public function calculateMarketLiability($market, $betData, $selections)
    {
        $marketLiabilities = new Collection();

        //get bets on the market
        $bets = $this->getBetsForMarket($market, $betData);

        //calculate the current liability
        foreach ($bets as $bet) {
            $marketLiabilities->put($bet->selection->first()->id, $marketLiabilities->get($bet->selection->first()->id, 0) +
                (int)($this->getBetLiability($bet)));
        }

        //calculate the liability for the current bet
        foreach ($selections as $selection) {
            $marketLiabilities->put($selection['selection']->id, $marketLiabilities->get($selection['selection']->id, 0) +
                (int)($betData['amount'] * $this->getSelectionPrice($selection)));
        }

        //get the max liability for the bet selections
        return $this->calculateMaxLiabilityForSelections($market, $selections, $marketLiabilities);

    }

    /**
     * Calculates the max liability for $selections
     * @param $market
     * @param $selections Collection
     * @param $marketLiabilities Collection
     * @return int
     */
    protected function calculateMaxLiabilityForSelections($market, $selections, $marketLiabilities)
    {
        $marketLiabilities = $marketLiabilities->sort();

        $maxLiabilities = $marketLiabilities->slice(-$this->getMaxWinners($market), $this->getMaxWinners($market), true);

        //no selections so just return sum
        if (!$selections->count()) {
            return $maxLiabilities->sum();
        }

        $maxLiability = 0;

        foreach ($selections as $selection) {
            if ($maxLiabilities->get($selection['selection']->id)) {
                //if selections is in max liabilities just take the sum
                $liability = $maxLiabilities->sum();
            } else {
                //selection isn't in max liabilities so sum the selection liability with the max liabilties
                $liability = $maxLiabilities->take(- $maxLiabilities->count() + 1)->sum() + $marketLiabilities->get($selection['selection']->id);
            }

            //check if liability exceeds max so far
            if ($liability > $maxLiability) {
                $maxLiability = $liability;
            }
        }

        return $maxLiability;
    }

    /**
     * Gets bets for market. Filters current $this->bets if not null.
     * @param $market
     * @param $betData
     * @return Collection
     */
    protected function getBetsForMarket($market, $betData)
    {
        if (! $this->bets) {
            return $this->betRepository->getBetsForUserByMarket($betData['user'], $market->id);
        }

        return $this->bets->filter(function ($v) use ($market) { return $v->selection->first()->market_id == $market->id; });
    }

    abstract protected function getSelectionPrice($selection);

    abstract protected function getBetLiability($bet);

    abstract protected function getMaxWinners($market);
}