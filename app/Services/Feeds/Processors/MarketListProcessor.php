<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/04/2015
 * Time: 9:49 AM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
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

    public function __construct(EventRepositoryInterface $eventRepository,
                                MarketTypeRepositoryInterface $marketTypeRepository,
                                MarketRepositoryInterface $marketRepository)
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
        if ( ! $event = $this->eventRepository->getEventDetails($eventId) ) {
            Log::error($this->logprefix."Event not found, external id: " . $eventId);
            return 0;
        }

        //process market type
        $marketType = null;
        if( $marketTypeName = array_get($data, 'BetTypeName', null) ) {
            $marketType = $this->processMarketType($marketTypeId, $marketTypeName, array_get($data, 'Period', null));
        }

        //process market
        $market = null;
        if( $marketType ) {
            $market = $this->processMarket($marketType['id'], $event['EventId'], $data);
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

        Log::info($this->logprefix."Processing Market Type, BetTypeName: $marketTypeName");

        //market type data
        $data = array(
            "external_bet_type_id" => $marketTypeId,
            "name" => $marketTypeName,
            "status_flag" => 1,
        );

        return $this->marketTypeRepository->updateOrCreate($data, 'name');
    }

    private function processMarket($marketType, $event, $data)
    {
        Log::info($this->logprefix."Processing Market");

        //market data
        $marketData = array(
            "market_type_id" => $marketType,
            "external_market_id" => $data['MarketId'],
            "external_event_id" => $data['GameId'],
            "event_id" => $event,
            "period" => array_get($data, 'Period', null),
            "market_status" => array_get($data, 'MarketStatus', ''),
        );

        $marketData = array_merge($marketData, $this->processExtraMarketData($data));

        return $this->marketRepository->updateOrCreate($marketData, 'external_market_id');
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