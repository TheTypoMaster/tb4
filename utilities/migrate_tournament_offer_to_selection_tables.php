<?php
require_once '../common/shell-bootstrap.php';

define('__DATA_MIGRATION_IN_PROGRESS__', true);

class MigrateTournamentOfferToSelectionTables extends TopBettaCLI
{
	public function initialise()
	{
		$this->addComponentModels('tournament');
		$this->addComponentModels('betting');

		jimport('mobileactive.database.query');
		$this->db = $this->getDBO();
		
		$this->selection 		=& JModel::getInstance('Selection', 'TournamentModel');
		$this->selection_price	=& JModel::getInstance('SelectionPrice', 'TournamentModel');
		$this->selection_result	=& JModel::getInstance('SelectionResult', 'TournamentModel');
		$this->offer 			=& JModel::getInstance('TournamentOffer', 'TournamentModel');
		
		$this->wapi 			= JModel::getInstance('WageringApi', 'BettingModel')->getWageringApiByKeyword('unitab');
		$this->bet_product 		= JModel::getInstance('BetProduct', 'BettingModel')->getBetProductByKeyword('unitab');
	}

	public function execute()
	{
		$db =& $this->db;
		
		$sql = 'SELECT * FROM `tbdb_tournament_offer` ORDER BY id ASC';
		$this->d($sql);
		$db->setQuery($sql);
		
		$offer_list = $db->loadObjectList();
		
		foreach($offer_list as $offer){
			
			$this->l('Migrating offer: '. $offer->name . '(' . $offer->id . ')');
			
			$selection = $this->selection->getSelectionByExternalSelectionIdAndWageringApiId($offer->external_offer_id, $this->wapi->id);
			
			if(is_null($selection)){
				$selection = clone $this->selection;
					
				$selection->market_id 				= (int) $offer->tournament_market_id;
				$selection->external_selection_id 	= $offer->external_offer_id;
				$selection->name					= $offer->name;
				$selection->wagering_api_id			= $this->wapi->id;
					
				if($this->_save($selection)){
					$this->l('Saved to selection table');
				}
			}
			
			$selection_price = $this->selection_price->getSelectionPriceBySelectionIDAndBetProductID($selection->id, $this->bet_product->id);
			
			if(is_null($selection_price)){
			
				$selection_price = clone $this->selection_price;
				
				$selection_price->win_odds 			= $selection->win_odds;
				$selection_price->bet_product_id 	= $this->bet_product->id;
				$selection_price->selection_id 		= $selection->id;
				if($this->_save($selection_price)){
					$this->l('Saved to selection price table');
				}
			}
			
			$selection_result = $this->selection_result->getSelectionResultBySelectionID($selection->id);
			
			if(is_null($selection_result)){

				$sql = 'SELECT * FROM `tbdb_tournament_offer_result` WHERE tournament_offer_id = ' . $offer->id;
				$this->d($sql);
				$db->setQuery($sql);
				
				$offer_result = $db->loadObject();
				
				if(!is_null($offer_result)){
					$selection_result 				= clone $this->selection_result;
					$selection_result->selection_id = $selection->id;
					$selection_result->payout_flag 	= $offer_result->payout_flag;
					if($this->_save($selection_result)){
						$this->l('Saved to selection result table');
					}
				}
				
			}
			
			$sql = 'UPDATE `tbdb_tournament_sport_bet` 
						SET tournament_offer_id = ' . $selection->id .' 
						WHERE tournament_offer_id = ' .$offer->id;
			
			$this->d($sql);
			$db->setQuery($sql);
			
			$result = $db->query();
			$update_count = $db->getAffectedRows();
			
			$this->l($update_count . ' bets have had their offer_id updated');
		}
	}
}

$job = new MigrateTournamentOfferToSelectionTables();
$job->debug(false);
$job->execute();