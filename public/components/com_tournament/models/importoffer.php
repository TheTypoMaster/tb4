<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelImportOffer extends JModel
{
	const XML_ROOT = 'http://tatts.com/pagedata/';
	private $xml_doc = null;

	/**
	 * Function to get offer from the feed by Match Name & Competition Id
	 * @param $id to check
	 * Return Array
	 */
	public function getImportOfferListByExternalMarketID($external_market_id = NULL){

		if($external_market_id){
			$market_model  		= JModel::getInstance('Market', 'TournamentModel');
			$market 			= $market_model->getMarketByExternalMarketID($external_market_id);

			$match_model  		= JModel::getInstance('Event', 'TournamentModel');
			$match 				= $match_model->load($market->event_id);
			
			$competition_model  = JModel::getInstance('TournamentCompetition', 'TournamentModel');
			$competition 		= $competition_model->getTournamentCompetition($match->tournament_competition_id);
			
			$sport_model  		= JModel::getInstance('SportMap', 'TournamentModel');
			$sport				= $sport_model->getTournamentSportByTournamentSportID($competition->tournament_sport_id);
		
			$file_path 		= self::XML_ROOT . "sports/subevents/subevent{$external_market_id}.xml";

			$simple_xml 	= simplexml_load_file($file_path);

			if ($simple_xml) { //--load only if there is any contents from the xml
				$offers = $simple_xml->xpath("//Sport/League/Meeting/MainEvent/SubEvent/Offer");

				if($offers){
					foreach ($offers as $offer){
						$ext_offer[] 	= array("offer_id" 		=> (int) $offer->attributes()->OfferId,
												"offer_name" 	=> (string) $offer->attributes()->OfferName,
												"external_odds"	=> (string) $offer->attributes()->WWInternetReturn );
					}
					return $ext_offer;
				}
			}


		}
	}
	/**
	 * Function to get individual offer from the feed by Meeting Id & match name
	 * @param $id to check
	 * Return Array
	 */
	public function getImportOfferByMatchAndExternalOfferID($name = NULL, $ext_offer_id = NULL){
		if($name && $ext_offer_id){
			$offer_model  		= JModel::getInstance('Selection', 'TournamentModel');
			$offer 				= $offer_model->getTournamentSelectionByExternalID($ext_offer_id);

			$market_model  		= JModel::getInstance('Market', 'TournamentModel');
			$market 			= $market_model->load($offer->tournament_market_id);

			$match_model  		= JModel::getInstance('Event', 'TournamentModel');
			$match 				= $match_model->load($market->tournament_match_id);

			$competition_model  = JModel::getInstance('TournamentCompetition', 'TournamentModel');
			$competition 		= $competition_model->getTournamentCompetition($match->tournament_competition_id);

			$sport_model  		= JModel::getInstance('SportMap', 'TournamentModel');
			$sport				= $sport_model->getTournamentSportByTournamentSportID($competition->tournament_sport_id);

			$file_path 			= self::XML_ROOT . "sport/$sport->external_sport_id/$competition->external_competition_id/season.xml";
			$simple_xml			= null;
			
			if (TournamentHelper::remoteFileExists($file_path)) {
				$simple_xml = simplexml_load_file($file_path);
			}

			if ($simple_xml) { //--load only if there is any contents from the xml
				$offers = $simple_xml->xpath("//Sport/League[@LeagueId=$competition->external_competition_id]/Round/Match[@EventName='$name']/Market[@BetTypeId=$market->external_bet_type_id]/Price[@OfferId=$ext_offer_id]");

				if($offers){
					foreach ($offers as $offer){
						$ext_offer[] 	= array("offer_id" 		=> $offer->attributes()->OfferId,
												"offer_name" 	=> $offer->attributes()->OfferName,
												"external_odds"	=> $offer->attributes()->WWInternetReturn );
					}
					return $ext_offer;
				}
			}
		}
	}

}