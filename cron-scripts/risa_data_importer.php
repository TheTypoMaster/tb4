<?php
require_once '../common/shell-bootstrap.php';

class RisaDataImporter extends TopBettaCLI{

	/**
	 * Lock file path/name
	 *
	 * @var string
	 */
	const LOCK_FILE = '/tmp/risa_data_importer.lck';

	const DATA_LOCATION = 'risa_data/';
	
	final public function initialise(){
		// Set the database access information as constants.
		$dbconfig = $this->getConfigSection('database');
		
		//if("dbtb01"==(string)$dbconfig->database->attributes()->name){
		DEFINE ('DB_USER', (string)$dbconfig->database->user);
		DEFINE ('DB_PASSWORD', (string)$dbconfig->database->password);
		DEFINE ('DB_HOST', (string)$dbconfig->database->host);
		DEFINE ('DB_NAME', (string)$dbconfig->database->name); 
	
		
		// Make the connnection and then select the database.
		$dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) OR Die ('Could not connect to MySQL.');
		mysql_select_db (DB_NAME) OR Die ('Could not select the database.');
		
	}
	
	public function execute()
	{
		$display_message = true;
			
		if(!$this->arg('debug')){
			while($this->_checkForRunningInstance(basename(__FILE__))){
				if($display_message){
					$this->l('RISA Importer instance already running. WAIT');
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
			// update silk images
			$this->updateSilkImages();
			
			//update DB with silk names and last starts
			$this->processFutureRisaData();
		
		}
	}
	
	
	function processRisaData() {
		
		$debug = false;
		// pull down the latest files
		$url = 'ftp://116.240.194.141/Top Betta/Risa XML 3.5/';
		$outputfile = "dl.html";
		$cmd = "wget --mirror -nd -nv -P ".self::DATA_LOCATION." --ftp-user=topbetta --ftp-password=topracing \"$url\" 2>&1";
		$output = shell_exec($cmd);
		$this->l("Output: ".$output);
		
		// sync the risa silks as well
	    //$silksCmd = "wget --mirror -nd -nv -P /mnt/data/sites/www.topbetta.com.au/silks --ftp-user=topbetta --ftp-password=topracing ftp://116.240.194.141/Top%20Betta/Jockey%20silks/library/ 2>&1" ;
	    ////$output = shell_exec($silksCmd);
	   // $this->l("Output: ".$output);
	   
		// get the list of files in the data directory
		//$dataDirectory = '/home/oshan/git/tb4/cron-scripts/risa_data';
		$xmlFiles = array_diff(scandir(self::DATA_LOCATION), array('..', '.', '.listing'));

		// loop on each file in directory
		foreach($xmlFiles as $fileName){
			if(pathinfo($fileName, PATHINFO_EXTENSION) == 'xml'){
				$this->l("Processing FileName: ".$fileName);

				// Check if filename is in DB
				$fileNameExist = "SELECT id from `tb_racing_data_risa_list` where `file_name` = '$fileName' LIMIT 1";
				if ($debug){
					$this->l("FileName Exists table query: $fileNameExist");
				}
				$fileNameExistResult = mysql_query($fileNameExist);
				$fileNameExistnum_rows = mysql_num_rows($fileNameExistResult);
								
				// if not add it
				if ($fileNameExistnum_rows){
					$this->l("Already in DB");
					$row = mysql_fetch_array($fileNameExistResult);
					$fileNameID = $row['id'];
				}else {
					// add new file_name
					$fileNameQuery = " INSERT INTO `tb_racing_data_risa_list` (`id`, `file_name` ) VALUES ";
					$fileNameQuery .= " ('', '$fileName' ); ";
					mysql_query($fileNameQuery);
					$fileNameID = mysql_insert_id();
					$this->l("Added to DB");
					if ($debug){
						$this->l("ADD event_group table query: $fileNameQuery");
					}
				}
			}
		}
		
		// query to get the list of unprocessed files
		$unprocessedFiles = "SELECT * from `tb_racing_data_risa_list` where `processed` = 0";
		if ($debug){
			$this->l("Unprocessed Files table query: $unprocessedFiles");
		}
		
		// query the DB
		$unprocessedFilesResult = mysql_query($unprocessedFiles);
		
		// loop on each result
		while ($row = mysql_fetch_array($unprocessedFilesResult, MYSQL_ASSOC)) {
			//print_r($row);
			$xmlFile = self::DATA_LOCATION . $row['file_name'];
			$xmlFileID = $row['id'];
			// echo "Working on : ". $xmlFile . "\n";
			$risaXML = new SimpleXMLElement(file_get_contents($xmlFile));
			
			// get the meeting details
			$meetDate = $risaXML->MeetDate;
			$codeType = $risaXML->CodeType;
			$codeType = 'R';
			$venueName = strtoupper($risaXML->Venue[0]->attributes()->VenueName);
			
			// loop on each race
			foreach ($risaXML->Races->Race as $risaRace){
				($risaRace->RaceNumber < 10) ? $raceNumber = '0' . $risaRace->RaceNumber : $raceNumber = $risaRace->RaceNumber;
				echo "Race Number: $raceNumber\n";
				foreach($risaRace->RaceEntries->RaceEntry as $raceEntry){
					($raceEntry->TabNumber < 10) ? $runnerNumber = '0' . $raceEntry->TabNumber : $runnerNumber = $raceEntry->TabNumber;
					//echo "Runner Number: $runnerNumber\n";
					$silkFileName = $raceEntry->JockeySilksImage->attributes()->FileName_NoExt;
					$lastStarts = $raceEntry->Form->LastStartsSummary;
					//echo "Silk File Name: $silkFileName\n";
					
					$runnerCode = $meetDate."-".$codeType."-".$venueName."-".$raceNumber."-".$runnerNumber;
					$this->l("Processing: ".$runnerCode." Silk:$silkFileName, LastStarts:$lastStarts");
					
					// Check if filename is in DB
					$runnerCodeExist = "SELECT id from `tb_racing_data_risa_silk_map` where `runner_code` = '$runnerCode' LIMIT 1";
					if ($debug){
						$this->l("Runner Code Exists table query: $runnerCodeExist");
					}
					
					$RunnerCodeExistResult = mysql_query($runnerCodeExist);
					$RunnerCodeExistnum_rows = mysql_num_rows($RunnerCodeExistResult);
					// if not add it
					if ($RunnerCodeExistnum_rows){
						$this->l("Already in DB");
						$row = mysql_fetch_array($RunnerCodeExistResult);
						$fileNameID = $row['id'];
					}else {
						
						$nowTime = date("Y-m-d H:i:s");
						// add new file_name
						$silkQuery = " INSERT INTO `tb_racing_data_risa_silk_map` (`id`, `runner_code`, `silk_file_name`, `last_starts`, `created_at`, `updated_at`) VALUES ";
						$silkQuery .= " ('', '$runnerCode', '$silkFileName', '$lastStarts', '$nowTime', '$nowTime'); ";
						mysql_query($silkQuery);
						$silkID = mysql_insert_id();
						$this->l("Added to DB");
						if ($debug){
							$this->l("Add Silk table Query: $silkQuery");
						}
					}
				}
			}
			
			// Mark the file as processed
			$updateQuery = " UPDATE `tb_racing_data_risa_list` SET `processed` = 1 WHERE id = '$xmlFileID'";
			mysql_query($updateQuery);
			$this->l("File Marked Processed: $updateQuery");
		}
	}

	
	function processFutureRisaData(){
		
		// pull down the latest files fro  the RISA FTP site
		$url = 'ftp://116.240.194.141/Top Betta/Risa XML 3.5/';
		$outputfile = "dl.html";
		$cmd = "wget --mirror -nd -nv -P ".self::DATA_LOCATION." --ftp-user=topbetta --ftp-password=topracing \"$url\" 2>&1";
		$output = shell_exec($cmd);
		$this->l("Output: ".$output);
		
		// get the list of files in the data directory
		$xmlFiles = array_diff(scandir(self::DATA_LOCATION), array('..', '.', '.listing'));
		
		// today's date
		$today = date('Ymd');
		
		// loop on each xml file
		foreach($xmlFiles as $fileName){
			
			// only process if it's a file with a .xml extension
			if(pathinfo($fileName, PATHINFO_EXTENSION) == 'xml'){
				$this->l("Processing FileName: ".$fileName);
			
				// extract meeting date from filename
				$meetingDate = substr($fileName, 0, 7);
				
				// process file if it in the future
				if ($meetingDate >= $today){
					
					// grab the file's xml contents
					$risaXML = new SimpleXMLElement(file_get_contents(self::DATA_LOCATION . $fileName));
						
					// get the meeting details
					$meetDate = $risaXML->MeetDate;
					$codeType = $risaXML->CodeType;
					$codeType = 'R';
					$venueName = strtoupper($risaXML->Venue[0]->attributes()->VenueName);
						
					// loop on each race
					foreach ($risaXML->Races->Race as $risaRace) {
						($risaRace->RaceNumber < 10) ? $raceNumber = '0' . $risaRace->RaceNumber : $raceNumber = $risaRace->RaceNumber;
						echo "Race Number: $raceNumber\n";
						foreach ($risaRace->RaceEntries->RaceEntry as $raceEntry) {
							($raceEntry->TabNumber < 10) ? $runnerNumber = '0' . $raceEntry->TabNumber : $runnerNumber = $raceEntry->TabNumber;
							
							$silkFileName = $raceEntry->JockeySilksImage->attributes ()->FileName_NoExt;
							$lastStarts = $raceEntry->Form->LastStartsSummary;

							// build up the unique runner code
							$runnerCode = $meetDate . "-" . $codeType . "-" . $venueName . "-" . $raceNumber . "-" . $runnerNumber;
							$this->l ("Processing: " . $runnerCode . " Silk:$silkFileName, LastStarts:$lastStarts");
							
							// Check if runner code is in DB
							$runnerCodeExist = "SELECT id from `tb_racing_data_risa_silk_map` where `runner_code` = '$runnerCode' LIMIT 1";
							$RunnerCodeExistResult = mysql_query ($runnerCodeExist );
							$RunnerCodeExistnum_rows = mysql_num_rows ($RunnerCodeExistResult);
							
							// if exists update record
							if ($RunnerCodeExistnum_rows) {
								$this->l ( "Already in DB" );
								$row = mysql_fetch_array ($RunnerCodeExistResult);
								$fileNameID = $row ['id'];
								$updateQuery  = " UPDATE tb_racing_data_risa_silk_map ";
								$updateQuery .= " SET silk_file_name = '$silkFileName', last_starts = '$lastStarts', updated_at = NOW()";
								mysql_query ( $updateQuery );
								if ($debug) {
									$this->l ( "Update Silk table Query: mysql_query" );
								}
							// otherwise add a record
							} else { 
								$nowTime = date ( "Y:m:d:H:i:s" );
								$silkQuery = " INSERT INTO `tb_racing_data_risa_silk_map` (`id`, `runner_code`, `silk_file_name`, `last_starts`, `created_at`, `updated_at`) VALUES ";
								$silkQuery .= " ('', '$runnerCode', '$silkFileName', '$lastStarts', '$nowTime', '$nowTime'); ";
								mysql_query ( $silkQuery );
								$silkID = mysql_insert_id ();
								$this->l ( "Added to DB" );
								if ($debug) {
									$this->l ( "Add Silk table Query: $silkQuery" );
								}
							}
						}
					}
				}
			}
		}
	}
	
	function updateSilkImages(){
		// sync the risa silks images
		$silksCmd = "wget --mirror -nd -nv -P /mnt/data/sites/www.topbetta.com.au/silks --ftp-user=topbetta --ftp-password=topracing ftp://116.240.194.141/Top%20Betta/Jockey%20silks/library/ 2>&1" ;
		$output = shell_exec($silksCmd);
		$this->l("Silk Images Update: ".$output);
	}
	
	
	function cleanUpRisaData(){
		
	}
	
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
				$path  .= $sl . 'mnt' . $sl . 'web' . $sl . 'server_igas.xml';
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
			$xml = simplexml_load_file('/mnt/web/server_igas.xml');
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

}

$risaImport = new RisaDataImporter();
$risaImport->debug(true);
$risaImport->execute();



?>