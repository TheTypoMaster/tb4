<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once JPATH_COMPONENT . DS . 'controller.php';

class TournamentRacingController extends TournamentController
{
	/**
	 * Display the list of racing tournaments
	 *
	 * @return void
	 */
	public function display() {
		global $mainframe, $option;

		$tournament_model =& $this->getModel('TournamentRacing', 'TournamentModel');
		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
		$filter_prefix = 'racingtournament';

		$tournament_type = $mainframe->getUserStateFromRequest(
		$filter_prefix.'private',
			'private',
		0
		);

		$order = $mainframe->getUserStateFromRequest(
		$filter_prefix . 'filter_order',
			'filter_order',
			't.id'
			);

			$direction = strtoupper($mainframe->getUserStateFromRequest(
			$filter_prefix . 'filter_order_Dir',
			'filter_order_Dir',
			'ASC'
			));

			$limit = $mainframe->getUserStateFromRequest(
			'global.list.limit',
			'limit',
			$mainframe->getCfg('list_limit')
			);

			$offset = $mainframe->getUserStateFromRequest(
			$filter_prefix . 'limitstart',
			'limitstart',
			0
			);

			$tournament_list = $tournament_model->getTournamentRacingList($tournament_type, $order, $direction, $limit, $offset);
			foreach($tournament_list as $tournament) {
				$tournament->entrants = $ticket_model->countTournamentEntrants($tournament->id);
			}

			jimport('joomla.html.pagination');
			$total = $tournament_model->getTotalTournamentRacingCount($tournament_type);
			$pagination = new JPagination($total, $offset, $limit);

			$view =& $this->getView('TournamentRacing', 'html', 'TournamentView');
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
	public function edit() {
		$tournament_id = JRequest::getVar('id', 0);

		// get number entrants
		if($tournament_id) {
			$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
			$entrants = $ticket_model->countTournamentEntrants($tournament_id);
		}

		// get tournament model
		$tournament_model =& $this->getModel('TournamentRacing', 'TournamentModel');
		$tournament_model->tournament_id = $tournament_id;

		// get meeting model
		$meeting_model =& $this->getModel('Meeting', 'TournamentModel');

		// get buy-in model
		$buyin_model = & $this->getModel('TournamentBuyIn', 'TournamentModel');
		if(!empty($tournament_id)) {
			$current_buy_in = $buyin_model->getBuyInByTournamentID($tournament_id);
		}

		// get the bet limit model
		$betlimit_model =& $this->getModel('TournamentBetLimit', 'TournamentModel');
		$betlimit_model->tournament_id = $tournament_id;

		// get the bet type model
		$bettype_model	=& $this->getModel('BetType', 'BettingModel');
		$bettype_list	= $bettype_model->getBetTypesByStatus(1, 'racing');

		// set up for display
		$view =& $this->getView('TournamentRacing', 'html', 'TournamentView');
		$view->setLayout('form');

		$view->setModel($tournament_model);
		$view->setModel($meeting_model);
		$view->setModel($buyin_model);
		$view->setModel($betlimit_model);
		$view->setModel($bettype_model);

		$view->tournament_id = $tournament_id;
		$view->current_buy_in = $current_buy_in;
		$view->entrants_disable = ($entrants);

		$view->assign('bettype_list', $bettype_list);

		$view->display();
	}

	/**
	 * Save tournament data from the edit form
	 *
	 * @return bool
	 */
	public function save() {
		// redirect back to the list when complete by default
		$this->setRedirect('index.php?option=com_tournament&controller=tournamentracing');
		$session =& JFactory::getSession();

		// models needed in a few places
		$tournament_model =& $this->getModel('Tournament', 'TournamentModel');
		$racing_model =& $this->getModel('TournamentRacing', 'TournamentModel');

		$submitted_id = JRequest::getVar('id', null);
		if(!empty($submitted_id)) {
			$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
			$entrants = $ticket_model->countTournamentEntrants($submitted_id);

			// only save the tournament name & description if players
			if(!empty($entrants)) {
				$changed = false;

				$tournament = $tournament_model->getTournament($submitted_id);
				$audit_model =& $this->getModel('TournamentAudit','TournamentModel');

				$tournament_params = array(
					'id' 			=> $submitted_id,
					'name' 			=> JRequest::getVar('name', ''),
					'description' 	=> JRequest::getVar('description', ''),
				);

				foreach($tournament_params as $field => $param){
					if($field != 'id'){
						if($param != $tournament->{$field}){
							$audit_params = array(
								'tournament_id' => $submitted_id,
								'field_name'	=> $field,
								'old_value'		=> $tournament->{$field},
								'new_value'		=> $param
							);

							$audit_model->store($audit_params);
							$changed = true;
						}
					}
				}

				if($changed){
					$tournament_id = $tournament_model->store($tournament_params);
				}

				return true;
			}
		}

		// setup meeting data
		$meeting_model =& $this->getModel('Meeting', 'TournamentModel');
		$meeting_type_model =& $this->getModel('MeetingType', 'TournamentModel');
		$meeting_id = JRequest::getVar('meeting_id', -1);
		$tab_meeting_id = JRequest::getVar('tab_meeting_id', null);
		$start_currency = JRequest::getVar('start_currency', 1000);
		$name = JRequest::getVar('name', null);
		$tod_flag = JRequest::getVar('tod_flag', '');
		$free_credit_flag = JRequest::getVar('free_credit_flag', 0);

		if($meeting_id == -1 && empty($tab_meeting_id)) {
			JError::raiseWarning(0, 'No meeting ID was provided');
			return false;
		}

		if(($submitted_id || !empty($tab_meeting_id) )&& empty($name)) {
			JError::raiseWarning(0, 'You need to provide a name for the tournament');
			return false;
		}

		// perform the import if the meeting is not being based on a future meeting ID
		if($meeting_id == -1) {
			$date =
			substr($tab_meeting_id, 3, 4) . '-'
			. substr($tab_meeting_id, 7, 2) . '-'
			. substr($tab_meeting_id, 9, 2);

			$meeting_code = substr($tab_meeting_id, 0, 2);
			$meeting_type_id = $meeting_type_model->getMeetingTypeIdByCode($tab_meeting_id{1});

			$meeting_data = $meeting_model->getMeetingByMeetingCodeAndDate($meeting_code, $date);
			if(is_null($meeting_data)) {
				$meeting_params = array(
					'name'            	=> $name,
					'meeting_code'    	=> $meeting_code,
					'meeting_type_id' 	=> $meeting_type_id,
					'meeting_date'    	=> $date
				);

				$meeting_id = $meeting_model->store($meeting_params);
			} else {
				$meeting_id = $meeting_data->id;
			}
		}

		// get the meeting data record
		$meeting_data = $meeting_model->getMeeting($meeting_id);

		if(empty($meeting_data)) {
			JError::raiseWarning(0, 'Unknown meeting');
			return false;
		}

		$meeting_type_data = $meeting_type_model->getMeetingType($meeting_data->meeting_type_id);

		// setup sport data
		$sport_model =& $this->getModel('TournamentSport', 'TournamentModel');
		$sport_data = $sport_model->getTournamentSportByName($meeting_type_data->name);

		if(empty($sport_data)) {
			JError::raiseWarning(0, 'Unknown sport specified');
			return false;
		}

		// buy-in and entry-fee
		$buyin_id = JRequest::getVar('tournament_value', 1);
		$buyin_model =& $this->getModel('TournamentBuyIn', 'TournamentModel');
		$buyin = $buyin_model->getTournamentBuyIn($buyin_id);

		if(empty($buyin)) {
			JError::raiseWarning(0, 'Unknown buy-in and entry-fee formula selected');
			return false;
		}
	
		if (empty($submitted_id) && empty($name)) {
			$name = $this->_generateTournamentAutomatedText('name');
		}
		
		$description = JRequest::getVar('description', '');
		if (empty($submitted_id) && empty($description)) {
			$description = $this->_generateTournamentAutomatedText('description');
		}
		
			
		// main tournament record
		$tournament_params = array(
			'id'                    => $submitted_id,
			'tournament_sport_id'   => $sport_data->id,
			'parent_tournament_id'  => JRequest::getVar('parent_tournament_id', null),
			'name'                  => $name,
			'description'           => $description,
			'start_currency'        => $start_currency * 100,
			'jackpot_flag'          => JRequest::getVar('jackpot_flag', 0),
			'buy_in'                => ($buyin->buy_in * 100),
			'entry_fee'             => ($buyin->entry_fee * 100),
			'minimum_prize_pool'    => (JRequest::getVar('minimum_prize_pool', 0) * 100),
			'paid_flag'             => 0,
			'auto_create_flag'      => 0,
			'cancelled_flag'        => 0,
			'cancelled_reason'      => '',
			'status_flag'           => 0,
			'tod_flag'           	=> strtoupper($tod_flag),
			'free_credit_flag'      => (int)$free_credit_flag
		);

		if(empty($submitted_id)) {
			$start_time = JRequest::getVar('start_time', null);

			if(!preg_match('/[0-9]{2}:[0-9]{2}:[0-9]{2}/', $start_time)) {
				JError::raiseWarning(0, 'Invalid start time provided, using default');
				$start_time = null;
			}

			if(is_null($start_time)) {
				$start_date = $meeting_data->meeting_date . ' 00:00:00';
				$end_date	= $meeting_data->meeting_date . ' 12:00:00';
			} else {
				$start_date = "{$meeting_data->meeting_date} {$start_time}";
				$end = new DateTime($start_date);

				$end->modify('+1 day');
				$end_date = $end->format('Y-m-d H:i:s');
			}


			$tournament_params['start_date'] 	= $start_date;
			$tournament_params['end_date'] 		= $end_date;
		}

		$tournament_id = $tournament_model->store($tournament_params);

		// if editing there is no need to update the mapping
		if(empty($submitted_id)) {
			if(empty($tournament_id)) {
				JError::raiseWarning(0, 'Tournament could not be saved');
				return false;
			}

			$racing_params = array(
				'tournament_id' 	=> $tournament_id,
				'meeting_id' 		=> $meeting_id
			);

			$tournament_racing_id = $racing_model->store($racing_params);

			if(empty($tournament_racing_id)) {
				JError::raiseWarning(0, 'Tournament racing mapping could not be saved');
				return false;
			}
		} else {
			$tournament_id = $submitted_id;
		}

		// get the availabe bet types
		$bettype_model =& $this->getModel('BetType', 'BettingModel');
		$bettype_list = $bettype_model->getBetTypesByStatus();

		// save the betlimit values
		$betlimit_model =& $this->getModel('TournamentBetLimit', 'TournamentModel');
		$betlimit_list = $betlimit_model->getBetLimitsByTournamentID($tournament_id);

		foreach($bettype_list as $bettype) {
			$value = JRequest::getVar('betlimit_' . $bettype->id, null);
			if(empty($value)) {
				if(!empty($betlimit_list[$bettype->id])) {
					$betlimit_model->deleteBetLimitByID($betlimit_list[$bettype->id]->id);
				}
			} else {
				$betlimit_params = array(
					'id'            	=> (!empty($betlimit_list[$bettype->id]) ? $betlimit_list[$bettype->id] : null),
					'tournament_id' 	=> $tournament_id,
					'bet_type_id'   	=> $bettype->id,
					'value'         	=> $value
				);

				$betlimit_model->store($betlimit_params);
			}
		}

		// all done, so publish the tournament if needed
		$status_flag = JRequest::getVar('status_flag', 0);
		if(!empty($status_flag)) {
			if(!$tournament_model->updateTournamentStatus($tournament_id, 1)) {
				JError::raiseWarning(0, 'Tournament created but could not be published');
				return false;
			}
		}

		return true;
	}
	/**
	 * Cloning a Sport Tournament
	 */
	public function cloneTournament(){
		$tournament_id			= JRequest::getVar('id', '');
		$tournament_model 		=& $this->getModel('Tournament', 'TournamentModel');
		$tournament_race_model	=& $this->getModel('TournamentRacing', 'TournamentModel');

		$clone_id = $tournament_model->cloneTournament($tournament_id);
		if($clone_id) {
			$result = $tournament_race_model->cloneTournamentRacingMeeting($clone_id, $tournament_id);
			$this->setRedirect("index.php?option=com_tournament&controller=tournamentracing&task=edit&id=" . $clone_id);
			$this->setMessage(JText::_('Tournament Cloned'));
		}
	}
	/**
	 * Cancel task to return to the tournament list
	 *
	 * @return void
	 */
	public function cancel() {
		$this->setRedirect('index.php?option=com_tournament&controller=tournamentracing');
	}

	/**
	 * Unregister an entrant
	 *
	 * @return void
	 */
	public function unregister() {
		$tournament_id = JRequest::getVar('id', 0);
		$user_id = JRequest::getVar('user', 0);

		$racing_model =& $this->getModel('TournamentRacing', 'TournamentModel');
		$tournament = $racing_model->getTournamentRacingByTournamentID($tournament_id);

		if(is_null($tournament)) {
			$this->setRedirect('index.php?option=com_tournament&controller=tournamentracing', JText::_('Tournament not found'), 'error');
			return;
		}

		$user =& JFactory::getUser($user_id);

		if($user->id == 0) {
			$this->setRedirect('index.php?option=com_tournament&controller=tournamentracing', JText::_('User not found'), 'error');
			return;
		}

		$err = '';
		$tournament_model =& $this->getModel('Tournament', 'TournamentModel');
		if($tournament_model->isFinished($tournament)) {
			$this->setRedirect('index.php?option=com_tournament&controller=tournamentracing', JText::_('All races are completed. Cannot unregister.'), 'error');
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

		$this->setRedirect('index.php?option=com_tournament&controller=tournamentracing&task=view&id=' . $tournament->id, JText::_($message), $type);
	}
}
