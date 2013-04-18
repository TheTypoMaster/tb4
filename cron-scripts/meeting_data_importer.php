<?php
define('MEETING_DATA_IMPORT', true);
require_once('race_data_importer.php');

/**
 * MeetingDataImport Class
 * CronJob that should run every 6 hours to pull in the latest meetings
 * @author geoff
 *
 */

class MeetingDataImport extends RaceDataImport {
	public function execute(){
		$display_message = true;
		
		while($this->_checkForRunningInstance(basename(__FILE__))){
			if($display_message){
				$this->l('Importer instance already running. WAIT');
				$display_message = false;
			}
			time_nanosleep(0, 500000000);
		}
		
		$this->consumeMeetingsFile();
	}
}

$cronjob = new MeetingDataImport();
$cronjob->execute();