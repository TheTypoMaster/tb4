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
					
					// Meeting Data
					case "MeetingList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							$meetingId = $dataArray['Id'];
							// TODO: maybe through eloquent check if the meeting already exists in DB
							$meetingExists = DB::table('tbdb_event_group')->where('external_event_group_id', $meetingId)->pluck('id');
							
							// exit;
							
							// get data to be stored from array
		/* 					$meetingId = $param;
		 				 	$meetingDate = $param;
							$meetingName = $param;
							$meetingRaceType = $param;
							$meetingSport = $param;
							$meetingCountry = $param;
							$meetingEventCount = $param;
							$meetingWeather = $param;
							$meetingTrack = $param; */
							
							if($meetingExists){
								echo "Meeting Exists:". $meetingExists ."\n";
								$raceMeet = RaceMeeting::find($meetingExists);
							}else{
								echo "Meeting NOT Exists:". $meetingExists ."\n";
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
															
						}
						break;
						
					// Race data
					case "RaceList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							echo"Race Object: ";
							print_r($dataArray);
							echo "\n";
						}
	/* 					$raceMeetingId = $param;
						$raceRaceNo = $param;
						$raceJumpTime = $param;
						$raceStatus = $param;
						$raceRunnerCount = $param; */
						break;
											
					// Selection/Runner Data
					case "RunnerList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							echo"Runner Object: ";
							print_r($dataArray);
							echo "\n";
						}
	/* 					$runnerMeetingId = $param;
						$runnerRaceNo = $param;
						$runnerRunnerNo = $param;
						$runnerBarrierNo = $param;
						$runnername = $param;
						$runnerJTD = $param;
						$runnerScratched = $param;
						$runnerWeight = $param; */
						break;
						
					// Result Data
					case "ResultList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							echo"Results Object: ";
							print_r($dataArray);
							echo "\n";
						}
	/* 					$resultMeetingID = $param;
						$resultRacenNo= $param;
						$resultBetType = $param;
						$resultPriceType = $param;
						$resultSelection = $param;
						$resultPlaceNo = $param;
						$resultPayout = $param; */
						break; 
							
					// Price Data
					case "PriceList":
						echo"CASE: $key\n";
						foreach ($racingArray as $dataArray){
							echo"Price Object: ";
							print_r($dataArray);
							echo "\n";
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
				return Response::json(array(
						'error' => true,
						'message' => 'Error: No Data found'),
						400
				);
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