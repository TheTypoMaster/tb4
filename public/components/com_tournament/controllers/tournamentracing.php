<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once JPATH_COMPONENT . DS . 'controller.php';

class TournamentRacingController extends TournamentController
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

		if ('list_tournaments' != JRequest::getVar('task')) {
			JRequest::setVar('task', 'list');
		}

		$jackpot    	= JRequest::getVar('jackpot', false);

		$sport_model	=& $this->getModel('TournamentSport', 'TournamentModel');
		$sport_name		= JRequest::getVar('competition_id', null);
		
		$sport_id		= null;
		if (!empty($sport_name)) {
			if ($sport = $sport_model->getTournamentSportByName($sport_name)) {
				$sport_id = $sport->id;
			}
		}

		//set up cookies for first visit, which will be used to display different banners when register
		if ($user->guest) {
			setcookie("FirstVisit", 'racing', time()+604800, '/');
		}

		$racing_model =& $this->getModel('TournamentRacing', 'TournamentModel');
		$tournament_model =& $this->getModel('Tournament', 'TournamentModel');
		
		$list_params = array(
			'sport_id'	=> $sport_id,
			'jackpot'	=> $jackpot,
			'private'	=> 0
		);
		$tournament_list = $racing_model->getTournamentRacingActiveList($list_params);

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
			$ticket_start = strtotime('yesterday 12:00:00');
			$ticket_list = $ticket_model->getTournamentTicketActiveListByUserID($user->id);
		}

		$view =& $this->getView('Tournament', 'html', 'TournamentView');

		$view->assignRef('tournament_list', $tournament_list);
		$view->assignRef('ticket_list', $ticket_list);
		$view->assign('jackpot', $jackpot);
		$view->assign('tournament_type', 'racing');

		$view->setModel($sport_model);

		$view->display();
	}

	/**
	 * Display the gameplay page
	 *
	 * @return void
	 */
	public function game()
	{
		$id = JRequest::getVar('id', null);
		if (is_null($id)) {
			$this->setRedirect('index.php', JText::_('No tournament selected'), 'error');
			return;
		}

		$tournament_model =& $this->getModel('TournamentRacing', 'TournamentModel');
		$tournament = $tournament_model->getTournamentRacingByTournamentID($id);

		if (is_null($tournament)) {
			$this->setRedirect('index.php', JText::_('Tournament not found'), 'error');
			return;
		}

		$info_link = '/tournament/details/' . $tournament->id;
		if (!empty($tournament->cancelled_flag)) {
			$this->setRedirect($info_link, JText::_('This tournament has been cancelled'), 'error');
			return;
		}
		
		$view =& $this->getView('TournamentRacing', 'html', 'TournamentView');
		$view->setLayout('game');
		
		$private_tournament_ident		= null;
		$password_protected_tournament	= false;
		if ($tournament->private_flag) {
			$private_tournament_model		=& $this->getModel('TournamentPrivate', 'TournamentModel');
			$private_tournament				= $private_tournament_model->getTournamentPrivateByTournamentID($id);

			$private_tournament_ident		= $private_tournament->display_identifier;
			$password_protected_tournament	= !empty($private_tournament->password);
		}

		$user =& JFactory::getUser();

		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
		$ticket = $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id);

		$leaderboard_model =& $this->getModel('TournamentLeaderboard', 'TournamentModel');

		if ($user->guest || is_null($ticket)) {
			$tournament->available_currency = 100000;
		} else {
			$tournament->available_currency = $ticket_model->getAvailableTicketCurrency($tournament->id, $user->id);
		}
		
		$race_model	=& $this->getModel('Race', 'TournamentModel');
		$number = JRequest::getVar('number', $race_model->getNextRaceNumberByMeetingID($tournament->meeting_id));
		if (is_null($number)) {
			$number = $race_model->getLastRaceNumberByMeetingID($tournament->meeting_id);
		}
		$race = $race_model->getRaceByMeetingIDAndNumber($tournament->meeting_id, $number);
		
		$turnover = $leaderboard_model->getTurnedOverByUserAndTournamentID($user->id, $tournament->id);

		$tournament->turnover_currency = ($turnover > $tournament->start_currency) ? 0 : $tournament->start_currency - $turnover;

		$bet_model =& $this->getModel('TournamentBet', 'TournamentModel');
		$tournament_bet_list = null;
		if (!is_null($ticket)) {
			$tournament_bet_list = $bet_model->getTournamentBetListByEventIDAndTicketID($race->id, $ticket->id);
		}
		
		if(is_null($tournament_bet_list)) {
			$tournament_bet_list = array();
		}

		$view->assignRef('ticket', $ticket);

		$view->assignRef('tournament', $tournament);
		$view->assignRef('race', $race);

		$view->assignRef('tournament_bet_list', $tournament_bet_list);
		$view->assignRef('bet_type_list', $bet_type_list);

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
		
		JRequest::setVar('meeting_id', $tournament->meeting_id);
		$this->meeting($view);
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
	private function betError($error, $redirect = false, $tournament = null, $race = null)
	{
		if($redirect) {
			$url = '/tournament/racing';
			if(!is_null($tournament)) {
				$url .= '/game/' . $tournament->id;
			}

			if(!is_null($race)) {
				$url .= '/' . $race->number;
			}

			$this->setRedirect($url, $error, 'error');
		} else {
			JRequest::setVar('task', 'beterror');

			$view =& $this->getView('TournamentRacing', 'html', 'TournamentView');
			$view->assign('error', $error);

			$view->assignRef('tournament', $tournament);
			$view->assignRef('race', $race);

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
		$id = JRequest::getVar('id', null);
		if(is_null($id)) {
			return $this->betError(JText::_('No tournament specified'), $save);
		}

		$tournament_model =& $this->getModel('TournamentRacing', 'TournamentModel');
		$tournament = $tournament_model->getTournamentRacingByTournamentID($id);
		
		if(is_null($tournament)) {
			return $this->betError(JText::_('Tournament not found'), $save);
		}

		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
		$ticket = $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id);

		if(is_null($ticket)) {
			return $this->betError(JText::_('You do not have a ticket for the selected tournament'), $save, $tournament);
		}

		$race_id = JRequest::getVar('race_id', null);
		if(is_null($race_id)) {
			return $this->betError(JText::_('No race specified'), $save, $tournament);
		}

		$race_model =& $this->getModel('Race', 'TournamentModel');
		$race = $race_model->getRace($race_id);

		if(is_null($race)) {
			return $this->betError(JText::_('Race was not found'), $save, $tournament);
		}

		if(strtotime($race->start_date) < time()) {
			return $this->betError(JText::_('Race has already jumped'), $save, $tournament, $race);
		}

		$bet_type_id = JRequest::getVar('bet_type_id', null);
		if(is_null($bet_type_id)) {
			return $this->betError(JText::_('No bet type selected'), $save, $tournament, $race);
		}

		$bet_type_model =& $this->getModel('BetType', 'BettingModel');
		$bet_type = $bet_type_model->getBetType($bet_type_id, true);

		if(is_null($bet_type)) {
			return $this->betError(JText::_('Invalid bet type selected'), $save, $tournament, $race);
		} else if (!WageringBet::isStandardBetType($bet_type->name)) {
			return $this->betError(JText::_('Exotic bets are not currently supported for tournaments. Coming soon!'), $save, $tournament, $race);
		}

		$value = JRequest::getVar('value', null);
		if(empty($value)) { // using empty to account for 0 as well
			return $this->betError(JText::_('No bet value specified'), $save, $tournament, $race);
		}

		$selection = JRequest::getVar('selection', null);
		if(empty($selection)) {
			return $this->betError(JText::_('Invalid bet selections'), $save, $tournament, $race);
		}

		$selection_list = explode(',', $selection);
		if(count($selection_list) == 0) {
			return $this->betError(JText::_('No selections found'), $save, $tournament, $race);
		}

		$runner_model =& $this->getModel('Runner', 'TournamentModel');
		$runner_list = $runner_model->getRunnerListByRaceID($race->id);

		$runner_validation_list = array();
		foreach($runner_list as $runner) {
			$runner_validation_list[$runner->id] = $runner;
		}

		$selected_runner_list = array();
		foreach($selection_list as $selection_id) {
			if(isset($runner_validation_list[$selection_id])) {
				$selected_runner_list[] = $runner_validation_list[$selection_id];
				continue;
			}

			return $this->betError(JText::_('One or more selected runners were not found in this race'), $save, $tournament, $race);
		}

		$value *= 100;
		$bet_total = count($selected_runner_list) * $value;
		if(strtolower($bet_type->name) == 'eachway') {
			$bet_total *= 2;
		}

		$current_currency = $ticket_model->getAvailableTicketCurrency($tournament->id, $user->id);

		if($current_currency < $bet_total) {
			$required = number_format(($bet_total - $current_currency) / 100, 2);
			return $this->betError(JText::_('You do not have enough bucks to place that bet (' . $required . ' more needed)'), $save, $tournament, $race);
		}
		
		// validation complete, so save or display depending on $save value
		if($save) {
			$this->storeBet($ticket, $race, $bet_type, $value, $selected_runner_list);
		} else {
			$this->displayBet($tournament, $race, $bet_type, $value, $bet_total, $selected_runner_list);
		}
	}

	/**
	 * Display the bet selection confirmation box
	 *
	 * @param object  $tournament
	 * @param object  $race
	 * @param object  $bet_type
	 * @param integer $value
	 * @param integer $bet_total
	 * @param array   $selected_runner_list
	 * @return void
	 */
	private function displayBet($tournament, $race, $bet_type, $value, $bet_total, $selected_runner_list)
	{
		$view =& $this->getView('TournamentRacing', 'html', 'TournamentView');

		$view->assignRef('tournament', $tournament);
		$view->assignRef('race', $race);

		$view->assignRef('bet_type', $bet_type);
		$view->assign('value', $value);

		$view->assign('bet_total', $bet_total);
		$view->assignRef('runner_list', $selected_runner_list);
		
		//Get the bet product
		$bet_product_model	=& JModel::getInstance('BetProduct', 'BettingModel');

		$bet_product = array();
		foreach($selected_runner_list as $runner){
			if($runner->bet_product_id != "")
			{ 
				if($bet_type->id >= 4){ // If exotics, then display SuperTab tote
					$bet_product[$runner->id]['name']['exotics'] = $bet_product_model->getBetProduct(4)->name;
				} else {
					if($bet_type->id == 1)
						$bet_product[$runner->id]['name']['win'] = $bet_product_model->getBetProduct($runner->w_product_id)->name;
					elseif($bet_type->id == 2)
						$bet_product[$runner->id]['name']['place'] = $bet_product_model->getBetProduct($runner->p_product_id)->name;
					elseif($bet_type->id == 3) {
						$bet_product[$runner->id]['name']['win'] = $bet_product_model->getBetProduct($runner->w_product_id)->name;
						$bet_product[$runner->id]['name']['place'] = $bet_product_model->getBetProduct($runner->p_product_id)->name;
					}
					else
						$bet_product[$runner->id]['name']['other'] = $bet_product_model->getBetProduct($runner->bet_product_id)->name;
				}

				$bet_product[$runner->id]['id'] = $runner->bet_product_id; 
			}
		}
		
		$view->assign('bet_product', $bet_product);

		$view->display();
	}

	/**
	 * Store a bet selection record
	 *
	 * @param object  $ticket
	 * @param object  $race
	 * @param object  $bet_type
	 * @param integer $value
	 * @param array   $selected_runner_list
	 * @return void
	 */
	private function storeBet($ticket, $race, $bet_type, $value, $selected_runner_list)
	{
		$user =& JFactory::getUser();

		$bet_model				=& $this->getModel('TournamentBet', 'TournamentModel');
		$bet_selection_model	=& $this->getModel('TournamentBetSelection', 'TournamentModel');

		$bet_record = ($bet_type->name == 'eachway') ? array('win', 'place') : array($bet_type->name);

		$bet_total  = 0;
		$error      = false;

		foreach ($selected_runner_list as $runner) {
			foreach($bet_record as $type) {
				$bet = array(
					'tournament_ticket_id'	=> $ticket->id,
					'bet_result_status'		=> 'unresulted',
					'bet_type'				=> $type,
					'bet_amount'			=> $value
				);
				
				$bet_id = $bet_model->storeUsingTypeNames($bet);
				
				$bet_total += $value;
				
				$bet_selection = array(
					'tournament_bet_id'	=> (int)$bet_id,
					'selection_id'		=> (int)$runner->id,
					'position'			=> 0
				);
				$bet_selection_model->store($bet_selection);
				
				if (!$this->confirmAcceptance($bet_id, $user->id, 'tournamentbet', strtotime($race->start_date))) {
					$error = true;
			
					$bet['id']					= $bet_id;
					$bet['resulted_flag']		= 1;
					$bet['win_amount']			= $value;
					$bet['bet_result_status']	= 'fully-refunded';
					
					$bet_model->storeUsingTypeNames($bet);
					$bet_total -= $value;
				}
			}
		}

		if ($bet_total > 0) {
			$leaderboard_model =& $this->getModel('TournamentLeaderboard', 'TournamentModel');
			$leaderboard_model->addTurnedOverByUserAndTournamentID($user->id, $ticket->tournament_id, $bet_total);
		}

		$url = '/tournament/racing/game/' . $ticket->tournament_id . '/' . $race->number;
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
	public function jackpotMap()
	{
		parent::jackpotMap();
	}
}
