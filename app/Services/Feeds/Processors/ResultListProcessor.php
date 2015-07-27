<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/04/2015
 * Time: 4:07 PM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
use TopBetta\Repositories\BetResultRepo;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface;

class ResultListProcessor extends AbstractFeedProcessor {

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var MarketRepositoryInterface
     */
    private $marketRepository;
    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;
    /**
     * @var SelectionResultRepositoryInterface
     */
    private $selectionResultRepository;
    /**
     * @var BetResultRepo
     */
    private $betResultRepository;

    public function __construct(EventRepositoryInterface $eventRepository,
                                MarketRepositoryInterface $marketRepository,
                                SelectionRepositoryInterface $selectionRepository,
                                SelectionResultRepositoryInterface $selectionResultRepository,
                                BetResultRepo $betResultRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->marketRepository = $marketRepository;
        $this->selectionRepository = $selectionRepository;
        $this->selectionResultRepository = $selectionResultRepository;
        $this->betResultRepository = $betResultRepository;
        $this->logprefix = 'SportsFeedService - ResultListProcessor: ';
    }

    public function process($data)
    {
        //make sure game and market ids exists
        if( ! ($eventId = array_get($data, 'GameId', false)) || ! ($marketId = array_get($data, 'MarketId', false)) ) {
            return 0;
        }

        //get the event
        if( ! $event = $this->eventRepository->getEventDetails($eventId) ) {
            Log::error($this->logprefix."External event id $eventId does not exists");
            return 0;
        }

        //get the market
        if( ! $market = $this->marketRepository->getMarketByExternalIds($marketId, $eventId) ) {
            Log::error("Back API Sports external market id $marketId does not exist for event id " . $event->id);
            return 0;
        }

        //check market status exists
        if ( ! $marketStatus = array_get($data, 'MarketStatus', null) ) {
            Log::error($this->logprefix."No market status");
            return 0;
        }

        //process market status
        $this->processMarketStatus($marketStatus, $market['id']);

        //process result
        return $this->processResults($data, $marketStatus, $eventId, $marketId);

    }

    private function processMarketStatus($marketStatus, $marketId)
    {
        return $this->marketRepository->updateWithId($marketId, array("market_status" => $marketStatus));
    }

    private function processResults($data, $marketStatus, $eventId, $marketId)
    {
        Log::info($this->logprefix."Processing result for event $eventId market $marketId");

        $result = 0;

        if($marketStatus == 'C' || $marketStatus == 'R') {

            //get the score type
            $scoreType = array_get($data,'ScoreType', null);

            if($scoreType == 'W') {
                //process result
                $result = $this->processSelectionResult($data, $eventId, $marketId);

                //result bets
                $this->betResultRepository->resultAllSportBetsForMarket($marketId);

            } else if ($scoreType == 'S') {
                $result = $this->processScore($data, $eventId);
            }
        } else {
            Log::error($this->logprefix."Market status invalid - $marketStatus");
        }

        return $result;
    }

    private function processSelectionResult($data, $eventId, $marketId)
    {

        //check Score field exists
        if ( ! $name = array_get($data, 'Score', null) ) {
            Log::error($this->logprefix."No Score field found ");
            return 0;
        }

        //get selection
        $selection = $this->selectionRepository->getByExternalIdsAndName($marketId, $eventId, $name);

        if( ! $selection ) {
            Log::error($this->logprefix."No selection for event $eventId, market $marketId, name $name ");
            return 0;
        }

        //result data
        $resultData = array(
            "selection_id" => $selection['id'],
            "position" => 1,
        );

        //create result record
        return $this->selectionResultRepository->updateOrCreate($resultData, 'selection_id');

    }

    private function processScore($data, $eventId)
    {
        //check Score field exists
        if ( ! $score = array_get($data, 'Score', null) ) {
            Log::error($this->logprefix."No Score field found ");
            return 0;
        }

        $event = $this->eventRepository->getEventDetails($eventId);

        return $this->eventRepository->updateWithdOd($event['EventId'], array('score' => $score));
    }
}