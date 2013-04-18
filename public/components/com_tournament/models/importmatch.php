<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('mobileactive.model.xml');

class TournamentModelImportMatch extends XMLModel
{
	const TZ_FEED = 'GMT';

	const XML_ROOT = 'http://tatts.com/pagedata/';
	private $xml_doc = null;

	/**
	 * Function to get matchs/leagues from the feed by Sports Id
	 * @param integer $competition_id to check
	 * @return array
	 */
	public function getImportMatchListByCompetitionID($competition_id = null) {

		if($competition_id) {
			$competition_model  = JModel::getInstance('TournamentCompetition', 'TournamentModel');
			$competition 		= $competition_model->getTournamentCompetition($competition_id);

			$sport_model  		= JModel::getInstance('SportMap', 'TournamentModel');
			$sport				= $sport_model->getTournamentSportByTournamentSportID($competition->tournament_sport_id);

			$file_path 			= self::XML_ROOT . "sports/sports.xml";
			$simple_xml			= null;
			
			if (TournamentHelper::remoteFileExists($file_path)) {
				$simple_xml = simplexml_load_file($file_path);
			}

			if ($simple_xml) { //--load only if there is any contents from the xml
				$meetings = $simple_xml->xpath("//Sports/Sport/League[@LeagueId=$competition->external_competition_id]/Meeting[@MatchRoundSeasonFlag='M']");

				if($meetings) {
					foreach ($meetings as $meeting){
						$meeting_id = $meeting->attributes()->MeetingId;
						if($meeting_id > 0) {
							$file_path_matchs	= self::XML_ROOT . "sports/meetings/meeting". $meeting_id .".xml";
							$simple_match_xml	= simplexml_load_file($file_path_matchs);

							if($simple_match_xml){
								$matchs = $simple_match_xml->xpath("//Sport/League/Meeting[@MatchRoundSeasonFlag='M']/MainEvent");
								/**
								 *  Grab the attributes for all the extrenal match/League &
								 *  generate an array to be used for the dropdown
								 *  and only if that has a meeting ID
								 */

								foreach ($matchs as $match){
									if($match->attributes()->MainEventId > 0) {
										$ext_match[] = array(
											"meeting_id" 	=> (int)$meeting_id,
											"ext_match_id"	=> (int)$match->attributes()->MainEventId,
				                    		"event_name" 	=> (string)$match->attributes()->EventName,
									  		"start_date" 	=> $this->formatLocalDateTime((string)$match->attributes()->EventStartTime, self::TZ_FEED)
				                    	);
									}
								}//foreach

							}//simple_match_xml

						}//foreach
					}

					return $ext_match;
				}
			}
		}
	}

	/**
	 * Function to get individual match/league from the feed by Id
	 * @param integer $meeting_id
	 * @param integer $match_id
	 * @return array
	 */
	public function getImportMatchExternalMeetingIDAndExternalMatchID($meeting_id = NULL, $match_id = NULL){
		if ($match_id && $meeting_id) {
			$file_path 		= self::XML_ROOT . "sports/meetings/meeting". $meeting_id .".xml";
			$simple_xml 	= simplexml_load_file($file_path);

			if ($simple_xml) { //--load only if there is any contents from the xml
				$matchs = $simple_xml->xpath("//Sport/League/Meeting[@MatchRoundSeasonFlag='M']/MainEvent[@MainEventId=$match_id]");
				foreach ($matchs as $match) {
					$ext_match = array(
						"ext_match_id" 	=> (int)$match->attributes()->MainEventId,
                    	"match_name" 	=> (string)$match->attributes()->EventName,
						"start_date" 	=> $this->formatLocalDateTime((string)$match->attributes()->EventStartTime, self::TZ_FEED)
                	);
				}
			}

			return $ext_match;// if ($simple_xml) ends
		}//if ($id) ends
	}
}