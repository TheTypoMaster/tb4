<?php TopBetta\api\backend\Racing;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 16:58
 * Project: tb4
 */

use Log;





class RaceResulting {

    public function ResultEvents($racingArray){

        $eventList = array();

        foreach ($racingArray as $dataArray) {
            $selectionsExists = $resultExists = 0;
            $firstProcess = false;

            // Check required data to update a Result is in the JSON
            if (isset($dataArray ['MeetingId']) && isset($dataArray ['RaceNo']) && isset($dataArray ['Selection']) && isset($dataArray ['BetType']) && isset($dataArray ['PriceType']) && isset($dataArray ['PlaceNo']) && isset($dataArray ['Payout'])) {

                // TODO: mapping between provider and TB should be added to constants or DB table
                if ($dataArray['BetType'] == "F")
                    $dataArray['BetType'] = "FF";

                $meetingId = $dataArray ['MeetingId'];
                $raceNo = $dataArray ['RaceNo'];
                $betType = $dataArray ['BetType'];
                $priceType = $dataArray ['PriceType'];
                $selection = $dataArray ['Selection'];
                $placeNo = $dataArray ['PlaceNo'];
                $payout = $dataArray ['Payout'];
                $providerName = "igas";
                $log_msg_prefix = "BackAPI: Racing - Processing Result. MID:$meetingId, RN:$raceNo";

                /*
                 * Check if this is a product we need to store in the DB
                 */
                $saveThisProduct = $this->_canProductBeProcessed($dataArray, $providerName, $raceNo, "Result");

                // We want this product
                if ($saveThisProduct) {
                    TopBetta\LogHelper::l($log_msg_prefix . " PriceType:$priceType. BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout", 1);

                    $eventID = TopBetta\RaceEvent::eventExists($meetingId, $raceNo);
                    if ($eventID && !array_key_exists($eventID, array_flip($eventList))) {
                        \Log::info("EVENTID First Process: " . $eventID);
                        array_push($eventList, $eventID);
                        $firstProcess = true;
                    }
                    // if this is the 1st time through for this event clear all previous results
                    if ($firstProcess == true) {

                        // update the flag so this only happens once
                        $firstProcess = false;

                        //
                        // delete all existing results data for this race
                        //

                        // Get ID of event record
                        // $eventID = TopBetta\RaceEvent::eventExists($meetingId, $raceNo);
                        // if there is an event found
                        if ($eventID) {
                            // grab the event
                            $raceEvent = TopBetta\RaceEvent::find($eventID);

                            // reset all exotic results to NULL
                            $raceEvent->quinella_dividend = $raceEvent->exacta_dividend = $raceEvent->trifecta_dividend = $raceEvent->firstfour_dividend = NULL;

                            // save the update
                            $raceEvent->save();

                            // delete all results records for this event
                            $deleteRaceID = TopBetta\RaceResult::deleteResultsForRaceId($eventID);

                            TopBetta\LogHelper::l($log_msg_prefix . " Existing Results for EventID: $eventID deleted. Response: $deleteRaceID.", 1);
                        }
                    }

                    // For win and place bets results are stored with the selection record
                    if ($betType == 'W' || $betType == 'P') {
                        // check if selection exists in the DB
                        $selectionsExists = TopBetta\RaceSelection::selectionExists($meetingId, $raceNo, $selection);
                        // if it exists
                        if ($selectionsExists) {
                            // Check if we have results already
                            $resultExists = \DB::table('tbdb_selection_result')->where('selection_id', $selectionsExists)->pluck('id');
                            if ($resultExists) {
                                TopBetta\LogHelper::l($log_msg_prefix . "  PriceType:$priceType Already in DB", 1);
                                $raceResult = TopBetta\RaceResult::find($resultExists);
                            } else {
                                TopBetta\LogHelper::l($log_msg_prefix . "  PriceType:$priceType Added to DB", 1);
                                $raceResult = new TopBetta\RaceResult ();

                                $raceResult->selection_id = $selectionsExists;
                            }

                            // grab position and correct dividend
                            $raceResult->position = $placeNo;
                            ($betType == 'W') ? $raceResult->position = 1 : $raceResult->position = $placeNo;
                            ($betType == 'W') ? $raceResult->win_dividend = $payout / 100 : $raceResult->place_dividend = $payout / 100;

                            // save win or place odds to DB
                            $raceResultSave = $raceResult->save();
                            $raceResultID = $raceResult->id;

                            TopBetta\LogHelper::l($log_msg_prefix . "  BetType:$betType, PriceType:$priceType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");
                        } else {
                            TopBetta\LogHelper::l($log_msg_prefix . "  Not Processed! Selection not found. PriceType:$priceType.  BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout", 2);
                        }
                        // Exotic results are stored with the event record
                    } else {

                        // Get ID of event record - used to store exotic results/divs if required
                        $eventID = TopBetta\RaceEvent::eventExists($meetingId, $raceNo);


                        if ($eventID) {
                            // grab the event
                            $raceEvent = TopBetta\RaceEvent::find($eventID);

                            // build the serialised result data for this result
                            $arrayKey = str_replace('-', '/', $selection);
                            $arrayValue = $payout / 100;
                            $exoticArray = array(
                                $arrayKey => $arrayValue
                            );

                            $previousDivArray = array();

                            TopBetta\LogHelper::l($log_msg_prefix . "  Exotic Type:$betType. Positions:$arrayKey, Dividend:$arrayValue.", 1);

                            // process each exotic type
                            switch ($betType) {
                                case "Q" : // Quinella
                                    // if we already have a dividend stored
                                    if ($raceEvent->quinella_dividend != NULL) {
                                        // if the new exotic results are the same as what we already have in the database
                                        if ($raceEvent->quinella_dividend != serialize($exoticArray)) {
                                            // unserialise the existing dividend from the database
                                            $previousDivArray = unserialize($raceEvent->quinella_dividend);
                                            // update or add selection dividends
                                            $previousDivArray[$arrayKey] = $arrayValue;
                                            // add the new dividends
                                            $raceEvent->quinella_dividend = serialize($previousDivArray);
                                        }
                                        // if we didn't have a result stored already then store it
                                    } else {
                                        $raceEvent->quinella_dividend = serialize($exoticArray);
                                    }
                                    TopBetta\LogHelper::l($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$raceEvent->quinella_dividend.", 1);
                                    break;

                                case "E" : // Exacta
                                    // if we already have a dividend stored
                                    if ($raceEvent->exacta_dividend != NULL) {
                                        // if the new exotic results are the same as what we already have in the database
                                        if ($raceEvent->exacta_dividend != serialize($exoticArray)) {
                                            // unserialise the existing dividend from the database
                                            $previousDivArray = unserialize($raceEvent->exacta_dividend);
                                            // update or add selection dividends
                                            $previousDivArray[$arrayKey] = $arrayValue;
                                            // add the new dividends
                                            $raceEvent->exacta_dividend = serialize($previousDivArray);
                                        }
                                        // if we didn't have a result stored already then store it
                                    } else {
                                        $raceEvent->exacta_dividend = serialize($exoticArray);
                                    }
                                    TopBetta\LogHelper::l($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$raceEvent->exacta_dividend.", 1);
                                    break;

                                case "T" : // Trifecta
                                    // if we already have a dividend stored
                                    if ($raceEvent->trifecta_dividend != NULL) {
                                        // if the new exotic results are the same as what we already have in the database
                                        if ($raceEvent->trifecta_dividend != serialize($exoticArray)) {
                                            // unserialise the existing dividend from the database
                                            $previousDivArray = unserialize($raceEvent->trifecta_dividend);
                                            // update or add selection dividends
                                            $previousDivArray[$arrayKey] = $arrayValue;
                                            // add the new dividends
                                            $raceEvent->trifecta_dividend = serialize($previousDivArray);
                                        }
                                        // if we didn't have a result stored already then store it
                                    } else {
                                        $raceEvent->trifecta_dividend = serialize($exoticArray);
                                    }
                                    TopBetta\LogHelper::l($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$raceEvent->trifecta_dividend.", 1);
                                    break;

                                case "FF" : // First Four
                                    // if we already have a dividend stored
                                    if ($raceEvent->firstfour_dividend != NULL) {
                                        // if the new exotic results are the same as what we already have in the database
                                        if ($raceEvent->firstfour_dividend != serialize($exoticArray)) {
                                            // unserialise the existing dividend from the database
                                            $previousDivArray = unserialize($raceEvent->firstfour_dividend);
                                            // update or add selection dividends
                                            $previousDivArray[$arrayKey] = $arrayValue;
                                            // add the new dividends
                                            $raceEvent->firstfour_dividend = serialize($previousDivArray);
                                        }
                                        // if we didn't have a result stored already then store it
                                    } else {
                                        $raceEvent->firstfour_dividend = serialize($exoticArray);
                                    }
                                    TopBetta\LogHelper::l($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$raceEvent->firstfour_dividend.", 1);
                                    break;

                                default :
                                    TopBetta\LogHelper::l($log_msg_prefix . " No valid betType found:$betType. Can't process", 2);
                            }

                            // save the exotic dividend
                            $raceEvent->save();
                        } else {
                            TopBetta\LogHelper::l($log_msg_prefix . "  Missing Event Record in DB", 2);
                        }
                    }
                } else { // not all required data available
                    TopBetta\LogHelper::l($log_msg_prefix . " Not Processed! PriceType:$priceType. MeetID: $meetingId, RaceCode:, RaceNo:$raceNo, BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout", 2);
                }
            } else {
                TopBetta\LogHelper::l($log_msg_prefix . " Missing Results data. Can't process", 2);
            }
        }

        list($partMsec, $partSec) = explode(" ", microtime());
        $currentTimeMs = $partSec.$partMsec;
        $racingJSONlog = \Input::json()->all();
        \File::append('/tmp/backAPIracingResultJSON-EventList-C' .count($eventList).'-'. $currentTimeMs, print_r($eventList,true));
        // ALL RESULTS PROCESSED - RESULT ALL BETS FOR THE EVENT LIST
        foreach ($eventList as $eventId) {
            \Log::info('RESULTING: all bets for event id: ' . $eventId);


            // get current micro time
            list($partMsec, $partSec) = explode(" ", microtime());
            $currentTimeMs = $partSec.$partMsec;
            $racingJSONlog = \Input::json()->all();
            \File::append('/tmp/backAPIracingResultJSON-E' .$eventId.'-'. $currentTimeMs, json_encode($racingJSONlog));

            $betResultRepo = new TopBetta\Repositories\BetResultRepo();
            $betResultRepo->resultAllBetsForEvent($eventId);
        }


    }

    private function _canProductBeProcessed($dataArray, $providerName, $raceNo, $type = null)
    {

        $productUsed = false;
        $meetingId = $dataArray['MeetingId'];
        $betType = $dataArray['BetType'];
        $priceType = $dataArray['PriceType'];

        // grab the meeting details we need
        $meetingTypeCodeResult = Topbetta\RaceMeeting::getMeetingDetails($meetingId);

        if (is_array($meetingTypeCodeResult)) {
            if (isset($meetingTypeCodeResult[0])) {
                $meetingTypeCode = $meetingTypeCodeResult[0]['type_code'];
                $meetingCountry = $meetingTypeCodeResult[0]['country'];
                $meetingGrade = $meetingTypeCodeResult[0]['meeting_grade'];

                // check if product is used
                $productUsed = TopBetta\BetProduct::isProductUsed($priceType, $betType, $meetingCountry, $meetingGrade, $meetingTypeCode, $providerName);

                if (!$productUsed) {
                    //TopBetta\LogHelper::l("BackAPI: Racing - Processing $type. IGNORED: MeetID:$meetingId, RaceNo:$raceNo, BetType:$betType, PriceType:$priceType, TypeCode:$meetingTypeCode, Country:$meetingCountry, Grade:$meetingGrade", 1);
                    return false;
                }
                Log::info("BackAPI: Racing - Processing $type. USED: MeetID:$meetingId, RaceNo:$raceNo, BetType:$betType, PriceType:$priceType, TypeCode:$meetingTypeCode, Country:$meetingCountry, Grade:$meetingGrade");
            } else {
                Log::debug("BackAPI: Racing - Processing $type: Meeting ID not found???? - " . print_r($meetingTypeCodeResult, true));
            }
        } else {
            Log::debug("BackAPI: Racing - Processing $type: Meeting ID not found???? - " . print_r($meetingTypeCodeResult, true));
        }
        return true;
    }

} 