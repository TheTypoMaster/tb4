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
							if(isset($dataArray['GameId'])){
								$eventId = $dataArray['GameId'];

								// Process Sport
								if(isset($dataArray['Sport'])){
									$sportName = $dataArray['Sport'];
									// Check if Sport exists in DB
									$sportExists = TopBetta\SportsSportName::sportExists($sportName);
									// if sport exists update that record
									if($sportExists){
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Sport, In DB: $sportExists", 1);
										//$sportModel = TopBetta\SportsSportName::find($sportExists);
										// if not add it and grab id
									}else{
										$sportModel = new TopBetta\SportsSportName;
										$sportModel->sportName = $sportName;
										$sportModel->save();
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Sport, Added to DB: $sportModel->id", 1);
									}
								}
								
								// Process League/Competition
								if(isset($dataArray['League'])){
									$competition = $dataArray['League'];
									// Check if comp/league exists in DB
									$compExists = TopBetta\SportsComps::compExists($competition);
									// if comp/league exists update that record
									if($compExists){
										TopBetta\LogHelper::l("BackAPI: Sports - Processing League, In DB: $compExists", 1);
										//$compModel = TopBetta\SportsComps::find($compExists);
										// if not create a new record
									}else{
										$compModel = new TopBetta\SportsComps;
										$compModel->name = $competition;
										$compModel->save();
										TopBetta\LogHelper::l("BackAPI: Sports - Processing League, Added to DB: $compModel->id", 1);
										$compExists =  $compModel->id;
									}
								}
									
								// Process Event
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

								// Add the event_group_event pivot table record to link the competition the the event
								$eventGEExists = TopBetta\SportEventGroupEvent::eventGEExists($eventModelSports->id, $compExists);
								// if event exists update that record
								if($eventGEExists){
									TopBetta\LogHelper::l("BackAPI: Sports - Processing EGE, In DB: $eventGEExists", 1);
									$eventGEModel = TopBetta\SportEventGroupEvent::find($eventGEExists);
									// if not create a new record
								}else{
									$eventGEModel = new TopBetta\SportEventGroupEvent;
									$eventGEModel->event_id = $eventModelSports->id;
									$eventGEModel->event_group_id = $compExists;
									// save the EGE record
									$eventGEModel->save();
								}
								
							}else{
								TopBetta\LogHelper::l("BackAPI: Sports - Processing Event. No Event ID, Can't process", 1);
							}
						}
						break;

					// Market data
					case "MarketList":
						TopBetta\LogHelper::l("BackAPI: Sports - Processing $objectCount: Market");
						foreach ($sportsArray as $dataArray){
								
							// only process if required keys eventId and marketId are in the JSON object
							if(isset($dataArray['EventId']) && $dataArray['MarketId']){
								$eventId = $dataArray['EventId'];
								$marketId = $dataArray['MarketId'];
								$betType = $dataArray['BetType'];

								// make sure the event this market is in exists 1st
								$eventExists = TopBetta\SportsMatches::eventExists($eventId);

								// if event exists update continue processing market
								if($eventExists){
										
									// check if market type exists
									$marketTypeExists = TopBetta\SportsMarketType::marketTypeExists($betType);

									// if market type exists update that record
									if($marketTypeExists){
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Market Type, In DB: $marketTypeExists", 1);
										$marketTypeModel = TopBetta\SportsMarketType::find($marketTypeExists);
									}else{ // if not create a new one
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Market Type, Adding to DB: $marketTypeExists", 1);
										$marketTypeModel = new TopBetta\SportsMarketType;
										$marketTypeModel->name = $betType;
										$marketTypeModel->description = "UPDATE ME";
									}

									// save or update the record
									$marketTypeSave = $marketTypeModel->save();
										
									// check if market record already exists
									$marketExists = TopBetta\SportsMarket::marketExists($marketId);
									TopBetta\LogHelper::l("BackAPI: Sports - Processing Market. DB ID: $marketExists", 1);
									// if market exists update that record
									if($marketExists){ 
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Market, In DB: $marketExists", 1);
										$marketModel = TopBetta\SportsMarket::find($marketExists);
									}else{ // if not create a new one
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Market, Adding to DB: $marketExists", 1);
										$marketModel = new TopBetta\SportsMarket;
										$marketModel->market_type_id = $marketTypeModel->id;
										$marketModel->event_id = $eventExists;
										$marketModel->external_market_id = $marketId;
									}
									//$marketModel->refund_flag = something;
									// save the market record
									$marketModelSave = $marketModel->save();
									$marketModelId = $marketModel->id;

									TopBetta\LogHelper::l("BackAPI: Sports - Processed Market. EventID:$eventId, MarketID:$marketId, BetType: $betType");
									
									
									// TODO: update the results for the home and away teams 
									// - can this work in motor racing etc etc
									// - do we currently store the results?
									// - check if the selection_results record exists
								}else{
									TopBetta\LogHelper::l("BackAPI: Sports - Processing Market. Event for Market does not exist. Can't process", 2);
								}
							}else{
								TopBetta\LogHelper::l("BackAPI: Sports - Processing Market. EventId or MarketId not set. Can't process", 2);
							}
						}
						break;

					// Selection/Team Data - The teams in the event
					case "SelectionList":
						TopBetta\LogHelper::l("BackAPI: Sports - Processing $objectCount: Selection");
						foreach ($sportsArray as $dataArray){
							$raceExists = $selectionsExists = 0;
							// Check all required data is available in the JSON for the Selection
							if(isset($dataArray['EventId'])  &&  isset($dataArray['MarketId']) && isset($dataArray['SelectionId'])){
								$eventId = $dataArray['EventId'];
								$marketId = $dataArray['MarketId'];
								$selectionId = $dataArray['SelectionId'];
									
								// check if market record for this event already exists
								$marketExists = TopBetta\SportsMarket::marketExists($marketId);

								// if the market exists
								if($marketExists){
										
									// check if selection exists in the DB
									$selectionsExists = TopBetta\SportsSelection::selectionExists($selectionId);

									// if selection exists update that record
									if($selectionsExists){
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Selection, In DB: $selectionsExists", 1);
										$selectionModel = TopBetta\SportsSelection::find($selectionsExists);
									}else{
										TopBetta\LogHelper::l("BackAPI: Sports - Processing Selection, Added to DB: $selectionsExists", 1);
										$selectionModel = new TopBetta\SportsSelection;
										$selectionModel->market_id = $marketExists;
										$selectionModel->external_selection_id = $selectionId;
									
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
											$selectionPriceModel->win_odds = $dataArray['Odds'];
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