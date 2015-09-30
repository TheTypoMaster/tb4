<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/04/2015
 * Time: 10:33 AM
 */

namespace TopBetta\Services\Feeds\Processors;

use Carbon\Carbon;
use Log;
use TopBetta\Models\MarketModel;
use TopBetta\Models\TeamModel;
use TopBetta\Repositories\Cache\Sports\EventRepository;
use TopBetta\Repositories\Cache\Sports\MarketRepository;
use TopBetta\Repositories\Cache\Sports\SelectionPriceRepository;
use TopBetta\Repositories\Cache\Sports\SelectionRepository;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\DbSelectionPriceRepository;
use TopBetta\Services\Events\CompetitorService;

class SelectionListProcessor extends AbstractFeedProcessor {

    /**
     * @var MarketRepositoryInterface
     */
    private $marketRepository;
    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var DbSelectionPricesRepository
     */
    private $selectionPricesRepository;
    /**
     * @var CompetitorService
     */
    private $competitorService;

    private $events = array();

    private $markets = array();

    private $competitors = array();

    public function __construct(MarketRepository $marketRepository,
                                SelectionRepository $selectionRepository,
                                EventRepository $eventRepository,
                                SelectionPriceRepository $selectionPricesRepository,
                                CompetitorService $competitorService)
    {
        $this->marketRepository = $marketRepository;
        $this->selectionRepository = $selectionRepository;
        $this->eventRepository = $eventRepository;
        $this->selectionPricesRepository = $selectionPricesRepository;
        $this->competitorService = $competitorService;
        $this->logprefix = 'SportsFeedService - SelectionListProcessor: ';
    }

    public function process($data)
    {
        if ( ! ($eventId = array_get($data, 'event_id', null)) || ! ($marketId = array_get($data, 'market_id', null))) {
            Log::error($this->logprefix."No EventId or MarketId");
            return 0;
        }

        if ( ! $event = $this->modelContainer->getEvent($eventId)) {
            if ((!$event = $this->eventRepository->getBySerenaId($eventId)) && (!array_get($data, 'GameId') || (!$event = $this->eventRepository->getEventModelFromExternalId($data['GameId']))) ){
                Log::error($this->logprefix . "Event $eventId does not exist");
                return 0;
            }

            $this->events[$eventId] = $event;
        }

        //get the market
        if ( ! $market = $this->modelContainer->getMarket($marketId)) {
            if ((!$market = $this->marketRepository->getBySerenaId($marketId)) && (!array_get($data, 'GameId') || !array_get($data, 'MarketId') || (!$market = $this->marketRepository->getMarketModelByExternalIds($data['MarketId'], $data['GameId'])) )) {
                Log::error($this->logprefix."Market $marketId does not exist");
                return 0;
            }

            $market->event = $event;
            $this->markets[$marketId] = $market;
        }


        Log::debug($this->logprefix."Selection/Price - GameId: " . $data['event_id'].", MarketId: ".$data['market_id'].", SelectionNo: ".$data['selection_id'].", Odds ".$data['selection_odds'].", Line: " .$data['selection_line']);

        //process selection
        $selection = $this->processSelection($market['id'], $data);
        $selection->market = $market;

        //process odds
        if($selection) {
            //process price
            if(Carbon::now('Australia/Sydney') < $event->start_date){
                $selection->price = $this->processSelectionPrice($selection['id'], $data);
            }

            //process line for market
            if($line = array_get($data, 'Line', 0) ) {
                $this->processMarketLine($market, $line);
            }

            $this->selectionRepository->addSelectionToMarket($selection, $market, $event->id, $event->start_date);

            return $selection['id'];
        }

        return 0;
    }

    private function processSelection($marketId, $data)
    {
        //selection data
        $selectionData = array(
            "market_id" => $marketId,
            "serena_selection_id" => $data['selection_id'],
            "external_selection_id" => array_get($data, 'SelectionNo'),
            "external_event_id" => array_get($data, 'GameId'),
            "external_market_id" => array_get($data, 'MarketId'),
            "home_away" => array_get($data, 'selection_home_away', null),
        );

        if( $selectionName = array_get($data, 'selection_name', null) ) {
            $selectionData['name'] = $selectionName;
        }

        //selection status
        if( $selectionStatus = array_get($data, 'selection_trading_status', null) ) {
            if($selectionStatus == 'S') { $selectionData['selection_status_id'] = 4; }
            else if ($selectionStatus == 'T') { $selectionData['selection_status_id'] = 1; }
        }

        //check if selection already exists
        if ($selection = $this->selectionRepository->getBySerenaId($data['selection_id'])) {
            $this->selectionRepository->update($selection, $selectionData);
        }
        if (array_get($data, 'SelectionNo') && array_get($data, 'MarketId') && array_get($data, 'GameId') && ($selection = $this->selectionRepository->getModelByExternalIds($data['SelectionNo'], $data['MarketId'], $data['GameId']))) {
            $this->selectionRepository->update($selection, $selectionData);
        } else {
            //create it otherwise
            $selection = $this->selectionRepository->create($selectionData);
        }

        if( $competitorId = array_get($data, 'selection_type_id', null) ) {


            if ((array_get($data, 'selection_type_name') == 'team' && $competitor = $this->modelContainer->getTeam($competitorId)) || ((array_get($data, 'selection_type_name') == 'player' && $competitor = $this->modelContainer->getPlayer($competitorId)))) {
                $this->competitorService->addCompetitorModelToSelection($selection, $competitor);
            } else {
                $competitor = $this->competitorService->addCompetitorToSelection($selection['id'], $competitorId, array_get($data, 'CompetitorId'), array_get($data, 'selection_type_name'));
                $this->competitors['CompetitorId'] = $competitor;
            }

            if ($competitor instanceof TeamModel) {
                $selection->team = $competitor;
                $this->modelContainer->addTeam($competitor, $competitorId);
            } else {
                $selection->player = $competitor;
                $this->modelContainer->addPlayer($competitor, $competitorId);
            }

        }

        return $selection;
    }

    private function processSelectionPrice($selection, $data)
    {
        //price data
        $priceData = array(
            "selection_id" => $selection,
            "win_odds" => array_get($data, 'Odds', 0) / 100,
            "line" => array_get($data, 'Line', 0)
        );

        if ($price = $this->selectionPricesRepository->getPriceForSelection($selection)) {
            return $this->selectionPricesRepository->update($price, $priceData);
        }

        return $this->selectionPricesRepository->create($priceData);
    }

    private function processMarketLine($market, $line)
    {
        $marketModel = clone $market;
        unset($marketModel->event);
        unset($marketModel->markettype);
        return $this->marketRepository->update($marketModel, array('line' => str_replace(array("+", "-"), "", $line)));
    }

}