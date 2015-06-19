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

    public function __construct(MarketRepositoryInterface $marketRepository,
                                SelectionRepositoryInterface $selectionRepository,
                                EventRepositoryInterface $eventRepository,
                                DbSelectionPriceRepository $selectionPricesRepository,
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
        if ( ! ($eventId = array_get($data, 'GameId', null)) || ! ($marketId = array_get($data, 'MarketId', null)) || ! ($selectionId = array_get($data, 'SelectionNo', null))) {
            Log::error($this->logprefix."No EventId, marketId or SelectionId specified");
            return 0;
        }

        //get the event
        if ( ! $event = $this->eventRepository->getEventDetails($eventId) ) {
            Log::error($this->logprefix."Event $eventId does not exist");
            return 0;
        }

        //get the market
        if ( ! $market = $this->marketRepository->getMarketByExternalIds($marketId, $eventId) ) {
            Log::error($this->logprefix."Market $marketId does not exist");
            return 0;
        }

        Log::debug($this->logprefix."Selection/Price - GameId: " . $data['GameId'].", MarketId: ".$data['MarketId'].", SelectionNo: ".$data['SelectionNo'].", Odds ".$data['Odds'].", Line: " .$data['Line']);

        //process selection
        $selection = $this->processSelection($market['id'], $data);

        //process odds
        if($selection) {
            //process price
            if(Carbon::now('Australia/Sydney') < $event['StartDate']) {
                $this->processSelectionPrice($selection['id'], $data);
            }

            //process line for market
            if($line = array_get($data, 'Line', 0) ) {
                $this->processMarketLine($market['id'], $line);
            }

            return $selection['id'];
        }

        return 0;
    }

    private function processSelection($marketId, $data)
    {
        //selection data
        $selectionData = array(
            "market_id" => $marketId,
            "external_selection_id" => $data['SelectionNo'],
            "external_event_id" => $data['GameId'],
            "external_market_id" => $data['MarketId'],
            "home_away" => array_get($data, 'HomeAway', null),
        );

        if( $selectionName = array_get($data, 'Selection', null) ) {
            $selectionData['name'] = $selectionName;
        }

        //selection status
        if( $selectionStatus = array_get($data, 'Status', null) ) {
            if($selectionStatus == 'S') { $selectionData['selection_status_id'] = 4; }
            else if ($selectionStatus == 'T') { $selectionData['selection_status_id'] = 1; }
        }

        //check if selection already exists
        if ($selection = $this->selectionRepository->getByExternalIds($data['SelectionNo'], $data['MarketId'], $data['GameId']) ) {
            $this->selectionRepository->updateWithId($selection['id'], $selectionData);
        } else {
            //create it otherwise
            $selection = $this->selectionRepository->create($selectionData);
        }

        if( $competitorId = array_get($data, 'CompetitorId', null) ) {
            $this->competitorService->addCompetitorToSelection($selection['id'], $competitorId);
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

        return $this->selectionPricesRepository->updateOrCreate($priceData, "selection_id");
    }

    private function processMarketLine($marketId, $line)
    {
        return $this->marketRepository->updateWithId($marketId, array('line' => str_replace(array("+", "-"), "", $line)));
    }

}