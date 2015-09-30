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
        if( ! ($eventId = array_get($data, 'event_id', false)) || ! ($marketId = array_get($data, 'market_id', false)) ||  ! ($marketTypeId = array_get($data, 'market_type_id', false)) ) {
            Log::error($this->logprefix."No event_id or market_id specified");
            return 0;
        }

        $eventId = array_get($data, 'event_id', false);
        //get the event
        if (! $event = $this->modelContainer->getEvent($eventId)) {
            if ((! $event = $this->eventRepository->getBySerenaId($eventId)) && (!array_get($data, 'GameId') ||  (! $event = $this->eventRepository->getEventModelFromExternalId(array_get($data, 'GameId')))) ) {
                Log::error($this->logprefix."Event not found, external id: " . $eventId);
                return 0;
            }

            $this->modelContainer->addEvent($event, $eventId);
        }


        //process market type
        $marketType = null;
        if( $marketTypeName = array_get($data, 'market_type_name', null) ) {
            $marketType = $this->processMarketType($marketTypeId, array_get($data, 'BetType'), $marketTypeName, array_get($data, 'Period', null));
        }

        //process market
        $market = null;
        if( $marketType ) {
            $market = $this->processMarket($marketType['id'], $event, $data);
            $market->markettype = $marketType;
            $this->modelContainer->addMarket($market, $market->serena_market_id);
        }

        if($marketTypeName && $market){
            Log::debug($this->logprefix."Market/Type - GameId: " . $data['event_id'].", MarketId: ".$data['market_id'].", MarketTypeName: ".$data['market_type_name'].", MarketName: " . $data['market_name'] . ", MarketStatus ".array_get($data, 'market_status', ''));
        }

        if( $market ) {
            return $market['id'];
        }

        return 0;
    }

    private function processMarketType($marketTypeId, $marketTypeIdBg, $marketTypeName, $period)
    {
        //add the period to the market type name
        if( $period ) {
            $marketTypeName = $marketTypeName . ' ' . $period;
        }

       // Log::info($this->logprefix."Processing Market Type, BetTypeName: $marketTypeName");

        //market type data
        $data = array(
            "serena_market_type_id" => $marketTypeId,
            "name" => $marketTypeName,
            "status_flag" => 1,
        );

        if ($marketType = $this->modelContainer->getMarketType($marketTypeId)) {
            $marketType = $this->marketTypeRepository->update($marketType, $data);
        } else if ($marketType = $this->marketTypeRepository->getBySerenaId($marketTypeId)) {
            $marketType = $this->marketTypeRepository->update($marketType, $data);
        } else if ($marketTypeIdBg && ($marketType = $this->marketTypeRepository->getByExternalId($marketTypeIdBg))) {
            $marketType = $this->marketTypeRepository->update($marketType, $data);
        } else {
            $marketType = $this->marketTypeRepository->create($data);
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
            "serena_market_id" => $data['market_id'],
            "external_event_id" => $data['GameId'],
            "event_id" => $event->id,
            "period" => array_get($data, 'Period', null),
            "market_status" => array_get($data, 'market_status', ''),
            "name" => array_get($data, "market_name"),
        );

        $marketData = array_merge($marketData, $this->processExtraMarketData($data));

        if ($market = $this->modelContainer->getMarket($data['market_id'])) {
            $market = $this->marketRepository->update($market, $marketData);
        } else if ($market = $this->marketRepository->getBySerenaId($data['market_id'])) {
            $market = $this->marketRepository->update($market, $marketData);
        } else if (array_get($data, 'MarketId') && array_get($data, 'GameId') && ($market = $this->marketRepository->getMarketModelByExternalIds($data['MarketId'], $data['GameId']))) {
            $market = $this->marketRepository->update($market, $marketData);
        } else {
            $market = $this->marketRepository->create($marketData);
        }

        $market->event = $event;
        $this->modelContainer->addMarket($market, $data['market_id']);

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