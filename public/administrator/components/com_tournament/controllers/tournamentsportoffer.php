<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class TournamentSportOfferController extends JController
{
	/**
	 * Controller URL is to reuse the same path to all the places nessessary
	 */
	private $controllerUrl = 'index.php?option=com_tournament&controller=tournamentsportoffer';
	/**
	 * Display the list of racing tournaments
	 *
	 * @return void
	 */
	public function listView()
	{
		global $mainframe, $option;
		$sport_id = JRequest::getVar('sportId', '');
		$competition_id = JRequest::getVar('competitionId', '');

		$event_group_model	=& $this->getModel('EventGroup', 'TournamentModel');
		$sports_model		=& $this->getModel('TournamentSport', 'TournamentModel');
		$event_model		=& $this->getModel('Event', 'TournamentModel');

		$filter_prefix = 'sportoffer';

		$order = $mainframe->getUserStateFromRequest(
			$filter_prefix.'filter_order',
			'filter_order',
			'id'
		);

		$direction = strtoupper($mainframe->getUserStateFromRequest(
			$filter_prefix.'filter_order_Dir',
			'filter_order_Dir',
			'ASC'
		));

		$limit = $mainframe->getUserStateFromRequest(
			'global.list.limit',
			'limit',
			$mainframe->getCfg('list_limit')
		);

		$offset = $mainframe->getUserStateFromRequest(
			$filter_prefix.'limitstart',
			'limitstart',
			0
		);

		$params = array(
			'order'		=> $order,
			'direction'	=> $direction,
			'limit'		=> $limit,
			'offset'	=> $offset,
			'type'		=> 'sports'	
		);
		
		$match_list = $event_model->getEventListBySportIDAndCompetitionID($sport_id, $competition_id, $params);

		foreach ($match_list as $match) {
			$match->is_resulted = $event_model->isEventResulted($match->id);
			$match->started = time() > strtotime($match->start_date) ? true : false;
		}

		$sports_all = $sports_model->getTournamentSportList();
		$sport_competitions = array();
		if($sport_id){
			$competition_model 	=& $this->getModel('TournamentCompetition', 'TournamentModel');
			$sport_competitions = $competition_model->getTournamentCompetitionListBySportID($sport_id);
		}
		jimport('joomla.html.pagination');

		$total		= $event_model->getTotalEventCount($sport_id, $competition_id, $params);
		
		$pagination	= new JPagination($total, $offset, $limit);
		$view		=& $this->getView('TournamentSportOffer', 'html', 'TournamentView');

		$view->assign('sports_all', $sports_all);
		$view->assign('sport_id', $sport_id);

		$view->assign('sport_competitions', $sport_competitions);
		$view->assign('competition_id', $competition_id);

		$view->assign('match_list', $match_list);
		$view->assign('order', $order);
		$view->assign('direction', $direction);
		$view->assign('pagination', $pagination->getListFooter());

		$view->display();
	}
	/**
	 * method to make the cancel button work
	 */
	public function cancel(){
		$this->setRedirect($this->controllerUrl);
		$session =& JFactory::getSession();
	}
	/**
	* To load the Edit Odds form
	*/
	public function editOdds()
	{
		$offer_model =& $this->getModel('Selection', 'TournamentModel'); // setting the offer model object
		$match_id = JRequest::getVar('match_id', null);

		$view =& $this->getView('TournamentSportOffer', 'html', 'TournamentView');
		$view->setLayout('editodds');

		if (JRequest::getVar('match_id') > 0) {
			$offer_list = $offer_model->getActiveTournamentSelectionListByEventID(JRequest::getVar('match_id'));
			
			$view->assign('offer_data',  $offer_list);
			$view->assign('match_id', $match_id);
			$view->display();
		}
	}

	/**
	* To load the result match
	*/
	public function resultMatch()
	{
		$match_id = JRequest::getVar('match_id', null);
		$offer_model =& $this->getModel('Selection', 'TournamentModel'); // setting the offer model object

		$view =& $this->getView('TournamentSportOffer', 'html', 'TournamentView');
		$view->setLayout('resultmatch');

		if (!is_null($match_id)) {
			$offer_list = $offer_model->getActiveTournamentSelectionListByEventID($match_id);

			$match_model =& $this->getModel('Event', 'TournamentModel');

			$match_is_resulted = $match_model->isEventResulted($match_id);

			//if($match_is_resulted){
				$offerresult_model = $this->getModel('SelectionResult', 'TournamentModel');
			//}

			$market_list = array();
			
			foreach ($offer_list as $offer) {

				if (!isset($market_list[$offer->market_id])) {
					$market = new stdClass;
					$market->name = $offer->market_type;
					$market->offer_list = array();
					$market->paying_offer = null;
					
				//	if ($match_is_resulted){
						$paying_offer = $offerresult_model->getSelectionResultByMarketID($offer->market_id);
						$market->paying_offer = is_null($paying_offer) ? '0' : $paying_offer;
				//	}

					$market_list[$offer->market_id] = $market;
				}
				
				$market->offer_list[$offer->id] = $offer->name . ' (#' . $offer->external_selection_id . ') ('.$offer->line.')';
			}
			
			$view->assign('match_is_resulted', $match_is_resulted);
			$view->assign('market_list',  $market_list);
			$view->assign('match_id', $match_id);

			$view->display();
		}
	}

	/**
	 * to Save the Sports odds
	 */
	public function saveOdds()
	{
		$this->setRedirect($this->controllerUrl);
		$session 		=& JFactory::getSession();
		$offer_price 	= JRequest::getVar('offer_price', array());

		/**
		 * Get the offer model
		 */
		$offer_price_model 	=& $this->getModel('SelectionPrice', 'TournamentModel');

		/**
		 * save all the odds overrides
		 */
		foreach ($offer_price as $id => $odds){

			$offer_price_params = array(
				'id' => $id,
				'override_odds'	=> $odds
			);

			$offer_price_model->store($offer_price_params);
		}
	}

	/**
	 * to Save the result
	 */
	public function saveResult()
	{
		$this->setRedirect($this->controllerUrl);
		$offer_selection 		= JRequest::getVar('offer_selection', '');
		$match_id 				= JRequest::getVar('match_id', '');

		$offer_result_model 	=& $this->getModel('SelectionResult', 'TournamentModel');
		$match_model  			=& $this->getModel('Event', 'TournamentModel');
		$market_model 			=& $this->getModel('Market', 'TournamentModel');
        $offerresult_model      = $this->getModel('SelectionResult', 'TournamentModel');


		/**
		 * save all the odds overrides
		 */
		foreach ($offer_selection as $market_id => $offer_id){
			if ($offer_id < 0) {
				JError::raiseWarning(0, 'Must select a result for all bet types.');
				return false;
			}

			// if offer id is 0, mark the market as refunded
			if ($offer_id == 0){
				$market_model->setMarketToRefund($market_id);
			} else {

                // delete existing result for this selection if it exists
                $offerresult_model->deleteSelectionResultBySelectionID($offer_id);

                $offer_result_params = array(
                    'selection_id'	=> $offer_id
                );

                $offer_result_id = $offer_result_model->store($offer_result_params);

                // set market status to resulted
                $market_model->setMarketToResulted($market_id);

			}
		}

		$match_model->setEventToPaying($match_id);
	}

	/**
	 * set all markets to refunded
	 */
	public function abandonMatch()
	{
		$this->setRedirect($this->controllerUrl);
		$match_id 		= JRequest::getVar('match_id', '');

		$market_model 	=& $this->getModel('Market', 'TournamentModel');
		$match_model  	=& $this->getModel('Event', 'TournamentModel');

		$market_list = $market_model->getMarketListByEventID($match_id);

		foreach ($market_list as $market){
			$market_model->setMarketToRefund($market->id);
		}

		$match_model->setEventToAbandoned($match_id);
	}

}