<?php namespace TopBetta\Services\Feeds\Racing;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 16:58
 * Project: tb4
 */

use Log;
use File;
use Carbon;
use Queue;
use Config;

use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;

use TopBetta\Repositories\BetResultRepo;
use TopBetta\Services\Betting\BetResults\BetResultService;

class RaceResulting {

    protected $events;
    protected $selections;
    protected $results;
    protected $competitions;
    protected $betproducts;
    protected $betresults;
    /**
     * @var BetResultService
     */
    private $betResultService;

    public function __construct(EventRepositoryInterface $events,
                                SelectionRepositoryInterface $selections,
                                SelectionResultRepositoryInterface $results,
                                CompetitionRepositoryInterface $competitions,
                                BetProductRepositoryInterface $betproducts,
                                BetResultRepo $betresults,
                                BetResultService $betResultService){
        $this->events = $events;
        $this->selections = $selections;
        $this->results = $results;
        $this->competitions = $competitions;
        $this->betproducts = $betproducts;
        $this->betresults = $betresults;
        $this->betResultService = $betResultService;
        $this->logprefix = 'RaceResultService - Result Events: ';
    }

    public function ResultEvents($racingArray){

        // Log the POST of results data
        //$date = substr(Carbon\Carbon::now(), 0, 10);
        //list($partMsec, $partSec) = explode(" ", microtime());
        //$currentTimeMs = $partSec.$partMsec;
        //File::append('/tmp/'.$date.'-ResultPost-'. $currentTimeMs, json_encode($racingArray));

        $eventList = array();
        $firstProcess = true;
        $eventModel = false;

        foreach ($racingArray as $dataArray) {

            // Check required data to update a Result is in the JSON
            if (!isset($dataArray ['MeetingId']) || !isset($dataArray ['RaceNo']) || !isset($dataArray ['Selection']) || !isset($dataArray ['BetType'])
                                                || !isset($dataArray ['PriceType']) || !isset($dataArray ['PlaceNo']) || !isset($dataArray ['Payout']))
            {
                Log::debug($this->logprefix."Missing Results data. Can't process");
                continue;
            }

            // TODO change serena to push FF from it's product profile
            if ($dataArray['BetType'] == "F") $dataArray['BetType'] = "FF";

            // get the result data from the payload
            $meetingId = $dataArray ['MeetingId'];
            $raceNo = $dataArray ['RaceNo'];
            $betType = $dataArray ['BetType'];
            $priceType = $dataArray ['PriceType'];
            $selection = $dataArray ['Selection'];
            $placeNo = $dataArray ['PlaceNo'];
            $payout = $dataArray ['Payout'];
            $providerName = "igas"; // TODO remove this hard coding

            $log_msg_prefix = $this->logprefix. " MID:$meetingId, RN:$raceNo -";

            // check if this is a product we need to store in the DB
            $productUsed = $this->_canProductBeProcessed($dataArray, $providerName, $raceNo, "Result");

            // dont process if TB does not use this
            if(!$productUsed) {
                Log::debug($log_msg_prefix . " Product Not Used: PriceType:$priceType. BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");
                continue;
            }

            Log::info($log_msg_prefix . " PriceType:$priceType. BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");
            // get the event model
            $eventModel = $this->events->getEventForMeetingIdRaceId($meetingId, $raceNo);

            if(!$eventModel) return array('error' => true, 'message' => "Error: No event found in database for meeting: $meetingId and race: $raceNo");

            // remove existing results
            if ($firstProcess == true) {
                // reset all exotic result to NULL
                $eventModel->quinella_dividend = $eventModel->exacta_dividend = $eventModel->trifecta_dividend = $eventModel->firstfour_dividend = NULL;

                // update the database
                $eventModel->save();

                // delete all results records for this event
                $deleteRaceID = $this->results->deleteResultsForRaceId($eventModel->id);

                // update the flag so this only happens once
                $firstProcess = false;

                Log::debug($log_msg_prefix . " Existing Results for EventID: {$eventModel->id} deleted. Response: $deleteRaceID.");
            }

            // win and place bets results are stored with the selection record
            if ($betType == 'W' || $betType == 'P') {
                // check if selection exists in the DB
                $selectionModel = $this->selections->getSelectionIdFromMeetingIdRaceNumberSelectionName($meetingId, $raceNo, $selection);

                if(!$selectionModel) {
                    Log::debug($log_msg_prefix . " Not Processed! Selection not found - {$selectionModel->id}. PriceType:$priceType.  BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");
                    continue;
                }

                // build new result record
                $raceResult = array();
                $raceResult['position'] = $placeNo;
                $raceResult['selection_id'] = $selectionModel->id;
                ($betType == 'W') ? $raceResult['position'] = 1 : $raceResult['position'] = $placeNo;
                ($betType == 'W') ? $raceResult['win_dividend'] = $payout / 100 : $raceResult['place_dividend'] = $payout / 100;

                // save result
                $raceResultSave = $this->results->updateOrCreate($raceResult, 'selection_id');

                Log::debug($log_msg_prefix . " Result Saved {$raceResultSave['id']} - BetType:$betType, PriceType:$priceType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");

            // Exotic results are stored with the event record
            } else {
                // build the serialised result data for this result
                $arrayKey = str_replace('-', '/', $selection);
                $arrayValue = $payout / 100;
                $exoticArray = array(
                    $arrayKey => $arrayValue
                );

                $previousDivArray = array();

                Log::debug($log_msg_prefix . "  Exotic Type:$betType. Positions:$arrayKey, Dividend:$arrayValue.");

                // process each exotic type
                switch ($betType) {
                    case "Q" : // Quinella
                        // if we already have a dividend stored
                        if ($eventModel->quinella_dividend != NULL) {
                            // if the new exotic results are the same as what we already have in the database
                            if ($eventModel->quinella_dividend != serialize($exoticArray)) {
                                // unserialise the existing dividend from the database
                                $previousDivArray = unserialize($eventModel->quinella_dividend);
                                // update or add selection dividends
                                $previousDivArray[$arrayKey] = $arrayValue;
                                // add the new dividends
                                $eventModel->quinella_dividend = serialize($previousDivArray);
                            }
                            // if we didn't have a result stored already then store it
                        } else {
                            $eventModel->quinella_dividend = serialize($exoticArray);
                        }
                        Log::debug($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$eventModel->quinella_dividend.");
                        break;

                    case "E" : // Exacta
                        // if we already have a dividend stored
                        if ($eventModel->exacta_dividend != NULL) {
                            // if the new exotic results are the same as what we already have in the database
                            if ($eventModel->exacta_dividend != serialize($exoticArray)) {
                                // unserialise the existing dividend from the database
                                $previousDivArray = unserialize($eventModel->exacta_dividend);
                                // update or add selection dividends
                                $previousDivArray[$arrayKey] = $arrayValue;
                                // add the new dividends
                                $eventModel->exacta_dividend = serialize($previousDivArray);
                            }
                            // if we didn't have a result stored already then store it
                        } else {
                            $eventModel->exacta_dividend = serialize($exoticArray);
                        }
                        Log::debug($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$eventModel->exacta_dividend.");
                        break;

                    case "T" : // Trifecta
                        // if we already have a dividend stored
                        if ($eventModel->trifecta_dividend != NULL) {
                            // if the new exotic results are the same as what we already have in the database
                            if ($eventModel->trifecta_dividend != serialize($exoticArray)) {
                                // unserialise the existing dividend from the database
                                $previousDivArray = unserialize($eventModel->trifecta_dividend);
                                // update or add selection dividends
                                $previousDivArray[$arrayKey] = $arrayValue;
                                // add the new dividends
                                $eventModel->trifecta_dividend = serialize($previousDivArray);
                            }
                            // if we didn't have a result stored already then store it
                        } else {
                            $eventModel->trifecta_dividend = serialize($exoticArray);
                        }
                        Log::debug($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$eventModel->trifecta_dividend.");
                        break;

                    case "FF" : // First Four
                        // if we already have a dividend stored
                        if ($eventModel->firstfour_dividend != NULL) {
                            // if the new exotic results are the same as what we already have in the database
                            if ($eventModel->firstfour_dividend != serialize($exoticArray)) {
                                // unserialise the existing dividend from the database
                                $previousDivArray = unserialize($eventModel->firstfour_dividend);
                                // update or add selection dividends
                                $previousDivArray[$arrayKey] = $arrayValue;
                                // add the new dividends
                                $eventModel->firstfour_dividend = serialize($previousDivArray);
                            }
                            // if we didn't have a result stored already then store it
                        } else {
                            $eventModel->firstfour_dividend = serialize($exoticArray);
                        }
                        Log::debug($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$eventModel->firstfour_dividend.");
                        break;

                    default :
                        Log::debug($log_msg_prefix . " No valid betType found:$betType. Can't process");
                }

                // save the exotic dividend
                $eventModel->save();

            }

        }


        /*
         * result BETS
         */
        if($eventModel){
            Log::info('RESULTING: all bets for event id: ' . $eventModel->id);

            // get current micro time
            list($partMsec, $partSec) = explode(" ", microtime());
            $currentTimeMs = $partSec.$partMsec;
            File::append('/tmp/'.$date.'-ResultPost-E' .$eventModel->id.'-'. $currentTimeMs, json_encode($racingArray));

            //$this->betresults->resultAllBetsForEvent($eventModel->id);
            Queue::push('TopBetta\Services\Betting\EventBetResultingQueueService', array('event_id' => $eventModel->id), Config::get('betresulting.queue'));
        }

        return array('error' => false,
                    'message' => "OK: Processed Successfully",
                    'status_code' => 200);

    }

    private function _canProductBeProcessed($dataArray, $providerName, $raceNo, $type = null)
    {
        $productUsed = false;
        $meetingId = $dataArray['MeetingId'];
        $betType = $dataArray['BetType'];
        $priceType = $dataArray['PriceType'];

        // get meeting details
        $meetingTypeCodeResult = $this->competitions->getMeetingDetails($meetingId);

        if(!$meetingTypeCodeResult) return false;

        $meetingTypeCode = $meetingTypeCodeResult['type_code'];
        $meetingCountry = $meetingTypeCodeResult['country'];
        $meetingGrade = $meetingTypeCodeResult['meeting_grade'];

        // check if product is used
        $productUsed = $this->betproducts->isProductUsed($priceType, $betType, $meetingCountry, $meetingGrade, $meetingTypeCode, $providerName);

        if (!$productUsed) {
            Log::debug("BackAPI: Racing - Processing $type. IGNORED: MeetID:$meetingId, RaceNo:$raceNo, BetType:$betType, PriceType:$priceType, TypeCode:$meetingTypeCode, Country:$meetingCountry, Grade:$meetingGrade");
            return false;
        }
        Log::info("BackAPI: Racing - Processing $type. USED: MeetID:$meetingId, RaceNo:$raceNo, BetType:$betType, PriceType:$priceType, TypeCode:$meetingTypeCode, Country:$meetingCountry, Grade:$meetingGrade");
        return true;
    }
}