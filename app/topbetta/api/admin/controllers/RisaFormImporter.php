<?php namespace TopBetta\admin;

use TopBetta;

class RisaFormImporter extends \BaseController {
	
	/**
	 * Risa Form Importer.
	 *
	 * @return Response
	 */
	public function formImporter() {
		// today's date
		$today = date ( 'Ymd' );
		$localStoragePath = \Config::get('risa.localFormStoragePath');
		
		// download silks images
		$xmlFiles = $this->downloadRISASilkFTP();
		// download form xml
		$xmlFiles = $this->downloadRISAFormFTP();
		// loop on each xml file
		foreach ($xmlFiles as $fileName) {
			
			// only process if it's a file with a .xml extension
			if (pathinfo ($fileName, PATHINFO_EXTENSION) == 'xml') {
								
				// extract meeting date from filename
				$meetingDate = substr ($fileName, 0, 8);
				
				\Log::info ("RisaImport: RunnerForm: Meeting Date: " . $meetingDate . ", Today: $today." );
				// process file if it in the future
				if ($meetingDate >= $today) {
					\Log::info ("RisaImport: RunnerForm: Processing FileName: " . $fileName);
					
					// grab the file's xml contents
					$risaXML = new \SimpleXMLElement (file_get_contents ($localStoragePath . $fileName ));
					
					// get the meeting details
					$meetDate = $risaXML->MeetDate;
					$codeType = $risaXML->CodeType;
					$codeType = 'R';
					$venueName = strtoupper ($risaXML->Venue [0]->attributes ()->VenueName);
					// loop on each race
					foreach ($risaXML->Races->Race as $risaRace) {
						($risaRace->RaceNumber < 10) ? $raceNumber = '0' . $risaRace->RaceNumber : $raceNumber = $risaRace->RaceNumber;
						// echo "Race Number: $raceNumber\n";
						$raceCode = $risaRace->attributes()->RaceCode;
						
						foreach ($risaRace->RaceEntries->RaceEntry as $raceEntry ) {
							($raceEntry->TabNumber < 10) ? $runnerNumber = '0' . $raceEntry->TabNumber : $runnerNumber = $raceEntry->TabNumber;
							
							// build up the unique runner code
							$runnerCode = str_replace(" ", "", $meetDate . "-" . $codeType . "-" . $venueName . "-" . $raceNumber . "-" . $runnerNumber);
							\Log::info  ("RisaImport: RunnerForm: Processing: " . $runnerCode);
							
							// horse results summary
							$totalResultsSummaries = $raceEntry->Form->ResultsSummaries;
							//\Log::info  ("RisaImport: RunnerForm: resultsummaries: " . $totalResultsSummaries);
							
							foreach($totalResultsSummaries->ResultsSummary as $resultsSummary){
								switch($resultsSummary->attributes()->Name){
									case "TotalResults":
										$totalResults = $this->getResultsSummary($resultsSummary);
										break;
									case "FirstUp":
										$firstUp = $this->getResultsSummary($resultsSummary);
										break;
									case "SecondUp":
										$secondUp = $this->getResultsSummary($resultsSummary);
										break;
									case "ThisTrack":
										$thisTrack = $this->getResultsSummary($resultsSummary);
										break;
									case "TrackAndDistance":
										$trackDistance = $this->getResultsSummary($resultsSummary);
										break;
									case "Good":
										$goodResults = $this->getResultsSummary($resultsSummary);
										break;
									case "Dead":
										$deadResults = $this->getResultsSummary($resultsSummary);
										break;
									case "Slow":
										$slowResults = $this->getResultsSummary($resultsSummary);
										break;
									case "Heavy":
										$heavyResults = $this->getResultsSummary($resultsSummary);
										break;
									case "AtThisDistance":
										$distanceResults = $this->getResultsSummary($resultsSummary);
										break;
										
								}
							}
												
							// Check if runner code is in DB
							$runnerExists = TopBetta\RisaForm::checkForRunnerCode($runnerCode);
							// if runner code exists update that record
							if($runnerExists){
								\Log::info("RisaImport: RunnerForm: Runner Form Exists, ID:$runnerExists");
								$runnerForm = TopBetta\RisaForm::find($runnerExists);
							}else{
								\Log::info("RisaImport: RunnerForm: Runner will be added");
								$runnerForm = new TopBetta\RisaForm;
							}
							
							// add data to the model instance
							$runnerForm->race_code = $raceCode;
							$runnerForm->horse_code = $raceEntry->Horse->attributes()->HorseCode;
							$runnerForm->runner_code = str_replace(" ", "", $meetDate . "-" . $codeType . "-" . $venueName . "-" . $raceNumber . "-" . $runnerNumber);
							$runnerForm->runner_name = $raceEntry->Horse->attributes()->HorseName;
							$runnerForm->age = $raceEntry->Horse->attributes()->Age;
							$runnerForm->sex = $raceEntry->Horse->attributes()->Sex;
							$runnerForm->colour = $raceEntry->Horse->attributes()->Colour;
							$runnerForm->career_results = $totalResults;
							$runnerForm->distance_results = $distanceResults;
							$runnerForm->track_results = $thisTrack;
							$runnerForm->track_distance_results = $trackDistance;
							$runnerForm->first_up_results = $firstUp;
							$runnerForm->second_up_results = $secondUp;
							$runnerForm->good_results = $goodResults;
							$runnerForm->dead_results = $deadResults;
							$runnerForm->slow_results = $slowResults;
							$runnerForm->heavy_results = $heavyResults;
							$runnerForm->last_starts_summary = $raceEntry->Form->LastStartsSummary;
							$runnerForm->silk_image = $raceEntry->JockeySilksImage->attributes ()->FileName_NoExt;
							$runnerForm->comment = "";
						
							// save or update the runner form record
							$runnerForm->save();
						
							// Process the last starts
							$lastStartCount = $this->lastStartsImporter($raceEntry->Form->LastStarts, $runnerCode, $raceEntry->Horse->attributes()->HorseCode, $runnerForm->id);
							
						}
					}
				}
			}
		}
	}
	
	
	/**
	 * Grab last starts from XML and add the to the database
	 *
	 * @return int number of last starts processed
	 */
	private function lastStartsImporter($lastStartsXML, $runnerCode, $horseCode, $runnerId){
		
		$lastStartCount = 0;
		foreach($lastStartsXML->HorseRaceSummary as $lastStart){
			$raceCode = $lastStart->attributes()->RaceCode;
			// check if the record already exists and if so update it
			$runnerLastStartExists = TopBetta\RisaLastStarts::checkForRunnerLastStart($raceCode, $runnerCode);
			// if last start record exists update that record
			if($runnerLastStartExists){
				\Log::info("RisaImport: RunnerLastStart: Runner Last Start exists: ID:$runnerLastStartExists, RaceCode:$raceCode, RunnerCode:$runnerCode");
				$runnerLastStart = TopBetta\RisaLastStarts::find($runnerLastStartExists);
			}else{
				\Log::info("RisaImport: RunnerLastStart: Runner Last Start will be added: RaceCode:$raceCode, RunnerCode:$runnerCode");
				$runnerLastStart = new TopBetta\RisaLastStarts;
				$runnerLastStart->race_code = $lastStart->attributes()->RaceCode;
				$runnerLastStart->runner_code = $runnerCode;
			}
			
			// add data to the model instance
			$runnerLastStart->runner_form_id = $runnerId;
			$runnerLastStart->horse_code = $horseCode;
			$runnerLastStart->finish_position = $lastStart->FinishPosition;
			$runnerLastStart->race_starters = $lastStart->RaceStarters;
			$runnerLastStart->abr_venue = $lastStart->attributes()->AbrVenue;
			$runnerLastStart->race_distance = $lastStart->RaceDistance;
			$runnerLastStart->name_race_form = $lastStart->NameRaceForm;
			$runnerLastStart->mgt_date = $lastStart->attributes()->MtgDate;
			$runnerLastStart->track_condition = $lastStart->TrackCondition;
			($lastStart->TrackCondition->attributes()->NumericTrackRating > 0) ? $runnerLastStart->numeric_rating = $lastStart->TrackCondition->attributes()->NumericTrackRating : $runnerLastStart->numeric_rating = "0";
			$runnerLastStart->jockey_initials = $lastStart->Jockey->Initials;
			$runnerLastStart->jockey_surname = $lastStart->Jockey->Surname;
			$runnerLastStart->handicap = $lastStart->Handicap;
			$runnerLastStart->barrier = $lastStart->BarrierNumber;
			$runnerLastStart->starting_win_price = str_replace("$", "", $lastStart->StartingWinPrice);
			$runnerLastStart->other_runner_name = $lastStart->OtherRunners[0]->OtherRunner->attributes()->HorseName;
			($lastStart->OtherRunners[0]->OtherRunner->attributes()->Barrier != NULL) ? $runnerLastStart->other_runner_barrier = $lastStart->OtherRunners[0]->OtherRunner->attributes()->Barrier : $runnerLastStart->other_runner_barrier = "0" ;
			
			foreach($lastStart->InRunning as $inRunning){
				($inRunning->attributes()->Distance == 800) ? $runnerLastStart->in_running_800 = $inRunning->attributes()->Position : $runnerLastStart->in_running_800 = 0;
				($inRunning->attributes()->Distance == 400) ? $runnerLastStart->in_running_400 = $inRunning->attributes()->Position : $runnerLastStart->in_running_400 = 0;
			}
			
			($lastStart->OtherRunners[0]->OtherRunner->attributes()->Time != NULL) ? $runnerLastStart->other_runner_time = $lastStart->OtherRunners[0]->OtherRunner->attributes()->Time : $runnerLastStart->other_runner_time = "";
			$runnerLastStart->margin_decimal = $lastStart->MarginDecimal;
			
			$runnerLastStart->save();
			$lastStartCount++;
		}
		\Log::info("RisaImport: RunnerLastStart: LastStarts Processed:$lastStartCount");
		return $lastStartCount;
	}
	
	/**
	 * Download RISA XML files from the FTP server
	 *
	 * @return array of files to process
	 */
	private function downloadRISAFormFTP() {
		
		// get ftp details from config
		$ftpUserName = \Config::get('risa.ftpUserName');
		$ftpPassword =\Config::get('risa.ftpPassword');
		$ftpIP = \Config::get('risa.ftpIP');
		$ftpPath = \Config::get('risa.ftpFormPath');
		$localStoragePath = \Config::get('risa.localFormStoragePath');
		$wgetPath = \Config::get('risa.wgetPath');
		
		// mirror the RISA ftp site with wget - // TODO: check out native way to do this
		$cmd = $wgetPath."wget --mirror -nd -nv -P " . $localStoragePath . " --ftp-user=" . $ftpUserName . " --ftp-password=" . $ftpPassword . " \"$ftpIP/$ftpPath\" 2>&1";
		$output = shell_exec ( $cmd );
		\Log::info( "RisaImport: FORM Wget Mirror Output: " . $output);
		
		// get the list of files in the data directory
		return array_diff ( scandir ( $localStoragePath ), array (
				'..',
				'.',
				'.listing' 
		) );
	}
	
	/**
	 * Download RISA Silks files from the FTP server
	 *
	 * @return array of files to process
	 */
	private function downloadRISASilkFTP() {
	
		// get ftp details from config
		$ftpUserName = \Config::get('risa.ftpUserName');
		$ftpPassword =\Config::get('risa.ftpPassword');
		$ftpIP = \Config::get('risa.ftpIP');
		$ftpPath = \Config::get('risa.ftpSilkPath');
		$localStoragePath = \Config::get('risa.localSilkStoragePath');
		$wgetPath = \Config::get('risa.wgetPath');
	
		// mirror the RISA ftp site with wget - // TODO: check out native way to do this
		$cmd = $wgetPath."wget --mirror -nd -nv -P " . $localStoragePath . " --ftp-user=" . $ftpUserName . " --ftp-password=" . $ftpPassword . " \"$ftpIP/$ftpPath\" 2>&1";
		$output = shell_exec ( $cmd );
		\Log::info( "RisaImport: SILK Wget Mirror Output: " . $output);
	
		// get the list of files in the data directory
		return array_diff ( scandir ( $localStoragePath ), array (
				'..',
				'.',
				'.listing'
		) );
	}
	
	
	/**
	 * Grag results/form from XML and return formatted
	 *
	 * @return string formatted with results
	 */
	private function getResultsSummary($resultsSummary){
		(isset($resultsSummary->attributes()->Starts)) ? $starts = $resultsSummary->attributes()->Starts : $starts = 0;
		(isset($resultsSummary->attributes()->Wins)) ? $wins = $resultsSummary->attributes()->Wins : $wins = 0;
		(isset($resultsSummary->attributes()->Seconds)) ? $seconds = $resultsSummary->attributes()->Seconds : $seconds = 0;
		(isset($resultsSummary->attributes()->Thirds)) ? $thirds = $resultsSummary->attributes()->Thirds : $thirds = 0;
		return $starts."(".$wins."-".$seconds."-".$thirds.")";
	}
	
	
	private function cleanUpRISADB() {
	}
}