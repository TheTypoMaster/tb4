<?php
require_once '../common/shell-bootstrap.php';

class SportsOfferImporter extends TopBettaCLI{

	private $import_offer;
	private $import_market;

	private $offer;
	private $market;
	private $match;

	protected $max_execution_time = 59;

	public function initialise(){
		$this->addComponentModels('tournament');
		$this->addComponentModels('betting');

		$this->import_offer		=& JModel::getInstance('ImportOffer', 'TournamentModel');

		$this->selection 		=& JModel::getInstance('Selection', 'TournamentModel');
		$this->selection_price	=& JModel::getInstance('SelectionPrice', 'TournamentModel');
		
		$this->market 			=& JModel::getInstance('Market', 'TournamentModel');
		
		$this->bet_product 		= JModel::getInstance('BetProduct', 'BettingModel')->getBetProductByKeyword('unitab');
		$this->wapi 			= JModel::getInstance('WageringApi', 'BettingModel')->getWageringApiByKeyword('unitab');
	}

	public function execute(){

		$this->l('Starting Sports Offer Importer');

		$markets_updated_count = 0;
		
		while($market = $this->market->getActiveTournamentMarketByUpdatedDate(true)){
		
			$this->d('Market data: '.print_r($market,1));
			$this->l('Updating Market: '. $market->name);

			$offer_list = $this->import_offer->getImportOfferListByExternalMarketID($market->external_market_id);
			
			foreach($offer_list as $offer){
				$this->d('Offer data: '.print_r($offer,1));

				$selection = $this->selection->getSelectionByExternalIDAndMarketId($offer['offer_id'],$market->id);
				
				if(is_null($selection)){
					$selection = clone $this->selection;
					
					$selection->id						= null;
					$selection->market_id 				= $market->id;
					$selection->external_selection_id 	= (string) $offer['offer_id'];
					$selection->name					= $offer['offer_name'];
					$selection->wagering_api_id			= $this->wapi->id;
					
					$this->_save($selection);
				}
				
				$selection_price = $this->selection_price->getSelectionPriceBySelectionIDAndBetProductID($selection->id, $this->bet_product->id);
				
				if(!is_null($selection_price)){
					if($selection_price->win_odds == $offer['external_odds']){
						$this->l("No changes to odds for {$offer['offer_name']}");
						continue;
					}
				}
				else{
					$selection_price = clone $this->selection_price;
				}
				
				$selection_price->win_odds = (float) $offer['external_odds'];
				$selection_price->bet_product_id = $this->bet_product->id;
				$selection_price->selection_id = $selection->id;
				
				$this->l("Updating odds for {$offer['offer_name']}");
				$this->l("Odds: {$offer['external_odds']}");
				$this->_save($selection_price);
			}
			/**
			 *  Set updated date for current market
			 */
			$this->_save($market);
			$markets_updated_count++;
			/**
			 * Check execution time
			 */
			if($this->hasExecutionTimeExpired()){
				$this->l($markets_updated_count.' markets were updated on this run.');
				exit;
			}
		}

		$this->l('No markets to update.');
		$this->l('Ending Sports Offer Importer');
	}
}

$cron = new SportsOfferImporter();
$cron->debug(false);

$cron->execute();