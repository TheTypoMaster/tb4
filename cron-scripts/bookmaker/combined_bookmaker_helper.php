<?php 
class Bookmaker	{
	/**
	* Topbetta Bookmaker.com.au API Helper Class
	* 
	* Integrating new Bookmaker API with existing bet processing scripts.
	* - get main list
	* - add competitor data into resultant array
	* - use this combined array to construct two csv's depending on what is selected
	* - invoke method in main consumer methods to import new data through existing betting scripts with minimal impact.
	*
	* @author  Patrick Muzyk
	* 
	*/
	
	const RACELIST_JSON 	= 'http://www.bookmaker.com.au/api/feed/racingList';
	//const RACELIST_JSON 	= 'http://mugbookie.com/racingList.json';
	const COMPETITOR_JSON 	= 'http://www.bookmaker.com.au/api/feed/eventRunners?event_id=';
	
	public $meetings_output = null;
	public $details_output = null;
	public $csv_output = null;
	public $default_tote = "midtote";
	
	/**
	* get Race List input data for CSV emulator
	* @author	Patrick Muzyk
	* @return	object
	* 
	*/
	private function getRaceListData(){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::RACELIST_JSON);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($sh, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($sh, CURLOPT_TIMEOUT, 120);
		$data = json_decode(curl_exec($ch));
		curl_close($ch);

		return $data;
	}
	
	/**
	* Get Competitor List input data for CSV emulator
	* @author	Patrick Muzyk
	* @param	string
	* @return	object
	* 
	*/
	private function getCompetitorListData($event_id=null){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::COMPETITOR_JSON . $event_id);
		curl_setopt($sh, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($sh, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$data = json_decode(curl_exec($ch));
		curl_close($ch);
		$array = json_decode(json_encode($data), true);
		return $array;
	}
	
	/**
	* Emulate The Odds Broker CSV
	* @author	Patrick Muzyk
	*
	* @param	string
	* @return	string
	* 
	*/
	public function emulateCSV(){
		error_reporting('E_ALL');		
		
		// iterate through each meeting object
		$time = "[". date('r') ."]";
		$processPID = getmypid();
		$prefix = '[PID:'.$processPID . '] ';
		$suffix =  "\n";
		$message = "[API] Getting race list data...";
		echo sprintf('%s %s%s %s', $time, $prefix, $message, $suffix);
		
		$data = $this->getRaceListData();
		$time = "[". date('r') ."]";
		$message = "[API] Complete";
		echo sprintf('%s %s%s %s', $time, $prefix, $message, $suffix);
		
		function sortmeetings($a, $b) {
			return $a['Meeting'] > $b['Meeting'] ? 1 : -1;
		}
		
		function sortracenum($a, $b) {
			return $a['RaceNum'] > $b['RaceNum'] ? 1 : -1;
		}
		// Sort JSON decoded array by meeting venue to group venues into alphabetical order.
		$array = json_decode(json_encode($data), true);
		usort($array, "sortmeetings");
		
		$meetings = array();
		$lastMeeting = null;
		
		// Refactor the array and make it more familiar with what race_data_importer expects.
		foreach($array as $meeting){
			//echo "Race: ".$meeting['OutcomeDateTime']." ".$meeting['Meeting'];

			if($meeting['RaceType'].$meeting['Meeting']. implode('', explode('-', $meeting['EffectiveRaceDate'])) != $lastMeeting){
				
				if($meeting['RaceType'] == "T")
				$meeting['RaceType'] = "R";

				if($meeting['RaceType'] == "H")
				$meeting['RaceType'] = "T";
				
				//print(strtoupper($meeting['Meeting'])."\n");
				//print($meeting['RaceType']);

			    //not the same meeting name as before? start new meeting array
			    $meetings[strtoupper($meeting['RaceType'].$meeting['Meeting']. implode('', explode('-', $meeting['EffectiveRaceDate'])))]['meeting'] = array(
				    'date' => $meeting['EffectiveRaceDate'],
				    'venue' => strtoupper($meeting['Meeting']),
				    'RaceType' => $meeting['RaceType'],
				    'eventid' => $meeting['EventID'],
					'results' => $meeting['Results'],
					'dividends' => $meeting['Dividends'],
					'runnerproducts' => $meeting['Dividends']['RunnerProducts']
				    );
			    
			    $meetings[strtoupper($meeting['RaceType'].$meeting['Meeting']. implode('', explode('-', $meeting['EffectiveRaceDate'])))]['raceList'][] = $meeting;
			} else {
			    //continue array
			    $meetings[strtoupper($meeting['RaceType'].$meeting['Meeting']. implode('', explode('-', $meeting['EffectiveRaceDate'])))]['raceList'][] = $meeting;
			    //grab last meeting value for difference
			    $lastMeeting = $meeting['RaceType'].$meeting['Meeting']. implode('', explode('-', $meeting['EffectiveRaceDate']));
			    
			}
		}
	
		// Arrange subarrays by race number per meeting array
		$ordered_meetings = array();
		print "Getting Competitors 0%\r";
		$percentage = 0;
		$i=0;
		$total = count($meetings);
		$time = "[". date('r') ."]";
		$message = "[API] Getting competitor list data for '$total' meetings...";
		echo sprintf('%s %s%s %s', $time, $prefix, $message, $suffix);
		foreach($meetings as $meeting_key => $meeting_array){
			//sort races by race number
			usort($meeting_array['raceList'], "sortracenum");
			
			// Re-establish our glorious array
			$ordered_meetings[$meeting_key] = $meeting_array;
			
			// fetch competitor lists for each race
			
			$percentage = ceil(($i/$total)*100);
			print "Getting Competitors ".$percentage."%\r";

			foreach($meeting_array['raceList'] as $race => $val){
				
				$competitors = $this->getCompetitorListData($val['EventID']);
				
				
			    $ordered_meetings[$meeting_key]['raceList'][$race]['competitors'] = $competitors[$val['EventID']]['competitors'];
    				
			}
			
			$i++;
		}
		$time = "[". date('r') ."]";
		$message = "[API] Complete";
		echo sprintf('%s %s%s %s', $time, $prefix, $message, $suffix);
		print "\n";
		
		// if requested, process meetings
		// if($output_type=="meetings"){
		    //Process CSV to emulate meetings.txt
		    $meetings = null;
			$i = 0;		      
			foreach($ordered_meetings as $meeting){ // Each event or Meeting
			  
			  $meetings .= date("Y-m-d", strtotime($meeting['meeting']['date'])).",".
					strtoupper($meeting['meeting']['venue']).",".
					$meeting['meeting']['RaceType'].",".
					str_replace(" ", "", $meeting['meeting']['venue']).$meeting['meeting']['RaceType'].",".
					str_replace(" ", "", $meeting['meeting']['venue']).$meeting['meeting']['RaceType'].",".
					str_replace(" ", "", $meeting['meeting']['venue']).$meeting['meeting']['RaceType'].",\n";

			  foreach($meeting['raceList'] as $race) { // Each race
				if(strtoupper($race['Status']) == "INTERIM" || strtoupper($race['Status']) == "PAYING"){
					$meetings .= "R".$race['RaceNum'].",".date("Y-m-d H:i:s", strtotime($race['SuspendDateTime'])).".0".",".$race['EventID'].",";
					$meetings .= $race['Distance'].",".$race['TrackCondition'].",".$race['Weather']."\n";
				} else {
					$meetings .= "R".$race['RaceNum'].",".date("Y-m-d H:i:s", strtotime($race['OutcomeDateTime'])).".0".",".$race['EventID'].",";
					$meetings .= $race['Distance'].",".$race['TrackCondition'].",".$race['Weather']."\n";
				}

				foreach($race['competitors'] as $competitor_id => $competitor){  // Each Competitor
					$scratched = ($competitor['Scratched']=="yes") ? 's' : '';
					
					$meetings .= 	$competitor['Saddle'].",".
							$competitor['Barrier'].",".
							strtoupper($competitor['Name']).",".
							strtoupper($competitor['Jockey']).",".
							$competitor['Weight'].",".
							$scratched.",".
							$competitor['RisaSilkID'].",".
							$competitor_id."\n";
					
				}
				
			  }
			  /* TODO: later remove this line below when data is importing and simplfify this method */
			  $meetings .= "---------------\n";
			
		      }
			$this->meetings_output = $meetings;
			
		//}
		
		
		
		
		// if($output_type=="details"){
			
			$details_list = null;

			foreach($ordered_meetings as $details){
			     // if(strtoupper($details['meeting']['venue']) != strtoupper("Mornington"))
			      //	continue;

			      // Output Venue line
			      $details_list .= 	date("Y-m-d", strtotime($details['meeting']['date'])).",".
						strtoupper($details['meeting']['venue']).",".
						$details['meeting']['RaceType'].",".
						str_replace(" ", "", $details['meeting']['venue']).$details['meeting']['RaceType'].",".
						str_replace(" ", "", $details['meeting']['venue']).$details['meeting']['RaceType'].",".
						str_replace(" ", "", $details['meeting']['venue']).$details['meeting']['RaceType']."\n";
				
			      foreach($details['raceList'] as $race) { 
					// Race line
					$race_status_all = ($race['Abandoned'] == 1) ? "ABANDONED" : strtoupper($race['Status']);
					$details_list .= "R".$race['RaceNum'].",".date("Y-m-d H:i:s", strtotime($race['OutcomeDateTime'])).".0".",".$race_status_all.",".$race['EventID']."\n";
					$w_odds = null;
					$p_odds = null;
					$w_results = null;
					$p_results = null;
					$tote_type = null;
					
					/* Ideal Process */
					// determine tote_type valid for race with first competitor
					// get odds data with the tote_type for this race
					// get win/place data with tote_type
					// get exotics with tote_type
					// check to see if results have been given and apply to the end of win/place and exotics rows.

					// Determine odds
					foreach($race['competitors'] as $competitor){ 				
						$w_tote_odds = null;
						$p_tote_odds = null;
						$w_tote_type = null;
						$p_tote_type = null;
						/*
						Determine tote type	
						*/
						// Win
						if($competitor['DetailedPricing']['bestOrSP'] == "true") {
							$w_tote_type = array('name' =>"bestorsp", 'bm_code'=>"bestOrSP");
							$w_tote_odds = $competitor['DetailedPricing'][$w_tote_type['bm_code'].'Price'];
						} else if($competitor['DetailedPricing']['topToteWin'] == "true"){ // toptote
							$w_tote_type = array('name' =>"toptote", 'bm_code'=>"topTote");
							$w_tote_odds = $competitor['DetailedPricing'][$w_tote_type['bm_code'].'WinPrice'];
						} else { // midtote
							$w_tote_type = array('name' =>"midtote", 'bm_code'=>"midTote");
							$w_tote_odds = $competitor['DetailedPricing'][$w_tote_type['bm_code'].'WinPrice'];
						}

						// Place
						if($competitor['DetailedPricing']['topTotePlace'] == "true") {
							$p_tote_type = array('name' =>"toptote", 'bm_code'=>"topTote");
							$p_tote_odds = $competitor['DetailedPricing'][$p_tote_type['bm_code'].'PlacePrice'];
						} else { // midtote
							$p_tote_type = array('name' =>"midtote", 'bm_code'=>"midTote");
							$p_tote_odds = $competitor['DetailedPricing'][$p_tote_type['bm_code'].'PlacePrice'];
						} 

						$w_odds .= $competitor['Saddle'].",".($w_tote_odds * 100)."\n";
						$p_odds .= $competitor['Saddle'].",".($p_tote_odds * 100)."\n";

					}
									
					// Get results if active
					if(strtoupper($race['Status']) == "PAYING"){
												
						//get positions with saddle numbers
						$positions = array();
						$p = 0;
						foreach($race['Results'] as $result){
							$positions[$result['position'].$p] = $result['saddle_number'];
							$p +=1;
						}
						
						
						// sort positions 
						ksort($positions);
										
						foreach($positions as $position => $saddle_number){
							
							if($w_tote_type['name']=="bestorsp") {
								$w_value = $race['Dividends']['RunnerProducts'][$saddle_number]['BestOrSP']*100;
								if ($w_value > 0) $w_results .= ",".$saddle_number."|".$w_value."P"; // TBA bestofBest results
							} elseif($w_tote_type['name']=="toptote") { // Toptote
								$w_value = $race['Dividends']['RunnerProducts'][$saddle_number]['TopeToteWin']*100;
								if ($w_value > 0) $w_results .= ",".$saddle_number."|".$w_value."P";
							} elseif($w_tote_type['name']=="midtote") { // Midtote
								$w_value = $race['Dividends']['RunnerProducts'][$saddle_number]['MidToteWin']*100;
								if ($w_value > 0) $w_results .= ",".$saddle_number."|".$w_value."P";
							}
							//break;
						}
						
						foreach($positions as $position => $saddle_number){
							// Get places
							if((int)$position < 40)
							{
								if($p_tote_type['name']=="toptote"){
									$value = $race['Dividends']['RunnerProducts'][$saddle_number]['TopeTotePlace']*100;
									$p_results .= ",".$saddle_number."|".$value."P";
									
								} elseif($p_tote_type['name']=="midtote") { // midtote
									$value = $race['Dividends']['RunnerProducts'][$saddle_number]['MidTotePlace']*100;
									$p_results .= ",".$saddle_number."|".$value."P";
							
								}	
							}
						}
					
					}

					//put together resultant race data
					//W Pool line, Result
					$details_list .= "W,".$this->default_tote.",".$w_tote_type['name'].",".$race['EventID']."W,0,0,".date("Y-m-d H:i:s", strtotime($race['OutcomeDateTime'])).".0".$w_results."\n";					
					//Odds lines
					$details_list .= $w_odds;
					//P Pool line, Result
					$details_list .= "P,".$this->default_tote.",".$p_tote_type['name'].",".$race['EventID']."P,0,0,".date("Y-m-d H:i:s", strtotime($race['OutcomeDateTime'])).".0".$p_results."\n";
					//Odds lines
					$details_list .= $p_odds;

					// Output exotics
					/*$duets = null;
					$result_du = 0;*/

					$pool_types = array(
						"Q" => "Quinella",
						"T" => "Trifecta",
						"FF" => "FirstFour",
						"E" => "Exacta"
					);

					foreach($pool_types as $key => $name){
						$result = null;
						$outcome = null;

						if(strtoupper($race['Status']) == "PAYING"){
							foreach($race['Dividends'] as $tote){
								if($tote['final_dividend'] != null){
									if($tote['pool_type'] == $name && $tote['source'] == "VIC"){
										$outcome = str_replace("/","-",$tote['outcome']);
										$result .= ",".$outcome."|".($tote['final_dividend']*100)."P";
									}
								}
							}
						}
						//no results yet? then output the line, otherwise append results
						$details_list .= $key.",SuperTab,SuperTab,".$race['EventID'].$key.",0,0,".date("Y-m-d H:i:s", strtotime($race['OutcomeDateTime'])).".0".$result."\n";
					
					}

					//foreach($race['Dividends'] as $tote){
						/*$result_q = null;
						$result_t = null;
						$result_ff = null;
						$result_e = null;*/
						
						/*if($tote_type['bm_code'] != "VIC"){ //Midtote and Toptote Exotics

						} else{*/
							


							

							/*if($tote['pool_type'] == "Trifecta"){
								if($tote['final_dividend'] != null && strtoupper($race['Status']) == "PAYING"){
									$outcome = str_replace("/","-",$tote['outcome']);
									$result_t = ",".$outcome."|".($tote['final_dividend']*100)."P";
								}
								$details_list .= "T,".$tote_type['name'].",".$race['EventID']."T,0,0,".date("Y-m-d H:i:s", strtotime($race['OutcomeDateTime'])).".0".$result_t."\n";
							}
							if($tote['pool_type'] == "FirstFour"){
								if($tote['final_dividend'] != null && strtoupper($race['Status']) == "PAYING"){
									$outcome = str_replace("/","-",$tote['outcome']);
									$result_ff = ",".$outcome."|".($tote['final_dividend']*100)."P";
								}
								$details_list .= "FF,".$tote_type['name'].",".$race['EventID']."FF,0,0,".date("Y-m-d H:i:s", strtotime($race['OutcomeDateTime'])).".0".$result_ff."\n";
							}
							if($tote['pool_type'] == "Exacta"){
								if($tote['final_dividend'] != null && strtoupper($race['Status']) == "PAYING"){
									$outcome = str_replace("/","-",$tote['outcome']);
									$result_e = ",".$outcome."|".($tote['final_dividend']*100)."P";
								}
								$details_list .= "E,".$tote_type['name'].",".$race['EventID']."E,0,0,".date("Y-m-d H:i:s", strtotime($race['OutcomeDateTime'])).".0".$result_e."\n";
							}*/
							/*if($tote['pool_type'] == "Duet"){
								if($tote['final_dividend'] != null && strtoupper($race['Status']) == "PAYING"){
									$outcome = str_replace("/","-",$tote['outcome']);
									$duets .= ",".$outcome."|".($tote['final_dividend']*100)."P";
								}
								$result_du = 1;
								
							}*/
						//}
					//}
					/*if($result_du != 0){
						$details_list .= "DU,".$tote_type['name'].",".$race['EventID']."DU,0,0,".date("Y-m-d H:i:s", strtotime($race['OutcomeDateTime'])).".0".$duets."\n";
					}*/
					
			    }
				
				
			      $details_list .= "---------------\n";
			}
			$this->details_output = $details_list;
			
			/*
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: application/octet-stream");
			header ("Content-disposition: attachment; filename=tournament-dollars-transactions-" . date("Ymd") . ".csv");
			header("Content-Transfer-Encoding: binary");
		
			echo $this->details_output;
			exit;
			*/
			
		// }
	
		// create ouput array and pipe it out to the consume methods in race_data_importer.
		
	}

	public function outputTest()
	{
		$this->emulateCSV('details');		
		//print_r($this->details_output);
	}
	
}
/*
$dat = new Bookmaker;
$dat->outputTest();
*/
