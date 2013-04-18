<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once JPATH_COMPONENT . DS . 'controller.php';

class TournamentSportController extends TournamentController
{
	private $controller_url = 'index.php?option=com_tournament&controller=tournamentsport';
	/**
	 * Display the list of racing tournaments
	 *
	 * @return void
	 */
	public function display()
	{
		global $mainframe, $option;

		$filter_prefix = 'sporttournament';

		$tournament_type = $mainframe->getUserStateFromRequest(
		$filter_prefix.'private',
			'private',
		0
		);

		$order = $mainframe->getUserStateFromRequest(
		$filter_prefix.'filter_order',
		'filter_order',
		't.id'
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

		$tournament_model =& $this->getModel('Tournament', 'TournamentModel');
		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');

		$tournament_list = $tournament_model->getSportTournamentListByType($tournament_type, $order, $direction, $limit, $offset);
		foreach($tournament_list as $tournament) {
			$tournament->entrants = $ticket_model->countTournamentEntrants($tournament->id);
		}
		jimport('joomla.html.pagination');
		$total = $tournament_model->getTotalTournamentCount($tournament_type);
		$pagination = new JPagination($total, $offset, $limit);

		$view =& $this->getView('TournamentSport', 'html', 'TournamentView');
		$view->assign('tournament_list', $tournament_list);
		$view->assign('order', $order);
		$view->assign('direction', $direction);
		$view->assign('tournament_type', $tournament_type);
		$view->assign('pagination', $pagination->getListFooter());

		$view->display();
	}

	/**
	 * Display the edit form
	 *
	 * @return void
	 */
	public function edit()
	{
		$tournament_id = JRequest::getVar('id', 0);
		$tournament_model =& $this->getModel('Tournament', 'TournamentModel');
		$buyin_model = & $this->getModel('TournamentBuyIn', 'TournamentModel');

		$tournament_data = $tournament_model->getTournament($tournament_id);
		$jackpot_parent_list = $tournament_model->getSportTournamentJackpotParentList($tournament_id);

		if($tournament_data->id) {
			$ticket_model 			=& $this->getModel('TournamentTicket', 'TournamentModel');
			$entrants 				= $ticket_model->countTournamentEntrants($tournament_id);

			$tournament_event_model =& $this->getModel('TournamentEvent', 'TournamentModel'); // setting the sport model object
			$events_all 			= $tournament_event_model->getTournamentEventListByCompetitonID($tournament_data->tournament_competition_id);
			$match_time				= $tournament_event_model->getTournamentEventFirstAndLastMatchTimeByEventID($tournament_data->tournament_event_id);

			if(!empty($tournament_id)) {
				$current_buy_in = $buyin_model->getBuyInByTournamentID($tournament_id);
			}
		}
		$buyin_list 		= $buyin_model->getTournamentBuyInListByPrivateFlag($tournament_data->private_flag);
		$tournament_buyin 	= $buyin_model->getBuyInByTournamentID($tournament_data->id);

		$sports_model 		=& $this->getModel('TournamentSport', 'TournamentModel'); // setting the sport model object
		$sports_all 		= $sports_model->getTournamentSportList();

		$competition_model  =& $this->getModel('TournamentCompetition', 'TournamentModel');
		$competitions 		= $competition_model->getTournamentCompetitionListBySportID($tournament_data->sport_id);

		if($tournament_id > 0 && $entrants > 0){
			/**
			 * allow only to update the name, description, reinvestment winning & bettypes
			 */
			$sport_bet_model=& $this->getModel('TournamentSportBet','TournamentModel');
			$betting_started = $sport_bet_model->isBettingStartedByTournamentId($tournament_id);
		}

		$view =& $this->getView('TournamentSport', 'html', 'TournamentView');
		$view->setLayout('edittournament');

		$view->assign('sports_all',  $sports_all);
		$view->assign('events_all',  $events_all);
		$view->assign('buyin_list',  $buyin_list);
		$view->assign('competitions',  $competitions);
		$view->assign('tournament_data', $tournament_data);
		$view->assign('current_buy_in', $tournament_buyin);
		$view->assign('entrants', $entrants);
		$view->assign('match_time', $match_time);
		$view->assign('betting_started', $betting_started);
		$view->assign('tournament_entrants', $tournament_entrants);
		$view->assign('jackpot_parent_list', $jackpot_parent_list);

		$view->display();
	}
	/**
	 * Save tournament data from the edit form
	 *
	 * @return bool
	 */
	/**
	 * to Save the Sports data
	 */
	public function save()
	{
		$tournament_id			= JRequest::getVar('id', '');
		$name 					= JRequest::getVar('name', '');
		$event_id 				= JRequest::getVar('event_id', '');
		$private_flag			= JRequest::getVar('private_flag',0);
		$tournament_value 		= JRequest::getVar('tournament_value','');
		$jackpot_flag	 		= JRequest::getVar('jackpot_flag',0);
		$parent_tournament_id 	= JRequest::getVar('parent_tournament_id','');
		$start_currency 		= JRequest::getVar('start_currency',1000);
		$tournament_value		= JRequest::getVar('tournament_value','');
		$minimum_prize_pool		= JRequest::getVar('minimum_prize_pool',0);
		$description			= JRequest::getVar('description','');
		$reinvest_winnings_flag = JRequest::getVar('reinvest_winnings_flag','');
		$status_flag			= JRequest::getVar('status_flag', 0);
		$all_betting_closed		= JRequest::getVar('all_betting_closed', 0);
		$tournament_sport_id	= JRequest::getVar('sportId', '');

		$this->setRedirect($this->controller_url . "&private=" . $private_flag);
		$session =& JFactory::getSession();

		
		$ticket_model=& $this->getModel('TournamentTicket', 'TournamentModel');
		$entrants 	 = $ticket_model->countTournamentEntrants($tournament_id);
		
		if (!empty($tournament_id) && empty($name)) {
			JError::raiseWarning(0, 'The fields cannot be empty!');
			return false;
		}
		
		/**
		 * Get the tournament model
		 */
		$tournament_model 	=& $this->getModel('Tournament', 'TournamentModel');
		$tournament			= $tournament_model->getTournamentByName($name);
		/**
		 * checking the tournament name already exists on insert & update
		 * except the same sport that has been edited
		 */
		if ($tournament->id != $tournament_id && $tournament->name == $name){
			JError::raiseWarning(0, 'The Tournament name already exists!');
			return false;
		}
		/**
		 * Vaidations
		 */
		if ($tournament_id > 0) {
			$tournament  = $tournament_model->getTournament($tournament_id);
			// only allow to save tournament name & description if registrants exists

			if ($entrants > 0) {
				$audit_model 		=& $this->getModel('TournamentAudit','TournamentModel');

				$tournament_params = array(
					'id' 			=> $tournament_id,
					'name' 			=> JRequest::getVar('name', ''),
					'description' 	=> JRequest::getVar('description', ''),
				);
				
				foreach($tournament_params as $field => $param){
					if($field != 'id'){
						if($param != $tournament->{$field}){
							$audit_params = array(
								'tournament_id' => $tournament_id,
								'field_name'	=> $field,
								'old_value'		=> $tournament->{$field},
								'new_value'		=> $param
							);

							$audit_model->store($audit_params);
							$changed = true;
						}
					}
				}
					
				if ($changed) {
						$tournament_id = $tournament_model->store($tournament_params);
				}
	
				return true;
			}
		}
		/**
		 *Validation ends
		 */
		if($tournament_value){
			$buyin_model =& $this->getModel('TournamentBuyIn', 'TournamentModel');
			$buyin = $buyin_model->getTournamentBuyIn($tournament_value);
		}
		$tournament_event_model	=& $this->getModel('TournamentEvent', 'TournamentModel');
		$tournament_event		= $tournament_event_model->getTournamentEventFirstAndLastMatchTimeByEventID($event_id);

		if($tournament_event->first_match_time != NULL){
			$tournament_start_date	= $tournament_event->first_match_time;
			$tournament_end_date	= $tournament_event->last_match_time;

			if($tournament_event->first_match_time){
				if($all_betting_closed == 1) $betting_closed_date = $tournament_event->first_match_time;
				else $betting_closed_date = $tournament_event->last_match_time;
			}
		}
		else if($tournament_event->first_match_time == NULL && $event_id > 0){
			$tournament_event		= $tournament_event_model->getTournamentEvent($event_id);
			$tournament_start_date	= $tournament_end_date = $betting_closed_date = $tournament_event->start_date;
		}
		/**
		 * If $tournament_params is not populated through different validation rules
		 */
		if(empty($tournament_params)){
			if (empty($tournament_id) && empty($name)) {
				$name = $this->_generateTournamentAutomatedText('name');
			}
			
			if (empty($tournament_id) && empty($description)) {
				$description = $this->_generateTournamentAutomatedText('description');
			}
			
			$tournament_params = array(
				'id'                    => $tournament_id,
				'tournament_sport_id'   => $tournament_sport_id,
				'parent_tournament_id'  => $parent_tournament_id,
				'name'                  => $name,
				'description'           => $description,
				'start_currency'        => $start_currency * 100,
				'start_date'            => $tournament_start_date,
				'end_date'              => $tournament_end_date,
				'jackpot_flag'          => $jackpot_flag,
				'buy_in'                => $buyin->buy_in * 100,
				'entry_fee'             => $buyin->entry_fee * 100,
				'minimum_prize_pool'    => $minimum_prize_pool * 100,
				'paid_flag'             => 0,
				'auto_create_flag'      => 0,
				'cancelled_flag'        => 0,
				'cancelled_reason'      => '',
				'private_flag'          => $private_flag,
				'status_flag'           => $status_flag
			);
		}
		$result = $tournament_model->store($tournament_params);
		if($tournament_id < 1) $tournament_id = $result;

		/**
		 * Adding data to Sport Event table
		 * Using TournamentSportEvent Modal object
		 */
		

		$tournament_event_params = array(
			'tournament_id'   						=> $tournament_id,
			'tournament_event_id'					=> $event_id,
			'closed_betting_on_first_match_flag'	=> $all_betting_closed,
			'betting_closed_date'					=> $betting_closed_date,
			'reinvest_winnings_flag'				=> $reinvest_winnings_flag,
		);
		$tournament_event_model	=& $this->getModel('TournamentSportEvent', 'TournamentModel');
		$result 				= $tournament_event_model->store($tournament_event_params);
		
		return true;
	}
	/**
	 * Cloning a Sport Tournament
	 */
	public function cloneTournament(){
		$tournament_id			= JRequest::getVar('id', '');
		$tournament_model 		=& $this->getModel('Tournament', 'TournamentModel');
		$tournament_event_model	=& $this->getModel('TournamentSportEvent', 'TournamentModel');

		$clone_id = $tournament_model->cloneTournament($tournament_id);
		if($clone_id) {
			$result = $tournament_event_model->cloneTournamentSportEvent($clone_id, $tournament_id);
			$this->setRedirect($this->controller_url . "&task=edit&id=" . $clone_id);
			$this->setMessage(JText::_('Tournament Cloned'));
		}
	}

	/**
	 * Unregister an entrant
	 *
	 * @return void
	 */
	public function unregister() {
		$tournament_id		= JRequest::getVar('id', 0);
		$user_id			= JRequest::getVar('user', 0);
		$redirect_url		= $this->controller_url . "&task=view&id=" .$tournament_id;
		$tournament_model	=& $this->getModel('Tournament', 'TournamentModel');
		$tournament			= $tournament_model->getTournament($tournament_id);

		if(is_null($tournament)) {
			$this->setRedirect($redirect_url, JText::_('Tournament not found'), 'error');
			return;
		}

		$user =& JFactory::getUser($user_id);
		if($user->id < 1) {
			$this->setRedirect($redirect_url, JText::_('User not found'), 'error');
			return;
		}

		$err = '';
		if($tournament_model->isFinished($tournament)) {
			$this->setRedirect($redirect_url, JText::_('Tournament already ended. Cannot unregister.'), 'error');
			return;
		}

		$message = '';
		$type = '';
		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
		if($ticket = $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id)) {
			$tournament_dollars =& $this->getModel('Tournamenttransaction', 'TournamentDollarsModel');
			if($ticket_model->refundTicketAdmin($tournament_dollars, $ticket->id, true)) {
				$leaderboard_model =& $this->getModel('TournamentLeaderboard', 'TournamentModel');
				$leaderboard_model->deleteByUserAndTournamentID($user->id, $tournament->id);
				$message  = JText::_('Ticket has been refunded');
				$type     = 'message';
			} else {
				$message  = JText::_('Ticket could not be refunded');
				$type     = 'error';
			}
		} else {
			$message = JText::_('Invalid ticket');
			$type     = 'error';
		}

		$this->setRedirect($redirect_url, JText::_($message), $type);
	}

}
