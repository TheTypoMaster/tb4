<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelImportMarket extends JModel
{
	const XML_ROOT = 'http://tatts.com/pagedata/';
	private $xml_doc = null;
	private $excluded_markets = array('Double Chance', 'Asian Handicap', 'Draw No Bet', '1st Batting Dismissal', '1st Over Runs', 'Next Man Out');

	/**
	 * Function to get market from the feed by external meeting id & external match Id
	 * @param $id to check
	 * Return Array
	 */
	public function getImportMarketListByExternalMeetingIDAndExternalMatchID($meeting_id, $match_id){

		if($match_id > 0 && $meeting_id > 0){
//			$competition_model  = JModel::getInstance('TournamentCompetition', 'TournamentModel');
//			$competition 		= $competition_model->getTournamentCompetition($competition_id);
//
//			$sport_model  		= JModel::getInstance('SportMap', 'TournamentModel');
//			$sport				= $sport_model->getTournamentSportByTournamentSportID($competition->tournament_sport_id);

			$file_path 		= self::XML_ROOT . "sports/meetings/meeting{$meeting_id}.xml";
			$simple_xml		= null;
			
			if (TournamentHelper::remoteFileExists($file_path)) {
				$simple_xml = simplexml_load_file($file_path);
			}

			if ($simple_xml) { //--load only if there is any contents from the xml
				$markets = $simple_xml->xpath("//Sport/League/Meeting/MainEvent[@MainEventId='$match_id']/SubEvent");

				if($markets){
					foreach ($markets as $market){
						$market_details = $this->getBetTypeDetailsByMarketId((int) $market->attributes()->SubEventId);
						
						if(!empty($market_details) && !in_array($market_details['name'], $this->excluded_markets)){
							$ext_market[] = array(
								"external_market_id" 	=> (int) $market->attributes()->SubEventId,
								"name"		   			=> $market_details['name'],
								"description"  			=> $market_details['description']
							);
						}
					}

					return $ext_market;
				}
			}
		}
	}

	/**
	 * Private function to get correct name from subevent xml
	 * @param $market_id
	 * return string
	 */
	private function getBetTypeDetailsByMarketId($market_id){

		$market_details = array();
		
		if($market_id){
			$file_path 		= self::XML_ROOT . "sports/subevents/subevent{$market_id}.xml";
			if ($simple_xml = simplexml_load_file($file_path)) {

				$market = $simple_xml->xpath("//Sport/League/Meeting/MainEvent/SubEvent/BetType");
	
				$market_details = array(
									'name'	=> (string) $market[0]->attributes()->BetTypeName,
									'description' => (string) $market[0]->attributes()->BetTypeDesc
								);
			}
		}
	
		return $market_details;
	}
}