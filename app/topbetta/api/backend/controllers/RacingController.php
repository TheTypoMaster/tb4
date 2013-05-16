<?php
namespace TopBetta\backend;

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
		
	/**
	 * Debugging mode flag
	 *
	 * @var boolean
	 */
	private $debug = false;
	
	
	
	
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
		return "Racing 2nd index";
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
		$this->l("BackAPI: Racing - Reciving POST");
		
		// get the JSON POST
		$racingJSON = \Input::json();
	
		if($this->debug){
			$racingDump = print_r($racingJSON,true);
			$timeStamp = date("YmdHis");
			\File::append('/tmp/backAPIracing-'.$timeStamp, $racingDump);
		}
		
		
		// make sure JSON was received
		$keyCount = count($racingJSON);
		if(!$keyCount){
			return Response::json(array(
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
		
		$this->l("BackAPI: Racing - Processing '$keyCount' Objects");
		$objectCount=1;
		// loop on objects in data
		foreach($racingJSON as $key => $racingArray){
			
			// Make sure we have some data to process in the array
			if(is_array($racingArray)){
				
				// process the meeting/race/runner data
				switch($key){
					
					// Meeting Data - the meeting/venue
					case "MeetingList":
						$this->l("BackAPI: Racing - Processing Meeting, Object:$objectCount");
						foreach ($racingArray as $dataArray){

							// store data from array
							if(isset($dataArray['Id'])){
								$meetingId = $dataArray['Id'];
								
								// check if meeting exists in DB
								$meetingExists = TopBetta\RaceMeeting::meetingExists($meetingId);
								
								// if meeting exists update that record
								if($meetingExists){
									$this->l("BackAPI: Racing - Processing Meeting, In DB: $meetingExists", 1);
									$raceMeet = TopBetta\RaceMeeting::find($meetingExists);
								}else{
									$this->l("BackAPI: Racing - Processing Meeting, Added to DB: $meetingExists", 1);
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
								$this->l("BackAPI: Racing - Processing Meeting, Record Added/Updated");
								
							}else{
								$this->l("BackAPI: Racing - Processing Meeting, Meeting: No Meeting ID, can't process", 1);
							}								
						}
						break;
						
					// Race data - the races in the meeting
					case "RaceList":
						$this->l("BackAPI: Racing - Processing $objectCount: Race");
						foreach ($racingArray as $dataArray){
													
							if(isset($dataArray['MeetingId']) && $dataArray['RaceNo']){
								$meetingId = $dataArray['MeetingId'];
								$raceNo = $dataArray['RaceNo'];
							
								// make sure the meeting this race is in exists 1st
								$meetingExists = TopBetta\RaceMeeting::meetingExists($meetingId);
	
								// if meeting exists update that record then continue to add/update the race record
								if($meetingExists){
									//check if race exists
									$raceExists = TopBetta\RaceEvent::eventExists($meetingId, $raceNo);
	
									// if race exists update that record
									if($raceExists){
										$this->l("BackAPI: Racing - Processing Race, In DB: $raceExists", 1);
										$raceEvent = TopBetta\RaceEvent::find($raceExists);
									}else{
										$this->l("BackAPI: Racing - Processing Race, Added to DB: $raceExists", 1);
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
												$this->l("BackAPI: Racing - Processing Race. No valid race status found. Can't process", 2);
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
									
									$this->l("BackAPI: Racing - Processing Race, Record Added/Updated");
																	
									// Add the event_group_event record if adding race
									
									// TODO: maybe through eloquent check if the race already exists in DB also need to check what event_id field stores
									$egeExists = \DB::table('tbdb_event_group_event')->where('event_id', $raceEventID)->where('event_group_id', $meetingExists)->pluck('event_id');
									
									if(!$egeExists){
										$eventGroupEvent = new RaceEventGroupEvent;
										$eventGroupEvent->event_id = $raceEventID;
										$eventGroupEvent->event_group_id = $meetingExists;
										$eventGroupEvent->save();
										$this->l("BackAPI: Racing - Processing Race, EGE in DB",1);
										echo "EGE: Added event_group_event record\n\n";
										// Add event_group event record
									}else{
										$this->l("BackAPI: Racing - Processing Race, EGE in DB",1);
									}
								}else{
									$this->l("BackAPI: Racing - Processing Race. Meeting for race does not exist. Can't process", 2);
								}
							}else{
								$this->l("BackAPI: Racing - Processing Race. MeetingID or RaceNo not set. Can't process", 2);
							}
						}
						break;
											
					// Selection/Runner Data - The runners in the race
					case "RunnerList":
						$this->l("BackAPI: Racing - Processing $objectCount: Runner");
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
										$this->l("BackAPI: Racing - Processing Runner, In DB: $selectionsExists", 1);
										$raceRunner = TopBetta\RaceSelection::find($selectionsExists);
									}else{
										$this->l("BackAPI: Racing - Processing Runner, Added to DB: $selectionsExists", 1);
										$raceRunner = new TopBetta\RaceSelection;

										// get market ID
										$marketTypeID = TopBetta\RaceMarketType::where('name', '=', $marketName)->pluck('id');
										
										// check if market for event exists
										$marketID = TopBetta\RaceMarket::marketExists($raceExists, $marketTypeID);
										
										if(!$marketID){
											// add market record
											$runnerMarket = new RaceMarket;
											$runnerMarket->event_id = $raceExists;
											$runnerMarket->market_type_id = 110; //TODO: this needs to come from db
											$runnerMarket->save();
											$marketID = $runnerMarket->id;
											
											$this->l("BackAPI: Racing - Processing Runner. Add market record for event: $raceExists");
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
													
									$this->l("BackAPI: Racing - Processing Runner, Runner Added/Updated. MID:$meetingId , Race:$raceNo, Runner:$runnerNo");
																	
								}else {
									$this->l("BackAPI: Racing - Processing Runner. No race found for this runner. MID:$meetingId, Race:$raceNo, Runner:$runnerNo Can't process", 2);
								}
							}else {
								$this->l("BackAPI: Racing - Processing Race. Missing Runner data. Can't process", 2);
							}
						}
						break;
						
					// Result Data - the actual results of the race
					case "ResultList":
						$this->l("BackAPI: Racing - Processing $objectCount: Result");
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
									
								// check if selection exists in the DB
								$selectionsExists = TopBetta\RaceSelection::selectionExists($meetingId, $raceNo, $selection);
								
								if ($selectionsExists){
									$this->l("BackAPI: Racing - Processing Result. Selection Exixts for result",1);
									$resultExists = \DB::table('tbdb_selection_result')->where('selection_id', $selectionsExists)->pluck('id');
								
									// if result exists update that record
									if($resultExists){
										$this->l("BackAPI: Racing - Processing Result, In DB: $resultExists, MID:$meetingId, Race:$raceNo, SEL:$selection", 1);
										$raceResult = TopBetta\RaceResult::find($resultExists);
									}else{
										$this->l("BackAPI: Racing - Processing Result, Added to DB: $resultExists, MID:$meetingId, Race:$raceNo, SEL:$selection", 1);
										$raceResult = new TopBetta\RaceResult;
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
											$this->l("BackAPI: Racing - Processing Result. No valid betType found:$betType. Can't process", 2);
										}
										
									// save or update the record
									$raceResultSave = $raceResult->save();
									$raceResultID = $raceResult->id;
									
									$this->l("BackAPI: Racing - Processing Result, Record Added/Updated. ID:$raceResultID");
								}else{
									$this->l("BackAPI: Racing - Processing Result. No Selection found. Results not updated", 2);
								}
							}else { // not all required data available
								$this->l("BackAPI: Racing - Processing Result. Missing Results data. Can't process", 2);
							}
						}
						break; 
							
					// Price Data
					case "PriceList":
						$this->l("BackAPI: Racing - Processing $objectCount: Odds");
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
								
								
								$this->l("BackAPI: Racing - Processing Odds. MID: $meetingId, Race: $raceNo, BT: $betType, PT: $priceType, PA: $poolAmount",1);
																
								// TODO: Check JSON data is valid
				
								
								// check if race exists in DB
								$raceExists = TopBetta\RaceEvent::eventExists($meetingId, $raceNo);
								
								// if race exists update that record
								if($raceExists){
									// check for odds
									if(is_array($oddsArray)){
										//loop on odds array
										$runnerCount = 1;
										
										foreach($oddsArray as $runnerOdds){
											$this->l("BackAPI: Racing - Processing Odds for Runner: $runnerCount", 1);
											// echo "RC: $runnerCount, ";
											// get selectionID for runner
											
											// check if selection exists in the DB
											$selectionExists = TopBetta\RaceSelection::selectionExists($meetingId, $raceNo, $runnerCount);
										
											if ($selectionExists){
												$this->l("BackAPI: Racing - Processing Odds. In DB", 1);
												$priceExists = \DB::table('tbdb_selection_price')->where('selection_id', $selectionExists)->pluck('id');
											
												// if result exists update that record otherwise create a new one
												if($priceExists){
													$this->l("BackAPI: Racing - Processing Odds, In DB: $priceExists", 1);
													//echo "Price in DB, ODDS:$runnerOdds, ";
													$runnerPrice = TopBetta\RaceSelectionPrice::find($priceExists);
												}else{
													$this->l("BackAPI: Racing - Processing Odds, Added to DB: $priceExists", 1);
													$runnerPrice = new TopBetta\RaceSelectionPrice;
													$runnerPrice->selection_id = $selectionExists;
												}
												$oddsSet = 0;
												// update the correct field
												switch($betType){
													case "WIN":
														$runnerPrice->win_odds = $runnerOdds / 100;
														$oddsSet = 1;
														//echo "Win odds set: $runnerOdds, ";
														break;
													case "PLC":
														$runnerPrice->place_odds = $runnerOdds / 100;
														$oddsSet = 1;
														//echo "Place odds set: $runnerOdds, ";
														break;
													case "QIN":
														//echo "QIN odds, ";
														break;
													default:
														$this->l("BackAPI: Racing - Processing Odds. 'Bet Type' not valid: $betType. Can't process", 2);
												}
												// save/update the price record
												if ($oddsSet){
													$runnerPrice->save();
													$this->l("BackAPI: Racing - Processing Odds. Record Added/Updated");
												}else{
													$this->l("BackAPI: Racing - Processing Odds. WIN/PLC Odds not saved", 2);
												}
											}else{
												$this->l("BackAPI: Racing - Processing Odds. No selction for Odds in DB. Can't process", 2);
											}
											$runnerCount++;
										}
										} else {
											$this->l("BackAPI: Racing - Processing Odds. No odds array found. Can't process", 2);
										}
									}else{
										$this->l("BackAPI: Racing - Processing Odds. No race found. Can't store results", 2);
									}
							}else{
								$this->l("BackAPI: Racing - Processing Odds. Missing Odds Data. Can't process", 2);
							}
							//echo "\n\n";
						}
	
						break;
						
						
					// Outcome Data
					case "OutcomeList":
						$this->l("BackAPI: Racing - Processing $objectCount: Outcome");
							//foreach ($racingArray as $dataArray){
							//	echo"Outcome Object: ";
							//	print_r($dataArray);
							//	echo "\n";
							//}
			
						break;
					
					default :
						$this->l("BackAPI: Racing - Processing $objectCount: $key", 2);
						return \Response::json(array(
							'error' => true,
							'message' => 'Error: Data format not recognised: '. $key),
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
			$objectCount++;
		}
		
		return \Response::json(array(
				'error' => false,
				'message' => 'OK: Processed Sucessfully'),
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

	/**
	 * Log a message to laravel logs
	 *
	 * @param string 	$message
	 * @param integer 	$type
	 * @param string 	$time_format
	 * @param boolean 	$add_new_line
	 */
	public function l($message, $type = null, $show_time = true, $time_format = null, $add_new_line = true) {
		if(is_null($type)) {
			$type = self::LOG_TYPE_NORMAL;
		}
	
		if($type == self::LOG_TYPE_DEBUG && $this->debug == FALSE){
			return 0;
		}
		
		if(self::LOG_TIME_SHOWN){
			$time = $this->_formatLogTime($time_format);
		}else{
			$time = '';
		}
		
		
		//$processPID = getmypid();
	
		$prefix = array(
				self::LOG_TYPE_NORMAL => 'Info: ',
				self::LOG_TYPE_DEBUG =>  'Debug: ',
				self::LOG_TYPE_ERROR =>  'Error: '
		);
	
		$suffix = ($add_new_line) ? "\n" : '';
	
		\Log::info(sprintf('%s %s%s %s', $time, $prefix[$type], $message, $suffix));
		//echo sprintf('%s %s%s %s', $time, $prefix[$type], $message, $suffix);
	}
	
	/**
	 * Format the timestamp for a log message
	 *
	 * @param string $format
	 */
	private function _formatLogTime($format = null) {
		if(is_null($format)) {
			$format = self::LOG_TIME_FORMAT_DEFAULT;
		}
	
		return '[' . date($format) . ']';
	}
	
}