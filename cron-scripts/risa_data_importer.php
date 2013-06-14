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
		//$dbconfig = $this->getConfigSection('oldatabase');
		
		//if("dbtb01"==(string)$dbconfig->database->attributes()->name){
/* 		DEFINE ('DB_USER', (string)$dbconfig->database->user);
		DEFINE ('DB_PASSWORD', (string)$dbconfig->database->password);
		DEFINE ('DB_HOST', (string)$dbconfig->database->host);
		DEFINE ('DB_NAME', (string)$dbconfig->database->name); */
				
		DEFINE ('DB_USER', 'topbetta_testing');
		DEFINE ('DB_PASSWORD', 'mysqlp@ss');
		DEFINE ('DB_HOST', 'localhost');
		DEFINE ('DB_NAME', 'topbetta_igas');
		
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
			$this->processRisaData();
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
						// add new file_name
						$silkQuery = " INSERT INTO `tb_racing_data_risa_silk_map` (`id`, `runner_code`, `silk_file_name`, `last_starts`) VALUES ";
						$silkQuery .= " ('', '$runnerCode', '$silkFileName', '$lastStarts'); ";
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
			$xml = simplexml_load_file('/mnt/web/server.xml');
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