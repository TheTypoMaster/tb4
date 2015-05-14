<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 12:00 PM
 */

namespace TopBetta\Services\Betting\BetSelection;

use TopBetta\Repositories\BetLimitRepo;
use TopBetta\Repositories\Contracts\BetSelectionRepositoryInterface;
use TopBetta\Services\Betting\EventService;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;
use TopBetta\Services\Betting\MarketService;
use TopBetta\Services\Betting\SelectionService;

abstract class AbstractBetSelectionService {

    /**
     * @var SelectionService
     */
    protected $selectionService;
    /**
     * @var MarketService
     */
    protected $marketService;
    /**
     * @var EventService
     */
    protected $eventService;
    /**
     * @var BetSelectionRepositoryInterface
     */
    protected $betSelectionRepository;
    /**
     * @var BetLimitRepo
     */
    protected $betLimitRepo;

    public function __construct(SelectionService $selectionService, MarketService $marketService, EventService $eventService, BetSelectionRepositoryInterface $betSelectionRepository, BetLimitRepo $betLimitRepo)
    {
        $this->selectionService = $selectionService;
        $this->marketService = $marketService;
        $this->eventService = $eventService;
        $this->betSelectionRepository = $betSelectionRepository;
        $this->betLimitRepo = $betLimitRepo;
    }

    public function createSelections($bet, $selections)
    {
        if( ! is_array($selections) ) {
            return $this->createSelection($bet, $selections);
        }

        $betSelections = array();
        foreach ($selections as $selection) {
            $betSelections[] = $this->createSelection($bet, $selection);
        }

        return $betSelections;
    }

    public function createSelection($bet, $selection, $extraData = array())
    {
        $data = array(
            'bet_id' => $bet['id'],
            'selection_id' => $selection->id,
        );

        $data = array_merge($extraData, $data);

        return $this->betSelectionRepository->create($data);
    }

    public function validateSelection($selection)
    {
        if( ! $this->selectionService->isSelectionAvailableForBetting($selection) ) {
            throw new BetSelectionException($selection, Lang::get("bets.selection_scratched"));
        }

        if ( ! $this->marketService->isSelectionMarketAvailableForBetting($selection) ) {
            throw new BetSelectionException($selection, Lang::get("bets.market_closed"));
        }

        //TODO: DIFFERENT MESSAGE
        if( ! $this->eventService->isSelectionEventAvailableForBetting($selection) ) {
            throw new BetSelectionException($selection, Lang::get("bets.market_closed"));
        }
    }

    abstract public function getAndValidateSelections($selections);

    abstract public function checkBetLimit($user, $amount, $betType, $selections);
}