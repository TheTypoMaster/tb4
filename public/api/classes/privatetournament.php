<?php 
/**
 * @version		$Id: racing.php  Michael Costa $
 * @package		API
 * @subpackage
 * @copyright	Copyright (C) 2012 Michael Costa. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
jimport('joomla.application.component.controller');

class Api_PrivateTournament extends JController {
     
	 function Api_PrivateTournament() {

	 }

     /**
	  * Method getPrivateTournamentForm
	  * Params none
	  * 
	  * Description Pre-requisite for createPrivateTournament method. 
	  *             Gets the data to display in the Private tournament form 
	  */
     public function getPrivateTournamentForm() {
        
		$user =& JFactory::getUser();

        if ($user->guest) {
			return OutputHelper::json(500, array('error' => 'Please login to create a tournament'  ));
		}

		$sport_id			= JRequest::getVar('sport_id', null);
		$competition_id		= JRequest::getVar('competition_id', null);
		$event_group_id		= JRequest::getVar('event_group_id', null);
		$from_tournament_id	= JRequest::getVar('from_tournament_id', null);
        
        
		//set up sports
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentsport.php');
		$sport_model		= new TournamentModelTournamentSport();
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentcompetition.php');
		$competition_model	= new TournamentModelTournamentCompetition();
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentsportevent.php');
		$sport_event_model	=new TournamentModelTournamentSportEvent();

		
		//$view =& $this->getView('Tournament', 'html', 'TournamentView');
        
		$session	=& JFactory::getSession();
		$formerrors	= array();
		if ($sessFormData	= $session->get('sessFormData', null, 'privatetournament')) {
			
			if ($formerrors	= $session->get('sessFormErrors', null, 'privatetournament')) {
				$session->clear('sessFormErrors', 'privatetournament');
			}

			foreach ($sessFormData as $k => $data) {
				$formdata[$k] = stripslashes($data);
			}

			$sport_id		= $formdata['sport_id'];
			$competition_id	= $formdata['competition_id'];
			$event_group_id	= $formdata['event_group_id'];

			$session->clear('sessFormData', 'privatetournament');
		}
		
		
		//$view->assignRef('formerrors', $formerrors);
		
		$sport_list			= $sport_event_model->getActiveTournamentSportList(0,1);
		if (is_null($sport_id)) {
			$sport_id = $sport_list[0]->id;
		}

		require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamenteventgroup.php');
		$event_group_model	= new TournamentModelTournamentEventGroup();
		
		$event_group_list 	= array();
		$competition_list 	= array();
		
		foreach($sport_list as $sp_list)
		{	
			$comp_list	= $competition_model->getActiveTournamentCompetitionListBySportID($sp_list -> id, 0);
			
			//get compition list
			$competition_list[]	= array('sport_id' => $sp_list -> id, 'competition' => $competition_model->getActiveTournamentCompetitionListBySportID($sp_list -> id, 0));
			
			foreach($comp_list as $com_list)
			{
				//get event group list
				$event_group_list[]	= array('sport_id' 		=> $sp_list -> id,
											'competition_id'=> $com_list -> id, 
											'event' 		=> $event_group_model->getActiveTournamentEventGroupListByCompetitonID($com_list -> id));
			}
		}
		
		if(is_null($competition_id)) 
		{
			$competition_list4id = $competition_model->getActiveTournamentCompetitionListBySportID($sport_id, 0);
			$competition_id = $competition_list4id[0] -> id;
		}
		
		//get pre-population field
		$formdata = array(
					'sport_id' 				=> $sport_id,
					'competition_id'		=> $competition_id,
					'event_group_id'		=> $event_group_id,
					'from_tournament_id'	=> $from_tournament_id,
				);
		        
		//set up prize formats
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentprizeformat.php');
		$prize_format_model	= new TournamentModelTournamentPrizeFormat();
		$prize_format_list	= $prize_format_model->getTournamentPrizeFormatsApi();

        
       //set up buy-ins
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentbuyin.php');
		$buyin_model		= new TournamentModelTournamentBuyIn();
		$buyin_list			= $buyin_model->getTournamentBuyInListApi();

		
		$data = array('sport_list' => $sport_list ,
			          'competition_list' => $competition_list ,
			          'event_group_list' => $event_group_list ,
			          'prize_format_list' => $prize_format_list ,
			          'buyin_list' => $buyin_list ,
			          'formdata' => $formdata ,
			          'from_tournament_id' => $from_tournament_id ,
			          'formerrors' => $formerrors );
		
		return OutputHelper::json(200, $data );

		//$view->setModel($sport_model);
		//$view->setModel($competition_model);
		//$view->setModel($event_group_model);
		//$view->setModel($prize_format_model);
		//$view->setModel($buyin_model);

		//$view->assignRef('sport_list', $sport_list);
		//$view->assignRef('competition_list', $competition_list);
		//$view->assignRef('event_group_list', $event_group_list);
		//$view->assignRef('prize_format_list', $prize_format_list);
		//$view->assignRef('buyin_list', $buyin_list);
		//$view->assignRef('formdata', $formdata);
		//$view->assignRef('from_tournament_id', $from_tournament_id);

		//$view->display();
         
	 }

	 /**
	 * Register private tournaments
	 *
	 * @return void
	 */
	public function registerPrivateTournament()
	{
		$user =& JFactory::getUser();

		if ($user->guest) {
			return OutputHelper::json(500, array('error' => 'Please login to register a tournament'  ));
		}
        
        
		$session =& JFactory::getSession();

		// begin the painstaking task of validating a tournament
		$sport_id			= JRequest::getVar('sport_id', null);
		$competition_id		= JRequest::getVar('competition_id', null);
		$event_group_id		= JRequest::getVar('event_group_id', null);
		$buyin_id			= JRequest::getVar('buyin_id', null);
		$prize_format_id	= JRequest::getVar('prize_format_id', null);
		$tournament_name	= JRequest::getVar('tournament_name', null);
		$required_password	= JRequest::getVar('required_password', 0);
		$password			= JRequest::getVar('password', null);
		$from_tournament_id	= JRequest::getVar('from_tournament_id', null);

		$error 			= array();
		//$redirect_link	= '/index.php?option=com_tournament&task=privatetournament&format=raw';

        require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournament.php');
		$tournament_model	= new TournamentModelTournament();
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentprivate.php');
		$tournament_private_model	=new TournamentModelTournamentPrivate();
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentbuyin.php');
		$buyin_model		= new TournamentModelTournamentBuyIn();
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentprizeformat.php');
		$prize_format_model	= new TournamentModelTournamentPrizeFormat();
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentsport.php');
		$sport_model		= new TournamentModelTournamentSport();
		
       
		//validations
		$is_racing	= ($sport_id == 'racing');
		if ($is_racing) {
			if (!in_array($competition_id, $sport_model->excludeSports)) {
				$error['competition_id'] = JText::_('Invalid competition');
			}
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'meeting.php');
			$meeting_model		= new TournamentModelMeeting();
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'meetingtype.php');
			$meeting_type_model		= new TournamentModelMeetingType();
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentracing.php');
			$racing_model		= new TournamentModelTournamentRacing();
			
			$meeting_type_id = $meeting_type_model->getMeetingTypeIDByName($competition_id);
			$event_list		= $meeting_model->getActiveMeetingListByMeetingTypeID($meeting_type_id);

			if (!isset($event_list[$event_id])) {
				$error['event_id'] = JText::_('Invalid event');
			}
		} else {
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentsportevent.php');
			$tournament_sport_event		= new TournamentModelTournamentSportEvent();
			require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamentcompetition.php');
			$competition_model		= new TournamentModelTournamentCompetition();
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamenteventgroup.php');
			$event_group_model		= new TournamentModelTournamentEventGroup();

			
			if (empty($sport_id)) {
				$error['sport_id'] = JText::_('Please select a sport');
			} else {
				$sport = $sport_model->getTournamentSport($sport_id);

				if (empty($sport)) {
					$error['sport_id'] = JText::_('Invalid sport');
				}
			}

			if (empty($competition_id)) {
				$error['competition_id'] = JText::_('Please select a competition');
			} else {
				$competition = $competition_model->getTournamentCompetition($competition_id);

				if (empty($competition)) {
					$error['competition_id'] = JText::_('Invalid competition');
				}
			}

			if (empty($event_group_id)) {
				$error['event_group_id'] = JText::_('Please select an event');
			} else {
				$event_group_list = $event_group_model->getActiveTournamentEventGroupListByCompetitonID($competition->id);
				if (!isset($event_group_list[$event_group_id])) {
					$error['event_group_id'] = JText::_('Invalid event');
				} else {
					$event_group = $event_group_model->getEventGroup($event_group_id);
				}
			}
		}

		if (empty($buyin_id)) {
			$error['buyin_id'] = JText::_('Please select a buy in option');
		} else {
			$buyin = $buyin_model->getTournamentBuyIn($buyin_id);

			//TODO: MC - Why is this checking for 1 and not valid?
			if (empty($buyin) || ($buyin->buy_in < 2 && $buyin->buy_in != 0) || $buyin->buyin > 100) {
				//$error['buyin_id'] = JText::_('Invalid buy-in option');
			}
		}

		if (empty($prize_format_id)) {
			$error['prize_format_id'] = JText::_('Please select a prize format');
		} else {
			$prize_format = $prize_format_model->getTournamentPrizeFormat($prize_format_id);

			if (empty($prize_format)) {
				$error['prize_format_id'] = JText::_('Invalid prize format');
			} else if (is_object($buyin) && 0 == $buyin->buy_in && $prize_format->keyword != 'all') {
				$error['prize_format_id'] = JText::_('Invalid Option');
			}
		}

		$tournament_name_len = strlen($tournament_name);
		if (0 == $tournament_name_len) {
			$error['tournament_name'] = JText::_('Please enter the tournament name');
		} else if ($tournament_name_len < 5 || $tournament_name_len > 75) {
			$error['tournament_name'] = 'Tournament name should between 5-75 characters long';
		}

		$password_len = strlen($password);
		if ($required_password && 0 == $password_len) {
			$error['password'] = JText::_('Please enter the tournament password');
		} else if ($required_password && $password_len < 5) {
			$error['password'] = JText::_('Passwords should be at least 5 characters long');
		}

		if (!$required_password && $password) {
			$error['required_password'] = JText::_('Please tick the box to enable password');
		}

		if ($error) {
			 return OutputHelper::json(500, array('error_msg' => $error ));
		}
         
		 $tournament_sport_id			= $sport_id;
		 require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'tournamenteventgroup.php');
		 $tournament_event_group_model		= new TournamentModelTournamentEventGroup();
		 $tournament_event_group			= $tournament_event_group_model->getEventGroupFirstAndLastEventTimeByEventGroupID($event_group_id);
		
		if (!empty($tournament_event_group->first_match_time)) {
			$start_date				= $tournament_event_group->first_match_time;
			$end_date				= $tournament_event_group->last_match_time;
		} else {
			$event_group	= $tournament_event_group_model->getEventGroup($event_group_id);
			$start_date	= $end_date = $event_group->start_date;
		}

		$description = $user->username . ' created this tournament on ' . date('d/m/y') . '. The total entry fee is $' . number_format($buyin->buy_in+$buyin->entry_fee, 2) . '. The prize format is "' . $prize_format->name . '".';

		//save tournament
		$params = array(
			'tournament_sport_id'					=> $tournament_sport_id,
			'parent_tournament_id'					=> -1,
			'event_group_id'						=> $event_group->id,
			'name'									=> $tournament_name,
			'description'							=> $description,
			'start_currency'						=> 100000,
			'start_date'							=> $start_date,
			'end_date'								=> $end_date,
			'jackpot_flag'							=> 0,
			'buy_in'								=> $buyin->buy_in * 100,
			'entry_fee'								=> $buyin->entry_fee * 100,
			'minimum_prize_pool'					=> 0,
			'paid_flag'								=> 0,
			'auto_create_flag'						=> 0,
			'cancelled_flag'						=> 0,
			'cancelled_reason'						=> '',
			'status_flag'							=> 1,
			'private_flag'							=> 1,
			'closed_betting_on_first_match_flag'	=> 0,
			'betting_closed_date'					=> $end_date,
			'reinvest_winnings_flag'				=> 1,
		);
		
        
		$tournament_id = $tournament_model->store($params);
		
		if (empty($tournament_id)) {
			return OutputHelper::json(500, array('error_msg' => 'Sorry, we were unable to create your private tournament.Please try again later.' ));
			//$error['general'] = JText::_("Sorry, we were unable to create your private tournament.Please try again later.");
			//$session->set('sessFormErrors', $error, 'privatetournament');
			
		}

		$private_params = array(
			'tournament_id'					=> $tournament_id ,
			'tournament_prize_format_id'	=> $prize_format->id,
			'user_id'						=> $user->id,
			'display_identifier'			=> $this->_generatePrivateTournamentCode($tournament_private_model),
			'password'						=> $password,
		);
		$private_tournament_id = $tournament_private_model->store($private_params);

		if (empty($private_tournament_id)) {
			return OutputHelper::json(500, array('error_msg' => 'Sorry, we were unable to create your private tournament.Please try again later.' ));
			//$error['general'] = JText::_("Sorry, we were unable to create your private tournament. Please try again later.");
			//$session->set('sessFormErrors', $error, 'privatetournament');
			
		}

        return OutputHelper::json(200, array('msg' => 'Success - Your private tournament has been created!' , 'tournament_id' => $tournament_id, 'tournament_code' => $private_params['display_identifier'] ));
		
	}

	private function _generatePrivateTournamentCode($private_tournament_model) {
		$code 		= '';
		$code_pool	= '2346789bcdfghjkmnpqrtvwxyz';
		$pool_len	= strlen($code_pool);

		$i = 0;
		while ($i < 6) {
			$code .= substr($code_pool, mt_rand(0, $pool_len-1), 1);
			$i++;
		}

		if ($private_tournament_model->getTournamentPrivateByIdentifier($code)) {
			$code = $this->_generatePrivateTournamentCode($private_tournament_model);
		}

		return $code;
	}


}
?>
