<?php

class RacingController extends BaseController {

	public function __construct()
	{
	 	//$this->beforeFilter('apiauth');
	}
	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		// return RaceMeetings::all();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// get the JSON POST
		$racingJSON = Input::json();
		
		//TODO: // make sure JSON was received
		if(is_array($racingJSON)){
			return Response::json(array(
					'error' => true,
					'message' => 'Error: No JSON data received'),
					400
			);
		}

		//$racingJSON = print_r($racingJSON, true);
		//echo"$racingJSON\n\n\n\n\n";
		//exit;
		
		//TODO: // validate the json. Create some rules and check the json validates
		// $validation = Validator::make($racingJSON, $rules);
		//if($validation->fails())
		//{
		//	return Response::json($validation->errors);
		//}
		//else
		//{
			// all OK!
		//}
		
		// JSON objects MeetingList/RaceList/RunnerList/ResultList/PriceList/OutcomeList
		
		$keyCount = count($racingJSON);
		echo "Key Count: $keyCount\n";
		
		// loop on objects in data
		foreach($racingJSON as $key => $racingArray){
			echo "Working on KEY: $key\n";
			
			// Make sure we have some data to process in the array
			if(is_array($racingArray)){
				
				// process the meeting/race/runner data
				switch($key){
					
					// Meeting Data - the meeting/venue
					case "MeetingList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							
							$meetingId = $dataArray['Id'];
							
							// check if meeting exists in DB
							// $meetingExists = DB::table('tbdb_event_group')->where('external_event_group_id', $meetingId)->pluck('id');
							// $meetingExists = RaceMeeting::where('external_event_group_id', '=', $meetingId)->pluck('id');
							$meetingExists = RaceMeeting::meetingExists($meetingId);
																				
							// if meeting exists update that record
							if($meetingExists){
								echo "Meeting: In DB: ". $meetingExists ."\n";
								$raceMeet = RaceMeeting::find($meetingExists);
							}else{
								echo "Meeting: Added to DB: ". $meetingExists ."\n";
								$raceMeet = new RaceMeeting;
								if(isset($dataArray['Id'])){
									$raceMeet->external_event_group_id = $dataArray['Id'];
								}
							}
							//echo "$raceMeet\n";
							// exit;
							// get meetings values from JSON
							if(isset($dataArray['Date'])){
								$raceMeet->start_date = $dataArray['Date'];
							}
							if(isset($dataArray['Name'])){
								$raceMeet->name = $dataArray['Name'];
							}
							if(isset($dataArray['Sport'])){
								switch($dataArray['Sport']){
									case "HORSE RACING":
										$raceMeet->type_code = 'R';
										$raceMeet->tournament_competition_id = '31';
										break;
									case "Harness":
										$raceMeet->type_code = 'H';
										$raceMeet->tournament_competition_id = '32';
										break;
									case "Greyhounds":
										$raceMeet->type_code = 'G';
										$raceMeet->tournament_competition_id = '33';
										break;
								}
							}
							// TODO: what do we do with country
							//if(isset($dataArray['Country'])){
							//	$raceMeet->type_code = $dataArray['Country'];
							//}
							if(isset($dataArray['EventCount'])){
								$raceMeet->events = $dataArray['EventCount'];
							}
							if(isset($dataArray['Weather'])){
								$raceMeet->weather = $dataArray['Weather'];
							}
							if(isset($dataArray['Track'])){
								$raceMeet->track = $dataArray['Track'];
							}
								
							// save or update the record
							$raceMeetSave = $raceMeet->save();
							$raceMeetID = $raceMeet->id;
							
							echo"Meeting: Record Added/Updated\n\n";
															
						}
						break;
						
					// Race data - the races in the meeting
					case "RaceList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							echo"Race Object: ";
							//print_r($dataArray);
							echo "\n";
							
							// grab the meeting and race for the DB query
							$meetingId = $dataArray['MeetingId'];
							$raceNo = $dataArray['RaceNo'];
							
							// make sure the meeting this race is in exists 1st
							//$meetingExists = RaceMeeting::where('external_event_group_id', '=', $meetingId)->pluck('id');
							$meetingExists = RaceMeeting::meetingExists($meetingId);
							
							
							// if meeting exists update that record then continue to add/update the race record
							if($meetingExists){
								 
								//check if race exists
								$raceExists = RaceEvent::eventExists($meetingId, $raceNo);

								// if race exists update that record
								if($raceExists){
									echo "Race: In DB: ". $raceExists ."\n";
									$raceEvent = RaceEvent::find($raceExists);
								}else{
									echo "Race:  Added to DB:". $raceExists ."$meetingId.\n";
									$raceEvent = new RaceEvent;
									if(isset($dataArray['MeetingId'])){
										$raceEvent->external_event_id = $meetingId;
									}
								}
								// exit;	
								// get race values from JSON
								if(isset($dataArray['RaceNo'])){
									$raceEvent->number = $dataArray['RaceNo'];
								}
								if(isset($dataArray['JumpTime'])){
									$raceEvent->start_date = $dataArray['JumpTime'];
								}
								
								//TODO: check race status code from code table and get codes from IGAS?
								if(isset($dataArray['RaceStatus'])){
									switch($dataArray['RaceStatus']){
										case "O":
											$raceEvent->event_status_id = '1';
											break;
										case "C":
											$raceEvent->event_status_id = '1';
											break;
										default:
											echo "Race|ERROR: No race status. Can't process\n";
									}
								
								}
								
								// TODO: Where is runner count currently?
								//if(isset($dataArray['RunnerCount'])){
								//	$raceEvent->type_code = $dataArray['RunnerCount'];
								//}
								
								if(isset($dataArray['RaceName'])){
									$raceEvent->name = $dataArray['RaceName'];
								}
								if(isset($dataArray['RaceDistance'])){
									$raceEvent->distance = $dataArray['RaceDistance'];
								}
								if(isset($dataArray['RaceClass'])){
									$raceEvent->class = $dataArray['RaceClass'];
								}
								
							
								// save or update the record
								$raceEventSave = $raceEvent->save();
								$raceEventID = $raceEvent->id;
								
								echo"Race: Record Added/Updated\n";
								
								// Add the event_group_event record if adding race
								
								// TODO: maybe through eloquent check if the race already exists in DB also need to check what event_id field stores
								$egeExists = DB::table('tbdb_event_group_event')->where('event_id', $raceEventID)->where('event_group_id', $meetingExists)->pluck('event_id');
								
								if(!$egeExists){
									$eventGroupEvent = new RaceEventGroupEvent;
									$eventGroupEvent->event_id = $raceEventID;
									$eventGroupEvent->event_group_id = $meetingExists;
									$eventGroupEvent->save();
									echo "EGE: Added event_group_event record\n\n";
									// Add event_group event record
								}else{
									echo"EGE: In DB\n\n";
								}
							}else{
								echo "Meeting|ERROR: Meeting for race does not exist\n";
							}	
							
						}
	
						break;
											
					// Selection/Runner Data - The runners in the race
					case "RunnerList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							$raceExists = $selectionsExists = 0;
							// Check all required data is available in the JSON for the runner
							if(isset($dataArray['MeetingId'])  &&  isset($dataArray['RaceNo']) && isset($dataArray['RunnerNo']) ){
								$meetingId = $dataArray['MeetingId'];
								$raceNo = $dataArray['RaceNo'];
								$runnerNo = $dataArray['RunnerNo'];
									
								//check if race exists in DB
								$raceExists = RaceEvent::eventExists($meetingId, $raceNo);
									
								//TODO: add error output to a log
								if($raceExists){
									
									// cehck if selection exists in the DB
									$selectionsExists = RaceSelection::selectionExists($meetingId, $raceNo, $runnerNo);
										
									// if runner exists update that record
									if($selectionsExists){
										echo "Runner:  Already in DB:". $selectionsExists ." $meetingId.\n";
										$raceRunner = RaceSelection::find($selectionsExists);
									}else{
										echo "Runner:  Added to DB:". $selectionsExists ." $meetingId.\n";
										$raceRunner = new RaceSelection;
											
										// check if market for event exists
										$marketID = DB::table('tbdb_market')->where('event_id', $raceExists)
																			->where('market_type_id', '110')->pluck('id');
											
										if(!$marketID){
											// add market record
											$runnerMarket = new RaceMarket;
											$runnerMarket->event_id = $raceExists;
											$runnerMarket->market_type_id = 110; //TODO: this needs to come from db
											$runnerMarket->save();
											$marketID = $runnerMarket->id;
											
											echo "Runner: Add market record for event: $raceExists\n";
										}
										$raceRunner->market_id = $marketID;
									}
											
									//TODO: SILK DATA REQUIRED
									// if(isset($dataArray['SilkNo'])){
									//	$raceEvent->silk = $dataArray['SilkNo'];
									//}
										
									if(isset($dataArray['BarrierNo'])){
										$raceRunner->barrier = $dataArray['BarrierNo'];
									}
									if(isset($dataArray['Name'])){
										$raceRunner->name = $dataArray['Name'];
									}
									if(isset($dataArray['RunnerNo'])){
										$raceRunner->number = $dataArray['RunnerNo'];
									}
									if(isset($dataArray['JTD'])){
										$raceRunner->associate = $dataArray['JTD'];
									}
									if(isset($dataArray['Scratched'])){
										$raceRunner->selection_status_id = $dataArray['Scratched'];
									}
									if(isset($dataArray['Weight'])){
										$raceRunner->weight = $dataArray['Weight'];
									}
										
									// save or update the record
								
									$raceRunnerSave = $raceRunner->save();
									$raceRunnerID = $raceRunner->id;
													
									echo"Runner: Record Added/Updated. MID:$meetingId , Race:$raceNo, Runner:$runnerNo \n\n";
									
								}else {
									echo"Runner|ERROR: No race found for this runner\n";
								}
							}else {
								echo "Runner|ERROR: Missing Runner data. Can't process";
							}
						}
						break;
						
					// Result Data - the actual results of the race
					case "ResultList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							//echo"Results Object: ";
							//print_r($dataArray);
							//echo "\n";
							$selectionsExists = $resultExists = 0;
							
							// Check required data to update a Result is in the JSON
							if(isset($dataArray['MeetingId'])  &&  isset($dataArray['RaceNo']) && isset($dataArray['Selection']) && isset($dataArray['BetType']) && isset($dataArray['PriceType']) && isset($dataArray['PlaceNo']) && isset($dataArray['Payout'])   ){
								$meetingId = $dataArray['MeetingId'];
								$raceNo = $dataArray['RaceNo'];
								$betType = $dataArray['BetType'];
								$priceType = $dataArray['PriceType'];
								$selection = $dataArray['Selection'];
								$placeNo = $dataArray['PlaceNo'];
								$payout = $dataArray['Payout'];
								
								// TODO: Check JSON data is valid
									
								// check if selection exists in the DB
								$selectionsExists = RaceSelection::selectionExists($meetingId, $raceNo, $runnerNo);
								
								if ($selectionsExists){
									echo "Result: Selection exists in DB: $selectionsExists\n";
									$resultExists = DB::table('tbdb_selection_result')->where('selection_id', $selectionsExists)->pluck('id');
								
									// if result exists update that record
									if($resultExists){
										echo "Result: Already in DB:$resultExists, MID:$meetingId, Race:$raceNo, SEL:$selection\n";
										$raceResult = RaceResult::find($resultExists);
										//$raceResult = RaceResult::where('selection_id', $selectionsExists);
									}else{
										echo "Result: Added to DB:$resultExists, MID:$meetingId, Race:$raceNo, SEL:$selection\n";
										$raceResult = new RaceResult;
										$raceResult->selection_id = $selectionsExists;
									}
									
									// process 1st 4 places
									switch($betType) {
										// 1st place
										case "WIN":
											$raceResult->position = $placeNo;
											$raceResult->win_dividend = $payout / 100;
											break;
										case "PLC":
											$raceResult->position = $placeNo;
											$raceResult->place_dividend = $payout / 100;
											break;
												
										default:
											echo "Result|ERROR: No valid betType found:$betType\n";
									}
										
									// save or update the record
									$raceResultSave = $raceResult->save();
									$raceResultID = $raceResult->id;
									
									echo"Result: Record Added/Updated: $raceResultID\n\n";
								}else{
									echo "Result|ERROR: No Selection found. Results not updated\n";
								}
							}else { // not all required data available
								echo "Result|ERROR: Missing Results data. Can't process";
							}
						}
						break; 
							
					// Price Data
					case "PriceList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							//echo"Price Object: ";
							//print_r($dataArray);
							//echo "\n";
							// reset counters
							$selectionExists = $resultExists = 0;
														
							if(isset($dataArray['MeetingId'])  &&  isset($dataArray['RaceNo']) && isset($dataArray['BetType']) && isset($dataArray['PriceType']) && isset($dataArray['PoolAmount']) && isset($dataArray['OddString'])){
								$meetingId = $dataArray['MeetingId'];
								$raceNo = $dataArray['RaceNo'];
								$betType = $dataArray['BetType'];
								$priceType = $dataArray['PriceType'];
								$poolAmount = $dataArray['PoolAmount'];
								$oddsString = $dataArray['OddString'];
								
								$oddsArray = explode(';', $oddsString);
								
								// TODO: Check JSON data is valid
									
								// check if race exists in DB
								$raceExists = RaceEvent::eventExists($meetingId, $raceNo);
								
								// if race exists update that record
								if($raceExists){
									// check for odds
									if(is_array($oddsArray)){
										//loop on odds array
										$runnerCount = 1;
										foreach($oddsArray as $runnerOdds){
											// get selectionID for runner
											
											// check if selection exists in the DB
											$selectionExists = RaceSelection::selectionExists($meetingId, $raceNo, $runnerCount);
										
											if ($selectionExists){
												echo "Price: Selection exists in DB: $selectionExists\n";
												$priceExists = DB::table('tbdb_selection_price')->where('selection_id', $selectionExists)->pluck('id');
											
												// if result exists update that record otherwise create a new one
												if($priceExists){
													echo "Price: Already in DB:$priceExists, MID:$meetingId, Race:$raceNo, SEL:$selectionExists, Runner:$runnerCount, ODD:$runnerOdds\n";
													$runnerPrice = RaceSelectionPrice::find($priceExists);
												}else{
													echo "Price: Added to DB:$resultExists, MID:$meetingId, Race:$raceNo, SEL:$selectionExists, Runner:$runnerCount, ODD:$runnerOdds\n";
													$runnerPrice = new RaceSelectionPrice;
													$runnerPrice->selection_id = $selectionExists;
												}
												$oddsSet = 0;
												// update the correct field
												switch($betType){
													case "WIN":
														$runnerPrice->win_odds = $runnerOdds / 100;
														$oddsSet = 1;
														echo "Price: Win odds set: $runnerOdds \n";
														break;
													case "PLC":
														$runnerPrice->place_odds = $runnerOdds / 100;
														$oddsSet = 1;
														echo "Price: Place odds set: $runnerOdds\n";
														break;
													case "QIN":
														echo "Price: Where do I update QIN odds\n";
														break;
													default:
														echo "Price|ERROR: bet Type not valid: $betType. Can't process\n";
												}
												// save/update the price record
												if ($oddsSet){
													$runnerPrice->save();
													echo "Price: Record updated/added\n";
												}else{
													echo "Price|ERROR: WIN/PLC Odds not saved\n";
												}
											}else{
												echo "Price|ERROR: No selction for Price in DB. Can't process\n";
											}
											$runnerCount++;
										}
										} else {
											echo "Price|ERROR: No odds array found. Can't process\n";
										}
									}else{
										echo "Price|ERROR: No race found. Can't store results\n";
									}
							}else{
								echo "Price|ERROR: Missing Price Data. Can't process\n";
							}
							echo "\n\n";
						}
	
						break;
						
						
					// Outcome Data
					case "OutcomeList":
						echo"CASE: $key\n";
						
							foreach ($racingArray as $dataArray){
								echo"Outcome Object: ";
								print_r($dataArray);
								echo "\n";
							}
					
						
					/* 	$meetingId = $param;
						$meetingDate = $param;
						$meetingName = $param;
						$meetingSport = $param;
						$meetingCountry = $param;
						$meetingEventCount = $param;
						$metingWeather = $param; */
						break;
					
					default :
						return Response::json(array(
							'error' => true,
							'message' => 'Error: Data format not recognised'),
							400
						);
				}
			}else{
				/* return Response::json(array(
						'error' => true,
						'message' => 'Error: No Data found'),
						400
				); */
			}
		}
		
		
		//return RaceMeetings::all();
		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}