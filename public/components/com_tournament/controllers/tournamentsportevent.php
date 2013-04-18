<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once JPATH_COMPONENT . DS . 'controller.php';

class TournamentSportEventController extends TournamentController
{
	/**
	 * Prevents access to tasks requiring authentication
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->extra_authenticate = array(
			'saveticket',
			'confirmbet',
			'savebet',
			'unregister'
			);

			$this->extra_squeezebox = array(
			'confirmbet',
			);

			parent::__construct();
	}

	/**
	 * Display the upcoming tournaments list, with an option to filter by jackpot_flag
	 *
	 * @return void
	 */
	public function display()
	{
		$user =& JFactory::getUser();

		if('list_tournaments' != JRequest::getVar('task')) {
			JRequest::setVar('task', 'list');
		}

		$sport_id		= JRequest::getVar('sport_id', null);
		$competition_id	= JRequest::getVar('competition_id', null);
		$jackpot    	= JRequest::getVar('jackpot', false);

		$sport_model	=& $this->getModel('TournamentSport', 'TournamentModel');
		//set up cookies for first visit, which will be used to display different banners when register
		if($user->guest) {
			$sport = $sport_model->getTournamentSport($sport_id);
			if(!empty($sport)) {
				setcookie("FirstVisit", $sport->id, time()+604800, '/');
			}
		}

		$tournament_model 				=& $this->getModel('Tournament', 'TournamentModel');
		$tournament_sport_event_model	=& $this->getModel('TournamentSportEvent', 'TournamentModel');

		//get sport tournament list
		$list_params = array(
			'sport_id'			=> $sport_id,
			'competition_id'	=> $competition_id,
			'jackpot'			=> $jackpot,
			'private'			=> 0
		);
		$tournament_list	= $tournament_sport_event_model->getTournamentSportActiveList($list_params);
		//get sport list
		$sport_list			= $tournament_sport_event_model->getActiveTournamentSportList(false, 0);
		//get competition list
		$competition_model	=& $this->getModel('TournamentCompetition', 'TournamentModel');
		$competition_list	= $competition_model->getActiveTournamentCompetitionListBySportId(null, 0, 0);

		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
		if(!empty($tournament_list)) {
			foreach($tournament_list as $tournament) {
				$tournament->entrants     = $ticket_model->countTournamentEntrants($tournament->id);
				$tournament->prize_pool   = $tournament_model->calculateTournamentPrizePool($tournament->id);
				$tournament->place_list   = $tournament_model->calculateTournamentPlacesPaid($tournament, $tournament->entrants, $tournament->prize_pool);
			}
		}

		$ticket_list = array();
		if(!$user->guest) {
			$ticket_list = $ticket_model->getTournamentTicketActiveListByUserID($user->id);
		}

		$view =& $this->getView('Tournament', 'html', 'TournamentView');
		
		//for user status
		$tb_status_model 			=& $this->getModel('TopbettaUser', 'TopbettaUserModel');
			if($tb_status_model->isTopbettaUser($user->id)) 
			{
				$view->assign('tb_user', true);
			}
			else
			{
				$view->assign('tb_user', false);
			}

		$view->assignRef('tournament_list', $tournament_list);
		$view->assignRef('sport_list', $sport_list);
		$view->assignRef('competition_list', $competition_list);
		$view->assign('jackpot', (bool)$jackpot);
		$view->assign('sport_id', (int)$sport_id);
		$view->assign('competition_id', (int)$competition_id);
		$view->assignRef('ticket_list', $ticket_list);
		$view->assign('tournament_type', 'sports');

		$event_group_model =& $this->getModel('EventGroup', 'TournamentModel');
		$view->setModel($event_group_model);
		
		$competition_model =& $this->getModel('TournamentCompetition', 'TournamentModel');
		$view->setModel($competition_model);

		$view->display();
	}



	/**
	 * Display the bet form popup
	 *
	 *	@return void
	 */
	public function betRefresh() {
		JRequest::setVar('task', 'game');
		$this->game(true);
	}

	/**
	 * Display the gameplay page
	 *
	 * @return void
	 */
	public function game($bet_refresh=false)
	{
		$id 		= JRequest::getVar('id', null);
		$match_id	= JRequest::getVar('match_id', null);
		$market_id	= JRequest::getVar('market_id', null);

		if(is_null($id)) {
			$this->setRedirect('index.php', JText::_('No tournament selected'), 'error');
			return;
		}

		$tournament_model	=& $this->getModel('TournamentSportEvent', 'TournamentModel');
		$tournament			= $tournament_model->getTournamentSportsByTournamentID($id);

		if(is_null($tournament)) {
			$this->setRedirect('index.php', JText::_('Tournament not found'), 'error');
			return;
		}

		$event_model	=& $this->getModel('Event', 'TournamentModel');
		$match_list		= $event_model->getEventListByEventGroupID($tournament->event_group_id);
		
		$info_link = '/tournament/details/' . $tournament->id;

		if(empty($match_id)) {
			$match = $event_model->getNextEventByEventGroupID($tournament->event_group_id);
		} else if(isset($match_list[$match_id])) {
			$match = $event_model->getEvent($match_id);
			//$match->tournament_match_id = $match->id;
		} else {
			$this->setRedirect($info_link, JText::_('Tournament match not found'), 'error');
			return;
		}

		if(empty($match_list)) {
			$this->setRedirect($info_link, JText::_('Betting opens approximately 24 hours before start time'));
			return;
		}

		if(!empty($tournament->cancelled_flag)) {
			$this->setRedirect($info_link, JText::_('This tournament has been cancelled'), 'error');
			return;
		}

		$private_tournament_ident = null;
		$password_protected_tournament	= false;
		if($tournament->private_flag) {
			$private_tournament_model	=& $this->getModel('TournamentPrivate', 'TournamentModel');
			$private_tournament			= $private_tournament_model->getTournamentPrivateByTournamentID($id);

			$private_tournament_ident = $private_tournament->display_identifier;
			$password_protected_tournament	= !empty($private_tournament->password);
		}

		$user =& JFactory::getUser();

		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
		$ticket = $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id);

		$session 				=& JFactory::getSession();
		$pending_bet_list		= $session->get('pending_bet_list', array(), 'sports_tournaments');
		$market_timestamp_list	= $session->get('market_timestamp_list', array(), 'sports_tournaments');

		if(!$bet_refresh) {
			unset($pending_bet_list[$tournament->id]);
			$session->set( 'pending_bet_list', $pending_bet_list, 'sports_tournaments');
			unset($market_timestamp_list[$tournament->id]);
			$session->set( 'market_timestamp_list', $market_timestamp_list, 'sports_tournaments');
		}

		if(empty($match_list)) {
			$this->setRedirect($info_link, JText::_('No match data available'), 'error');
			return;
		}

		if(empty($match)) {
			$match = reset($match_list);
		}

		$event_status_model =& $this->getModel('EventStatus', 'TournamentModel');
		$match->status		= $event_status_model->getEventStatus($match->event_status_id);

		$selection_result_model	=& $this->getModel('SelectionResult', 'TournamentModel');
		$result_list			= $selection_result_model->getSelectionResultListByEventID($match->id);

		$market_model		=& $this->getModel('Market', 'TournamentModel');
		$market_list		= $market_model->getMarketListByEventIDAndEventGroupID($match->id, $tournament->event_group_id);
		
		if($market_id) {
			$market				= $market_model->getMarket($market_id);
			$market_type_model	= $this->getModel('MarketType', 'TournamentModel');
			$market_type		= $market_type_model->getMarketType($market->market_type_id);
			$market->name		= $market_type->name;
		} else {
			$market = reset($market_list);
		}

		$market_timestamp_list[$market->id] = time();
		$session->set('market_timestamp_list', $market_timestamp_list, 'sports_tournaments');

		$selection_model =& $this->getModel('Selection', 'TournamentModel');
		$offer_list = $selection_model->getSelectionListByMarketID($market->id);

		$leaderboard_model =& $this->getModel('TournamentLeaderboard', 'TournamentModel');

		if($user->guest || is_null($ticket)) {
			$tournament->available_currency = 100000;
		} else {
			$tournament->available_currency = $ticket_model->getAvailableTicketCurrency($tournament->id, $user->id);
		}

		$turnover = $leaderboard_model->getTurnedOverByUserAndTournamentID($user->id, $tournament->id);

		$tournament->turnover_currency = ($turnover > $tournament->start_currency) ? 0 : $tournament->start_currency - $turnover;

		$bet_model 				=& $this->getModel('TournamentBet', 'TournamentModel');
		$bet_selection_model 	=& $this->getModel('TournamentBetSelection', 'TournamentModel');
		
		$bet_list = array();
		if (!is_null($ticket)) {
			$bet_list	= $bet_model->getTournamentBetListByEventIDAndTicketID($match->id, $ticket->id);
		}

		$match_status_model			=& $this->getModel('EventStatus', 'TournamentModel');
		$match_status_abandoned_id	= $match_status_model->getEventStatusByKeyword('abandoned')->id;
		
		$view =& $this->getView('TournamentSportEvent', 'html', 'TournamentView');
		$view->setLayout('game');

		$view->assignRef('tournament', $tournament);

		$view->assignRef('match_list', $match_list);
		$view->assignRef('match', $match);

		$view->assignRef('market_list', $market_list);
		$view->assignRef('market', $market);

		$view->setModel($selection_model);
		$view->assignRef('offer_list', $offer_list);
		$view->assignRef('bet_list', $bet_list);

		$view->setModel($bet_model);
		$view->setModel($bet_selection_model);
		$view->assignRef('ticket', $ticket);

		$view->assignRef('result_list', $result_list);

		$view->assignRef('bet_refresh', $bet_refresh);
		$view->assignRef('pending_bet_list', $pending_bet_list[$id][$market->id]);

		$view->assignRef('offer_market_limit', $market_model->offer_market_limit);

		$view->assignRef('match_status_abandoned_id', $match_status_abandoned_id);

		$view->assignRef('private_tournament_ident', $private_tournament_ident);
		$view->assignRef('password_protected_tournament', $password_protected_tournament);
		
		//for user status
		$tb_status_model 			=& $this->getModel('TopbettaUser', 'TopbettaUserModel');
			if($tb_status_model->isTopbettaUser($user->id)) 
			{
				$view->assign('tb_user', true);
			}
			else
			{
				$view->assign('tb_user', false);
			}		

		$view->display();
	}

	public function listOffers()
	{
		$session 				=& JFactory::getSession();
		$pending_bet_list		= $session->get('pending_bet_list', array(), 'sports_tournaments');
		$market_timestamp_list	= $session->get('market_timestamp_list', array(), 'sports_tournaments');

		$id				= JRequest::getVar('id', null);
		$match_id		= JRequest::getVar('match_id', null);
		$market_id		= JRequest::getVar('market_id', null);
		$from_market_id	= JRequest::getVar('from_market_id', null);
		$bets			= JRequest::getVar('bets', array());

		$tournament_model	=& $this->getModel('TournamentSportEvent', 'TournamentModel');
		$tournament			= $tournament_model->getTournamentSportsByTournamentID($id);

		if($bets) {
			$pending_bet_list[$id][$from_market_id] = array();
			foreach($bets as $offer_id => $value) {
				if(!empty($value)) {
					$pending_bet_list[$id][$from_market_id][$offer_id] = $value;
				}
			}
		}

		$market_timestamp_list[$market_id] = time();
		$session->set('pending_bet_list', $pending_bet_list, 'sports_tournaments');
		$session->set('market_timestamp_list', $market_timestamp_list, 'sports_tournaments');

		$market_model	=& $this->getModel('Market', 'TournamentModel');
		$market			= $market_model->getMarket($market_id);
		
		$market_type_model	= $this->getModel('MarketType', 'TournamentModel');
		$market_type		= $market_type_model->getMarketType($market->market_type_id);
		$market->name		= $market_type->name;

		$match_model	=& $this->getModel('Event', 'TournamentModel');
		$match			= $match_model->getEvent($match_id);

		$selection_model	=& $this->getModel('Selection', 'TournamentModel');
		$offer_list			= $selection_model->getSelectionListByMarketID($market->id);

		$match_status_model			=& $this->getModel('EventStatus', 'TournamentModel');
		$match_status_abandoned_id	= $match_status_model->getEventStatusByKeyword('abandoned')->id;

		$view =& $this->getView('TournamentSportEvent', 'html', 'TournamentView');

		$view->assignRef('tournament', $tournament);
		$view->setLayout('offer_list');
		$view->assignRef('offer_list', $offer_list);
		$view->assignRef('pending_bet_list', $pending_bet_list[$id][$market_id]);
		$view->assignRef('match', $match);
		$view->assignRef('market', $market);
		$view->assignRef('offer_market_limit', $market_model->offer_market_limit);
		$view->assignRef('match_status_abandoned_id', $match_status_abandoned_id);
		
		//for user status
		$tb_status_model 			=& $this->getModel('TopbettaUser', 'TopbettaUserModel');
			if($tb_status_model->isTopbettaUser(JFactory::getUser()->id)) 
			{
				$view->assign('tb_user', true);
			}
			else
			{
				$view->assign('tb_user', false);
			}

		$view->display();
	}

	/**
	 * Validate a bet selection and then display the error or confirmation box
	 *
	 * @return void
	 */
	public function confirmBet()
	{
		$this->validateBet(false);
	}

	/**
	 * Validate a bet selection and then save it or redirect
	 *
	 * @return void
	 */
	public function saveBet()
	{
		$this->validateBet(true);
	}

	/**
	 * Redirect or display a bet validation error
	 *
	 * @param string  $error
	 * @param boolean $redirect
	 * @param object  $tournament
	 * @param object  $race
	 * @return void
	 */
	private function betError($error, $redirect = false, $tournament = null, $match = null)
	{
		if($redirect) {
			$url = '/tournament/sports';
			if(!is_null($tournament)) {
				$url .= '/game/' . $tournament->id;
			}

			if(!is_null($match)) {
				$url .= '/' . $match->tournament_match_id;
			}

			$this->setRedirect($url, $error, 'error');
		} else {
			JRequest::setVar('task', 'beterror');

			$view =& $this->getView('TournamentSportEvent', 'html', 'TournamentView');
			$view->assign('error', $error);

			$view->assignRef('tournament', $tournament);
			$view->assignRef('match', $match);

			$view->display();
		}
	}

	/**
	 * Validate a bet selection
	 *
	 * @param boolean $save
	 * @return void
	 */
	private function validateBet($save = false)
	{
		$user =& JFactory::getUser();

		// begin the painstaking task of validating a bet
		$id			= JRequest::getVar('id', null);
		$match_id	= JRequest::getVar('match_id', null);
		$market_id	= JRequest::getVar('market_id', null);
		$bets		= JRequest::getVar('bets', array());

		$session				=& JFactory::getSession();
		$pending_bet_list		= $session->get('pending_bet_list', array(), 'sports_tournaments');
		$market_timestamp_list	= $session->get('market_timestamp_list', array(), 'sports_tournaments');
		$bet_ticket_timestamp	= $session->get('bet_ticket_timestamp', array(), 'sports_tournaments');

		if(is_null($id)) {
			return $this->betError(JText::_('No tournament specified'), $save);
		}

		if(is_null($match_id)) {
			return $this->betError(JText::_('No match specified'), $save);
		}

		$tournament_model	=& $this->getModel('TournamentSportEvent', 'TournamentModel');
		$tournament			= $tournament_model->getTournamentSportsByTournamentID($id);

		if(is_null($tournament)) {
			return $this->betError(JText::_('Tournament not found'), $save);
		}

		$ticket_model	=& $this->getModel('TournamentTicket', 'TournamentModel');
		$ticket			= $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id);

		if(is_null($ticket)) {
			return $this->betError(JText::_('You do not have a ticket for the selected tournament'), $save, $tournament);
		}
		
		$match_model	=& $this->getModel('Event', 'TournamentModel');
		$match			= $match_model->getEvent($match_id);
		$match->tournament_match_id = $match->id;

		if(empty($match)) {
			return $this->betError(JText::_('Match was not found'), $save, $tournament);
		}

		if(strtotime($match->start_date) < time()) {
			return $this->betError(JText::_('Match has already started'), $save, $tournament, $match);
		}

		if(strtotime($tournament->betting_closed_date) < time()) {
			return $this->betError(JText::_('Betting is already closed'), $save, $tournament, $match);
		}

		$market_model	=& $this->getModel('Market', 'TournamentModel');
		if(!$save) {
			if(is_null($market_id)) {
				return $this->betError(JText::_('No market specified'), $save);
			}

			$market_list	= $market_model->getMarketListByEventIDAndEventGroupID($match->id, $tournament->event_group_id);

			if(!isset($market_list[$market_id])) {
				return $this->betError(JText::_('Market was not found'), $save, $tournament);
			}
			$market = $market_model->getMarket($market_id);

			$pending_bet_list[$id][$market_id] = array();
			foreach($bets as $offer_id => $value) {
				$pending_bet_list[$id][$market_id][$offer_id] = $value;
			}
			$session->set('pending_bet_list', $pending_bet_list, 'sports_tournaments');

			if(empty($pending_bet_list)) {
				return $this->betError(JText::_('No offers specified'), $save);
			}
		}

		$selection_model	=& $this->getModel('Selection', 'TournamentModel');
		$bet_model			=& $this->getModel('TournamentBet', 'TournamentModel');
		$bet_list			= array();
		$bet_total			= 0;

		$offer_updated_list = array();
		$odds_updated		= false;

		if(isset($pending_bet_list[$tournament->id]) && is_array($pending_bet_list[$tournament->id])) {
			foreach($pending_bet_list[$tournament->id] as $tournament_market_id => $market_offers) {
				$updated_list	= $selection_model->getUpdatedSelectionListByMarketID($tournament_market_id, $market_timestamp_list[$tournament_market_id]);
				$market			= $market_model->getMarket($tournament_market_id);

				if(empty($updated_list) && isset($bet_ticket_timestamp[$tournament->id])) {
					$updated_list = $selection_model->getUpdatedSelectionListByMarketID($tournament_market_id, $bet_ticket_timestamp[$tournament->id]);
				}

				if(!empty($updated_list)) {
					$offer_updated_list[$tournament_market_id] = $updated_list;
					$odds_updated = true;
				}

				if ($tournament->bet_limit_flag) {
					$offer_count = count($selection_model->getSelectionListByMarketID($tournament_market_id));
					if(isset($market_model->offer_market_limit[$offer_count])) {
						$market_bet_limit = $market_model->offer_market_limit[$offer_count];
					} else {
						$market_bet_limit = $market_model->offer_market_limit[9];
					}
					
					if('unlimited' == $market_bet_limit) {
						$market_bet_limit = $tournament->start_currency;
					}
				}
	
				foreach($market_offers as $offer_id => $value) {
					$offer						= $selection_model->getSelectionDetails($offer_id);
					$pending_offer_bet_value	= 0;

					if($offer->market_id == $tournament_market_id && $value > 0) {
						$bet_value					= $value * 100;
						$bet_list[$offer_id]		= $bet_value;
						$bet_total					+= $bet_value;
						$pending_offer_bet_value	+= $bet_value;
					}
					
					if ($tournament->bet_limit_flag) {
						$offer_betted_value = $bet_model->getTournamentBetTotalsBySelectionIDAndTicketID($offer_id, $ticket->id);
	
						$offer_bet_value_credit = $market_bet_limit - $offer_betted_value;
	
						if($offer_bet_value_credit < $pending_offer_bet_value) {
							$maximum_bet = number_format($offer_bet_value_credit / 100, 2);
							return $this->betError(JText::_('Your bet for ' . $offer->name . ' (' . $offer->market_type . ') has exceeded the bet limit. You can only bet ' . $maximum_bet), $save, $tournament, $match);
						}
					}
				}
			}
		}

		if(empty($bet_list)) {
			return $this->betError(JText::_('Please enter at least a bet.'), $save, $tournament, $match);
		}
		//odds has been updated, refresh the bet form
		if($save && $odds_updated) {
			$this->betRefresh();
			return;
		}

		$current_currency = $ticket_model->getAvailableTicketCurrency($tournament->id, $user->id);

		if($current_currency < $bet_total) {
			$required = number_format(($bet_total - $current_currency) / 100, 2);
			return $this->betError(JText::_('You do not have enough bucks to place that bet (' . $required . ' more needed)'), $save, $tournament, $match);
		}

		if(!$tournament->reinvest_winnings_flag) {
			$leaderboard_model =& $this->getModel('TournamentLeaderboard', 'TournamentModel');
			$turnover = $leaderboard_model->getTurnedOverByUserAndTournamentID($user->id, $tournament->id);

			if($turnover + $bet_total > $tournament->start_currency) {
				$maximum_total_bet = number_format($tournament->start_currency / 100, 2);
				return $this->betError(JText::_('Your total bets cannot be more than ' . $maximum_total_bet), $save, $tournament, $match);
			}
		}

		// validation complete, so save or display depending on $save value
		if($save) {
			$this->storeBet($tournament,$ticket, $match, $bet_list);
		} else {
			$this->displayBet($tournament, $match, $bet_total, $bet_list, $offer_updated_list);
		}
	}

	/**
	 * Display the bet selection confirmation box
	 *
	 * @param object  $tournament
	 * @param object  $match
	 * @param int $bet_total
	 * @param object  $bet_list
	 * @return void
	 */
	private function displayBet($tournament, $match, $bet_total, $bet_list, $offer_updated_list)
	{
		$current_timestamp						= time();
		$session								=& JFactory::getSession();
		$bet_ticket_timestamp					= $session->get('bet_ticket_timestamp', array(), 'sports_tournaments');
		$bet_ticket_timestamp[$tournament->id]	= $current_timestamp;
		$session->set('bet_ticket_timestamp', $bet_ticket_timestamp, 'sports_tournaments');

		$market_timestamp_list					= $session->get('market_timestamp_list', array(), 'sports_tournaments');

		foreach($market_timestamp_list as $market_id => $timestamp) {
			$market_timestamp_list[$market_id] = $current_timestamp;
		}
		$session->set('market_timestamp_list', $market_timestamp_list, 'sports_tournaments');

		$offer_model		=& $this->getModel('Selection', 'TournamentModel');
		$offer_price_model	=& $this->getModel('SelectionPrice', 'TournamentModel');
		$market_model	=& $this->getModel('Market', 'TournamentModel');

		$view =& $this->getView('TournamentSportEvent', 'html', 'TournamentView');

		$view->setModel($offer_model);
		$view->setModel($offer_price_model);
		$view->setModel($market_model);

		$view->assignRef('tournament', $tournament);
		$view->assignRef('ticket', $ticket);

		$view->assignRef('match', $match);
		$view->assign('bet_total', $bet_total);
		$view->assign('bet_list', $bet_list);
		$view->assign('offer_updated_list', $offer_updated_list);

		$view->display();
	}

	/**
	 * Store a bet selection record
	 *
	 * @param object  $tournament
	 * @param object  $ticket
	 * @param object  $match
	 * @param array   $bet_list
	 * @return void
	 */
	private function storeBet($tournament, $ticket, $match, $bet_list)
	{
		$user 					=& JFactory::getUser();
		$bet_model				=& $this->getModel('TournamentBet', 'TournamentModel');
		$bet_selection_model	=& $this->getModel('TournamentBetSelection', 'TournamentModel');
		$bet_status_model		=& $this->getModel('BetResultStatus', 'BettingModel');
		$offer_model			=& $this->getModel('Selection', 'TournamentModel');
		$bet_product_model		=& $this->getModel('BetProduct', 'BettingModel');
		$bet_type_model			=& $this->getModel('BetType', 'BettingModel');
		$bet_total				= 0;
		$error					= false;
		
		$unitab					= $bet_product_model->getBetProductByKeyword('unitab');
		$win_bet_type			= $bet_type_model->getBetTypeByName('win');
		$bet_status_unresulted	= $bet_status_model->getBetResultStatusByName('unresulted');
		
		foreach($bet_list as $offer_id => $bet_value) {

			$offer	= $offer_model->getSelectionDetails($offer_id);
			$odds	= $offer->win_odds;
			if(!empty($offer->override_odds) && $offer->override_odds < $offer->win_odds) {
				$odds = $offer->override_odds;
			}
			
			$bet = array(
				'id'					=> null,
				'tournament_ticket_id'	=> $ticket->id,
				'bet_result_status_id'	=> $bet_status_unresulted->id,
				'bet_type_id'			=> $win_bet_type->id,
				'bet_product_id'		=> $unitab->id,
				'bet_amount'			=> $bet_value,
				'win_amount'			=> 0,
				'fixed_odds'			=> $odds,
				'flexi_flag'			=> 0,
				'resulted_flag'			=> 0,
			);

			$id	= $bet_model->store($bet);
			$bet_total += $bet_value;
			
			$bet_selection = array(
				'id'				=> null,
				'selection_id'		=> $offer->id,
				'tournament_bet_id'	=> $id,
				'position'			=> null
			);
			$bet_selection_model->store($bet_selection);

			$betting_closed_date = $match->start_date;
			if(!empty($tournament->betting_closed_date) && ($match->start_date > $tournament->betting_closed_date)) {
				$betting_closed_date = $tournament->betting_closed_date;
			}

			if(!$this->confirmAcceptance($id, $user->id, 'tournamentsportbet', strtotime($betting_closed_date))) {
				$error					= true;
				$bet_status_refunded	= $bet_status_model->getBetResultStatusByName('fully-refunded');

				$bet['id']					= $id;
				$bet['resulted_flag']		= 1;
				$bet['win_amount']			= $bet_value;
				$bet['bet_result_status_id']	= $bet_status_refunded->id;

				$bet_model->store($bet);
				$bet_total -= $bet_value;
			}
		}

		if($bet_total > 0) {
			$leaderboard_model =& $this->getModel('TournamentLeaderboard', 'TournamentModel');
			$leaderboard_model->addTurnedOverByUserAndTournamentID($user->id, $ticket->tournament_id, $bet_total);
		}

		$url = '/tournament/sports/game/' . $tournament->id . '/' . $match->id;
		if($error) {
			$this->setRedirect($url, JText::_('One or more bets could not be saved'), 'error');
		} else {
			$this->setRedirect($url, JText::_('Bets have been registered'));
		}
	}
	/**
	 * Display the jackpot map
	 *
	 * @return void
	 */
	public function jackpotMap() {
		parent::jackpotMap();
	}
	/**
	 * confirm Ticket
	 */
	public function confirmTicket(){
		parent::confirmTicket();
	}
}
