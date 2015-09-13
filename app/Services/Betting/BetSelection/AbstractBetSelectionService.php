<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 12:00 PM
 */

namespace TopBetta\Services\Betting\BetSelection;

use Lang;
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

    public function __construct(SelectionService $selectionService, MarketService $marketService, EventService $eventService, BetSelectionRepositoryInterface $betSelectionRepository)
    {
        $this->selectionService = $selectionService;
        $this->marketService = $marketService;
        $this->eventService = $eventService;
        $this->betSelectionRepository = $betSelectionRepository;
    }

    /**
     * Create the selection records
     * @param $bet
     * @param $selections
     * @return array
     */
    public function createSelections($bet, $selections)
    {
        //create selections
        $betSelections = array();
        foreach ($selections as $selection) {
            $betSelections[] = $this->createSelection($bet, $selection);
        }

        return $betSelections;
    }

    /**
     * Create a selection record
     * @param $bet
     * @param $selection
     * @param array $extraData
     * @return mixed
     */
    public function createSelection($bet, $selection, $extraData = array())
    {
        $data = array(
            'bet_id' => $bet['id'],
            'selection_id' => $selection['selection']->id,
            'position' => 0,
        );

        $data = array_merge($data, $extraData);

        return $this->betSelectionRepository->create($data);
    }

    /**
     * validates the selection
     * @param $selection
     * @param int $winDividend
     * @param int $placeDividend
     * @throws BetSelectionException
     */
    public function validateSelection($selection, $winDividend = 0, $placeDividend = 0)
    {
        //selection is in correct status
        if( ! $this->selectionService->isSelectionAvailableForBetting($selection) ) {
            throw new BetSelectionException($selection, Lang::get("bets.selection_scratched"));
        }

        //market is available
        if( ! $this->marketService->isSelectionMarketAvailableForBetting($selection) ) {
            throw new BetSelectionException($selection, Lang::get("bets.market_closed"));
        }

        //event is available
        if( ! $this->eventService->isSelectionEventAvailableForBetting($selection) ) {
            throw new BetSelectionException($selection, Lang::get("bets.event_closed"));
        }

        //TODO: check competition ?
    }

    /**
     * Gets the selection models and validates
     * each selection is stored in array with key 'selection' and any extra data
     * @param $selections
     * @return array
     * @throws BetSelectionException
     */
    public function getAndValidateSelections($selections)
    {
        $selectionModels = array();

        foreach($selections as $selection) {

            $selectionModel = $this->selectionService->getSelection($selection['id']);

            if( ! $selectionModel ) {
                throw new BetSelectionException(null, "Selection not found");
            }

            $this->validateSelection($selectionModel, array_get($selection, 'win_dividend', 0), array_get($selection, 'place_dividend', 0));

            $selectionModels[] = array("selection" => $selectionModel, 'win_dividend' => array_get($selection, 'win_dividend', 0), 'place_dividend' => array_get($selection, 'place_dividend', 0));
        }

        return $selectionModels;
    }
}