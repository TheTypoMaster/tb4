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
		//echo"$racingJSON";
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
		
		// loop on objects in data
		foreach($racingJSON as $key => $racingArray){
			echo "Working on KEY: $key\n";
			
			// Make sure we have some data to process in the array
			if(is_array($racingArray)){
				
				switch($key){
					
					// Meeting Data - the meeting/venue
					case "MeetingList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							
							$meetingId = $dataArray['Id'];
							// TODO: maybe through eloquent check if the meeting already exists in DB
							$meetingExists = DB::table('tbdb_event_group')->where('external_event_group_id', $meetingId)->pluck('id');

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
							
							// get meetings values from JSON
							if(isset($dataArray['Date'])){
								$raceMeet->start_date = $dataArray['Date'];
							}
							if(isset($dataArray['Name'])){
								$raceMeet->name = $dataArray['Name'];
							}
							if(isset($dataArray['RaceType'])){
								$raceMeet->type_code = $dataArray['RaceType'];
							}
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
							//echo"Race Object: ";
							//print_r($dataArray);
							//echo "\n";
							
							// grab the meeting and race for the DB query
							$meetingId = $dataArray['MeetingId'];
							$raceNumber = $dataArray['RaceNo'];
							
							// make sure the meeting this race is in exists 1st
							// TODO: Through eloquent
							$meetingExists = DB::table('tbdb_event_group')->where('external_event_group_id', $meetingId)->pluck('id');
							
							// if meeting exists update that record then continue to add/update the race record
							if($meetingExists){
							
								// TODO: Through eloquent?
								$raceExists = DB::table('tbdb_event')
									->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
									->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
									->where('tbdb_event_group.external_event_group_id',$meetingId )
									->where('tbdb_event.number',$raceNumber)->pluck('tbdb_event.id');
									//->select('tbdb_event.id');

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
									
								// get race values from JSON
								if(isset($dataArray['RaceNo'])){
									$raceEvent->number = $dataArray['RaceNo'];
								}
								if(isset($dataArray['JumpTime'])){
									$raceEvent->start_date = $dataArray['JumpTime'];
								}
								
								//TODO: check race status code from code table?
								//if(isset($dataArray['RaceStatus'])){
								//	$raceEvent->name = $dataArray['RaceStatus'];
								//}
								
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
								echo "Meeting for race does not exist\n";
							}	
							
						}
	
						break;
											
					// Selection/Runner Data - The runners in the race
					case "RunnerList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							$raceExists = $runnerExists = 0;
							// Check all required data is available in the JSON for the runner
							if(isset($dataArray['MeetingId'])  &&  isset($dataArray['RaceNo']) && isset($dataArray['RunnerNo']) ){
								$meetingId = $dataArray['MeetingId'];
								$raceNo = $dataArray['RaceNo'];
								$runnerNo = $dataArray['RunnerNo'];
									
								// TODO: check that race exists before moving on - Eloquent?
								$raceExists = DB::table('tbdb_event')
								->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
								->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
								->where('tbdb_event_group.external_event_group_id', $meetingId )
								->where('tbdb_event.number', $raceNo)->pluck('tbdb_event.id');
									
								//TODO: add error output to a log
								if($raceExists){
									
									// TODO: check if runner is in the DB
									$runnerExists = DB::table('tbdb_selection')
									->join('tbdb_market', 'tbdb_selection.market_id', '=', 'tbdb_market.id')
									->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_market.event_id')
									->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
									->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
									->where('tbdb_event_group.external_event_group_id',$meetingId )
									->where('tbdb_event.number', $raceNo)
									->where('tbdb_selection.number', $runnerNo)->pluck('tbdb_selection.id');
										
									// if runner exists update that record
									if($runnerExists){
										echo "Runner:  Already in DB:". $runnerExists ." $meetingId.\n";
										$raceRunner = RaceSelection::find($runnerExists);
									}else{
										echo "Runner:  Added to DB:". $runnerExists ." $meetingId.\n";
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
									echo"Runner: No race found for this runner\n";
								}
							}else {
								echo "Runner: Missing Runner data. Can't process";
							}
						}
						break;
						
					// Result Data - the actual results of the race
					case "ResultList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							echo"Results Object: ";
							print_r($dataArray);
							echo "\n";
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
									
								// Check required records exist in the DB to store this result
								//- Meeting/Race/Selection exist
								$selectionsExists = DB::table('tbdb_selection')
								->join('tbdb_market', 'tbdb_selection.market_id', '=', 'tbdb_market.id')
								->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_market.event_id')
								->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
								->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
								->where('tbdb_event_group.external_event_group_id',$meetingId )
								->where('tbdb_event.number', $raceNo)
								->where('tbdb_selection.number', $selection)->pluck('tbdb_selection.id');
								
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
											echo "Result: No valid betType found:$betType\n";
									}
										
									// save or update the record
									$raceResultSave = $raceResult->save();
									$raceResultID = $raceResult->id;
									
									echo"Result: Record Added/Updated: $raceResultID\n\n";
								}else{
									echo "Result: No Selection found. Results not updated\n";
								}
							}else { // not all required data available
								echo "Result: Missing Results data. Can't process";
							}
						}
						break; 
							
					// Price Data
					case "PriceList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							echo"Price Object: ";
							print_r($dataArray);
							echo "\n";
							// reset counters
							$selectionsExists = $resultExists = 0;
														
							if(isset($dataArray['MeetingId'])  &&  isset($dataArray['RaceNo']) && isset($dataArray['BetType']) && isset($dataArray['PriceType']) && isset($dataArray['PoolAmount']) && isset($dataArray['OddString'])){
								$meetingId = $dataArray['MeetingId'];
								$raceNo = $dataArray['RaceNo'];
								$betType = $dataArray['BetType'];
								$priceType = $dataArray['PriceType'];
								$selection = $dataArray['PoolAmount'];
								$placeNo = $dataArray['OddString'];
								
								// TODO: Check JSON data is valid
									
								// Check required records exist in the DB to store this result
								//- Meeting/Race/Selection exist
								$selectionsExists = DB::table('tbdb_selection')
								->join('tbdb_market', 'tbdb_selection.market_id', '=', 'tbdb_market.id')
								->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_market.event_id')
								->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
								->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
								->where('tbdb_event_group.external_event_group_id',$meetingId )
								->where('tbdb_event.number', $raceNo)
								->where('tbdb_selection.number', $selection)->pluck('tbdb_selection.id');
								
								if ($selectionsExists){
									echo "Price: Selection exists in DB: $selectionsExists\n";
									$priceExists = DB::table('tbdb_selection_price')->where('selection_id', $selectionsExists)->pluck('id');
								
									// if result exists update that record
									if($priceExists){
										echo "Price: Already in DB:$priceExists, MID:$meetingId, Race:$raceNo, SEL:$selection\n";
										$raceResult = RaceResult::find($resultExists);
									//$raceResult = RaceResult::where('selection_id', $selectionsExists);
									}else{
										echo "Price: Added to DB:$priceExists, MID:$meetingId, Race:$raceNo, SEL:$selection\n";
										$raceResult = new RaceResult;
										$raceResult->selection_id = $selectionsExists;
									}
										
								
								
								}
								
								
								
								
							}else{
								echo "Price: Missing Price Data. Can't process\n";
							}
							
							
							
							
							
							
						}
	/* 					$priceMeetingId = $param;
						$priceRaceno = $param;
						$priceBetType = $param;
						$pricePriceType = $param;
						$pricePoolAmount = $param;
						$priceOddString = $param; */
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