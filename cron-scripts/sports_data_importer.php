<?php
require_once '../common/shell-bootstrap.php';

class SportsBMProcessor extends TopBettaCLI
{
	/**
	 * Lock file path/name
	 *
	 * @var string
	 */
	const LOCK_FILE = '/tmp/sports_data_importer.lck';
	/**
	 * Log file path
	 * @var string
	 */
	
	// const LOG_PATH = '/usr/local/topbetta/logs/topbetta-crons/';
		
	final public function initialise(){
		// $this->account_balance		=& JModel::getInstance('AccountTransaction', 'PaymentModel');
		//$this->db =& $this->getDBO();
	}
	/**
	 * Main script method
	 */
	public function execute()
		{
			$display_message = true;
			
			if(!$this->arg('debug')){
				while($this->_checkForRunningInstance(basename(__FILE__))){
					if($display_message){
						$this->l('Sports Importer instance already running. WAIT');
						$display_message = false;
					}
					time_nanosleep(0, 500000000);
				}
			}
			
			if($this->arg('debug')){
				$this->_debugArgument($this->arg('debug'));
			}
			
			else{
			    touch(self::LOCK_FILE);
				$this->getSportsData();
			}
		}
	
	final private function getSportsData(){
		
		// Set debug flags (0 for none, 1 for queries)
		$debug = 1;
		
		// Set the database access information as constants.
		$dbconfig = $this->getConfigSection('database');
		
		//if("dbtb01"==(string)$dbconfig->database->attributes()->name){
		DEFINE ('DB_USER', 'root');
		DEFINE ('DB_PASSWORD', 't0pb3tt@mysqlp@ss');
		DEFINE ('DB_HOST', 'localhost');
		DEFINE ('DB_NAME', 'topbetta_igas');
		
		$sportcount = '0';
		$eventcount = '0';
		$debug = "off";
		$dbPrefix = "tbdb_";

		$sportTable = $dbPrefix."tournament_sport";
		$eventGroupTable = $dbPrefix."event_group";
		$eventTable = $dbPrefix."event";
		$selectionTable = $dbPrefix."selection";
		$selectionPriceTable = $dbPrefix."selection_price";
		$marketTable = $dbPrefix."market";
		$marketTypeTable = $dbPrefix."market_type";
		$eventGroupEventTable = $dbPrefix."event_group_event";
			
		$time_start = time();
		
		// Make the connnection and then select the database.
		$dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) OR Die ('Could not connect to MySQL.');
		mysql_select_db (DB_NAME) OR Die ('Could not select the database.');
		
		// Bookmaker Sport Feed URLS
		$sportListFeedURL = "http://www.bookmaker.com.au/api/feed/availableSports";
		$sportEventFeedURL = "http://www.bookmaker.com.au/api/feed/sportList?sport=";
		$sportEventDetailsFeedURL = "http://www.bookmaker.com.au/api/feed/sportCompetitors?event_id=";
		$bulkSportsFeedURL = "http://www.bookmaker.com.au/api/feed/bulkSports?sport=";
		
		//$date = date("Y-m-d", strtotime("-1 day"));
		$date = date("Y-m-d", strtotime("today"));
		
		$this->l("Getting Sports list from BM API...");
		$sportListFeedJSON = json_decode(file_get_contents("$sportListFeedURL"),true);
		$this->l("Done");
		
		$daysAhead = "14";
		
		// get all the available sports
		foreach($sportListFeedJSON as $sportName) {
			// echo "Sport: ". $sportName ."\n";
			$sportcount = $sportcount + 1;
			// Check if sport exists
			$sportExist = "SELECT id from `".$dbPrefix."tournament_sport` where `name` = '$sportName' LIMIT 1";
			$sportExistResult = mysql_query($sportExist);
			$sportExistnum_rows = mysql_num_rows($sportExistResult);
		
			if ($sportExistnum_rows){
				$this->l("[$sportName] exists in DB");
				$row = mysql_fetch_array($sportExistResult);
				$sportID = $row['id'];
			}else {
				// add new sport
				$this->l("[$sportName] added to DB");
				$sportsQuery = " INSERT INTO `".$dbPrefix."tournament_sport` (`id`, `name`, `status_flag`) VALUES ";
				$sportsQuery .= " ('', '$sportName', '1'); ";
				mysql_query($sportsQuery);
				// get ID of sport
				$sportID = mysql_insert_id();
			}
			$this->l("[Sport $sportName] DB ID: $sportID");
		
		
			for($i=0; $i <= $daysAhead; $i++)
			{
				if($i == 0){
					$date = date("Y-m-d", strtotime("today"));
				}else{
					$date = date("Y-m-d", strtotime("+".$i." day"));
				}
				
				//$sportName = "Boxing";
				//$sportID = 5;
				
				$bulkSportsFeedURLFull = $bulkSportsFeedURL . $sportName. "&date=". $date;
				$this->l("Getting bulk sports data from BM API for $sportName...");
				$bulkListFeedJSON = json_decode(file_get_contents($bulkSportsFeedURLFull),true);
				$this->l("Done");
				
				//print_r($bulkListFeedJSON);
				foreach($bulkListFeedJSON as $bulkEvent){
					
					foreach($bulkEvent as $bulkMarket){
							
						// get main event data
						$competitionEventID = $bulkMarket['EventID'];
						$competitionDescription = mysql_escape_string($bulkMarket['Description']);
						$competitionWeather = $bulkMarket['Weather'];
						$compSuspendDate =  substr($bulkMarket['SuspendDateTime'], 0, -8);
						$outcomeDate =  substr($bulkMarket['OutcomeDateTime'], 0, -8);
						$competitionName = mysql_escape_string($bulkMarket['Competition']);
						$competitionName = str_replace ("  ", " ", $competitionName);
						$mainEvent = mysql_escape_string($bulkMarket['MainEvent']);
						
						
						// current date/time
						$nowTime = date("Y-m-d H:i:s");
						// competitor count
						$competitorCount = count($bulkMarket['Competitors']['Competitors']);
						
						// Skip data that's no good
						if ($competitorCount < 1){
							$this->l("[$sportName] [Event ID: $competitionEventID] [EG: $competitionName] No Competitors found");
						}elseif($competitionName == ''){
							$this->l("[$sportName] [Event ID: $competitionEventID] [EG: $competitionName] No Competition Name found");
						}elseif($compSuspendDate < $nowTime){
							$this->l("[$sportName] [Event ID: $competitionEventID] [EG: $competitionName] Market Closed");
						}else{
							$this->l("[$sportName] [Event ID: $competitionEventID] [EG: $competitionName] Competitors: $competitorCount");
					
							// Add event_group record if it does not exist
							$this->l("Adding event_group record");
							$eventGroupID = $this->addEventGroupRecord($eventGroupTable, $competitionName, $sportID, $compSuspendDate, $debug);
							$this->l("ID: $eventGroupID - Done");
							
							// Get market type
							$matchType = $bulkMarket['Competitors']['Type'];
							$marketEventID = $bulkMarket['Competitors']['EventID'];
								
							// Bet types NOT head to head, line, margin
							if ($matchType != 'Match'){
								
								$this->l("[$sportName] [EID: $competitionEventID] [EG: $competitionName] [BT: Competitor]");
								//print_r($bulkMarket);
								
								$marketEventID = $bulkMarket['EventID'];
								$marketWeather = $bulkMarket['Weather'];
								$marketSuspendDateTime = $bulkMarket['SuspendDateTime'];
								$marketOutcomeDateTime = $bulkMarket['OutcomeDateTime'];
								$suspendDate =  substr($marketSuspendDateTime, 0, -8);
								$outcomeDate =  substr($marketOutcomeDateTime, 0, -8);
								$marketCompetition = mysql_escape_string($bulkMarket['Competition']);
								$markeActiveCompetitorst = $bulkMarket['ActiveCompetitors'];
								$marketTotalCompetitors = $bulkMarket['TotalCompetitors'];
								$marketHasWinOdds = $bulkMarket['HasWinOdds'];
								$marketDescription = mysql_escape_string($bulkMarket['Description']);
								
								// remove event name from description if it is there
								
								// cater for vs and v mainEvent names
								$mainEventString = " - ".$mainEvent;
								$mainESV = str_replace (" v ", " vs ", $mainEventString);
																
								// $eventBetType = mysql_escape_string(substr($marketDescription, 0, strpos($marketDescription, $separator)));
								$eventBetType = str_replace($mainEventString, "", $marketDescription);
								$eventBetType = str_replace($mainESV, "", $eventBetType);
								//echo "EBT: $eventBetType\nMD : $marketDescription\nMES: $mainEventString\nMSV:$mainESV";
								
								
								
								// Add event record if not there yet
								$this->l("Adding event record");
								$eventID = $this->addEventRecord($eventTable, $mainEvent, $suspendDate, $marketEventID, $debug);
								$this->l("ID: $eventID - Done");
								
								// Update the event_group close time
								$updateEventGroupCloseDate = $this->updateEventCloseTime($eventGroupTable, $competitionName, $sportID, $compSuspendDate, $suspendDate, $debug);

								// Add event_group_event table (links event to event_group)
								$this->l("Adding event_group_event record");
								$this->addEventGroupEventRecord($eventGroupEventTable, $eventID, $eventGroupID, $debug);
								$this->l("Done");

								// Get/add the market_type table
								$this->l("Adding market_group record");
								$marketTypeID = $this->addMarketTypeRecord($marketTypeTable, $eventBetType, $debug);
								$this->l("ID: $marketTypeID - Done");
								
								// ADD the market table record that links the selection to the event and get market record ID
								$this->l("Adding market record");
								$marketID = $this->addMarketRecord($marketTable, $eventID, $marketTypeID, $debug);
								$this->l("ID: $marketID - Done");
								
								$this->l("Processing Comp:$marketCompetition, Event:$mainEvent, Bet Type:$eventBetType");
								
								// add the bet options (selection/selction_price table records)
								foreach($bulkMarket['Competitors']['Competitors'] as $marketCompetitor){
									$marketCompetitorTeam = mysql_escape_string($marketCompetitor['Team']);
									$marketCompetitorWinDividend = $marketCompetitor['Win'];
									$marketCompetitorCompetitorID = $marketCompetitor['CompetitorID'];
									$marketCompetitorEliminated = $marketCompetitor['Eliminated'];
									$marketCompetitorOrderOfEntry = $marketCompetitor['OrderOfEntry'];
									$marketCompetitorHasWinOdds = $marketCompetitor['HasWinOdds'];
									$marketCompetitorAllowBets = $marketCompetitor['AllowBets'];
									$marketCompetitorIsSuspended = $marketCompetitor['IsSuspended'];
									$marketCompetitorEventStatus = $marketCompetitor['EventStatus'];
									$marketCompetitorPosition = $marketCompetitor['Position'];
									
									// print_r($marketCompetitor);
									
									$bet_type_ref = "win";
									
									// Add data to _selection table
									$this->l("Adding selection record");
									$selectionID = $this->addSelectionRecord($selectionTable, $marketCompetitorCompetitorID, $marketID, $bet_type_ref, $marketEventID, $marketCompetitorTeam, $debug);
									$this->l("ID: $selectionID - Done");
												
									// Add data to selection_price table
									$this->l("Adding selection_price record");
									$selectionPriceID = $this->addSelectionPriceRecord($selectionPriceTable, $selectionID, $marketCompetitorWinDividend, $marketCompetitorAllowBets, $marketCompetitorIsSuspended, $selectionPriceID, $debug);
									$this->l("ID: $selectionPriceID - Done");
											
								}
								
								
							// head to head/line/margin's	
							} else{
								//print_r($bulkMarket);
								$suspendDate =  substr($bulkMarket['SuspendDateTime'], 0, -8);
								$outcomeDate =  substr($bulkMarket['OutcomeDateTime'], 0, -8);
								$marketSport = $bulkMarket['Competitors']['Sport'];
								$marketSport = str_replace ("'", "\'", $marketSport);
								$marketDescription = $bulkMarket['Competitors']['Description'];
								$marketDescription = str_replace ("'", "\'", $marketDescription);
								$marketType = $bulkMarket['Competitors']['Type'];
								$marketOutcomeDateTime = $bulkMarket['Competitors']['OutcomeDateTime'];
								$marketNumberPlacings = $bulkMarket['Competitors']['NumberPlacings'];
								$marketStatus = $bulkMarket['Competitors']['Status'];
								$marketTeamA = $bulkMarket['Competitors']['TeamA'];
								$marketTeamB = $bulkMarket['Competitors']['TeamB'];
								$marketComment = $bulkMarket['Competitors']['Comment'];
								$marketSuspendDateTime = $bulkMarket['Competitors']['SuspendDateTime'];
								$marketBetRule = $bulkMarket['Competitors']['BetRule'];
								$marketLocation = $bulkMarket['Competitors']['Location'];
								$marketDraw = $bulkMarket['Competitors']['Draw'];
								$marketTotalOver = $bulkMarket['Competitors']['TotalOver'];
								$marketTotalUnder = $bulkMarket['Competitors']['TotalUnder'];
								$marketOverDiv = $bulkMarket['Competitors']['OverDiv'];
								$marketUnderDiv = $bulkMarket['Competitors']['UnderDiv'];
								$marketMargin1Low = $bulkMarket['Competitors']['Margin1Low'];
								$marketMargin1High = $bulkMarket['Competitors']['Margin1High'];
								$marketMargin2 = $bulkMarket['Competitors']['Margin2'];
								$marketHasWinOdds = $bulkMarket['Competitors']['HasWinOdds'];
								$marketHasLineOdds = $bulkMarket['Competitors']['HasLineOdds'];
								$marketHasDrawOdds = $bulkMarket['Competitors']['HasDrawOdds'];
								$marketHasMargin1Odds = $bulkMarket['Competitors']['HasMargin1Odds'];
								$marketHasMargin2Odds = $bulkMarket['Competitors']['HasMargin2Odds'];
								$marketHasUnderOverOdds = $bulkMarket['Competitors']['HasUnderOverOdds'];
								$marketHasTotalPointsOdds = $bulkMarket['Competitors']['HasTotalPointsOdds'];
								$marketHasOdds = $bulkMarket['Competitors']['HasOdds'] ;
								$marketTotalOddsColumns = $bulkMarket['Competitors']['TotalOddsColumns'];
								$marketMarketCount = $bulkMarket['Competitors']['MarketCount'];
								$marketTeams = $marketTeamA . " vs " . $marketTeamB;
								$marketTeams = str_replace ("'", "\'", $marketTeams);
								$this->l("[$sportName] [Event ID: $competitionEventID] [EG: $competitionName] Status: $marketStatus, Team A: $marketTeamA v Team B: $marketTeamB, Type: $marketType");
								
								// Add event record if not there yet
								$this->l("Adding event record");
								$eventID = $this->addEventRecord($eventTable, $mainEvent, $suspendDate, $marketEventID, $debug);
								$this->l("ID: $eventID - Done");

								// Update the event_group close time
								$updateEventGroupCloseDate = $this->updateEventCloseTime($eventGroupTable, $competitionName, $sportID, $compSuspendDate, $suspendDate, $debug);
																
								// Add event_group_event table (links event to event_group)
								$this->l("Adding event_group_event record");
								$eventGroupEventID = $this->addEventGroupEventRecord($eventGroupEventTable, $eventID, $eventGroupID, $debug);
								$this->l("ID: $eventGroupEventID - Done");
								
								// the draw data exists twice but you only want to add it once
								$drawCount = 1;
								
								// Loop on event competitors
								foreach ($bulkMarket['Competitors']['Competitors'] as $competitor){
									// print_r($competitor);
									// store JSON data in variables
									$competitorName = $competitor['Name'];
									$competitorName = str_replace ("'", "\'", $competitorName);
									$competitorWin = $competitor['Win'];
									$competitorLine = $competitor['Line'];
									$competitorLineDividend = $competitor['LineDiv'];
									$competitorLineEventID = $competitor['LineEventID'];
									$competitorDraw = $competitor['Draw'];
									$competitorMargin1Div = $competitor['Margin1Div'];
									$competitorMargin2Div = $competitor['Margin2Div'];
									$competitorUnderOver = $competitor['UnderOver'];
									$competitorUnderOverHandicap = $competitor['UnderOverHandicap'];
									$competitorUnderOverString = $competitor['UnderOverString'];
									$competitorScoreNT = $competitor['ScoreNT'];
									$competitorScoreOT = $competitor['ScoreOT'];
									$competitorevelOT = $competitor['LevelOT'];
									$competitorAllowBets = $competitor['AllowBets'];
									$competitorIsSuspended = $competitor['IsSuspended'];
									$competitorWinID = $competitor['WinID'];
									$competitorDrawID = $competitor['DrawID'];
									$competitorPointsID = $competitor['PointsID'];
									$competitorLineID = $competitor['LineID'];
									$competitorMargin1ID = $competitor['Margin1ID'];
									$competitorMargin2ID = $competitor['Margin2ID'];
									$competitorUnderOverID = $competitor['UnderOverID'];
									$competitorPosition = $competitor['Position'];
									$competitorHasWinOdds = $competitor['HasWinOdds'];
									$competitorHasLineOdds = $competitor['HasLineOdds'];
									$competitorHasDrawOdds = $competitor['HasDrawOdds'];
									$competitorHasMargin1Odds = $competitor['HasMargin1Odds'];
									$competitorHasMargin2Odds = $competitor['HasMargin2Odds'];
									$competitorHasUnderOverOdds = $competitor['HasUnderOverOdds'];
									$competitorAllowBets = ($competitorAllowBets == "yes" ? "1" : "0");
									$competitorIsSuspended = ($competitorIsSuspended == "yes" ? "1" : "0");
									
									// Head to Head bet Type (awin, bwin)
									if ($competitorHasWinOdds){
										$this->l("[$sportName] [Event ID: $competitionEventID] [EG: $competitionName] Processing $competitorWinID: $competitorName: $competitorWin");
										
										// Force Win bets ATM - needs to be smartened up
										$betPlaceRef = $competitorWinID;
										
										$eventBetType = "Head To Head";
										// Get/add the market_type table
										$this->l("Adding market_type record");
										$marketTypeID = $this->addMarketTypeRecord($marketTypeTable, $eventBetType, $debug);
										$this->l("ID: $marketTypeID - Done");
										
										// ADD the _market table record that links the selection to the event and get market record ID
										$this->l("Adding market record");
										$marketID = $this->addMarketRecord($marketTable, $eventID, $marketTypeID, $debug);
										$this->l("ID: $marketID - Done");
										
										
										$bet_type_ref = "win";
										
										// Add data to _selection table
										$this->l("Adding selection record");
										$selectionID = $this->addSelectionRecord($selectionTable, $betPlaceRef, $marketID, $bet_type_ref, $marketEventID, $competitorName, $debug);
										$this->l("ID: $selectionID - Done");
										
										// Add data to selection_price table
										$this->l("Adding selection_price record");
										$selectionPriceID = $this->addSelectionPriceRecord($selectionPriceTable, $selectionID, $competitorWin, $competitorAllowBets, $competitorIsSuspended, $selectionPriceID, $debug);
										$this->l("ID: $selectionPriceID - Done");
																				
									} //end if haswinodds
									
									
									// DRAW BET OPTION ON HEAD TO HEAD
									if ($competitorHasDrawOdds && $drawCount == 1){
									$this->l("[$sportName] [Event ID: $competitionEventID] [EG: $competitionName] Processing $competitorDrawID: $marketTeams: $competitorDraw");
										
										// Force draw bets ATM - needs to be smartened up
										$betPlaceRef = $competitorDrawID;
										
										$eventBetType = "Head To Head";
										$marketTeams = " Draw";

										// Get/add the market_type table
										$this->l("Adding market_type record");
										$marketTypeID = $this->addMarketTypeRecord($marketTypeTable, $eventBetType, $debug);
										$this->l("ID: $marketTypeID - Done");
										
										// ADD the market table record that links the selection to the event and get market record ID
										$this->l("Adding market record");
										$marketID = $this->addMarketRecord($marketTable, $eventID, $marketTypeID, $debug);
										$this->l("ID: $marketID - Done");
										
										$bet_type_ref = "draw";
										
										// Add data to selection table
										$this->l("Adding selection record");
										$selectionID = $this->addSelectionRecord($selectionTable, $betPlaceRef, $marketID, $bet_type_ref, $marketEventID, $marketTeams, $debug);
										$this->l("ID: $selectionID - Done");

										
										// Add data to selection_price table
										$this->l("Adding selection_price record");
										$selectionPriceID = $this->addSelectionPriceRecord($selectionPriceTable, $selectionID, $competitorDraw, $competitorAllowBets, $competitorIsSuspended, $selectionPriceID, $debug);
										$this->l("ID: $selectionPriceID - Done");
																			
										$drawCount++;
									}
								}
							}
						}
					}
				}
			}
			//exit;
		}
		
	} // end function getSportsData
	
	/**
	 * Return the directory separator used by the current OS
	 *
	 * @return string
	 */
	final private function getDirectorySeparator() {
		$slash = '/';
	
		if(stristr(PHP_OS, 'WIN')) {
			$slash = '\\';
		}
	
		return $slash;
	}
	
	/**
	 * Get a SimpleXML object for the server config file
	 *
	 * @param string $path
	 * @return SimpleXML
	 */
	final private function getServerXML($path = null) {
		static $xml = null;
	
		if(is_null($xml)) {
			if(is_null($path)) {
				$sl = $this->getDirectorySeparator();
	
				$path   = ($sl == '\\') ? 'C:' : '';
				$path  .= $sl . 'mnt' . $sl . 'web' . $sl . 'server.xml';
			}
	
			$xml = simplexml_load_file($path);
		}
	
		return $xml;
	}
	
	/**
	 * Get a specific section of the server config file
	 *
	 * @param string $sectionName
	 * @return mixed
	 */
	final private function getConfigSection($sectionName) {
		static $sectionList = array();
	
		if(!isset($sectionList[$sectionName])) {
			$xml = $this->getServerXML();
			$section = $xml->xpath("/setting/section[@name='{$sectionName}']");
	
			if(empty($section)) {
				trigger_error("The section '{$sectionName}' was not found in the server config.", E_USER_ERROR);
			}
	
			if(count($section) > 1) {
				trigger_error("Server config contains multiple nodes for section '{$sectionName}'. Using first node.", E_USER_WARNING);
			}
	
			$sectionList[$sectionName] = $section[0];
		}
	
		return $sectionList[$sectionName];
	}
	
	
	final private function addEventGroupRecord($eventGroupTable, $competitionName, $sportID, $suspendDate, $debug){
		// set now time
		$nowTime = date("Y-m-d H:i:s");
		$debug = 1;
		
		$eventGroupExist = "SELECT id from `".$eventGroupTable."` where `name` = '$competitionName' AND `sport_id` = '$sportID' LIMIT 1";
		if ($debug == 1){
			$this->l("Check event_group table query: $eventGroupExist");
		}
		$eventGroupExistResult = mysql_query($eventGroupExist);
		$eventGroupExistnum_rows = mysql_num_rows($eventGroupExistResult);
		if ($eventGroupExistnum_rows){
			$this->l("Already in DB");
			$row = mysql_fetch_array($eventGroupExistResult);
			$eventGroupID = $row['id'];
		}else {
			// add new competition
			$eventGroupQuery = " INSERT INTO `".$eventGroupTable."` (`id`, `sport_id`, `name`, `display_flag`, `start_date`, `close_time`, `created_date` ) VALUES ";
			$eventGroupQuery .= " ('', '$sportID', '$competitionName', '1', '$suspendDate', '$suspendDate', '$nowTime' ); ";
			mysql_query($eventGroupQuery);
			$eventGroupID = mysql_insert_id();
			$this->l("Added to DB");
			if ($debug == 1){
				$this->l("ADD event_group table query: $eventGroupQuery");
			}
		}
		
		// Add tournament event group record
		$teventGroupExist = "SELECT id from `tbdb_tournament_competition` where `name` = '$competitionName' AND `tournament_sport_id` = '$sportID' LIMIT 1";
		if ($debug == 1){
			$this->l("Check tournament_competition table query: $teventGroupExist");
		}
		$teventGroupExistResult = mysql_query($teventGroupExist);
		$teventGroupExistnum_rows = mysql_num_rows($teventGroupExistResult);
		if ($teventGroupExistnum_rows){
			$this->l("Already in DB");
			$row = mysql_fetch_array($teventGroupExistResult);
			$teventGroupID = $row['id'];
		}else {
			// add new competition
			$teventGroupQuery = " INSERT INTO `tbdb_tournament_competition` (`id`, `tournament_sport_id`, `external_competition_id`, `name`, `status_flag`) VALUES ";
			$teventGroupQuery .= " ('', '$sportID', '', '$competitionName', '1'); ";
			mysql_query($teventGroupQuery);
			$teventGroupID = mysql_insert_id();
			$this->l("Added to DB");
			if ($debug == 1){
				$this->l("ADD tournament_competition table query: $teventGroupQuery");
			}
		}

		// add tournament competition ID
                $TCUpdateQuery = " UPDATE `".$eventGroupTable."` SET tournament_competition_id = '$teventGroupID'  where `name` = '$competitionName' AND `sport_id` = '$sportID'";
                mysql_query($TCUpdateQuery);
                if($debug == 1){
	                $this->l("TC update query: $TCUpdateQuery");
                }
		
		


		return $eventGroupID;
	}
	
	
	
	
	
	private function updateEventCloseTime($eventGroupTable, $competitionName, $sportID, $suspendDate, $closeDate, $debug){
		// set now time
		$nowTime = date("Y-m-d H:i:s");
		// $debug = 1;
	
		$eventGroupExist = "SELECT id, close_time from `".$eventGroupTable."` where `name` = '$competitionName' AND `sport_id` = '$sportID' LIMIT 1";
		if ($debug == 1){
			$this->l("Check event_group table query: $eventGroupExist");
		}
		$eventGroupExistResult = mysql_query($eventGroupExist);
		$eventGroupExistnum_rows = mysql_num_rows($eventGroupExistResult);
		if ($eventGroupExistnum_rows){
			
			$row = mysql_fetch_array($eventGroupExistResult);
			$eventGroupID = $row['id'];
			$currentCloseDate = $row['close_time'];
			if ($closeDate > $currentCloseDate) {
				$this->l("Close time updated: Old:$suspendDate New:$closeDate ");
				$closeDateUpdateQuery = " UPDATE `".$eventGroupTable."` SET close_time = '$closeDate' WHERE id = '$eventGroupID'";
				mysql_query($closeDateUpdateQuery);
				if($debug == 1){
					$this->l("EVENT GROUP close_time UPDATE QUERY: $closeDateUpdateQuery");
				}
				
			}
		}else {
			
			if ($debug == 1){
				$this->l("Cant find event_group record. Close time NOT updated");
			}
		}
		return $eventGroupID;
	}
	
	final private function addEventRecord($eventTable, $marketTeams, $suspendDate, $marketEventID, $debug){
		// set now time
		$nowTime = date("Y-m-d H:i:s");
		$debug = 1;
		// Check if event exists in database
		
		//substr($marketOutcomeDateTime, 0, -8);
		$justDate = substr($suspendDate, 0, -9);
		
		
		$eventExist = "SELECT id from `".$eventTable."` where `name` = '$marketTeams' AND `start_date` LIKE '$justDate%' LIMIT 1";
		$eventExistResult = mysql_query($eventExist);
		$eventExistnum_rows = mysql_num_rows($eventExistResult);
		if ($debug == 1){
			$this->l("Check event table query: $eventExist");
		}
		if ($eventExistnum_rows){
			$this->l("Already in DB");
			$row = mysql_fetch_array($eventExistResult);
			$eventID = $row['id'];
		}else {
			$eventQuery = " INSERT INTO `".$eventTable."` (`id`, `event_id`, `name`, `start_date`, `created_date`, `event_status_id`) VALUES ";
			$eventQuery .= " ('', '$marketEventID', '$marketTeams', '$suspendDate', '$nowTime', '1' ); ";
			mysql_query($eventQuery);
			$eventID = mysql_insert_id();
			$this->l("Added to DB");
			if ($debug == 1){
				$this->l("Add event table Query: $eventQuery");
			}
		}
		return $eventID;
	} 
	
	final private function addEventGroupEventRecord($eventGroupEventTable, $eventID, $eventGroupID, $debug){
		$eventGroupEventExist = "SELECT event_id from `".$eventGroupEventTable."` where `event_id` = '$eventID' AND `event_group_id` = '$eventGroupID' LIMIT 1";
		$eventGroupEventExistResult = mysql_query($eventGroupEventExist);
		$eventGroupEventExistnum_rows = mysql_num_rows($eventGroupEventExistResult);
		if($debug == 1){
			$this->l("Check event_group_event table query: $eventGroupEventExist");
		}
		if ($eventGroupEventExistnum_rows){
			$this->l("Already in DB");
			$row = mysql_fetch_array($eventGroupEventExistResult);
			$eventGroupEventID = $row['id'];
		}else {
			// add new event_group_event record and get id
			$eventQuery = " INSERT INTO `".$eventGroupEventTable."` (`event_group_id`, `event_id`) VALUES ";
			$eventQuery .= " ('$eventGroupID', '$eventID'); ";
			mysql_query($eventQuery);
			$eventGroupEventID = mysql_insert_id();
			$this->l("Added to DB");
			if ($debug == 1){
				$this->l("Add event table Query: $eventQuery");
			}
		}
		return $eventGroupEventID;
	}
	
	final private function addMarketTypeRecord($marketTypeTable, $eventBetType, $debug){
		
		$nowTime = date("Y-m-d H:i:s");
		//$debug = 1;
		$marketTypeQuery = "SELECT id from `".$marketTypeTable."` where `name` = '$eventBetType' LIMIT 1";
		if($debug == 1){
			$this->l("NOT MATCH - Check market_type table query: $marketTypeQuery");
		}
		$marketGroupExistResult = mysql_query($marketTypeQuery);
		$marketGroupExistnum_rows = mysql_num_rows($marketGroupExistResult);
		if ($marketGroupExistnum_rows){
			$this->l("Already in DB");
			$row = mysql_fetch_array($marketGroupExistResult);
			$marketTypeID = $row['id'];
		}else {
			// add new market type and get ID
			$nowTime = date("Y-m-d H:i:s");
			$marketTypeTableQuery = " INSERT INTO `".$marketTypeTable."` (`id`, `name`, `description`, `status_flag`, `created_date`) VALUES ";
			$marketTypeTableQuery .= " ('', '$eventBetType', 'UPDATE ME', '1', '$nowTime' ); ";
			mysql_query($marketTypeTableQuery);
			$marketTypeID = mysql_insert_id();
			$this->l("Added to DB");
			if ($debug == 1){
				$this->l("Add market_type table Query: $marketTypeTableQuery");
			}
		}
		return $marketTypeID;
	}
	
	final private function addMarketRecord($marketTable, $eventID, $marketTypeID){
		
		$nowTime = date("Y-m-d H:i:s");
		$marketQuery = "SELECT id from `".$marketTable."` where `event_id` = '$eventID' AND `market_type_id` = '$marketTypeID' LIMIT 1";
		if($debug == 1){
			$this->l("Check market table query: $marketQuery");
		}
		$marketExistResult = mysql_query($marketQuery);
		$marketExistnum_rows = mysql_num_rows($marketExistResult);
		if ($marketExistnum_rows){
			$this->l("Already in DB");
			$row = mysql_fetch_array($marketExistResult);
			$marketID = $row['id'];
		}else {
		
			$marketTableQuery = " INSERT INTO `".$marketTable."` (`id`, `event_id`, `market_type_id`, `wagering_api_id`, `refund_flag`, `created_date`) VALUES ";
			$marketTableQuery .= " ('', '$eventID', '$marketTypeID', '4', '0', '$nowTime'); ";
			mysql_query($marketTableQuery);
			$marketID = mysql_insert_id();
			$this->l("Added to DB");
			if($debug == 1){
				$this->l("Add market table Query: $marketTableQuery");
			}
		}
		return $marketID;
	}
	
	final private function addSelectionRecord($selectionTable, $marketCompetitorCompetitorID, $marketID, $bet_type_ref, $marketEventID, $marketCompetitorTeam, $debug){
		
		// $debug = 1;
		$nowTime = date("Y-m-d H:i:s");
		// Check is selection exists in database
		$selectionExist = "SELECT id from `".$selectionTable."` where `bet_place_ref` = '$marketCompetitorCompetitorID' AND `market_id` = '$marketID' LIMIT 1";
		if($debug == 1){
			$this->l("selection exist table Query: $selectionExist");
		}
		
		$selectionExistResult = mysql_query($selectionExist);
		$selectionExistnum_rows = mysql_num_rows($selectionExistResult);
		if ($selectionExistnum_rows){
			$this->l("Already in DB");
			$row = mysql_fetch_array($selectionExistResult);
			$selectionID = $row['id'];
		}else {
			// add new selection and get id of new selection record added
			$selectionQuery = " INSERT INTO `".$selectionTable."` (`id`, `market_id`, `bet_type_ref`, `bet_place_ref`, `external_selection_id`, `name`, `created_date`) VALUES ";
			$selectionQuery .= " ('', '$marketID', '$bet_type_ref', '$marketCompetitorCompetitorID', '$marketEventID', '$marketCompetitorTeam', '$nowTime'); ";
			mysql_query($selectionQuery);
			$selectionID = mysql_insert_id();
			$this->l("Added to DB");
			if($debug == 1){
				$this->l("ADD Selection table query: $selectionQuery");
			}
		}
		return $selectionID;
	}
	
	final private function addSelectionPriceRecord($selectionPriceTable, $selectionID, $marketCompetitorWinDividend, $marketCompetitorAllowBets, $marketCompetitorIsSuspended, $selectionPriceID, $debug){
		$debug == 1;
		$nowTime = date("Y-m-d H:i:s");
		// Check is compettiton exists in database
		$selectionPriceExist = "SELECT id from `".$selectionPriceTable."` where `selection_id` = '$selectionID' LIMIT 1";
		if($debug == 1){
			$this->l("Add selection_price exist Query: $selectionPriceExist");
		}
		$selectionPriceExistResult = mysql_query($selectionPriceExist);
		$selectionPriceExistnum_rows = mysql_num_rows($selectionPriceExistResult);
		//echo"$selectionExist\n";
		if ($selectionPriceExistnum_rows){
			$this->l("Already in DB");
			$row = mysql_fetch_array($selectionPriceExistResult);
			$selectionPriceID = $row['id'];
			$selectionPriceUpdateQuery = " UPDATE `".$selectionPriceTable."` SET place_bet_dividend = '$marketCompetitorWinDividend', win_odds = '$marketCompetitorWinDividend', allow_bets = '$marketCompetitorAllowBets', is_suspended = '$marketCompetitorIsSuspended' WHERE id = '$selectionPriceID'";
			mysql_query($selectionPriceUpdateQuery);
			if($debug == 1){
				$this->l("selection_price UPDATE QUERY: $selectionPriceUpdateQuery");
			}
		
		}else {
			// add new selecion_price and get id or new record
			$nowTime = date("Y-m-d H:i:s");
			$selectionPriceQuery = " INSERT INTO `".$selectionPriceTable."` (`id`, `selection_id`, `place_bet_dividend`, `win_odds`, allow_bets`, `is_suspended` ,`created_date`) VALUES ";
			$selectionPriceQuery .= " ('', '$selectionID', '$marketCompetitorWinDividend', '$marketCompetitorWinDividend', '$marketCompetitorAllowBets', '$marketCompetitorIsSuspended', '$nowTime'); ";
			mysql_query($selectionPriceQuery);
			$selectionPriceID = mysql_insert_id();
			$this->l("Added to DB");
			if($debug == 1){
				$this->l("ADD selection_price Query: $selectionPriceQuery");
			}
		}
		return $selectionPriceID;
	}
}


	

$cronjob = new SportsBMProcessor();
$cronjob->debug(false);
$cronjob->execute();