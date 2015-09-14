<?php namespace TopBetta\Services\Feeds\Racing;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 16:58
 * Project: tb4
 */

use Illuminate\Support\Collection;
use Log;
use File;
use Carbon;
use Queue;
use Config;

use TopBetta\Repositories\Cache\RaceRepository;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\ResultPricesRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;

use TopBetta\Repositories\BetResultRepo;
use TopBetta\Services\Betting\BetResults\BetResultService;
use TopBetta\Services\Racing\RaceResultService;

use TopBetta\Helpers\RiskManagerAPI;

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
    /**
     * @var BetTypeRepositoryInterface
     */
    private $betTypeRepository;
    /**
     * @var ResultPricesRepositoryInterface
     */
    private $resultPricesRepository;
    /**
     * @var BetTypeMapper
     */
    private $betTypeMapper;
    /**
     * @var RaceRepository
     */
    private $raceRepository;
    /**
     * @var RaceResultService
     */
    private $resultService;

    private $riskhelper;

    public function __construct(EventRepositoryInterface $events,
                                SelectionRepositoryInterface $selections,
                                SelectionResultRepositoryInterface $results,
                                CompetitionRepositoryInterface $competitions,
                                BetProductRepositoryInterface $betproducts,
                                BetResultRepo $betresults,
                                BetResultService $betResultService,
                                RaceRepository $raceRepository,
                                RaceResultService $resultService,
                                BetTypeMapper $betTypeMapper,
                                ResultPricesRepositoryInterface $resultPricesRepository,
                                RiskManagerAPI $riskhelper){
        $this->events = $events;
        $this->selections = $selections;
        $this->results = $results;
        $this->competitions = $competitions;
        $this->betproducts = $betproducts;
        $this->betresults = $betresults;
        $this->betResultService = $betResultService;
        $this->logprefix = 'RaceResultService - Result Events: ';
        $this->raceRepository = $raceRepository;
        $this->resultService = $resultService;
        $this->resultPricesRepository = $resultPricesRepository;
        $this->betTypeMapper = $betTypeMapper;
        $this->riskhelper = $riskhelper;
    }

    public function deleteWrongResults($results, $eventModel, $product)
    {
        $currentResults = $this->results->getResultsForEvent($eventModel->id);
        $toDelete = array();

        foreach ($currentResults as $result) {
            //hacky way to find if result exists in $results since we have no external id
            if (!in_array(
                array("Selection" => $result->selection->name, "PlaceNo" => $result->position),
                array_map(function ($v) {return array('Selection' => $v['Selection'], 'PlaceNo' => $v['PlaceNo']);}, $results)
            )) {
                $toDelete[] = $result->id;
            }
        }

        //delete wrong results
        $this->results->deleteResults($toDelete);
        $this->resultPricesRepository->deletePricesForResults($toDelete);

        //delete exotic results for product
        return $this->resultPricesRepository->deleteExoticPricesForEventAndProduct($eventModel->id, $product->id);
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

        $currentResults = null;

        if (count($racingArray)) {
            // get the event model
            $eventModel = $this->events->getEventForMeetingIdRaceId($racingArray[0]['MeetingId'], $racingArray[0]['RaceNo']);

            // check if this is a product we need to store in the DB
            $productUsed = $this->betproducts->getProductByCode($racingArray[0]['PriceType']);

            $this->deleteWrongResults($racingArray, $eventModel, $productUsed);
        }

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

            $betTypeModel = $this->betTypeMapper->getBetType($betType);

            if (!$betTypeModel) {
                Log::error($this->logprefix . " Bet type not found Type: " . $betType);
            }

            $log_msg_prefix = $this->logprefix. " MID:$meetingId, RN:$raceNo -";

            // dont process if TB does not use this
            if(!$productUsed) {
                Log::debug($log_msg_prefix . " Product Not Used: PriceType:$priceType. BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");
                continue;
            }

            Log::info($log_msg_prefix . " PriceType:$priceType. BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");


            if(!$eventModel) return array('error' => true, 'message' => "Error: No event found in database for meeting: $meetingId and race: $raceNo");

            // remove existing results
            if (!$currentResults) {
                $currentResults= $this->results->getResultsForRace($eventModel->id);
            }

            // win and place bets results are stored with the selection record
            if ($betType == 'W' || $betType == 'P') {
                // check if selection exists in the DB
                $selectionModel = $this->selections->getSelectionIdFromMeetingIdRaceNumberSelectionName($meetingId, $raceNo, $selection);

                if(!$selectionModel) {
                    Log::debug($log_msg_prefix . " Not Processed! Selection not found - {$selection}. PriceType:$priceType.  BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");
                    continue;
                }

                // build new result record
                $raceResult = array();
                $raceResult['position'] = $placeNo;
                $raceResult['selection_id'] = $selectionModel->id;
                ($betType == 'W') ? $raceResult['position'] = 1 : $raceResult['position'] = $placeNo;

                // save result
                $raceResultSave = $this->results->updateOrCreate($raceResult, 'selection_id');

                $price = array(
                    'product_id' => $productUsed->id,
                    'selection_result_id' => $raceResultSave['id'],
                    'event_id' => $eventModel->id,
                    'bet_type_id' => ($betTypeModel) ? $betTypeModel->id : null,
                    'dividend' => $payout / 100
                );

                if ($existingPrice = $this->resultPricesRepository->getByResultProductAndBetType($raceResultSave['id'], $productUsed->id, $betTypeModel->id)) {
                    $this->resultPricesRepository->updateWithId($existingPrice->id, $price);
                } else {
                    $this->resultPricesRepository->create($price);
                }

                $riskResultsPayload = array('external_event_id' => $eventModel->external_event_id,
                                            'external_selection_id' => $selectionModel->external_selection_id,
                                            'number' => $selectionModel->number,
                                            'position' => $placeNo,
                                            'product_name' => $productUsed->name,
                                            'bet_type_name' => $betTypeModel->name,
                                            'dividend' => $payout / 100);

                // push result update to Risk...
                Log::debug($this->logprefix. 'Pushing W/P results details to Risk', $riskResultsPayload);
                // TODO: add notification
                try{
                    $this->riskhelper->sendResultData(array('RaceResults' => $riskResultsPayload));
                }catch (Exception $e){
                    Log::error($this->logprefix. 'Pushing W/P results details to Risk Failed'. print_r($e->getMessage(), true));
                }


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

                $this->resultPricesRepository->create(array(
                    'event_id' => $eventModel->id,
                    'product_id' => $productUsed->id,
                    'bet_type_id' => $betTypeModel->id,
                    'dividend' => $payout/100,
                    'result_string' => str_replace('-', '/', $selection),
                ));

                $riskResultsPayload = array('external_event_id' => $eventModel->external_event_id,
                                            'external_selection_id' => $selectionModel->external_selection_id,
                                            'product_name' => $productUsed->name,
                                            'bet_type_name' => $betTypeModel->name,
                                            'dividend' => $payout / 100,
                                            'result_string' => str_replace('-', '/', $selection));

                // push result update to Risk...
                Log::debug($this->logprefix. 'Pushing Exotic results details to Risk', $riskResultsPayload);
                // TODO: add notification
                try{
                    $this->riskhelper->sendResultData(array('RaceResults' => $riskResultsPayload));
                }catch (Exception $e){
                    Log::error($this->logprefix. 'Pushing Exotic results details to Risk Failed'. print_r($e->getMessage(), true));
                }
            }



        }

        //update results in cache
        $this->raceRepository->makeCacheResource($eventModel);
        $race = $this->raceRepository->getRace($eventModel->id);
        $this->resultService->loadResultForRace($race, true);
        $this->raceRepository->save($race);

        /*
         * result BETS
         */
        if($eventModel){
            Log::info($log_msg_prefix . "RESULTING: all bets for event id: " . $eventModel->id);

            // get current micro time
//            list($partMsec, $partSec) = explode(" ", microtime());
//            $currentTimeMs = $partSec.$partMsec;
//            File::append('/tmp/'.$date.'-ResultPost-E' .$eventModel->id.'-'. $currentTimeMs, json_encode($racingArray));

            //$this->betresults->resultAllBetsForEvent($eventModel->id);
            Queue::push('TopBetta\Services\Betting\EventBetResultingQueueService', array('event_id' => $eventModel->id, 'product_id' => $productUsed->id), Config::get('betresulting.queue'));
        }

        return array('error' => false,
                    'message' => "OK: Processed Successfully",
                    'status_code' => 200);

    }
}