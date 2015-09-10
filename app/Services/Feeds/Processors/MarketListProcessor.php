<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/04/2015
 * Time: 9:49 AM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
use TopBetta\Repositories\Cache\Sports\EventRepository;
use TopBetta\Repositories\Cache\Sports\MarketRepository;
use TopBetta\Repositories\Cache\Sports\MarketTypeRepository;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;

class MarketListProcessor extends AbstractFeedProcessor {

    /**
     * @var MarketTypeRepositoryInterface
     */
    private $marketTypeRepository;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var MarketRepositoryInterface
     */
    private $marketRepository;

    public function __construct(EventRepository $eventRepository,
                                MarketTypeRepository $marketTypeRepository,
                                MarketRepository $marketRepository)
    {
        $this->marketTypeRepository = $marketTypeRepository;
        $this->eventRepository = $eventRepository;
        $this->marketRepository = $marketRepository;
        $this->logprefix = 'SportsFeedService - MarketListProcessor: ';
    }

    public function process($data)
    {
        //make sure game and market ids exists
        if( ! ($eventId = array_get($data, 'GameId', false)) || ! ($marketId = array_get($data, 'MarketId', false)) ||  ! ($marketTypeId = array_get($data, 'BetType', false)) ) {
            Log::error($this->logprefix."No GameId or MarketId specified");
            return 0;
        }

        $eventId = array_get($data, 'GameId', false);
        //get the event
        if (! $event = $this->modelContainer->getEvent($eventId)) {
            if ( ! $event = $this->eventRepository->getEventDetails($eventId) ) {
                Log::error($this->logprefix."Event not found, external id: " . $eventId);
                return 0;
            }

            $this->modelContainer->addEvent($event, $eventId);
        }


        //process market type
        $marketType = null;
        if( $marketTypeName = array_get($data, 'MarketTypeName', null) ) {
            $marketType = $this->processMarketType($marketTypeId, $marketTypeName, array_get($data, 'Period', null));
        }

        //process market
        $market = null;
        if( $marketType ) {
            $market = $this->processMarket($marketType['id'], $event, $data);
            $market->markettype = $marketType;
            $this->modelContainer->addMarket($market, $market->external_market_id);
        }

        if($marketTypeName && $market){
            Log::debug($this->logprefix."Market/Type - GameId: " . $data['GameId'].", MarketId: ".$data['MarketId'].", MarketTypeName: ".$data['MarketTypeName'].", MarketName: " . $data['MarketName'] . ", MarketStatus ".array_get($data, 'MarketStatus', ''));
        }

        if( $market ) {
            return $market['id'];
        }

        return 0;
    }

    private function processMarketType($marketTypeId, $marketTypeName, $period)
    {
        //add the period to the market type name
        if( $period ) {
            $marketTypeName = $marketTypeName . ' ' . $period;
        }

       // Log::info($this->logprefix."Processing Market Type, BetTypeName: $marketTypeName");

        //market type data
        $data = array(
            "external_bet_type_id" => $marketTypeId,
            "name" => $marketTypeName,
            "status_flag" => 1,
        );

        if ($marketType = $this->modelContainer->getMarketType($marketTypeId)) {
            $marketType = $this->marketTypeRepository->update($marketType, $data);
        } else {
            $marketType = $this->marketTypeRepository->updateOrCreate($data, 'name');
        }

        $this->modelContainer->addMarketType($marketType, $marketTypeId);

        return $marketType;
    }

    private function processMarket($marketType, $event, $data)
    {
       // Log::info($this->logprefix."Processing Market");

        //market data
        $marketData = array(
            "market_type_id" => $marketType,
            "external_market_id" => $data['MarketId'],
            "external_event_id" => $data['GameId'],
            "event_id" => $event->id,
            "period" => array_get($data, 'Period', null),
            "market_status" => array_get($data, 'MarketStatus', ''),
            "name" => array_get($data, "MarketName"),
        );

        $marketData = array_merge($marketData, $this->processExtraMarketData($data));

        if ($market = $this->modelContainer->getMarket($data['MarketId'])) {
            $market = $this->marketRepository->update($market, $marketData);
        } else {
            $market = $this->marketRepository->updateOrCreate($marketData, 'external_market_id');
        }

        $market->event = $event;
        $this->modelContainer->addMarket($market, $data['MarketId']);

        return $market;
    }

    private function processExtraMarketData($data)
    {
        $extraData = array();

        //--- BASEBALL DATA ---
        if( $homeNo = array_get($data, 'PitcherHomeNo', null) ) {
            $extraData['pitcher_home_no'] = $homeNo;
        }

        if( $homeName = array_get($data, 'PitcherHomeName', null) ) {
            $extraData['pitcher_home_name'] = $homeName;
        }

        if( $awayNo = array_get($data, 'PitcherAwayNo', null)) {
            $extraData['pitcher_away_no'] = $awayNo;
        }

        if( $awayName = array_get($data, 'PitcherAwayName', null) ) {
            $extraData['pitcher_away_name'] = $awayName;
        }

        return $extraData;
    }
}