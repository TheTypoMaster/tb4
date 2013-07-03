<?php namespace TopBetta\backend;

use TopBetta;

class RacingController extends \BaseController {

	/**
	 * Default log message type
	 *
	 * @var integer
	 */
	const LOG_TYPE_NORMAL = 0;
	
	/**
	 * Debug log message type
	 *
	 * @var integer
	 */
	const LOG_TYPE_DEBUG = 1;
	
	/**
	 * Log message type for errors
	 *
	 * @var integer
	 */
	const LOG_TYPE_ERROR = 2;
	
	/**
	 * Default time formatting string for log messages
	 *
	 * @var string
	 */
	const LOG_TIME_FORMAT_DEFAULT = 'r';
	
	/**
	 * Show time string in log messages
	 *
	 * @var string
	 */
	const LOG_TIME_SHOWN = false;
		
	//TODO: move to provder class
	 
	const 	EXOTICS_TOTE 		= 'SUP',
			METRO_GALLOPS_WIN 	= 'TOP',
			METRO_GALLOPS_PLC 	= 'TOP',
			PROV_GALLOPS_WIN 	= 'TOP',
			PROV_GALLOPS_PLC 	= 'MID',
			COUNTRY_GALLOPS_WIN = 'TOP',
			COUNTRY_GALLOPS_PLC = 'MID',
			OVERSEAS_GALLOPS_WIN = 'MID',
			OVERSEAS_GALLOPS_PLC = 'MID',
			AUNZ_HARNESS_WIN 	= 'MID',
			AUNZ_HARNESS_PLC 	= 'MID',
			AUNZ_GREYHOUNDS_WIN = 'MID',
			AUNZ_GREYHOUNDS_PLC = 'MID';

	private $product_types = array(
			self::METRO_GALLOPS_WIN ,
			self::METRO_GALLOPS_PLC	,
			self::PROV_GALLOPS_WIN ,
			self::PROV_GALLOPS_PLC,
			self::OVERSEAS_GALLOPS_WIN,
			self::OVERSEAS_GALLOPS_PLC,
			self::AUNZ_HARNESS_WIN,
			self::AUNZ_HARNESS_PLC,
			self::AUNZ_GREYHOUNDS_WIN,
			self::AUNZ_GREYHOUNDS_PLC,
			self::EXOTICS_TOTE
	);
	
	
	// Map igas products names to ours
	private $product_type_lookup = array(
			self::METRO_GALLOPS_WIN 	=> 'topsp',
			self::METRO_GALLOPS_PLC		=> 'top',
			self::PROV_GALLOPS_WIN 		=> 'topsp',
			self::PROV_GALLOPS_PLC 		=> 'mid',
			self::COUNTRY_GALLOPS_WIN 	=> 'topsp',
			self::COUNTRY_GALLOPS_PLC 	=> 'mid',
			self::OVERSEAS_GALLOPS_WIN 	=> 'mid',
			self::OVERSEAS_GALLOPS_PLC 	=> 'mid',
			self::AUNZ_HARNESS_WIN 		=> 'mid',
			self::AUNZ_HARNESS_PLC 		=> 'mid',
			self::AUNZ_GREYHOUNDS_WIN 	=> 'mid',
			self::AUNZ_GREYHOUNDS_PLC 	=> 'mid',
			self::EXOTICS_TOTE 			=> 'supertab'
	);
	
	
		
	/**
	 * Debugging mode flag
	 *
	 * @var boolean
	 */
	private $debug = true;
	
	
	
	
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
		return "Racing API Index";
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
		
		// Log this
		TopBetta\LogHelper::l("BackAPI: Racing - Reciving POST");
		
		// get the JSON POST
		$racingJSON = \Input::json();
		//$racingJSON = unserialize(file_get_contents('/tmp/backAPIracing-20130614072624'));
		$jsonSerialized = serialize($racingJSON);

		if($this->debug){
			$timeStamp = date("YmdHis");
			\File::append('/tmp/backAPIracing-'.$timeStamp, $jsonSerialized);
		}
		
		// make sure JSON was received
		$keyCount = count($racingJSON);
		if(!$keyCount){
			TopBetta\LogHelper::l("BackAPI: Racing - No Data In POST",2);
			return \Response::json(array(
					'error' => true,
					'message' => 'Error: No JSON data received'),
					400
			);
		}

		// Set the market Type
		$marketName = "Racing";
		 
		//$racingJSON = print_r($racingJSON, true);
		//echo"$racingJSON\n\n\n\n\n";
		//exit;
		
		//TODO: // validate the json. Create some rules and check the json validates
		/* $validation = Validator::make(array('json'=> $racingJSON),array('json' => 'mime:json'));
		if($validation->fails())
		{
			return Response::json($validation->errors);
		}
		else
		{
			// all OK!
		}
		exit; */
		// JSON objects MeetingList/RaceList/RunnerList/ResultList/PriceList/OutcomeList
		
		TopBetta\LogHelper::l("BackAPI: Racing - Processing '$keyCount' Objects");
		$objectCount=1;
		// loop on objects in data
		foreach($racingJSON as $key => $racingArray){
			
			// Make sure we have some data to process in the array
			if(is_array($racingArray)){
				
				// process the meeting/race/runner data
				switch($key){
					
					// Meeting Data - the meeting/venue
					case "MeetingList":
						TopBetta\LogHelper::l("BackAPI: Racing - Processing Meeting, Object:$objectCount");
						foreach ($racingArray as $dataArray){
							// store data from array
							if(isset($dataArray['Id'])){
								$meetingId = $dataArray['Id'];
								
								// check if meeting exists in DB
								$meetingExists = TopBetta\RaceMeeting::meetingExists($meetingId);
								
								// if meeting exists update that record
								if($meetingExists){
									TopBetta\LogHelper::l("BackAPI: Racing - Processing Meeting, In DB: $meetingExists", 1);
									$raceMeet = TopBetta\RaceMeeting::find($meetingExists);
								}else{
									TopBetta\LogHelper::l("BackAPI: Racing - Processing Meeting, Added to DB: $meetingExists", 1);
									$raceMeet = new TopBetta\RaceMeeting;
									if(isset($dataArray['Id'])){
										$raceMeet->external_event_group_id = $dataArray['Id'];
									}
								}
								
								if(isset($dataArray['Date'])){
									$raceMeet->start_date = $dataArray['Date'];
								}
								if(isset($dataArray['Name'])){
									$raceMeet->name = $dataArray['Name'];
								}
								
								
								if(isset($dataArray['RaceType'])){
									switch($dataArray['RaceType']){
										case "R":
											$raceMeet->type_code = 'R';
											$raceMeet->tournament_competition_id = '31';
											break;
										case "T":
											$raceMeet->type_code = 'H';
											$raceMeet->tournament_competition_id = '32';
											break;
										case "G":
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
								if(isset($dataArray['State'])){
									$raceMeet->state = $dataArray['State'];
								}
								if(isset($dataArray['Country'])){
									$raceMeet->country = $dataArray['Country'];
								}
								if(isset($dataArray['MeetingType'])){
									$raceMeet->meeting_grade = $dataArray['MeetingType'];
								}
								if(isset($dataArray['RailPosition'])){
									$raceMeet->rail_position = $dataArray['RailPosition'];
								}
								
								// save or update the record
								$raceMeetSave = $raceMeet->save();
								$raceMeetID = $raceMeet->id;
								TopBetta\LogHelper::l("BackAPI: Racing - Processed Meeting. MID:$meetingId, Date:$raceMeet->start_date, Name:$raceMeet->name, Type:$raceMeet->type_code, Events:$raceMeet->events, Weather:$raceMeet->weather, Track:$raceMeet->track");
								
							}else{
								TopBetta\LogHelper::l("BackAPI: Racing - Processing Meeting. No Meeting ID, Can't process", 1);
							}								
						}
						break;
						
					// Race data - the races in the meeting
					case "RaceList":
						TopBetta\LogHelper::l("BackAPI: Racing - Processing $objectCount: Race");
						foreach ($racingArray as $dataArray){
													
							if(isset($dataArray['MeetingId']) && $dataArray['RaceNo']){
								$meetingId = $dataArray['MeetingId'];
								$raceNo = $dataArray['RaceNo'];
							
								// make sure the meeting this race is in exists 1st
								$meetingExists = TopBetta\RaceMeeting::meetingExists($meetingId);
	
								// if meeting exists update that record then continue to add/update the race record
								if($meetingExists){
									
									$meetingRecord = TopBetta\RaceMeeting::find($meetingExists);
								
									// check if race exists
									$raceExists = TopBetta\RaceEvent::eventExists($meetingId, $raceNo);
	
									// if race exists update that record
									if($raceExists){
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Race, In DB: $raceExists", 1);
										$raceEvent = TopBetta\RaceEvent::find($raceExists);
									}else{
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Race, Added to DB: $raceExists", 1);
										$raceEvent = new TopBetta\RaceEvent;
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
									
									//TODO: Code Table lookup on different race status
									//TODO: Triggers for tournament processing on race status of R (final divs) and A (abandoned) 
									if(isset($dataArray['RaceStatus'])){
										switch($dataArray['RaceStatus']){
											case "O":
												$raceEvent->event_status_id = '1';
												break;
											case "C":
												$raceEvent->event_status_id = '5';
												break;
											case "S":
												$raceEvent->event_status_id = '5'; // no suspended status in code table
												break;
											case "I":
												$raceEvent->event_status_id = '6';
												break;
											case "R":
												$raceEvent->event_status_id = '2';
												break;
											case "A":
												$raceEvent->event_status_id = '3';
												break;

											default:
												TopBetta\LogHelper::l("BackAPI: Racing - Processing Race. No valid race status found. Can't process. ", 2);
										}
									
									}
									
									// TODO: Not stored or needed?
									/* if(isset($dataArray['RunnerCount'])){
										$raceEvent->type_code = $dataArray['RunnerCount'];
									} */
									
									if(isset($dataArray['RaceName'])){
										$raceEvent->name = $dataArray['RaceName'];
									}
									if(isset($dataArray['Distance'])){
										$raceEvent->distance = $dataArray['Distance'];
									}
									if(isset($dataArray['RaceClass'])){
										$raceEvent->class = $dataArray['RaceClass'];
									}
								
									// save or update the record
									$raceEvent->weather = $meetingRecord->weather;
									$raceEvent->track_condition = $meetingRecord->track;
									$raceEventSave = $raceEvent->save();
									$raceEventID = $raceEvent->id;
									
									TopBetta\LogHelper::l("BackAPI: Racing - Processed Race. MID:$meetingId, RaceNo:$raceNo, Name: $raceEvent->name, JumpTime:$raceEvent->start_date, Status:$raceEvent->event_status_id");
																	
									// Add the event_group_event record if adding race
									
									// TODO: maybe through eloquent check if the race already exists in DB also need to check what event_id field stores
									$egeExists = \DB::table('tbdb_event_group_event')->where('event_id', $raceEventID)->where('event_group_id', $meetingExists)->pluck('event_id');
									
									if(!$egeExists){
										$eventGroupEvent = new TopBetta\RaceEventGroupEvent;
										$eventGroupEvent->event_id = $raceEventID;
										$eventGroupEvent->event_group_id = $meetingExists;
										$eventGroupEvent->save();
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Race, Added to DB",1);
										// Add event_group event record
									}else{
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Race, EGE in DB",1);
									}
								}else{
									TopBetta\LogHelper::l("BackAPI: Racing - Processing Race. Meeting for race does not exist. Can't process", 2);
								}
							}else{
								TopBetta\LogHelper::l("BackAPI: Racing - Processing Race. MeetingID or RaceNo not set. Can't process", 2);
							}
						}
						break;
											
					// Selection/Runner Data - The runners in the race
					case "RunnerList":
						TopBetta\LogHelper::l("BackAPI: Racing - Processing $objectCount: Runner");
						foreach ($racingArray as $dataArray){
							$raceExists = $selectionsExists = 0;
							// Check all required data is available in the JSON for the runner
							if(isset($dataArray['MeetingId'])  &&  isset($dataArray['RaceNo']) && isset($dataArray['RunnerNo'])){
								$meetingId = $dataArray['MeetingId'];
								$raceNo = $dataArray['RaceNo'];
								$runnerNo = $dataArray['RunnerNo'];
									
								//check if race exists in DB
								$raceExists = TopBetta\RaceEvent::eventExists($meetingId, $raceNo);
									 
								//TODO: add error output to a log
								if($raceExists){
									
									// check if selection exists in the DB
									$selectionsExists = TopBetta\RaceSelection::selectionExists($meetingId, $raceNo, $runnerNo);
										
									// if runner exists update that record
									if($selectionsExists){
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Runner, In DB: $selectionsExists", 1);
										$raceRunner = TopBetta\RaceSelection::find($selectionsExists);
									}else{
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Runner, Added to DB: $selectionsExists", 1);
										$raceRunner = new TopBetta\RaceSelection;

										// get market ID
										$marketTypeID = TopBetta\RaceMarketType::where('name', '=', $marketName)->pluck('id');
										
										// check if market for event exists
										$marketID = TopBetta\RaceMarket::marketExists($raceExists, $marketTypeID);
										
										if(!$marketID){
											// add market record
											$runnerMarket = new TopBetta\RaceMarket;
											$runnerMarket->event_id = $raceExists;
											$runnerMarket->market_type_id = 110; //TODO: this needs to come from db
											$runnerMarket->save();
											$marketID = $runnerMarket->id;
											
											TopBetta\LogHelper::l("BackAPI: Racing - Processing Runner. Add market record for event: $raceExists");
										}
										$raceRunner->market_id = $marketID;
										
									}
								
									if(isset($dataArray['BarrierNo'])){
										$raceRunner->barrier = $dataArray['BarrierNo'];
									}
									if(isset($dataArray['Name'])){
										$raceRunner->name = $dataArray['Name'];
									}
									if(isset($dataArray['RunnerNo'])){
										$raceRunner->number = $dataArray['RunnerNo'];
									}
									
									//TODO: Code Table Lookup/Provider matching table							
									if(isset($dataArray['Scratched'])){
										if ($dataArray['Scratched'] == '1'){
											$raceRunner->selection_status_id = '2';
										}else{
											$raceRunner->selection_status_id = '1';
										}
									}
									if(isset($dataArray['Weight'])){
										$raceRunner->weight = $dataArray['Weight'] / 10;
									}

									// get the meeting record ID
									$meetingExists = TopBetta\RaceMeeting::meetingExists($meetingId);
									
									// Get meeting type
									$meetingRecord = TopBetta\RaceMeeting::find($meetingExists);
									$meetingType = $meetingRecord->type_code;
									
									// Get silkID and Last Starts for runner from RISA table
									if($meetingType == "R"){
										// get silk ID from RISA data: tb_racing_data_risa_silk_map
										// check if meeting exists in DB
										$meetingExists = TopBetta\RaceMeeting::meetingExists($meetingId);
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Runner. Looking up silk and LastStarts");
										if($meetingExists){
											// if meeting exists get the record
											$raceMeet = TopBetta\RaceMeeting::find($meetingExists);
											// Grab the date from the date/time field
											$meetDate = substr($raceMeet->start_date, 0, 10);
											// default to Racing atm
											$codeType = 'R';
											// get the venue name
											$venueName = $raceMeet->name;
											// make sure the numbers are 2 digits
											($raceNo < 10) ? $raceNumber = '0' . $raceNo : $raceNumber = $raceNo;
											($runnerNo < 10) ? $runnerNumber = '0' . $runnerNo : $runnerNumber = $runnerNo;
											// Build the runner code
											$runnerCode = $meetDate."-".$codeType."-%".$venueName."%-".$raceNumber."-".$runnerNumber;
											TopBetta\LogHelper::l("BackAPI: Racing - Processing Runner. Runner Code: $runnerCode");
											// Get Silk ID for this runner
											$runnerSilkObject = TopBetta\backend\RisaSilks::where('runner_code', 'LIKE', "$runnerCode" )->get();
												//$o = print_r($runnerSilkObject, true);
												// TopBetta\LogHelper::l("BackAPI: Racing - Processing Runner RISA Object:$o.");
												if(count($runnerSilkObject) > 0){
													if(isset($runnerSilkObject[0]->silk_file_name)){
														$raceRunner->silk_id = $runnerSilkObject[0]->silk_file_name;
													}
													if(isset($runnerSilkObject[0]->last_starts)){
														$raceRunner->last_starts = $runnerSilkObject[0]->last_starts;
													}
												}
											
											TopBetta\LogHelper::l("BackAPI: Racing - Processing Runner. Runner Code: $runnerCode, Silk:$raceRunner->silk_id, LastStarts:$raceRunner->last_starts.");
										}
									}

									if(isset($dataArray['Jockey'])){
										$raceRunner->associate = $dataArray['Jockey'];
									}
									if(isset($dataArray['Trainer'])){
										$raceRunner->trainer = $dataArray['Trainer'];
							
									}
									// save or update the record
									$raceRunnerSave = $raceRunner->save();
									$raceRunnerID = $raceRunner->id;
									$raceRunner->wager_id = $raceRunner->id;
									$raceRunnerSave = $raceRunner->save();
													
									TopBetta\LogHelper::l("BackAPI: Racing - Processed Runner. MID:$meetingId , RaceNo:$raceNo, RunnerNo:$runnerNo, Barrier:$raceRunner->barrier, Name:$raceRunner->name, Jockey:$raceRunner->associate, Scratched:$raceRunner->selection_status_id, Weight:$raceRunner->weight ");
																	
								}else {
									TopBetta\LogHelper::l("BackAPI: Racing - Processing Runner. No race found for this runner. MID:$meetingId, Race:$raceNo, Runner:$runnerNo Can't process", 2);
								}
							}else {
								TopBetta\LogHelper::l("BackAPI: Racing - Processing Race. Missing Runner data. Can't process", 2);
							}
						}
						break;
						
					// Result Data - the actual results of the race
					case "ResultList":
						TopBetta\LogHelper::l("BackAPI: Racing - Processing $objectCount: Result");
						foreach ($racingArray as $dataArray){
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
									
								// Get ID of event record - used to store exotic results/divs if required
								$eventID = TopBetta\RaceEvent::eventExists($meetingId, $raceNo);

								// grab the race type code
								$raceTypeCodeResult = Topbetta\RaceMeeting::where('external_event_group_id', '=', $meetingId)->get();
								$o = print_r($raceTypeCodeResult,true);
								
								TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, Racetype object:$o ", 1);
								//if(is_array($raceTypeCodeResult)){
								$meetingTypeCode = $raceTypeCodeResult[0]['type_code'];
								$meetingCountry = $raceTypeCodeResult[0]['country'];
								$meetingGrade = $raceTypeCodeResult[0]['meeting_grade'];
								
									
								//}else{
								//	TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, No Meeting Code or Country found. MID:$meetingId, RN:$raceNo, SEL:$selection.", 1);
								//}
					
								
								$saveRecord = 0;
															
								/*
								 * Process results with the correct Products/TOTES
								 */
								
								// Processs win  results
								if($betType == 'W'){
									// check if selection exists in the DB
									$selectionsExists = TopBetta\RaceSelection::selectionExists($meetingId, $raceNo, $selection);
									if ($selectionsExists){
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Result. Selection Exixts for result. MID:$meetingId, RN:$raceNo, SEL:$selection.",1);
										// Check if we have results already
										$resultExists = \DB::table('tbdb_selection_result')->where('selection_id', $selectionsExists)->pluck('id');
										switch($meetingTypeCode){
											
											case "R": // Gallops
												// metro
												if ($meetingCountry == "AU" && $meetingGrade == "METRO" && $priceType == self::METRO_GALLOPS_WIN){
													if($resultExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Already in DB", 1);
														$raceResult = TopBetta\RaceResult::find($resultExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
														$raceResult = new TopBetta\RaceResult;
														$raceResult->selection_id = $selectionsExists;
													}
													$saveRecord = 1;
												}
													
												// prov
												if ($meetingCountry == "AU" && $meetingGrade == "PROVINCIAL" && $priceType == self::PROV_GALLOPS_WIN){
													if($resultExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Already in DB", 1);
														$raceResult = TopBetta\RaceResult::find($resultExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
														$raceResult = new TopBetta\RaceResult;
														$raceResult->selection_id = $selectionsExists;
													}
													$saveRecord = 1;
												}
												
												// country
												if ($meetingCountry == "AU" && $meetingGrade == "COUNTRY" && $priceType == self::COUNTRY_GALLOPS_WIN){
													if($resultExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Already in DB", 1);
														$raceResult = TopBetta\RaceResult::find($resultExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
														$raceResult = new TopBetta\RaceResult;
														$raceResult->selection_id = $selectionsExists;
													}
													$saveRecord = 1;
												}
												
												// os
												if ($meetingCountry != "AU" && $priceType == self::OVERSEAS_GALLOPS_WIN){
													if($resultExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Already in DB", 1);
														$raceResult = TopBetta\RaceResult::find($resultExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
														$raceResult = new TopBetta\RaceResult;
														$raceResult->selection_id = $selectionsExists;
													}
													$saveRecord = 1;
												}
												break;
												
											case "G":
												if($priceType =="MID"){
													// if result exists update that record
													if($resultExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType. Already in DB", 1);
														$raceResult = TopBetta\RaceResult::find($resultExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
														$raceResult = new TopBetta\RaceResult;
														$raceResult->selection_id = $selectionsExists;
													}
													$saveRecord = 1;
												}
												break;
											case "H";
											if($priceType =="MID"){
												// if result exists update that record
												if($resultExists){
													TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType. Already in DB", 1);
													$raceResult = TopBetta\RaceResult::find($resultExists);
												}else{
													TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
													$raceResult = new TopBetta\RaceResult;
													$raceResult->selection_id = $selectionsExists;
												}
												$saveRecord = 1;
											}
											default:

										} // end switch
									}else{
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Result. No Selection found. Results not updated", 1);
									}

									if($saveRecord){
										$raceResult->position = $placeNo;
										$raceResult->win_dividend = $payout / 100;
											
										// save win or place odds to DB
										$raceResultSave = $raceResult->save();
										$raceResultID = $raceResult->id;

										TopBetta\LogHelper::l("BackAPI: Racing - Processed Result. MID: $meetingId, RaceCode:$meetingTypeCode, RaceNo:$raceNo, BetType:$betType, PriceType:$priceType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");
									}else{

										TopBetta\LogHelper::l("BackAPI: Racing - Processing Result. PriceType:$priceType Not saved. MID: $meetingId, RaceCode:$meetingTypeCode, RaceNo:$raceNo, BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout", 1);
									}
										
								
								// Process place results 
								}elseif($betType == "P"){	
									// check if selection exists in the DB
									$selectionsExists = TopBetta\RaceSelection::selectionExists($meetingId, $raceNo, $selection);
									if ($selectionsExists){
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Result. Selection Exixts for result. MID:$meetingId, RN:$raceNo, SEL:$selection",1);
										// Check if we have results already
										$resultExists = \DB::table('tbdb_selection_result')->where('selection_id', $selectionsExists)->pluck('id');
									
										switch($meetingTypeCode){
											case "R": // Gallops
												// metro
												if ($meetingCountry == "AU" && $meetingGrade == "METRO" && $priceType == self::METRO_GALLOPS_PLC){
													if($resultExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Already in DB", 1);
														$raceResult = TopBetta\RaceResult::find($resultExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
														$raceResult = new TopBetta\RaceResult;
														$raceResult->selection_id = $selectionsExists;
													}
													$saveRecord = 1;
												}
													
												
												// prov
												if ($meetingCountry == "AU" && $meetingGrade == "PROVINCIAL" && $priceType == self::PROV_GALLOPS_PLC){
													if($resultExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Already in DB", 1);
														$raceResult = TopBetta\RaceResult::find($resultExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
														$raceResult = new TopBetta\RaceResult;
														$raceResult->selection_id = $selectionsExists;
													}
													$saveRecord = 1;
												}
												
												// country
												if ($meetingCountry == "AU" && $meetingGrade == "COUNTRY" && $priceType == self::COUNTRY_GALLOPS_PLC){
													if($resultExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Already in DB", 1);
														$raceResult = TopBetta\RaceResult::find($resultExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
														$raceResult = new TopBetta\RaceResult;
														$raceResult->selection_id = $selectionsExists;
													}
													$saveRecord = 1;
												}
												
												// os
												if ($meetingCountry != "AU" && $priceType == self::OVERSEAS_GALLOPS_PLC){
													if($resultExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Already in DB", 1);
														$raceResult = TopBetta\RaceResult::find($resultExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
														$raceResult = new TopBetta\RaceResult;
														$raceResult->selection_id = $selectionsExists;
													}
													$saveRecord = 1;
												}
												break;
												
											case "G":
												if($priceType =="MID"){
													// if result exists update that record
													if($resultExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType. Already in DB", 1);
														$raceResult = TopBetta\RaceResult::find($resultExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
														$raceResult = new TopBetta\RaceResult;
														$raceResult->selection_id = $selectionsExists;
													}
													$saveRecord = 1;
												}
												break;
											case "H";
											if($priceType =="MID"){
												// if result exists update that record
												if($resultExists){
													TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType. Already in DB", 1);
													$raceResult = TopBetta\RaceResult::find($resultExists);
												}else{
													TopBetta\LogHelper::l("BackAPI: Racing - Processing Result, RaceCode:$meetingTypeCode, PriceType:$priceType Added to DB", 1);
													$raceResult = new TopBetta\RaceResult;
													$raceResult->selection_id = $selectionsExists;
												}
												$saveRecord = 1;
											}
											default:
									
										} // end switch
									}else{
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Result. No Selection found. Results not updated", 1);
									}
									
									if($saveRecord){
										$raceResult->position = $placeNo;
										$raceResult->place_dividend = $payout / 100;
											
										// save win or place odds to DB
										$raceResultSave = $raceResult->save();
										$raceResultID = $raceResult->id;
									
										TopBetta\LogHelper::l("BackAPI: Racing - Processed Result. MID: $meetingId, RaceCode:$meetingTypeCode, RaceNo:$raceNo, BetType:$betType, PriceType:$priceType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");
									}else{
									
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Result. PriceType:$priceType Not saved. MID: $meetingId, RaceCode:$meetingTypeCode, RaceNo:$raceNo, BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout", 1);
									}
										
								// EXOTICS
								}else{
									if ($priceType == "SUP"){
										// grab the event
										$raceEvent = TopBetta\RaceEvent::find($eventID);
											
										// build the serialised result data for this result
										$arrayKey = str_replace('-', '/', $selection);
										$arrayValue = $payout  / 100;
										$exoticArray = array($arrayKey => $arrayValue);
										$previousDivArray = array();
										// work on each exotic type
										switch($betType) {
											case "Q": // Quinella
												if(!$raceEvent->quinella_dividend == NULL){
													if(!$raceEvent->quinella_dividend == serialize($exoticArray)){
														// unserialise the existing dividend
														$previousDivArray = unserialize($raceEvent->quinella_dividend);
														// add the new dividends
														$raceEvent->quinella_dividend = serialize(array_merge($previousDivArray,$exoticArray));
													}
												}else{
													$raceEvent->quinella_dividend = serialize($exoticArray);
												}
													
												break;
											case "E": // Exacta
												if(!$raceEvent->exacta_dividend == NULL){
													if(!$raceEvent->exacta_dividend  == serialize($exoticArray)){
														// unserialise the existing dividend
														$previousDivArray = unserialize($raceEvent->exacta_dividend);
														// add the new dividends
														$raceEvent->exacta_dividend  = serialize(array_merge($previousDivArray,$exoticArray));
													}
												}else{
													$raceEvent->exacta_dividend = serialize($exoticArray);
												}
												break;
											case "T": // Trifecta
												if(!$raceEvent->trifecta_dividend == NULL){
													if(!$raceEvent->trifecta_dividend  == serialize($exoticArray)){
														// unserialise the existing dividend
														$previousDivArray = unserialize($raceEvent->trifecta_dividend);
														// add the new dividends
														$raceEvent->trifecta_dividend  = serialize(array_merge($previousDivArray,$exoticArray));
													}
												}else{
													$raceEvent->trifecta_dividend = serialize($exoticArray);
												}
												break;
											case "F": // First Four
												if(!$raceEvent->firstfour_dividend == NULL){
													if(!$raceEvent->firstfour_dividend  == serialize($exoticArray)){
														// unserialise the existing dividend
														$previousDivArray = unserialize($raceEvent->firstfour_dividend);
														// add the new dividends
														$raceEvent->firstfour_dividend  = serialize(array_merge($previousDivArray,$exoticArray));
													}
												}else{
													$raceEvent->firstfour_dividend = serialize($exoticArray);
												}
													
												break;
											default:
												TopBetta\LogHelper::l("BackAPI: Racing - Processing Result. No valid betType found:$betType. Can't process", 2);
										}
										// save the exotic dividend
										$raceEvent->save();
									}else{
										TopBetta\LogHelper::l("BackAPI: Racing - Processing Result. Exotic result not processed for:$priceType.", 2);
									}
								}
							}else { // not all required data available
								TopBetta\LogHelper::l("BackAPI: Racing - Processing Result. Missing Results data. Can't process", 2);
							}
						}
						break; 
							
					// Price Data
					case "PriceList":
						TopBetta\LogHelper::l("BackAPI: Racing - Processing $objectCount: Odds");
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
								
								TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds. MID: $meetingId, Race: $raceNo, BT: $betType, PT: $priceType, PA: $poolAmount",1);
																
								// TODO: Check JSON data is valid
				
								// TODO: Cater properly for other odds type's 
								if ($priceType == "TOP" || $priceType == "MID"){
									// check if race exists in DB
									$raceExists = TopBetta\RaceEvent::eventExists($meetingId, $raceNo);
									
									// grab the race type code
									$raceTypeCode = Topbetta\RaceMeeting::where('external_event_group_id', '=', $meetingId)->pluck('type_code');
									
									// if race exists update that record
									if($raceExists){
										// check for odds
										if(is_array($oddsArray)){
											//loop on odds array
											$runnerCount = 1;
											
											foreach($oddsArray as $runnerOdds){
												TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds for Runner: $runnerCount", 1);
												// echo "RC: $runnerCount, ";
												// get selectionID for runner
												
												// check if selection exists in the DB
												$selectionExists = TopBetta\RaceSelection::selectionExists($meetingId, $raceNo, $runnerCount);
											
												if ($selectionExists){
													TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds. In DB", 1);
													$priceExists = \DB::table('tbdb_selection_price')->where('selection_id', $selectionExists)->pluck('id');
												
													// if result exists update that record otherwise create a new one
													if($priceExists){
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds, In DB: $priceExists", 1);
														//echo "Price in DB, ODDS:$runnerOdds, ";
														$runnerPrice = TopBetta\RaceSelectionPrice::find($priceExists);
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds, Added to DB: $priceExists", 1);
														$runnerPrice = new TopBetta\RaceSelectionPrice;
														$runnerPrice->selection_id = $selectionExists;
													}
													$oddsSet = 0;
													// update the correct field
													
													if($priceType == "TOP" && $raceTypeCode == "R" ){
														
														switch($betType){
															case "W":
																$runnerPrice->win_odds = $runnerOdds / 100;
																$oddsSet = 1;
																//echo "Win odds set: $runnerOdds, ";
																break;
															case "P":
																$runnerPrice->place_odds = $runnerOdds / 100;
																$oddsSet = 1;
																//echo "Place odds set: $runnerOdds, ";
																break;
															default:
																TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds. 'Bet Type' not valid: $betType. Can't process", 2);
														}
														
													}elseif($priceType == "MID" && $raceTypeCode != "R" ){
														switch($betType){
															case "W":
																$runnerPrice->win_odds = $runnerOdds / 100;
																$oddsSet = 1;
																//echo "Win odds set: $runnerOdds, ";
																break;
															case "P":
																$runnerPrice->place_odds = $runnerOdds / 100;
																$oddsSet = 1;
																//echo "Place odds set: $runnerOdds, ";
																break;
															default:
																TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds. 'Bet Type' not valid: $betType. Can't process", 2);
															}
														}
										
													// save/update the price record
													if ($oddsSet){
														$runnerPrice->save();
														TopBetta\LogHelper::l("BackAPI: Racing - Processed Odds. MID:$meetingId, RaceNo:$raceNo, BT:$betType, PT:$priceType, PA:$poolAmount, ODDS:$runnerOdds");
													}else{
														TopBetta\LogHelper::l("BackAPI: Racing - Not processed. MID:$meetingId, RaceNo:$raceNo, BT:$betType, PT:$priceType, PA:$poolAmount, ODDS:$runnerOdds ", 2);
													}
												}else{
													TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds. No selction for Odds in DB. Can't process", 2);
												}
												$runnerCount++;
											}
											} else {
												TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds. No odds array found. Can't process", 2);
											}
										}else{
											TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds. No race found. Can't store results", 2);
										}
								}else{
									TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds: Price Type not TOP or MID:$priceType.", 2);
								}
							}else{
								TopBetta\LogHelper::l("BackAPI: Racing - Processing Odds. Missing Odds Data. Can't process", 2);
							}
							//echo "\n\n";
						}
	
						break;
						
						
					// Outcome Data
					case "OutcomeList":
						TopBetta\LogHelper::l("BackAPI: Racing - Processing $objectCount: Outcome");
							//foreach ($racingArray as $dataArray){
							//	echo"Outcome Object: ";
							//	print_r($dataArray);
							//	echo "\n";
							//}
			
						break;
					
					default :
						TopBetta\LogHelper::l("BackAPI: Racing - Processing $objectCount: $key", 2);
						return \Response::json(array(
							'error' => true,
							'message' => 'Error: Data format not recognised: '. $key),
							400
						);
				}
			}else{
				TopBetta\LogHelper::l("BackAPI: Racing - Processing $objectCount: $key. No Data. Can't Process", 2);
				/* return Response::json(array(
						'error' => true,
						'message' => 'Error: No Data found'),
						400
				); */
			}
			$objectCount++;
		}
		
		return \Response::json(array(
				'error' => false,
				'message' => 'OK: Processed Successfully'),
				200
		);
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

	
	private function canProductBeProcessed($priceType,$product_types)
	{
		return in_array($priceType, $product_types);
	}
	
	
}