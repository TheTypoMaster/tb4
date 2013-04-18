<?php

require_once('../common/shell-bootstrap.php');
require_once('bookmaker/combined_bookmaker_helper.php');
jimport('mobileactive.wagering.api');

/**
 * RacingDataImport Class
 * Command line arguments:
 * "full-update"  => Downloads and updates the whole days results
 * e.g. usage: #> php race_data_importer.php full-update
 *
 * CronJob that should run every 10 mins to pull in race information for created tournaments
 * @author geoff
 * @contributor patrick
 */
class RaceDataImport extends TopBettaCLI
{
	/**
	 * Tournament model pointer
	 *
	 * @var TournamentModelTournamentRacing
	 */
	private $tournament_racing;

	/**
	 * Race model pointer
	 *
	 * @var TournamentModelRace
	 */
	private $race;

	/**
	 * Race model pointer
	 *
	 * @var TournamentModelRaceStatus
	 */
	private $race_status;

	/**
	 * Runner model
	 *
	 * @var TournamentModelRunner
	 */
	private $runner;

	/**
	 * Runner Status model
	 *
	 * @var TournamentModelRunnerStatus
	 */
	private $runner_status;
	/**
	 * Max execution time
	 * 
	 * @var integer
	 */
	protected $max_execution_time = 600;
	
	/**
	 * Push API Pointer
	 * 
	 * @var object
	 */
	private $push = null;
	/**
	 * Current wagering API ID
	 * 
	 * @var integer
	 */
	private $wapi_id = null;
	/**
	 * Current bet product ID
	 * 
	 * @var integer
	 */
	private $bet_product_id = null;
	/**
	 * Race status list
	 * 
	 * @var array
	 */
	protected $race_status_list = array();
	/**
	 * Lock file path/name
	 * 
	 * @var string
	 */
	const LOCK_FILE = '/tmp/race_data_importer.lck';
	/**
	 * Log file path
	 * @var string
	 */
	const LOG_PATH = '/usr/local/topbetta/logs/topbetta-crons/';
	
	const MEETINGS_FILE = 'https://www.theoddsbroker.com/ob-webapi/racing/meetings.txt';
	const DETAILS_FILE = 'https://www.theoddsbroker.com/ob-webapi/racing/details.txt';
	
	//const TOTE = 'SuperTab';
	const POOL_TYPE_WIN 	= 'W',
		POOL_TYPE_PLACE 	= 'P',
		POOL_TYPE_QUINELLA 	= 'Q',
		POOL_TYPE_EXACTA 	= 'E',
		POOL_TYPE_TRIFECTA 	= 'T',
		POOL_TYPE_FIRSTFOUR = 'FF';
		
	static $pool_type_lookup = array(
		self::POOL_TYPE_WIN 		=> 'win',
		self::POOL_TYPE_PLACE		=> 'place',
		self::POOL_TYPE_QUINELLA 	=> 'quinella',
		self::POOL_TYPE_EXACTA 		=> 'exacta',
		self::POOL_TYPE_TRIFECTA 	=> 'trifecta',
		self::POOL_TYPE_FIRSTFOUR 	=> 'firstfour'
	);
		
	/**
	 * Log handle pointer
	 * @var unknown_type
	 */
	private $log_handle = null;
	
	/**
	 * Constructor
	 * @return void
	 */
	public function initialise() {
		//set_error_handler(array($this,'notifyError'));
		
		declare(ticks =1);
		//MC pcntl_signal(SIGINT, array(&$this, 'cleanExit'));
		
		$this->addComponentModels('tournament');
		$this->addComponentModels('betting');
		
		$this->tournament			=& JModel::getInstance('Tournament', 'TournamentModel');
		$this->tournament_racing	=& JModel::getInstance('TournamentRacing', 'TournamentModel');
		$this->competition			=& JModel::getInstance('TournamentCompetition', 'TournamentModel');
		$this->tournament_ticket	=& JModel::getInstance('TournamentTicket', 'TournamentModel');
		
		$this->meeting				=& JModel::getInstance('Meeting', 'TournamentModel');
		$this->meeting_venue		=& JModel::getInstance('MeetingVenue', 'TournamentModel');

		$this->race					=& JModel::getInstance('Race', 'TournamentModel');
		$this->event_group_event	=& JModel::getInstance('EventGroupEvent', 'TournamentModel');
		$this->event_status			=& JModel::getInstance('EventStatus', 'TournamentModel');
		$this->wagering_api			=& JModel::getInstance('WageringApi', 'BettingModel');

		$this->runner				=& JModel::getInstance('Runner', 'TournamentModel');
		$this->market				=& JModel::getInstance('Market', 'TournamentModel');
		$this->selection_status		=& JModel::getInstance('SelectionStatus', 'TournamentModel');
		$this->selection_result		=& JModel::getInstance('SelectionResult', 'TournamentModel');
		$this->selection_price		=& JModel::getInstance('SelectionPrice', 'TournamentModel');
		$this->ticket				=& JModel::getInstance('TournamentTicket', 'TournamentModel');
		
		$this->market_type			= JModel::getInstance('MarketType', 'TournamentModel')->getMarketTypeByName('Racing');
		
		$this->bet_product			=& JModel::getInstance('BetProduct', 'BettingModel');
		
		$this->wapi_id				= 4;//$this->wagering_api->getWageringApiByKeyword('tob')->id;
		//$this->bet_product_id 		= 0;//$this->bet_product->getBetProductByKeyword('toptote')->id; //default to toptote and drop down as needed.
	
		$this->race_status_list = array(
			'OPEN'			=> TournamentModelRace::STATUS_SELLING,
			'CLOSED'		=> TournamentModelRace::STATUS_CLOSED,
			'INTERIM' 	=> TournamentModelRace::STATUS_INTERIM,
			'PAYING' 	=> TournamentModelRace::STATUS_PAYING,
			'ABANDONED' 	=> TournamentModelRace::STATUS_ABANDONED
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TopBettaCLI::execute()
	 */
 	public function execute()
	{
		
		$display_message = true;
		
		if(!$this->arg('debug')){
			while($this->_checkForRunningInstance(basename(__FILE__))){
				if($display_message){
					$this->l('Importer instance already running. WAIT');
					// email notification to alerts@topbetta.com
					$myHostname = gethostname();
					$emailAddress = "alerts@topbetta.com";
					$emailSubject = "TopbBetta Alert: $myHostname: combined_race_data_importer.php is still running";
					$emailBody = "";
					$sendEmail = $this->_sendNotificationEmail($emailAddress, $emailSubject, $emailBody);
					$display_message = false;
				}
				time_nanosleep(0, 500000000);
			}
		}
		
		if($this->arg('debug')){
			$this->_debugArgument($this->arg('debug'));
		}
		
		if($this->arg('meetings')){
			$this->consumeMeetingsFile();
		}
		else{
			// while (1) {
			    touch(self::LOCK_FILE);
				$this->_executionExpiryCheck();
				$this->consumeBothFiles();
				$processTournaments = shell_exec('/usr/bin/php -f /mnt/data/sites/topbetta.com/cron-scripts/tournament_processor.php');
				echo"$processTournaments";
				//$this->consumeDetailsFile();
				// $this->consumeMeetingsFile();
				// sleep(2);
			//}
		}
	}
	
	
	final private function _executionExpiryCheck()
	{
		if ($this->hasExecutionTimeExpired()) {
			$this->cleanExit();
		}
	}
	
	final private function _getFileContentList($file){
		//Fix this by reading from server.xml instead.
		$auth = base64_encode('chiefy:Roosters');
		$header = array("Authorization: Basic $auth");

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$file);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$data = curl_exec($ch);
		curl_close($ch);
		return explode("---------------\n", $data);
	}
	
	final public function consumeMeetingsFile(){
		// Get id's for scratched and non scratced runners
		$scratchedIDmodel = $selection_status_model = $this->selection_status->getSelectionStatusIdByKeyword(TournamentModelRunner::STATUS_SCRATCHED);
		$notScratchedIDmodel = $selection_status_model = $this->selection_status->getSelectionStatusIdByKeyword(TournamentModelRunner::STATUS_NOT_SCRATCHED);
		$scratchedID = (int) $scratchedIDmodel->id;
		$notScratchedID = (int) $notScratchedIDmodel->id;
		
		$meetings_csv = new Bookmaker;		
		$meetings_csv->emulateCSV('meetings');
		$meeting_list = explode("---------------\n", $meetings_csv->meetings_output);

		foreach($meeting_list as $meeting){
			 $meeting_detail = explode("\n", $meeting);
			 
			 if(empty($meeting_detail[0])) continue;
			 list($date, $name, $type_code, $nsw_code, $supertab_code, $unitab_code)  = explode(',', $meeting_detail[0]);
			 if(empty($supertab_code) || empty($name)) continue;
			 
			 $external_meeting_id = $this->_formatExternalMeetingId($date, $supertab_code);
			 
			 $data[$external_meeting_id]['name'] = $name;
			 $data[$external_meeting_id]['date'] = $date;
			 $data[$external_meeting_id]['external_id'] = $external_meeting_id;
			 $data[$external_meeting_id]['type_code'] = $type_code;
			 $data[$external_meeting_id]['supertab_code'] = $supertab_code;
			 
			 $this->l('Importing meeting: '. $name);
			 
			 foreach($meeting_detail as $line){
			 	$column = explode(',', $line);
			 	
			 	if(preg_match('/^R(\d{1,2})$/', $column[0], $match_list)){
					$this->l('Importing race number: '. $match_list[1]);
					$number = $match_list[1];
					
					if ($number != 0) {
					
					list($race_code, $datetime, $event_id,$distance,$track_condition,$weather)  = explode(',', $line);
					$external_event_id = $external_meeting_id . '-' . $race_code;
					
					$data[$external_meeting_id]['race_list'][$number]['number'] = $number; 
					$data[$external_meeting_id]['race_list'][$number]['start_date'] = $datetime; 
					$data[$external_meeting_id]['race_list'][$number]['external_id'] = $external_event_id;
					$data[$external_meeting_id]['race_list'][$number]['event_id'] = $event_id;
					$data[$external_meeting_id]['race_list'][$number]['distance'] = $distance;
					$data[$external_meeting_id]['race_list'][$number]['track_condition'] = $track_condition;
					$data[$external_meeting_id]['race_list'][$number]['weather'] = $weather;
					}
				}	
		
				if(preg_match('/^\d{1,2}$/', $column[0], $match_list)){
					$this->l('Importing runner number: '. $match_list[0]);
					list($runner_number, $barrier, $name, $associate, $weight, $scratched, $silk_url, $runner_id)  = explode(',', $line);
					$external_runner_id = $this->_formatExternalSelectionId($external_event_id, $runner_number);

					$runner_data = array(
						'barrier' 	=> $barrier,
						'name'		=> $name,
						'associate' => $associate,
						'weight'	=> $weight,
						'scratched' => $scratched,
						'silk_url'	=> $silk_url,
						'number'	=> $runner_number,
						'wager_id'	=> $runner_id,
						'silk_id'	=> $silk_url,
						'scratched' => $scratched == 's' ? true : false
					);
					
					$data[$external_meeting_id]['race_list'][$number]['runner_list'][$runner_number] = $runner_data; 
					$data[$external_meeting_id]['race_list'][$number]['runner_list'][$runner_number]['external_id'] = $external_runner_id;
				}

			 }	
		}
		//var_dump($meeting_list);
		
		//exit();
		
		foreach($data as $meeting){
			$meeting_object = $this->_processMeetingData($meeting);
			
		 	foreach($meeting['race_list'] as $race_number => $race_data){
		 		
                if ($race_number != 0) {
	                $race = $this->_processRaceData($race_data, $meeting_object);
	                $market = $this->market->getMarketByEventID($race->id);
	                $marketID = $market->id;
    				foreach($race_data['runner_list'] as $runner_data){
                    	$this->_processRunnerData($runner_data, $race, $scratchedID, $notScratchedID, $marketID);
                	}
				} else {
				    $this->l('Not processing race 0');
				}
		 	}
		 }
	}

	final private function consumeBothFiles(){
		
		// Call the combined bookmaker helper 
		$combined_csv = new Bookmaker;
		$combined_csv->emulateCSV();
		
		
		//
		// OLD MEETINGS FUNCTION
		//
		// Get id's for scratched and non scratced runners
		$scratchedIDmodel = $selection_status_model = $this->selection_status->getSelectionStatusIdByKeyword(TournamentModelRunner::STATUS_SCRATCHED);
		$notScratchedIDmodel = $selection_status_model = $this->selection_status->getSelectionStatusIdByKeyword(TournamentModelRunner::STATUS_NOT_SCRATCHED);
		$scratchedID = (int) $scratchedIDmodel->id;
		$notScratchedID = (int) $notScratchedIDmodel->id;
		
// 		$meetings_csv->emulateCSV('meetings');
		$meeting_list = explode("---------------\n", $combined_csv->meetings_output);
		
		foreach($meeting_list as $meeting){
			$meeting_detail = explode("\n", $meeting);
		
			if(empty($meeting_detail[0])) continue;
			list($date, $name, $type_code, $nsw_code, $supertab_code, $unitab_code)  = explode(',', $meeting_detail[0]);
			if(empty($supertab_code) || empty($name)) continue;
		
			$external_meeting_id = $this->_formatExternalMeetingId($date, $supertab_code);
		
			$data[$external_meeting_id]['name'] = $name;
			$data[$external_meeting_id]['date'] = $date;
			$data[$external_meeting_id]['external_id'] = $external_meeting_id;
			$data[$external_meeting_id]['type_code'] = $type_code;
			$data[$external_meeting_id]['supertab_code'] = $supertab_code;
		
			$this->l('Importing meeting: '. $name);
		
			foreach($meeting_detail as $line){
				$column = explode(',', $line);
					
				if(preg_match('/^R(\d{1,2})$/', $column[0], $match_list)){
					$this->l('Importing race number: '. $match_list[1]);
					$number = $match_list[1];
						
					if ($number != 0) {
							
						list($race_code, $datetime, $event_id,$distance,$track_condition,$weather)  = explode(',', $line);
						$external_event_id = $external_meeting_id . '-' . $race_code;
							
						$data[$external_meeting_id]['race_list'][$number]['number'] = $number;
						$data[$external_meeting_id]['race_list'][$number]['start_date'] = $datetime;
						$data[$external_meeting_id]['race_list'][$number]['external_id'] = $external_event_id;
						$data[$external_meeting_id]['race_list'][$number]['event_id'] = $event_id;
						$data[$external_meeting_id]['race_list'][$number]['distance'] = $distance;
						$data[$external_meeting_id]['race_list'][$number]['track_condition'] = $track_condition;
						$data[$external_meeting_id]['race_list'][$number]['weather'] = $weather;
					}
				}
		
				if(preg_match('/^\d{1,2}$/', $column[0], $match_list)){
					$this->l('Importing runner number: '. $match_list[0]);
					list($runner_number, $barrier, $name, $associate, $weight, $scratched, $silk_url, $runner_id)  = explode(',', $line);
					$external_runner_id = $this->_formatExternalSelectionId($external_event_id, $runner_number);
		
					$runner_data = array(
							'barrier' 	=> $barrier,
							'name'		=> $name,
							'associate' => $associate,
							'weight'	=> $weight,
							'scratched' => $scratched,
							'silk_url'	=> $silk_url,
							'number'	=> $runner_number,
							'wager_id'	=> $runner_id,
							'silk_id'	=> $silk_url,
							'scratched' => $scratched == 's' ? true : false
					);
						
					$data[$external_meeting_id]['race_list'][$number]['runner_list'][$runner_number] = $runner_data;
					$data[$external_meeting_id]['race_list'][$number]['runner_list'][$runner_number]['external_id'] = $external_runner_id;
				}
		
			}
		}
		//var_dump($meeting_list);
		
		//exit();
		
		foreach($data as $meeting){
			$meeting_object = $this->_processMeetingData($meeting);
				
			foreach($meeting['race_list'] as $race_number => $race_data){
				 
				if ($race_number != 0) {
					$race = $this->_processRaceData($race_data, $meeting_object);
					$market = $this->market->getMarketByEventID($race->id);
					$marketID = $market->id;
					foreach($race_data['runner_list'] as $runner_data){
						$this->_processRunnerData($runner_data, $race, $scratchedID, $notScratchedID, $marketID);
					}
				} else {
					$this->l('Not processing race 0');
				}
			}
		}
		
		
		
		
		//
		// OLD DETAILS FUNCTION
		//
		//$meeting_list = $this->_getFileContentList(self::DETAILS_FILE);
		$data = array();
		$totes = array();
	
		// Get available tote's
		$toteList = $this->bet_product->getBetProductKeywordListWithID();
	
		foreach($toteList as $toteTypes){
			$toteArray[$toteTypes['keyword']] = $toteTypes['id'];
		}
	
		// $details_csv = new Bookmaker;
		// $details_csv->emulateCSV('details');
		$meeting_list = explode("---------------\n", $combined_csv->details_output);
	
		foreach($meeting_list as $meeting){
			$meeting_detail = explode("\n", $meeting);
			$tote = null;
	
			if(empty($meeting_detail[0])) continue;
			list($date, $name, $type_code, $nsw_code, $supertab_code, $unitab_code) = explode(',', $meeting_detail[0]);
			if(empty($supertab_code) || empty($name)) continue;
	
			$external_meeting_id = $this->_formatExternalMeetingId($date, $supertab_code);
			$data[$external_meeting_id] = array(
					'name' => $name,
					'race_list' => array()
			);
	
			foreach($meeting_detail as $line){
				$column = explode(',', $line);
	
				if(preg_match('/^R(\d{1,2})$/', $column[0], $match_list)){
					$number = $match_list[1];
					$race_status = $column[2];
					$race_start = $column[1];
	
					/*
					 if(strtotime($race_start) < strtotime('-2 hours')){
					continue 2;
					}
					*/
	
					$data[$external_meeting_id]['race_list'][$number]['external_id'] = $external_meeting_id . '-' . $column[0];
					$data[$external_meeting_id]['race_list'][$number]['status'] = $race_status;
					$data[$external_meeting_id]['race_list'][$number]['start_date'] = $race_start;
				}
	
				if(preg_match('/^[WPQETF]{1}[EF]{0,1}$/', $column[0], $match_list)){
					$tote = $column[1];
					$pool_data = $line;
						
						
					//if(self::TOTE == $tote){
					list($type, $tote, $bm_tote, $external_race_pool_id, $pool_size, $jackpot_size, $last_update) = explode(',', $line);
					// $this->l('Importing pool information: '. $type .' '.$tote .' '.$bm_tote);
					$data[$external_meeting_id]['race_list'][$number]['pool_list'][$type] = array(
							'pool_id' 		=> $external_race_pool_id,
							'tote' 			=> $toteArray[$tote],
							'bm_tote'		=> $toteArray[$bm_tote],
							'pool_size' 	=> $pool_size,
							'jackpot'		=> $jackpot_size,
							'last_update' 	=> $last_update
					);
	
					if(isset($column[7])){
							
						switch ($type){
							case self::POOL_TYPE_PLACE:
								$result1 	= $this->_extractResult($column[7]);
								$result2 = $this->_extractResult($column[8]);
								if(isset($column[9])){
									$result3 	= $this->_extractResult($column[9]);
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$result3['number']]['place_dividend'] 	= $result3['dividend'];
								}
								if(isset($column[10])){
									$result4 	= $this->_extractResult($column[10]);
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$result4['number']]['place_dividend'] 	= $result4['dividend'];
								}
								$data[$external_meeting_id]['race_list'][$number]['result_list'][$result1['number']]['place_dividend'] 	= $result1['dividend'];
								$data[$external_meeting_id]['race_list'][$number]['result_list'][$result2['number']]['place_dividend'] 	= $result2['dividend'];
								break;
							case self::POOL_TYPE_WIN:
								$first 	= $this->_extractResult($column[7]);
								$data[$external_meeting_id]['race_list'][$number]['result_list'][$first['number']]['win_dividend'] = $first['dividend'];
								if(isset($column[8])){
									$dh_result2 = $this->_extractResult($column[8]);
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$dh_result2['number']]['win_dividend'] = $dh_result2['dividend'];
								}
								break;
							case self::POOL_TYPE_EXACTA:
								$extra = $this->_extractResult($column[7]);
								$runner_list = explode('-', $extra['number']);
									
								if(isset($column[8])){
									$position = 1;
									$dh_result = $this->_extractResult($column[8]);
									$runner_list_dh = explode('-', $dh_result['number']);
										
									$runner_cnt = $j = 0;
									for($i=1; $i <= $position; $i++){
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i + $j;
	
										if($runner_list[$runner_cnt] !== $runner_list_dh[$runner_cnt]){
											$runner_cnt++;
											$j=1;
											$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i;
										}
										$runner_cnt++;
									}
								}else{
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[0]]['position'] = 1;
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[1]]['position'] = 2;
								}
							case self::POOL_TYPE_TRIFECTA:
								$trifecta = $this->_extractResult($column[7]);
								$runner_list = explode('-', $trifecta['number']);
									
								if(isset($column[8])){
									$position = 2;
									$dh_result = $this->_extractResult($column[8]);
									$runner_list_dh = explode('-', $dh_result['number']);
										
									$runner_cnt = $j = 0;
									for($i=1; $i <= $position; $i++){
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i + $j;
	
										if($runner_list[$runner_cnt] !== $runner_list_dh[$runner_cnt]){
											$runner_cnt++;
											$j=1;
											$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i;
										}
										$runner_cnt++;
									}
								}else{
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[0]]['position'] = 1;
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[1]]['position'] = 2;
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[2]]['position'] = 3;
								}
							case self::POOL_TYPE_FIRSTFOUR:
								$firstfour = $this->_extractResult($column[7]);
								$runner_list = explode('-', $firstfour['number']);
									
								if(isset($column[8])){
									$position = 3;
									$dh_result = $this->_extractResult($column[8]);
									$runner_list_dh = explode('-', $dh_result['number']);
										
									$runner_cnt = $j = 0;
									for($i=1; $i <= $position; $i++){
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i + $j;
	
										if($runner_list[$runner_cnt] !== $runner_list_dh[$runner_cnt]){
											$runner_cnt++;
											$j=1;
											$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i;
										}
										$runner_cnt++;
									}
								}else{
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[0]]['position'] = 1;
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[1]]['position'] = 2;
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[2]]['position'] = 3;
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[3]]['position'] = 4;
								}
							default:
								$result = $this->_extractResult($column[7]);
								$result_array = array($result);
									
								if(isset($column[8])){
									$result_dh = $this->_extractResult($column[8]);
									$result_array =array($result,$result_dh);
								}
									
								$data[$external_meeting_id]['race_list'][$number]['dividend_list'][$type] = $result_array;
								break;
						}
	
						//$this->l('Importing results');
					}
	
					//$race = $this->_processPoolData($line, $external_event_id);
					//}
				}
	
				//if(self::TOTE == $tote){
				if(preg_match('/^\d{1,2}$/', $column[0])){
	
					$data[$external_meeting_id]['race_list'][$number]['runner_list'][$column[0]][$type] = $column[1];
	
	
					//$this->_processOddsData($line, $race, $pool_data);
				}
				//}
			}
		}
		
		
		/*		// Get event status and store in array
		 $eventStatusList = $this->event_status->getEventStatusByKeywordList();
		foreach($eventStatusList as $eventStatus){
		$eventStatusArray[$eventStatus['keyword']] = $eventStatus['id'];
		}
		*/
		foreach($data as $meeting){
			foreach($meeting['race_list'] as $race_data){
				if(strtotime($race_data['start_date']) > strtotime('-2 hours')){
					$this->_processRaceStatus($race_data);
					$this->_processOddsData($race_data);
					$this->_processResultData($race_data);
				}
			}
		}
	}
	
	
	final private function consumeDetailsFile(){
		//$meeting_list = $this->_getFileContentList(self::DETAILS_FILE);
		$data = array();
		$totes = array();
		
		// Get available tote's
		$toteList = $this->bet_product->getBetProductKeywordListWithID();
		
		foreach($toteList as $toteTypes){
			$toteArray[$toteTypes['keyword']] = $toteTypes['id'];
		}
				
		$details_csv = new Bookmaker;
				
		$details_csv->emulateCSV('details');
		$meeting_list = explode("---------------\n", $details_csv->details_output);		

		foreach($meeting_list as $meeting){
			 $meeting_detail = explode("\n", $meeting);
			 $tote = null;
			 
			 if(empty($meeting_detail[0])) continue;
			 list($date, $name, $type_code, $nsw_code, $supertab_code, $unitab_code) = explode(',', $meeting_detail[0]);
			 if(empty($supertab_code) || empty($name)) continue;

			 $external_meeting_id = $this->_formatExternalMeetingId($date, $supertab_code);
			 $data[$external_meeting_id] = array(
			 	'name' => $name,
			 	'race_list' => array()
			 );
			 
			 foreach($meeting_detail as $line){
			 	$column = explode(',', $line);
			 				 	
			 	if(preg_match('/^R(\d{1,2})$/', $column[0], $match_list)){
			 		$number = $match_list[1];
			 		$race_status = $column[2];
			 		$race_start = $column[1];
			 		
			 		/*
			 		if(strtotime($race_start) < strtotime('-2 hours')){
			 			continue 2;
		 			}
		 			*/
		
					$data[$external_meeting_id]['race_list'][$number]['external_id'] = $external_meeting_id . '-' . $column[0];
					$data[$external_meeting_id]['race_list'][$number]['status'] = $race_status;
					$data[$external_meeting_id]['race_list'][$number]['start_date'] = $race_start;
				}	 	

				if(preg_match('/^[WPQETF]{1}[EF]{0,1}$/', $column[0], $match_list)){
					$tote = $column[1];
					$pool_data = $line;
					
					
					//if(self::TOTE == $tote){
						list($type, $tote, $bm_tote, $external_race_pool_id, $pool_size, $jackpot_size, $last_update) = explode(',', $line);
						// $this->l('Importing pool information: '. $type .' '.$tote .' '.$bm_tote);
						$data[$external_meeting_id]['race_list'][$number]['pool_list'][$type] = array(
							'pool_id' 		=> $external_race_pool_id,
							'tote' 			=> $toteArray[$tote],
							'bm_tote'		=> $toteArray[$bm_tote],
							'pool_size' 	=> $pool_size,
							'jackpot'		=> $jackpot_size,
							'last_update' 	=> $last_update
						);

						if(isset($column[7])){
							
							switch ($type){
								case self::POOL_TYPE_PLACE:
									$result1 	= $this->_extractResult($column[7]);
									$result2 = $this->_extractResult($column[8]);
									if(isset($column[9])){
										$result3 	= $this->_extractResult($column[9]);
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$result3['number']]['place_dividend'] 	= $result3['dividend'];
									}
									if(isset($column[10])){
										$result4 	= $this->_extractResult($column[10]);
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$result4['number']]['place_dividend'] 	= $result4['dividend'];
									}
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$result1['number']]['place_dividend'] 	= $result1['dividend'];
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$result2['number']]['place_dividend'] 	= $result2['dividend'];
									break;
								case self::POOL_TYPE_WIN:
									$first 	= $this->_extractResult($column[7]);
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$first['number']]['win_dividend'] = $first['dividend'];
									if(isset($column[8])){
									$dh_result2 = $this->_extractResult($column[8]);
									$data[$external_meeting_id]['race_list'][$number]['result_list'][$dh_result2['number']]['win_dividend'] = $dh_result2['dividend'];	
									}
									break;
								case self::POOL_TYPE_EXACTA:
									$extra = $this->_extractResult($column[7]);
									$runner_list = explode('-', $extra['number']);
									
									if(isset($column[8])){
										$position = 1;
										$dh_result = $this->_extractResult($column[8]);
										$runner_list_dh = explode('-', $dh_result['number']);
											
										$runner_cnt = $j = 0;
										for($i=1; $i <= $position; $i++){
											$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i + $j;

											if($runner_list[$runner_cnt] !== $runner_list_dh[$runner_cnt]){
												$runner_cnt++;
												$j=1;
												$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i;
											}
											$runner_cnt++;
										}
									}else{
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[0]]['position'] = 1;
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[1]]['position'] = 2;
									}
								case self::POOL_TYPE_TRIFECTA:
									$trifecta = $this->_extractResult($column[7]);
									$runner_list = explode('-', $trifecta['number']);
									
									if(isset($column[8])){
										$position = 2;
										$dh_result = $this->_extractResult($column[8]);
										$runner_list_dh = explode('-', $dh_result['number']);
											
										$runner_cnt = $j = 0;
										for($i=1; $i <= $position; $i++){
											$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i + $j;

											if($runner_list[$runner_cnt] !== $runner_list_dh[$runner_cnt]){
												$runner_cnt++;
												$j=1;
												$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i;
											}
											$runner_cnt++;
										}
									}else{
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[0]]['position'] = 1;
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[1]]['position'] = 2;
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[2]]['position'] = 3;
									}									
								case self::POOL_TYPE_FIRSTFOUR:
									$firstfour = $this->_extractResult($column[7]);
									$runner_list = explode('-', $firstfour['number']);
									
									if(isset($column[8])){
										$position = 3;
										$dh_result = $this->_extractResult($column[8]);
										$runner_list_dh = explode('-', $dh_result['number']);
											
										$runner_cnt = $j = 0;
										for($i=1; $i <= $position; $i++){
											$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i + $j;

											if($runner_list[$runner_cnt] !== $runner_list_dh[$runner_cnt]){
												$runner_cnt++;
												$j=1;
												$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[$runner_cnt]]['position'] = $i;
											}
											$runner_cnt++;
										}
									}else{
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[0]]['position'] = 1;
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[1]]['position'] = 2;
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[2]]['position'] = 3;
										$data[$external_meeting_id]['race_list'][$number]['result_list'][$runner_list[3]]['position'] = 4;
									}
								default:
									$result = $this->_extractResult($column[7]);
									$result_array = array($result);
									
									if(isset($column[8])){
									$result_dh = $this->_extractResult($column[8]);	
									$result_array =array($result,$result_dh);
									}
									
									$data[$external_meeting_id]['race_list'][$number]['dividend_list'][$type] = $result_array;
									break;
							}								
								
							//$this->l('Importing results');
						}
						
						//$race = $this->_processPoolData($line, $external_event_id);
					//}
				}	

				//if(self::TOTE == $tote){
					if(preg_match('/^\d{1,2}$/', $column[0])){
						
						$data[$external_meeting_id]['race_list'][$number]['runner_list'][$column[0]][$type] = $column[1];
						

						//$this->_processOddsData($line, $race, $pool_data);	
					} 
				//}
			 }		 
		}


/*		// Get event status and store in array
		$eventStatusList = $this->event_status->getEventStatusByKeywordList();
		foreach($eventStatusList as $eventStatus){
			$eventStatusArray[$eventStatus['keyword']] = $eventStatus['id'];
		}
*/
		foreach($data as $meeting){
		 	foreach($meeting['race_list'] as $race_data){
		 		if(strtotime($race_data['start_date']) > strtotime('-2 hours')){
		 			$this->_processRaceStatus($race_data);
		 			$this->_processOddsData($race_data);
		 			$this->_processResultData($race_data);
		 		}
		 	}
		 }
	}
	
	final private function _getApi(){
	
		try {
			$api = WageringApi::getInstance(WageringApi::API_TOB); 
			
			
		}
		catch(Exception $e){
			$this->l('API Error: '.$e->getMessage());
			exit;
		}
	}
	
	final private function _processMeetingData($data){		
		$comp_lookup = array('R' => 'Galloping', 'G' => 'Greyhounds', 'T' => 'Harness');
		$type_code = $data['type_code'] == 'T' ? 'H' : $data['type_code'];
		
		$external_meeting_id = $data['external_id'];
		$meeting_name = ucwords(strtolower($data['name']));
		
		$venue = $this->meeting_venue->getMeetingVenueByName($meeting_name);
		if (empty($venue)) {
			$this->l("Venue name '$meeting_name' not found. Creating new venue record.");
			$meeting_venue_params = array(
				'name' => $meeting_name
			);
			if($this->meeting_venue->store($meeting_venue_params)) {
				$this->l("New tbdb_meeting_venue record for '$meeting_name' created.");
			} else {
				$this->l("Failed to create venue record for '$meeting_name'.");
			}
		}
	
		$meeting = $this->meeting->getEventGroupByExternalEventGroupIdAndWageringApiId($external_meeting_id, $this->wapi_id);
		$comp_id = $this->competition->getTournamentCompetitionIdByName($comp_lookup[$data['type_code']]);
				
		//check for future meetings
		if(is_null($meeting)){
			$meeting = $this->meeting->getMeetingByNameAndTypeCodeAndDate($meeting_name, $type_code, time());
		}
		
		if(is_null($meeting)){
			$meeting = clone $this->meeting;
		}
		
		ksort($data['race_list']);
		
		$race_list =  $data['race_list'];

		$first_race = array_shift($data['race_list']);
		
		// Update the Tounament Dates Based on Meeting ID
		$last_race = array_pop($data['race_list']);
		if(!is_null($meeting) && !empty($first_race) && !empty($last_race)){
		$this->tournament->groupUpdateStartDateByMeetingID($meeting->id, $first_race['start_date']);
		$this->tournament->groupUpdateEndDateByMeetingID($meeting->id, $last_race['start_date']);
		}
		
		$meeting->external_event_group_id 	= $external_meeting_id;
		$meeting->wagering_api_id 			= (int) $this->wapi_id;
		$meeting->name 						= $meeting_name;
		$meeting->tournament_competition_id = $comp_id;
		$meeting->display_flag 				= 1;
		$meeting->state 					= '';
		$meeting->meeting_code 				= $data['supertab_code'];
		$meeting->type_code				    = $type_code;
		$meeting->start_date				= $first_race['start_date'];
		$meeting->events 					= count($race_list);
		
		$this->_save($meeting);
		
		return $meeting;
	}
	
	final private function _processRaceData($race_data, $meeting){
		
		$external_event_id = $race_data['external_id'];
		if ($external_event_id == '20121106-FLEMINGTONR-R0') {
		    return false;
		}
		$race = $this->race->getEventByExternalEventIDAndWageringApiID($external_event_id, $this->wapi_id);
		
		//Check the previous date data exist or not
		$previous_date = date('Ymd', strtotime(substr($race_data['start_date'],0,10) .' -1 day'));
		$arrExternalId = explode('-',$external_event_id);

		if(count($arrExternalId) > 2) {
			$new_external_event_id = $previous_date.'-'.$arrExternalId[1].'-'.$arrExternalId[2];
			//chekc the reace exist or not
			//MC if(is_null($race)) $race = $this->race->getEventByExternalEventIDAndWageringApiID($new_external_event_id, $this->wapi_id);
		}
		
		if(is_null($race)){
			$race = clone $this->race;
			$this->l('Creating race: (' . $external_event_id . ')');
		}
		else{
			$this->l('Updating race: (' . $external_event_id . ')');
		}
		
		$race->external_event_id 	= $external_event_id;
		$race->number 				= (int) $race_data['number'];
		$race->name 				= '';
		$race->distance 			= $race_data['distance'];
		$race->weather 				= $race_data['weather'];
		$race->track_condition 		= $race_data['track_condition'];
		$race->start_date 			= $race_data['start_date'];
		$race->wagering_api_id 		= (int) $this->wapi_id;
		$race->event_id 			= (int) $race_data['event_id'];
		
		$this->_save($race);
		
		$market = $this->market->getMarketByEventID($race->id);
		
		if(is_null($market)){
			$market = clone $this->market;
		}
		
		$market->market_type_id		= (int) $this->market_type->id;
		$market->external_market_id	= (int) $external_event_id;
		$market->wagering_api_id 	= (int) $this->wapi_id;
		$market->event_id 			= (int) $race->id;
		$this->_save($market);
		
		$event_group_event = $this->event_group_event->getEventGroupEventByEventGroupIDAndEventID($meeting->id, $race->id);
		
		if(is_null($event_group_event)){
			$event_group_event = clone $this->event_group_event;
			
			$event_group_event->event_group_id 	= $meeting->id;
			$event_group_event->event_id 		= $race->id;
			$this->_save($event_group_event);
		}
		
		return $race;	
	}
	
	final private function _processRunnerData($runner_data, $race, $scratchedID ,$notScratchedID, $marketID){
		
		$external_selection_id = $runner_data['external_id'];
		$runner = $this->runner->getSelectionByExternalSelectionIdAndWageringApiId($external_selection_id, $this->wapi_id);
	
		if(is_null($runner)){
			$runner = clone $this->runner;
			$this->l('Creating runner: ' . $runner_data['name'] . '(' . $external_selection_id . ')');
		}
		else {
			$this->l('Updating runner: ' . $runner_data['name'] . '(' . $external_selection_id . ')');
		}
		
		if($runner_data['scratched']){
			$runner->selection_status_id 	= (int)$scratchedID;
		}
		else {
			$runner->selection_status_id 	= (int)$notScratchedID;
		}
		
		$runner->market_id 				= (int) $marketID;
		$runner->external_selection_id 	= $external_selection_id;
		$runner->number 				= (int) $runner_data['number'];
		$runner->name 					= $runner_data['name'];
		$runner->weight 				= (float)$runner_data['weight'];
		$runner->barrier 				= $runner_data['barrier'];
		$runner->wagering_api_id 		= $this->wapi_id;
		$runner->associate 				= $runner_data['associate'];
		$runner->ident					= $this->runner->formatIdentFromName($runner_data['name']);
		$runner->wager_id 				= (int) $runner_data['wager_id'];
		$runner->silk_id				= (int) $runner_data['silk_id'];

		$this->_save($runner);
	}
	
	final private function _processOddsData($pool_data){		
		if(!isset($pool_data['runner_list'])){
			return;
		}
		
		$log_flag=true;
		foreach($pool_data['runner_list'] as $number => $odds){
			//$win_odds = isset($odds['W'])  ? $odds['W']/100 : $odds['P']/100;
			//$place_odds = isset($odds['P'])  ? $odds['P']/100 : $odds['P']/100;

			$win_odds = $odds['W']/100;
			$place_odds = $odds['P']/100;
			//print_r($number." - ".$odds['W']."\n");
			if(is_null($win_odds) && is_null($place_odds)){
				//continue;
			}
			
			$tote = $pool_data['pool_list']['W']['tote']; //default tote
			$w_tote = $pool_data['pool_list']['W']['bm_tote'];
			$p_tote = $pool_data['pool_list']['P']['bm_tote'];

			$last_update_win = strtotime($pool_data['pool_list']['W']['last_update']);
			$last_update_place = isset($pool_data['pool_list']['P']['last_update']) ? strtotime($pool_data['pool_list']['P']['last_update']) : null;
			
			$last_update = $last_update_win > $last_update_place ? $last_update_win : $last_update_place;
			
			$external_selection_id = $pool_data['external_id'] .'-'. $number;
			$runner = $this->runner->getSelectionByExternalSelectionIdAndWageringApiId($external_selection_id, $this->wapi_id);

			if(is_null($runner)){
				$this->l('ERROR: No runner found');
				continue;
			}
			
			$selection_price = $this->selection_price->getSelectionPriceBySelectionIDAndBetProductID($runner->id, $tote);
			
			if(is_null($selection_price)){
				$selection_price = clone $this->selection_price;
			}
			else {
				if(strtotime($selection_price->updated_date) > $last_update){
					$this->l('Runner '. $number . ' skipped - has latest odds');
					continue;
				}
			}
			
			$update_db = false;
			$selection_price->selection_id 	= (int) $runner->id;
			$selection_price->bet_product_id = (int) $tote;
			$selection_price->w_product_id = (int) $w_tote;
			$selection_price->p_product_id = (int) $p_tote;

			if(!is_null($win_odds)){
				$this->compareValueForUpdate($selection_price->win_odds,$win_odds,$update_db);
				$selection_price->win_odds 	= (float) $win_odds;
			} 
			if($win_odds == 0){
				$selection_price->win_odds 	= (float) $win_odds;
				$update_db = true;
			}

			if(!is_null($place_odds)){
				$this->compareValueForUpdate($selection_price->place_odds,$place_odds,$update_db);
				$selection_price->place_odds = (float) $place_odds;
			} 
			if($place_odds == 0){
				$selection_price->place_odds 	= (float) $place_odds;
				$update_db = true;
			}
			
			if($update_db){
				// Update only in case of Data Change
				if($log_flag){
					$this->l('Updating odds for: ' . $pool_data['external_id']);
					$log_flag = false;
				}
				$this->_save($selection_price);
				$this->l('Runner '. $number . ' odds updated');
			}
			
		}
	}
	
	final private function _processRaceStatus($race_data){
		//print_r($race_data['external_id']);		
		$race = $this->race->getEventByExternalEventIDAndWageringApiID($race_data['external_id'], $this->wapi_id);
		
		if(is_null($race)){
			$this->l('Race does not exist. (' . $race_data['external_id'] . ')');
			return;
		}
		
		$race_status = $race_data['status'];
		$event_status = $this->event_status->getEventStatusByKeyword($this->race_status_list[$race_status]);
		//$eventStatusID = $eventStatusArray($this->race_status_list[$race_status]);

		
				
		if(is_null($event_status)){
			$this->l('ERROR: Event status not found in lookup table');
		}
		
		$pool_id_list = array();
		
		if(isset($race_data['pool_list'])){
			foreach($race_data['pool_list'] as $type => $pool){
				$pool_id_list[$type] = $pool['pool_id'];
			}
		}
		
		$update_db = false;
		if(isset($race_data['dividend_list'])){
			
			foreach($race_data['dividend_list'] as $pool_type => $dividend_array){
				$dividend_list = array();
				
				foreach ($dividend_array as $key => $value) {
					if($value['status'] == 'P'){
					$position_list = str_replace('-','/', $value['number']);
					$dividend_list[$position_list] = $value['dividend'];
					}
				}
				$column_name = self::$pool_type_lookup[$pool_type] . '_dividend';
				$this->compareValueForUpdate($race->{$column_name},serialize($dividend_list),$update_db);
				$race->{$column_name} = serialize($dividend_list);
			}
		}
		
		$this->compareValueForUpdate($race->external_race_pool_id_list,serialize($pool_id_list),$update_db);
		$race->external_race_pool_id_list = serialize($pool_id_list);
		
		$this->compareValueForUpdate($race->event_status_id,$event_status->id,$update_db);
		$race->event_status_id = (int) $event_status->id;
		
		if($update_db){
			// Update only in case of Data Change
			$this->_save($race);
			$this->l('External event status: '. $race_status);
			$this->l('Event status ('.$race_data['external_id'].'): '. $this->race_status_list[$race_status]);
		}
		
	}
	
	final private function _processResultData($race_data){
	
		if(isset($race_data['result_list'])){
		
			$event = $this->race->getEventByExternalEventIDAndWageringApiID($race_data['external_id'], $this->wapi_id);
			$existing_result_list = $this->selection_result->getSelectionResultListByEventID($event->id);
		
			foreach($race_data['result_list'] as $runner_number => $runner_data){
			
				$external_selection_id = $race_data['external_id'] . '-' . $runner_number;

				$runner = $this->runner->getSelectionByExternalSelectionIdAndWageringApiId($external_selection_id, $this->wapi_id);
				if(is_null($runner)){
					$this->l('ERROR: No runner found');
					continue;
				}
				$selection_result = $this->selection_result->getSelectionResultBySelectionID($runner->id);
			
				if(is_null($selection_result)){
					$selection_result = clone $this->selection_result;
				}
			
				//$this->l('Selection id: '. $runner->id);
				$selection_result->selection_id 	= $runner->id;
				
				$update_db = false;
				$this->compareValueForUpdate($selection_result->position,$runner_data['position'],$update_db);
				$selection_result->position 		= (int) $runner_data['position'];
				
				if(isset($runner_data['win_dividend'])){
				    $this->l('>> Win Dividend: ' . $runner_data['win_dividend'] . " <<");
					$this->compareValueForUpdate($selection_result->win_dividend,$runner_data['win_dividend'],$update_db);
					$selection_result->win_dividend = (float) $runner_data['win_dividend'];
				}
				if(isset($runner_data['place_dividend'])){
					$this->compareValueForUpdate($selection_result->place_dividend,$runner_data['place_dividend'],$update_db);
					$selection_result->place_dividend = (float) $runner_data['place_dividend'];
				}
				
				if($update_db){
					$this->_save($selection_result);
					$this->l('Selection id: '. $runner->id . ' Result updated');
				}
				
				if(!is_null($existing_result_list)){
					$runner_list[] = $runner->id;
				}
			}
			
			if(!empty($runner_list)){
				foreach($existing_result_list as $result){
					if(!in_array($result->selection_id, $runner_list)){
						$selection_result->deleteSelectionResultBySelectionID($result->selection_id);
					}
				}
			}
		}
	}
	
	/**
	 * Compare Values before update DB  
	 *
	 * @param $value1,$value2,$update_db optional
	 */
	final private function compareValueForUpdate($value1,$value2,&$update_db)
	{
		if(!$update_db){
			$update_db = $value1 != $value2 ? true : false;
		}
	}
	/**
	 * Process debug parameter option  
	 *
	 * @param string $arg
	 */
	final private function _debugArgument($arg)
	{
		switch($arg){
			case 'current-message':
				$this->_showCurrentMessage();
				break;
			case 'current-status':
				$this->_showCurrentStatus();
				break;
			case 'show-message':
				$this->_showMessage();
				break;
		}
		exit;
	}
	
	/**
	 * Send a notification email
	 *
	 * @param string 	$emailAddress
	 * @param string 	$emailSubject
	*/
	private function _sendNotificationEmail($emailAddress, $emailSubject, $emailBody) {
		$mailer = new UserMAIL();
	
		$email_params	= array(
				'subject'	=> $emailSubject,
				'mailto'	=> $emailAddress
		);
		
		$mailer->sendUserEmail('combinedRaceDataImporter', $email_params, $emailBody);
	}
	
	/**
	 * Notify of an error
	 * @deprecated Done by nagios
	 * @param int $errno
	 * @param string $errstr
	 * @return boolean
	 */
	final public function notifyError($errno, $errstr)
	{
		static $mail_count = 0;
		//$support_email = $this->debug() ? 'geoff.wellman@mobileactive.com' : 'techsupport@mobileactive.com';
    	
		if($mail_count < 1){
        	//mail($support_email,'Race Data Importer Error', $errstr); 
        	$mail_count++;
		}
   
    	return false;
	}
	
	/**
	 * Make sure script writes to lock file when script exits
	 * @param int $sig
	 */
	final public function cleanExit($sig=null)
	{
		exit;
	}	
	
	/**
	 * Construct external selection id
	 * @param int $event_id
	 * @param int $selection_number
	 * @return string
	 */
	private function _formatExternalSelectionId($event_id, $selection_number){
		return $event_id . '-' . $selection_number;
	}
	
	private function _formatExternalMeetingId($date, $meeting_code){
		return implode('', explode('-', $date)) . '-' . $meeting_code;
	}
	
	private function _extractResult($data){
		preg_match('/([\d\-]+)\|(\d+)(\w*)/', $data, $match_list);
		return array('number' => $match_list[1], 'dividend' => number_format($match_list[2]/100, 2), 'status' => $match_list[3]);
	}
}

if(!defined('MEETING_DATA_IMPORT')){
	$cronjob = new RaceDataImport();
	$cronjob->debug(false);
	$cronjob->execute();
}
