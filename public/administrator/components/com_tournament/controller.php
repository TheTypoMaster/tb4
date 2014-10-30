<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class TournamentController extends JController
{
	public function listView()
	{
		list($order, $direction, $limit, $offset) = ListViewHelper::getParameterList('tournament');

		$keyword		= JRequest::getVar('keyword', null);
		$private_flag	= JRequest::getVar('private_flag', 0);
		$start_date		= JRequest::getVar('start_date', null);
		$end_date		= JRequest::getVar('end_date', null);
		
		$params = array(
			'keyword'		=> $keyword,
			'private_flag'	=> $private_flag,
			'start_date'	=> $start_date,
			'end_date'		=> $end_date,
			'order'			=> $order,
			'direction'		=> $direction,
			'limit'			=> $limit,
			'offset'		=> $offset
		);
		
		$tournament_model 	=& JModel::getInstance('Tournament', 'TournamentModel');
		$tournament_list 	= $tournament_model->getTournamentAdminList($params);

		$ticket =& JModel::getInstance('TournamentTicket', 'TournamentModel');
		foreach($tournament_list as &$tournament) {
			$tournament->entrant_count	= $ticket->countTournamentEntrants($tournament->id);
			$tournament->prize_pool		= $tournament_model->calculateTournamentPrizePool($tournament->id);
		}

		jimport('joomla.html.pagination');
		
		$tod_list = array();
		$tod_list = $tournament_model->getAppTournamentOfTheDay();

		$tournament_count 	= $tournament_model->getTotalTournamentCount($params);
		$pagination 		= new JPagination($tournament_count, $offset, $limit);

		$view = $this->getView('Tournament', 'html', 'TournamentView');
		$view->setLayout('listview');

		$view->assign('tod_list', $tod_list);
		$view->assign('tournament_list', $tournament_list);
		$view->assign('pagination', $pagination->getPagesLinks());

		$view->assign('order', $order);
		$view->assign('direction', $direction);

		$view->assign('limit', $limit);
		$view->assign('offset', $offset);

		$view->assign('keyword', $keyword);
		$view->assign('private_flag', $private_flag);
		$view->assign('start_date',	$start_date);
		$view->assign('end_date', $end_date);
		
		$view->display();
	}

	/**
	 * Display the view form
	 *
	 * @return void
	 */
	public function view()
	{
		$tournament_id 		= JRequest::getVar('id', 0);

		$tournament_model 	=& $this->getModel('Tournament', 'TournamentModel');
		$sport_model =& $this->getModel('TournamentSport', 'TournamentModel');
		
		$is_racing_tournament = $sport_model->isRacingByTournamentId($tournament_id);

		if($is_racing_tournament){
			$racing_model =& $this->getModel('TournamentRacing', 'TournamentModel');
			$tournament = $racing_model->getTournamentRacingByTournamentID($tournament_id);
			$tournament->event_group_id = $tournament->meeting_id;
		}
		else{
			$sport_event_model =& $this->getModel('TournamentSportEvent', 'TournamentModel');
			$tournament = $sport_event_model->getTournamentSportsByTournamentID($tournament_id);
		}

		if(is_null($tournament)) {
			$this->setRedirect('index.php', JText::_('Tournament not found'));
			return;
		}

		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
		
		$player_list        = $ticket_model->getTournamentEntrantList($tournament->id);

		$leaderboard_model 	=& $this->getModel('TournamentLeaderboard', 'TournamentModel');
		$leaderboard 		= $leaderboard_model->getLeaderBoardRank($tournament);

		$tournament_model 		=& $this->getModel('Tournament', 'TournamentModel');
		$prize_pool   			= $tournament_model->calculateTournamentPrizePool($tournament->id);
		
		$tournament_completed 	= $tournament_model->isFinished($tournament);
		/**
		 * Private Tournament
		 */
		$private_tournament = null;
		if($tournament->private_flag > 0){
			$private_tournament_model 	=& $this->getModel('TournamentPrivate', 'TournamentModel');
			$private_tournament			= $private_tournament_model->getPrivateTournamentCreatorInfoByTournamentID($tournament->id);
		}
		$place_list   = $tournament_model->calculateTournamentPlacesPaid($tournament, count($player_list), $prize_pool);
		
		// Get tournament features
		// $tournament_feature_list = JModel::getInstance('TournamentFeature', 'TournamentModel')->getTournamentFeatureList();

		$parent_link = null;
		if(!empty($tournament->jackpot_flag) && !empty($tournament->parent_tournament_id)) {
			$parent_link = JRoute::_('/index.php?option=com_tournament&task=view&id='. $tournament->parent_tournament_id);
		}
		$event_group_model 	= & $this->getModel('EventGroup', 'TournamentModel');
		$event_group		= $event_group_model->getEventGroup($tournament->event_group_id);

		$ticket_model 	=& $this->getModel('TournamentTicket', 'TournamentModel');
		$entrants 		= $ticket_model->countTournamentEntrants($tournament_id);

		$view =& $this->getView('Tournament', 'html', 'TournamentView');
		$view->setLayout('viewform');

		$view->assignRef('tournament', $tournament);
		$view->assignRef('private_tournament', $private_tournament);

		$view->assign('place_list', $place_list);
		$view->assign('prize_pool', $prize_pool);
		$view->assignRef('player_list', $player_list);
		$view->assignRef('leaderboard', $leaderboard);
		$view->assign('parent_link', $parent_link);
		$view->assign('entrants', $entrants);
		$view->assign('event_group', $event_group->name);
		//$view->assign('tournament_feature_list', $tournament_feature_list);
		$view->assign('is_racing_tournament', $is_racing_tournament);
		
		$view->display();
	}

	private function _getFieldList()
	{
		static $field_list = array(
			'tournament_sport_id' 					=> -1,
			'tournament_competition_id' 			=> -1,
			'event_group_id' 						=> -1,
			'ticket_value' 							=> -1,
			'minimum_prize_pool' 					=> 10,
			'jackpot_flag' 							=> 0,
			'parent_tournament_id' 					=> -1,
			'name' 									=> '',
			'start_currency' 						=> 1000,
			'description' 							=> '',
			'meeting_code'							=> '',
			'start_date'							=> '',
			'start_time'							=> '',
			'end_date'								=> '',
			'end_time'								=> '',
			'closed_betting_on_first_match_flag' 	=> 0,
			'reinvest_winnings_flag'				=> 1,
			'bet_limit_flag'						=> 0,
			'status_flag'							=> 1,
			'betting_closed_date'					=> '',
			'future_meeting_venue'					=> '',
			'tod_flag'								=> '',
			'free_credit_flag'						=> 0,
            'tournament_sponsor_name'                => '',
            'tournament_sponsor_logo'                => '',
            'tournament_sponsor_logo_link'           => '',
            'tournament_prize_format'               => '3',
            'entries_close'                         => '',
			//'feature_keyword'						=> -1
			
		);

		return $field_list;
	}

	public function edit()
	{
		$id 		= JRequest::getVar('id', null);
		$tournament_model = $this->getModel('Tournament', 'TournamentModel');
		//$tournament =& JModel::getInstance('Tournament', 'TournamentModel', $id);
		$tournament = $tournament_model->getTournament($id);

		$session 	=& JFactory::getSession();

		$error_list = $session->get('error_list', null, 'tournament');
		$session->clear('error_list', 'tournament');
		if (is_null($error_list)) {
			$error_list = array();
		}

		$formdata	= $this->_getFieldList();
		$fields		= array_keys($this->_getFieldList());
		
		$buy_in_model	= $this->getModel('TournamentBuyIn', 'TournamentModel');
		$buy_in_list	= $buy_in_model->getTournamentBuyInList();

        $prize_model	= $this->getModel('TournamentPrizeFormat', 'TournamentModel');
        $prize_list	= $prize_model->getTournamentPrizeFormatList();
		
		// Tournament labels model
		$tournament_label_model = $this->getModel('TournamentLabels', 'TournamentModel');
		$tournament_labels = $tournament_label_model->getTournamentLabels();
		
		$entrants	= 0;
		if (empty($id)) {
			$formdata['start_time']	= '12:00:00';
		} else {
			foreach ($fields as $field) {
				if ($field != 'future_meeting_venue') {
					$formdata[$field] = $tournament->$field;
				}
			}
				
			$formdata['ticket_value'] = $buy_in_model->getBuyInByTournamentID($id)->id;
			
			$event_group_model = $this->getModel('EventGroup', 'TournamentModel');
			$event_group = $event_group_model->getEventGroup($tournament->event_group_id);
			$formdata['tournament_competition_id'] = $event_group->tournament_competition_id;
			$formdata['start_date']	= '';
			$formdata['start_time']	= '';
			$formdata['end_date']	= '';
			$formdata['end_time']	= '';
			$formdata['start_currency'] = $formdata['start_currency'] / 100;
			$formdata['minimum_prize_pool'] = $formdata['minimum_prize_pool'] / 100;
			
			$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
			$entrants = $ticket_model->countTournamentEntrants($id);
			
			
		}
		$formdata['tod_flag'] 		= $tournament->tod_flag;
		$formdata['free_credit']	= $tournament->free_credit_flag;

        // tournament sponsor details
        $formdata['tournament_sponsor_name']	= $tournament->tournament_sponsor_name;
        $formdata['tournament_sponsor_logo']	= $tournament->tournament_sponsor_logo;
        $formdata['tournament_sponsor_logo_link']	= $tournament->tournament_sponsor_logo_link;

        //$this->formdata['entries_close']	= $tournament->entries_close;
        $this->formdata['entries_date']	= substr($tournament->entries_close, 0 ,10);
        $this->formdata['entries_time']	= substr($tournament->entries_close, 11, 8);

		//$formdata['tournament_feature_id'] = $tournament->feature_keyword;
		
		if ($sessFormData = $session->get('sessFormData', null, 'tournament'))
		{
			foreach (array_keys($this->_getFieldList()) as $field) {
				if (isset($sessFormData[$field])) {
					$formdata[$field] = stripslashes($sessFormData[$field]);
				}
			}
			$session->clear('sessFormData', 'tournament');
		}
		
		$sport_model = $this->getModel('TournamentSport', 'TournamentModel');
		$sport_list = $sport_model->getTournamentSportList();
		
		$is_racing_sport	= false;
		if ($formdata['tournament_sport_id'] != -1) {
			$sport_selected		= $sport_model->getTournamentSport($formdata['tournament_sport_id']);
			$is_racing_sport	= (bool)$sport_selected->racing_flag;
		}

		$competition_list = array();
		if (!empty($formdata['tournament_sport_id']) && $formdata['tournament_sport_id'] != -1) {
			$competition_list = JModel::getInstance('TournamentCompetition', 'TournamentModel')
									->getTournamentCompetitionListBySportID($formdata['tournament_sport_id']);
						
		}

		$event_group_list = array();
		if (!empty($formdata['tournament_competition_id']) && $formdata['tournament_sport_id'] != -1) {
			$event_group_list = JModel::getInstance('EventGroup', 'TournamentModel')
									->getActiveEventGroupListByCompetitonID($formdata['tournament_competition_id']);
		}
						
		$venue_list = JModel::getInstance('MeetingVenue', 'TournamentModel')
						->getMeetingVenueList();

		$list_params = array(
			'type'	=> ($is_racing_sport ? 'racing' : 'sports'),
			'order'	=> 'lower(t.name)'
		);
		$parent_tournament_list = $tournament_model->getTournamentActiveList($list_params);
		
		$tod_list = array();
		$tod_list = $tournament_model->getAppTournamentOfTheDay();

		//$default_list = FormHelper::getDefaultList($this->_getFieldList(), $tournament, $submit_list);

		$view =& $this->getView('Tournament', 'html', 'TournamentView');
		$view->setLayout('edit');

		$view->assign('tournament', $tournament);
		$view->assign('id',	$id);

		$view->assign('sport_list', $sport_list);
		$view->assign('competition_list', $competition_list);

		$view->assign('parent_tournament_list', $parent_tournament_list);

		$view->assign('event_group_list', $event_group_list);
		$view->assign('buy_in_list', $buy_in_list);
		$view->assign('prize_list', $prize_list);

		$view->assign('venue_list', $venue_list);
		
		

		$view->assign('error_list', $error_list);
		$view->assign('formdata', $formdata);

		$view->assign('is_racing_sport', $is_racing_sport);
		$view->assign('entrants_disable', ($entrants > 0));
		
		$view->assign('tod_list', $tod_list);
		
		/*
		 * Tournament Labels
		 */
		// Get tournament labels
		$tournament_label_list = JModel::getInstance('TournamentLabels', 'TournamentModel')->getTournamentLabels();
		
		// Get labels assigned to tournament
		$tournament_labels_selected_list = JModel::getInstance('TournamentLabels', 'TournamentModel')->getTournamentLabelsByTournamentId($id);
		
		// Assign tournament Labels to the tournament edit view
		$view->assign('tournament_label_list', $tournament_label_list);
		
		// Assign tournament selected Labels to the tournament edit view
		$view->assign('tournament_label_selected_list', $tournament_labels_selected_list);
			
		$view->display();
	}

	public function save()
	{
		
		$id					= JRequest::getVar('id', null);
		$tournament			=& JModel::getInstance('Tournament', 'TournamentModel');
		$tournament_buyin	=& JModel::getInstance('TournamentBuyIn', 'TournamentModel');
		
		$tournament_sport_id		= JRequest::getVar('tournament_sport_id', null);
		$tournament_competition_id	= JRequest::getVar('tournament_competition_id', null);

        $sponser_name   = JRequest::getVar('tournament_sponsor_name', null);
        $sponsor_logo   = JRequest::getVar('tournament_sponsor_logo', null);
        $sponsor_link   = JRequest::getVar('tournament_sponsor_logo_link', null);

       // $entries_close   = JRequest::getVar('entries_close', null);

        $entries_date   = JRequest::getVar('entries_date', null);
        $entries_time   = JRequest::getVar('entries_time', null);


        $name			= JRequest::getVar('name', null);
		$description	= JRequest::getVar('description', null);

        $prize_id       = JRequest::getVar('tournament_prize_format', 3);
		
		$entrants				= 0;
		$is_future_tournament	= false;
		$error_list				= array();
		$type_code_lookup		= array('galloping' => 'R', 'greyhounds' => 'G', 'harness' => 'H');
		
		if (!empty($id)) {
			$tournament = $tournament->load($id);
			$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
			$entrants = $ticket_model->countTournamentEntrants($id);
		} else {
			if (empty($description)) {
				$description = $this->_generateTournamentAutomatedText('description');
			}
			
			if (empty($name)) {
				$name = $this->_generateTournamentAutomatedText('name');
			}
		}
		
		$tournament->id										= (int)$id;
		$tournament->name									= $name;
		$tournament->description							= $description;
        $tournament->tournament_prize_format                = $prize_id;
				
		$sport_model	=& $this->getModel('TournamentSport', 'TournamentModel');
		
		if ($entrants == 0) {
			$sport			= $sport_model->getTournamentSport($tournament_sport_id);
			$is_racing		= in_array($sport->name, $sport_model->excludeSports);
			
			$start_date = trim(sprintf('%s %s',
							JRequest::getVar('start_date', ''),
							JRequest::getVar('start_time', '')));
	
			$end_date 	= $start_date;
			
			$event_group_id			= JRequest::getVar('event_group_id', null);
			$future_meeting_venue	= JRequest::getVar('future_meeting_venue', null);
			
			if (empty($tournament_competition_id) || $tournament_competition_id < 0) {
				$error_list['competition'] = array('Please select a competition');
			}
			
			if ($event_group_id != -1 && $future_meeting_venue != -1) {
				$error_list['event_group'] = array('Could not select both of current meeting and future meeting');
			}
			
			$event_group_id = JRequest::getVar('event_group_id', null);
			
			$event_group_model	=& $this->getModel('EventGroup', 'TournamentModel');
			
			if ($event_group_id != -1) {
				
				$tournament_event	= $event_group_model->getEventGroupFirstAndLastEventTimeByEventGroupID($event_group_id);
	
				$closed_betting_on_first_match_flag = JRequest::getVar('closed_betting_on_first_match_flag', 0);
				
				if ($tournament_event->first_match_time != NULL) {
					$start_date	= $tournament_event->first_match_time;
					$end_date	= $tournament_event->last_match_time;
		
					if ($tournament_event->first_match_time) {
						if ($closed_betting_on_first_match_flag) {
							$betting_closed_date = $tournament_event->first_match_time;
						} else {
							$betting_closed_date = $tournament_event->last_match_time;
						}
					}
				} else if ($tournament_event->first_match_time == NULL && $event_group_id > 0) {
					$tournament_event		= $event_group_model->getEventGroup($event_group_id);
					$start_date	= $end_date = $betting_closed_date = $tournament_event->start_date;
				}
			}
			
			$jackpot_flag			= (int)JRequest::getVar('jackpot_flag', 0);
			$parent_tournament_id	= (int)JRequest::getVar('parent_tournament_id', -1);
			
			if (!$jackpot_flag) {
				$parent_tournament_id = -1;
			}
			
			$tod_flag = JRequest::getVar('tod_flag', null);
			$tod = $tournament->isThereTournamentOfTheDay (substr($start_date,0,10),$tod_flag,(int)$id);
		
			if($tod && JRequest::getVar('tod_flag', null) !== '') 
			{
				foreach($tod as $to)
				{
				$error_list['tod'] = array('One of the tournaments (<a href="index.php?option=com_tournament&task=edit&id='.$to->id. '" >'. $to->name .'</a>) alredy selected as Tournament of the day for ' . substr($start_date,0,10));
				}
			}
			
			$tournament->tournament_sport_id					= (int)$tournament_sport_id;
			$tournament->parent_tournament_id					= $parent_tournament_id;
			$tournament->event_group_id							= (int)$event_group_id;
			$tournament->start_currency							= (int)(JRequest::getVar('start_currency', null) * 100);
			$tournament->start_date								= $start_date;
			$tournament->end_date								= $end_date;
			$tournament->jackpot_flag							= $jackpot_flag;
			$tournament->ticket_value							= JRequest::getVar('ticket_value', 0);
			$tournament->minimum_prize_pool						= (int)(JRequest::getVar('minimum_prize_pool', 0) * 100);
			$tournament->paid_flag								= (int)JRequest::getVar('paid_flag', 0);
			$tournament->auto_create_flag						= (int)JRequest::getVar('auto_create_flag', 0);
			$tournament->cancelled_flag							= (int)JRequest::getVar('cancelled_flag', 0);
			$tournament->cancelled_reason						= JRequest::getVar('cancelled_reason', null);
			$tournament->status_flag							= (int)JRequest::getVar('status_flag', 0);
			$tournament->private_flag							= JRequest::getVar('private_flag', 0);
			$tournament->closed_betting_on_first_match_flag		= 0;
			$tournament->betting_closed_date					= null;
			$tournament->reinvest_winnings_flag					= 1;
			$tournament->bet_limit_flag							= 0;
			$tournament->tod_flag								= strtoupper($tod_flag);
			$tournament->free_credit_flag						= (int)JRequest::getVar('free_credit_flag', 0);

			//$tournament->feature_keyword						= $feature_keyword;

           	//if (!$is_racing) {
				$tournament->closed_betting_on_first_match_flag		= (int)$closed_betting_on_first_match_flag;
				$tournament->betting_closed_date					= $betting_closed_date;
				$tournament->reinvest_winnings_flag					= (int)JRequest::getVar('reinvest_winnings_flag', 0);
				$tournament->bet_limit_flag							= (int)JRequest::getVar('bet_limit_flag', 0);
			//}



			$is_future_tournament = ($is_racing && $future_meeting_venue != -1);
			
			if (!isset($error_list['event_group']) && $is_future_tournament && isset($type_code_lookup[$sport->name])) {
				$meeting_model	=& $this->getModel('meeting', 'TournamentModel');
				if ($meeting_model->getMeetingByNameAndTypeCodeAndDate($future_meeting_venue, $type_code_lookup[$sport->name], strtotime($start_date))) {
					$error_list['event_group'] = array('Meeting already exists');
				}
			}
		}
		
		$tournament->validate();
		
		if ($is_future_tournament) {
			$tournament->clearErrorList('event_group_id');
		}
		$error_list = array_merge($tournament->getErrorList(), $error_list);
		
		$session =& JFactory::getSession();
		if (!empty($error_list)) {
			$session->set('error_list', $error_list, 'tournament');
			$session->set('sessFormData', $_POST, 'tournament');

			$this->setRedirect('index.php?option=com_tournament&task=edit&id=' . $id);
		} else {
			if ($is_future_tournament) {
				$meeting	=& $this->getModel('Meeting', 'TournamentModel');
				$api		=& $this->getModel('wageringapi', 'BettingModel');
				
				$api->getWageringApiByKeyword('tastab');
				
				$meeting->wagering_api_id			= (int)$api->getWageringApiByKeyword('tastab')->id;
				$meeting->name						= $future_meeting_venue;
				$meeting->tournament_competition_id	= (int)$tournament_competition_id;
				$meeting->start_date				= $start_date;
				
				// Add new meeting code to be used when matching up to provider data
				$shortDate = substr($meeting->start_date, 0, 10);
				$meeting->type_code	= (isset($type_code_lookup[$sport->name]) ? $type_code_lookup[$sport->name] : NULL);
				$meeting->meeting_code = str_replace(" ", "",  strtoupper($meeting->name) . "-". $meeting->type_code ."-" . $shortDate);
					
				$event_group_id = $meeting->save();
				
				$tournament->event_group_id	= (int)$event_group_id;
			}

            $tournament->tournament_sponsor_name = $sponser_name;
            $tournament->tournament_sponsor_logo = $sponsor_logo;
            $tournament->tournament_sponsor_logo_link = $sponsor_link;

            $tournament->entries_close = $entries_date .' '.$entries_time;

            $tournament->save();
		
			$post = JRequest::get( 'post' );
			
			
			
			// Get the tournament labels from the post
			$tournament_labels = JRequest::getVar('tournament_label_id', '','array');
						
			// Get the labels model
			$labels_model	=& $this->getModel('TournamentLabels', 'TournamentModel');
			
			// Remove existing labels for tournament
			$labels_model->deleteTournamentLabelsByTournamentId($tournament->id);
						
			// Add new labels for tournament
			foreach($tournament_labels as $label){
				$labels_model->addTournamentLabelToTournament($tournament->id, $label);
			}
			
			
			
			$change_log = $tournament->getChangeLog();
			foreach ($change_log as $key => $value) {
				$this->_saveAuditRecord($tournament->id, $key, $value, $tournament->$key);
			}
			
			$this->setRedirect('index.php?option=com_tournament');
		}
	}

	private function _saveBetLimit($tournament_id, $bet_type_id, $limit, $bet_limit_id = null)
	{
		$model =& $this->getModel('TournamentBetLimit', 'TournamentModel');

		$param_list = array(
			'tournament_id' => $tournament_id,
			'bet_type_id' 	=> $bet_type_id,
			'value' 		=> $limit
		);

		if(!is_null($bet_limit_id)) {
			$param_list['id'] = $bet_limit_id;
		}

		return $model->store($param_list);
	}

	private function _saveAuditList($tournament, $audit_list)
	{
		foreach($audit_list as $field_name => $old_value) {
			$this->_saveAuditRecord($tournament->id, $field_name, $old_value, $tournament->$field_name);
		}
	}

	private function _saveAuditRecord($tournament_id, $field_name, $old_value, $new_value)
	{
		static $model = null;

		if(is_null($model)) {
			$model =& JModel::getInstance('TournamentAudit', 'TournamentModel');
		}

		$param_list = array(
			'tournament_id' => $tournament_id,
			'field_name'	=> $field_name,
			'old_value'		=> $old_value,
			'new_value'		=> $new_value
		);

		return $model->store($param_list);
	}

	/**
	 * Generate tournament automated text
	 *
	 * @param string $field
	 * @return string
	 */
	protected function _generateTournamentAutomatedText($field)
	{
		$tournament_model =& $this->getModel('Tournament', 'TournamentModel');
		
		$jackpot_flag			= JRequest::getVar('jackpot_flag', 0);
		$parent_tournament_id	= JRequest::getVar('parent_tournament_id', null);
		$minimum_prize_pool		= JRequest::getVar('minimum_prize_pool', 0);


        $reinvest_winnings_flag = JRequest::getVar('reinvest_winnings_flag', 0);
        $closed_betting_on_first_match_flag = JRequest::getVar('closed_betting_on_first_match_flag', 0);
        $tournament_sponsor_name = JRequest::getVar('tournament_sponsor_name', null);

		
		$buyin_id					= JRequest::getVar('ticket_value', 1);
		$buyin_model				=& $this->getModel('TournamentBuyIn', 'TournamentModel');
		$buyin						= $buyin_model->getTournamentBuyIn($buyin_id);
		$buyin_amount				= number_format($buyin->buy_in, 2);
		$minimum_prize_pool_amount	= number_format($minimum_prize_pool, 2);
		$free_credit_flag 			= (int)JRequest::getVar('free_credit_flag', 0);

		$automated_text = '';
		$tournamntType = '';
		
		switch ($field) {
			case 'name':
				$meeting_id				= JRequest::getVar('event_group_id', -1);
				$event_id				= JRequest::getVar('event_id', -1);
				$future_meeting_venue	= JRequest::getVar('future_meeting_venue', -1);
				
				if (!empty($meeting_id) && $meeting_id != -1) {
					$meeting_model	=& $this->getModel('Meeting', 'TournamentModel');
					$meeting		= $meeting_model->getMeeting($meeting_id);
					$automated_text	.= $meeting->name ;
				} else if (!empty($future_meeting_venue) && $future_meeting_venue != -1) {
					$automated_text .= $future_meeting_venue;
				}
				// $automated_text .= ($buyin->buy_in > 0 ? ' $' . $buyin_amount : ' FREE');
				
			//	if (!$jackpot_flag) {
			//		$automated_text .= '/' . $minimum_prize_pool_amount;
			//	}
				
			break;
			case 'description':
				if ($jackpot_flag) {
					$tournamntType = 'jackpot';
				} elseif ($free_credit_flag){
					$tournamntType = 'free credit';
				}else {
					$tournamntType = 'cash';
				}
				$automated_text  = 'This is a ' . $tournamntType . ' tournament.';
				$automated_text .= ' The cost of entry is ';
				
				if ($buyin->buy_in > 0) {
					$automated_text .= '$' . $buyin_amount . ' + $' . number_format($buyin->entry_fee, 2) . '.';
				} else {
					$automated_text .= 'Free.';
					
				}

                if ($closed_betting_on_first_match_flag == 1){
                    $automated_text .= ' You can not bet after the 1st event in this tournament starts.';
                }

                if ($reinvest_winnings_flag == 0 && $closed_betting_on_first_match_flag != 1){
                    $automated_text .= ' You can not re-invest your winnings in this tournament.';
                }
				
				$automated_text .= ' Winners will receive';
				
				if (empty($jackpot_flag) || -1 == $parent_tournament_id) {
					$automated_text .= ' a share of a guaranteed $' . $minimum_prize_pool_amount;
					if($free_credit_flag){
						$automated_text .= ' in free credit.';
					} else {
						$automated_text .= '.';
					}
					
					if ($buyin->buy_in > 0) {
						$automated_text .= ' Once the minimum is reached, the prize pool will continue to grow by $' . $buyin_amount . ' per entrant.';
					}
				} else {
					$parent_tournament	= $tournament_model->getTournament($parent_tournament_id);
					$start_date_time	= strtotime($parent_tournament->start_date);
					
					$automated_text .= ' a ticket into the ' . $parent_tournament->name;
					$automated_text .= ' tournament on ' . date('D', $start_date_time) . ' ' . date('jS F', $start_date_time) . '.';
					
					if ($buyin->buy_in == 0) {
						
						$ticket_count	= floor($minimum_prize_pool * 100 / ($parent_tournament->entry_fee + $parent_tournament->buy_in));
						
						if($ticket_count > 1) {
							$automated_text .= ' There are ' . $ticket_count . ' tickets to be won.';
						} else {
							$automated_text .= ' There is ' . $ticket_count . ' ticket to be won.';
						}
					} else {
						$automated_text .= ' The Number of tickets awarded will depend on the number of entrants.';
					}
				}
				
				$automated_text .= "\n\nGood luck and good punting!";
				
			break;
		}
		
		return $automated_text;
	}
	
	/**
	 * Cancel Tournament
	 */
	public function cancelForm()
	{
		$tournament_id = JRequest::getVar('id', 0);

		// get tournament model
		$tournament_model 	=& $this->getModel('Tournament', 'TournamentModel');
		$tournament 		= $tournament_model->getTournament($tournament_id);

		// set up for display
		$view =& $this->getView('Tournament', 'html', 'TournamentView');
		$view->setLayout('cancelform');
		$view->assign('tournament',  $tournament);
		$view->display();
	}
	
	/**
	 * Cancel Save
	 */
	public function cancelSave()
	{
		$this->setRedirect('index.php?option=com_tournament&=controller=tournament');

		$id = JRequest::getVar('id', 0);
		if(empty($id)) {
			JError::raiseWarning(666, JText::_('No tournament specified.'));
		}

		$tournament_model =& $this->getModel('Tournament', 'TournamentModel');
		$tournament = $tournament_model->getTournament($id);
		if(is_null($tournament)) {
			JError::raiseWarning(666, JText::_('Invalid tournament specified.'));
			return false;
		}
		
		if($tournament->paid_flag) {
			JError::raiseWarning(666, JText::_('Can\'t cancel a paid tournament.'));
			return false;
		}

		$admin_cancelled_reason = JRequest::getVar('admin_cancelled_reason', '');
		if(empty($admin_cancelled_reason)) {
			JError::raiseWarning(666, JText::_('A reason must be specified to cancel a tournament.'));
			return false;
		}
		
		if($tournament->id > 0){
			//XXX: should not need to assign all the values, but it's now required by tournament super model validations
			$tournament_params = array(
					'id'                    => $tournament->id,
					'start_date'			=> $tournament->start_date,
					'end_date'				=> $tournament->end_date,
					'buy_in'				=> $tournament->buy_in,
					'entry_fee'				=> $tournament->entry_fee,
					'tournament_sport_id'	=> $tournament->tournament_sport_id,
					'event_group_id'		=> $tournament->event_group_id,
					'name'					=> $tournament->name,
					'description'			=> $tournament->description,
					'start_currency'		=> $tournament->start_currency,
					'cancelled_flag'        => 1,
					'cancelled_reason'      => trim($admin_cancelled_reason)
			);

			if($tournament_model->store($tournament_params)) {
				$this->setMessage(JText::_('Tournament has been cancelled.'));
				$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
				$ticket_list = $ticket_model->getTournamentTicketListByTournamentID($tournament->id);

				if(!empty($ticket_list)) {
					$tournament_dollars =& $this->getModel('Tournamenttransaction', 'TournamentDollarsModel');

					foreach($ticket_list as $ticket) {
						if(!$ticket_model->refundTicketAdmin($tournament_dollars, $ticket->id, true)) {
							JError::raiseWarning(0, JText::_('Ticket could not be refunded - ID ' . $ticket->id));
						}
					}
				}

				return true;
			}
		}

		JError::raiseWarning(666, JText::_('Tournament could not be cancelled.'));
		return false;
	}
	
	/**
	 * Delete a tournament
	 *
	 * @return void
	 */
	public function delete()
	{
		$tournament_model =& $this->getModel('Tournament', 'TournamentModel');
		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');

		$id = JRequest::getVar('id', null);
		if (empty($id)) {
			JError::raiseWarning(0, 'No tournament ID specified');
		} else {
			if ($tournament = $tournament_model->getTournament($id)) {
				$entrants = $ticket_model->countTournamentEntrants($tournament->id);
				if (empty($entrants)) {
					$tournament_model->delete($tournament->id);

					$this->setMessage(JText::_('Tournament deleted'));
				} else {
					JError::raiseWarning(0, 'The tournament has entrants and can not be deleted');
				}
			} else {
				JError::raiseWarning(0, 'Invalid tournament specified');
			}
		}
		$this->setRedirect('index.php?option=com_tournament&controller=tournament');
	}
	
	/**
	 * Cloning a Sport Tournament
	 */
	public function cloneTournament(){
		$tournament_id			= JRequest::getVar('id', '');
		$tournament_model 		=& $this->getModel('Tournament', 'TournamentModel');

		$clone_id = $tournament_model->cloneTournament($tournament_id);
		if($clone_id) {
			$this->setRedirect("index.php?option=com_tournament&controller=tournament&task=edit&id=" . $clone_id);
			$this->setMessage(JText::_('Tournament Cloned'));
		}
	}
	

	/**
	 * Method to export tournament entrants in csv
	 *
	 * @return void
	 */

	public function export_entrants()
	{
		$tournament_id 		= JRequest::getVar('id', 0);
		$tournament_model 	=& $this->getModel('Tournament', 'TournamentModel');
		
		$tournament = $tournament_model->load($tournament_id);
		
		if (is_null($tournament)) {
			$this->setRedirect('index.php', JText::_('Tournament not found'));
			return;
		}
		
		$leaderboard_model 	=& $this->getModel('TournamentLeaderboard', 'TournamentModel');
		TournamentHelper::exportEntrantsCsv($leaderboard_model->getLeaderBoardRank($tournament));
		exit;
	}
}
