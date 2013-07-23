<?php namespace TopBetta\backend;

use TopBetta;

class SportsController extends \BaseController {

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
		TopBetta\LogHelper::l("BackAPI: Sports - Reciving POST");

		// get the JSON POST
		$sportsJSON = \Input::json();
		//$sportsJSON = unserialize(file_get_contents('/tmp/backAPIracing-20130524203203'));
		$jsonSerialized = serialize($sportsJSON);

		if($this->debug){
			$timeStamp = date("YmdHis");
			\File::append('/tmp/backAPIsports-'.$timeStamp, $jsonSerialized);
		}

		// make sure JSON was received
		$keyCount = count($sportsJSON);
		if(!$keyCount){
			TopBetta\LogHelper::l("BackAPI: Sports - No Data In POST",2);
			return \Response::json(array(
					'error' => true,
					'message' => 'Error: No JSON data received'),
					400
			);
		}

		//TODO: // validate the json. Create some rules and check the json validates

		// JSON objects EventList/MarketList/SelectionList
		TopBetta\LogHelper::l("BackAPI: Sports - Processing '$keyCount' Objects");
		$objectCount=1;
		// loop on objects in data
		foreach($sportsJSON as $key => $sportsArray){
				
			// Make sure we have some data to process in the array
			if(is_array($sportsArray)){

				// process the sports data
				switch($key){
						
					// Sport/Comp/Event data
					case "GameList":
						TopBetta\LogHelper::l("BackAPI: Sports - Processing Event, Object:$objectCount");
						// Loop on each EventList JSOn object 
						foreach ($sportsArray as $dataArray){

							// Check minimum required data is available (EventID is unique key)
							if(isset($dataArray['GameId']) && isset($dataArray['Sport'])){
								$eventId = $dataArray['GameId'];

								// Process Sport
								if(isset($dataArray['Sport'])){
									$sportName = $dataArray['Sport'];
									// Check if Sport exists in DB
									$sportExists = TopBetta\SportsSportName::sportExists($sportName);
									// if sport exists update that record
									if($sportExists){
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Sport, In DB: $sportExists", 1);
									
									}else{
										$sportModel = new TopBetta\SportsSportName;
										$sportModel->name = $sportName;
										$sportModel->save();
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Sport, Added to DB: $sportModel->id", 1);
										$sportExists = $sportModel->id;
									}
								}
								
								/*
								 * Add/Update League/Competition
								*/
								
								if(isset($dataArray['League'])){
									$competition = $dataArray['League'];
									// Check if comp/league exists in DB
									$compExists = TopBetta\SportsComps::compExists($competition);
									// if comp/league exists update that record
									if($compExists){
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Competition:$competition, Already In DB: $compExists", 1);
										$compModel = TopBetta\SportsComps::find($compExists);
									}else{
										$compModel = new TopBetta\SportsComps;
										$compModel->name = $competition;
										$compModel->external_event_group_id = $eventId;
										$compModel->sport_id = $sportExists;
										TopBetta\LogHelper::l("BackAPI: Sports - Processed Competition:$competition, Added to DB: $compModel->id", 1);
									}
									
									$compModel->start_date = date_format(date_create($dataArray['EventTime']), 'y/m/d');
									$compModel->close_time = $dataArray['EventTime'];
									$compModel->save();
									
									/*
									 * Add/Update tournament competition record
									*/
									
									// Check if the record exists already
									$tournamentCompetitonExists = TopBetta\TournamentCompetition::tournamentCompetitionExists($competition, $sportExists);
									
									if($tournamentCompetitonExists){
										$tournamentCompetitionModel = TopBetta\TournamentCompetition::find($tournamentCompetitonExists);
									}else{	
										// add the new record
										$tournamentCompetitionModel = new TopBetta\TournamentCompetition;
										$tournamentCompetitionModel->tournament_sport_id = $sportExists;
										$tournamentCompetitionModel->name = $competition;
										
									}
									// save the tournament competition record
									$tournamentCompetitionModel->status_flag = 1;
									$tournamentCompetitionModel->save();
									
									// add the tournament competition ID to the event group table....
									$compModel = TopBetta\SportsComps::find($compModel->id);
									$compModel->tournament_competition_id = $tournamentCompetitionModel->id;
									TopBetta\LogHelper::l("BackAPI: Sports - Processing Competition. TC ID: $compModel->tournament_competition_id", 1);
									$compModel->save();
									$compExists =  $compModel->id;
								}

								/*
								 * Add/Update Event record
								*/
								
								$eventExists = TopBetta\SportsMatches::eventExists($eventId);
								// if event exists update that record
								if($eventExists){
									TopBetta\LogHelper::l("BackAPI: Sports - Processing Event, In DB: $eventExists", 1);
									$eventModelSports = TopBetta\SportsMatches::find($eventExists);
								// if not create a new record
								}else{
									$eventModelSports = new TopBetta\SportsMatches;
									$eventModelSports->external_event_id = $eventId;
								 }

								if(isset($dataArray['EventTime'])){
									$eventModelSports->start_date = $dataArray['EventTime'];
								}
								if(isset($dataArray['EventName'])){
									$eventModelSports->name = $dataArray['EventName'];
								}
								
								// save or update the record
								$eventSave = $eventModelSports->save();
								$EventDBID = $eventModelSports->id;
								TopBetta\LogHelper::l("BackAPI: Sports - Processing Event, Added to DB: $EventDBID", 1);
								TopBetta\LogHelper::l("BackAPI: Sports - Processed Event. Event:$eventId, Date:$eventModelSports->start_date, Name:$eventModelSports->name");

								/*
								 * Add/Update the close time on the Leage/Competition record to be the last events start time
								*/
								
								// update competiton with new event start time if it's after the current stored time
								if ($dataArray['EventTime'] > $compModel->close_time){
									$compModel->close_time = $dataArray['EventTime'];
									$compModel->save();
								}
								
								// grab the date from the event start times
								$newShortDate = date_format(date_create($dataArray['EventTime']), 'y/m/d');
								$oldShortDate = date_format(date_create($compModel->close_time), 't/m/d');
								
								// update competiton with new event start time if it's after the current stored time
								if ($oldShortDate > $newShortDate){
									$compModel->start_date = $newShortDate;
									$compModel->save();
								}
								
								// Add the event_group_event pivot table record to link the competition the the event
								$eventGEExists = TopBetta\SportEventGroupEvent::eventGEExists($eventModelSports->id, $compExists);
								// if event exists update that record
								if($eventGEExists){
									TopBetta\LogHelper::l("BackAPI: Sports - Processing EGE, In DB: $eventGEExists", 1);
									// $eventGEModel = TopBetta\SportEventGroupEvent::find($eventGEExists);
									// if not create a new record
								}else{
									$eventGEModel = new TopBetta\SportEventGroupEvent;
									$eventGEModel->event_id = $eventModelSports->id;
									$eventGEModel->event_group_id = $compExists;
									// save the EGE record
									$eventGEModel->save();
								}
								
							}else{
								TopBetta\LogHelper::l("BackAPI: Sports - Processing Event. No Event ID or Sport, Can't process", 1);
							}
						}
						break;

					// Market data
					case "MarketList":
						TopBetta\LogHelper::l("BackAPI: Sports - Processing $objectCount: Market");
						foreach ($sportsArray as $dataArray){
								
							// only process if required keys eventId and marketId are in the JSON object
							if(isset($dataArray['GameId']) && $dataArray['MarketId']){
								$eventId = $dataArray['GameId'];
								$marketId = $dataArray['MarketId'];
																
								TopBetta\LogHelper::l("BackAPI: Sports - Processing Market: EventID:$eventId, MarketID:$marketId.");

								// make sure the event this market is in exists 1st
								$eventExists = TopBetta\SportsMatches::eventExists($eventId);

								// if event exists update continue processing market
								if($eventExists){
									// add or update the market type
									if(isset($dataArray['BetTypeName']) && isset($dataArray['BetType'])){
										$externalMarketTypeID = $dataArray['BetType'];
										$betTypeName = $dataArray['BetTypeName'];
										// check if market type exists
										$marketTypeExists = TopBetta\SportsMarketType::marketTypeExists($betTypeName);
	
										// if market type exists update that record
										if($marketTypeExists){
											TopBetta\LogHelper::l("BackAPI: Sports - Processing Market Type. BetTypeName: $betTypeName,  BetTypeID:$externalMarketTypeID, In DB: $marketTypeExists", 1);
											$marketTypeModel = TopBetta\SportsMarketType::find($marketTypeExists);
										}else{ // if not create a new one
											TopBetta\LogHelper::l("BackAPI: Sports - Processing Market Type, BetTypeName: $betTypeName,  BetTypeID:$externalMarketTypeID, Adding to DB: $marketTypeExists", 1);
											$marketTypeModel = new TopBetta\SportsMarketType;
											$marketTypeModel->name = $betTypeName;
											$marketTypeModel->description = "UPDATE ME";
											
										}

										// update the status flag
										$marketTypeModel->status_flag = "1";
										
										// update the bet_type_id
										$marketTypeModel->external_bet_type_id = $externalMarketTypeID;
										
										// save or update the record
										$marketTypeSave = $marketTypeModel->save();
									}else{
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Market. MarketID:$marketId, EventID:$eventId, BetType and Name not in JSON. Can't process", 2);
									}
									
									// check if market record already exists
									$marketExists = TopBetta\SportsMarket::marketExists($marketId, $eventExists);
									TopBetta\LogHelper::l("BackAPI: Sports - Processing Market. DB ID: $marketExists", 1);
									// if market exists update that record
									if($marketExists){ 
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Market, In DB: $marketExists", 1);
										$marketModel = TopBetta\SportsMarket::find($marketExists);
										$marketModel->external_event_id = $eventId;
									}else{ // if not create a new one
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Market, Adding to DB: $marketExists", 1);
										$marketModel = new TopBetta\SportsMarket;
										$marketModel->market_type_id = $marketTypeModel->id;
										$marketModel->external_market_id = $marketId;
										$marketModel->external_event_id = $eventId;
									}
									
									//$marketModel->refund_flag = something;
									// save the market record
									$marketModel->event_id = $eventExists;
									$marketModelSave = $marketModel->save();
									$marketModelId = $marketModel->id;

									TopBetta\LogHelper::l("BackAPI: Sports - Processed Market. EventID:$eventId, MarketID:$marketId.");
									
									
									// TODO: update the results for the home and away teams 
									// - can this work in motor racing etc etc
									// - do we currently store the results?
									// - check if the selection_results record exists
								}else{
									TopBetta\LogHelper::l("BackAPI: Sports - Processing Market. Event for Market does not exist. Can't process, EventID:$eventId, MarketID:$marketId.", 2);
								}
							}else{
								$o = print_r($dataArray, true);
								TopBetta\LogHelper::l("BackAPI: Sports - Processing Market. EventId or MarketId not set. Can't process, Object:$o.", 2);
							}
						}
						break;

					// Selection/Team Data - The teams in the event
					case "SelectionList": // key is eventid/marketid/selection
						TopBetta\LogHelper::l("BackAPI: Sports - Processing $objectCount: Selection");
						foreach ($sportsArray as $dataArray){
							$raceExists = $selectionsExists = 0;
							// Check all required data is available in the JSON for the Selection
							if(isset($dataArray['GameId'])  &&  isset($dataArray['MarketId']) && isset($dataArray['SelectionNo'])){
								$eventId = $dataArray['GameId'];
								$marketId = $dataArray['MarketId'];
								$selectionId = $dataArray['SelectionNo'];
									
								// check if market record for this event already exists
								$marketExists = TopBetta\SportsMarket::sportMarketExists($marketId, $eventId);

								// if the market exists
								if($marketExists){
									// check if selection exists in the DB
									$selectionsExists = TopBetta\SportsSelection::sportSelectionExists($selectionId, $marketId, $eventId);

									// if selection exists update that record
									if($selectionsExists){
										TopBetta\LogHelper::l("BackAPI: Sports - MarketDBID: $marketExists, Processing Selection, In DB: $selectionsExists", 1);
										$selectionModel = TopBetta\SportsSelection::find($selectionsExists);
										$selectionModel->market_id = $marketExists;
									}else{
										TopBetta\LogHelper::l("BackAPI: Sports - MarketDBID: $marketExists, Processing Selection, Added to DB: $selectionsExists", 1);
										$selectionModel = new TopBetta\SportsSelection;
										$selectionModel->market_id = $marketExists;
										$selectionModel->external_selection_id = $selectionId;
										$selectionModel->external_event_id = $eventId;
										$selectionModel->external_market_id = $marketId;
									}
									if(isset($dataArray['Selection'])){
										$selectionModel->name = $dataArray['Selection'];
									}
									
									// add/update the selection record
									$selectionSave = $selectionModel->save();
																		
									// update selection odds if there included in the data
									if(isset($dataArray['Odds'])){
										// check if odds record exists
										$oddsExists = TopBetta\SportsSelectionPrice::selectionPriceExists($selectionModel->id);
										
										// if selection exists update that record
										if($oddsExists){
											TopBetta\LogHelper::l("BackAPI: Sports - Processing Selection Price, In DB: $oddsExists", 1);
											$selectionPriceModel = TopBetta\SportsSelectionPrice::find($oddsExists);
										}else{
											TopBetta\LogHelper::l("BackAPI: Sports - Processing Selection Price, Adding to DB: $oddsExists", 1);
											$selectionPriceModel = new TopBetta\SportsSelectionPrice;
											$selectionPriceModel->selection_id = $selectionModel->id;
											// TODO: $selectionPriceModel->bet_product_id = "Should we add an iGAS record"
											$selectionPriceModel->win_odds = $dataArray['Odds'] / 100;
										}
										// Add/update the selection odds/price record
										$selectionPriceModel->save();
										TopBetta\LogHelper::l("BackAPI: Sports - Processed Selection Price. EID:$eventId , MarketId:$marketId, SelectionId:$selectionId, Odds:".$dataArray['Odds']);
									}
									TopBetta\LogHelper::l("BackAPI: Sports - Processed Selection. EID:$eventId , MarketId:$marketId, SelectionId:$selectionId");
										
								}else {
									TopBetta\LogHelper::l("BackAPI: Sports - Processing Selection. No Market found for this selection. EID:$eventId, MarketID:$marketId, SelectionId:$selectionId Can't process", 2);
								}
							}else {
								TopBetta\LogHelper::l("BackAPI: Sports - Processing Selection. Missing Selection data. Can't process", 2);
							}
						}
						break;
					
					case "ResultList":
// 						$gameId = $dataArray['GameId'];
// 						$marketId = $dataArray['MarketId'];
// 						$marketStatus = $dataArray['MarketStatus'];
// 						$score = $dataArray['Score'];
// 						$scoreType = $dataArray['ScoreType'];
						
						
						$o = print_r($dataArray,true);
						
						TopBetta\LogHelper::l("BackAPI: Sports - Processing Result: ARRAY:$o.", 1);
						break;
						
					default :
						TopBetta\LogHelper::l("BackAPI: Sports - Processing $objectCount: $key", 2);
						return \Response::json(array(
								'error' => true,
								'message' => 'Error: Data format not recognised: '. $key),
								400
						);
				}
			}else{
				TopBetta\LogHelper::l("BackAPI: Sports - Processing $objectCount: $key. No Data. Can't Process", 2);
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

}