<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelImportCompetition extends JModel
{
	const XML_ROOT = 'http://tatts.com/pagedata/';
	private $xml_doc = null;

	/**
	 * Function to get competitions/leagues from the feed by Sports Id
	 * @param $id to check
	 * Return Array
	 */
	public function getImportCompetitionListBySportID($sport_id=''){
		
		if($sport_id){
			$file_path 		= self::XML_ROOT . "sports/sports.xml";
			$simple_xml			= null;
			
			if (TournamentHelper::remoteFileExists($file_path)) {
				$simple_xml = simplexml_load_file($file_path);
			}

			if ($simple_xml) { //--load only if there is any contents from the xml

				$competitions = $simple_xml->xpath("//Sport[@SportId=$sport_id]/League");
				/**
				 *  Grab the attributes for all the extrenal competition/League &
				 *  generate an array to be used for the dropdown
				 */
				foreach ($competitions as $competition){
					$ext_competition[] = array( "league_id" => (int)$competition->attributes()->LeagueId,
	                      "league_name" => (string)$competition->attributes()->LeagueName
	                    );
				}
				return $ext_competition;
			}
		}
	}
	/**
	 * Function to get individual competition/league from the feed by Id
	 * @param $id to check
	 * Return Array
	 */
	public function getImportCompetitionByExternalID($id=""){
		if ($id) {
			$file_path 		= self::XML_ROOT . "sports/sports.xml";
		$simple_xml 	= simplexml_load_file($file_path);

		if ($simple_xml) { //--load only if there is any contents from the xml

			$competitions = $simple_xml->xpath("//Sport[@LeagueId=$id]/League");

			foreach ($competitions as $competition){
				$ext_competition = array( "ext_sport_id" => (int)$competition->attributes()->LeagueId,
                      "ext_sport_name" => (string)$competition->attributes()->LeagueName
                    );
                    return $ext_competition;
				}
			}// if ($simple_xml) ends
		}//if ($id) ends
	}

}