<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class TournamentSportEventController extends JController
{
	/**
	 * Controller URL is to reuse the same path to all the places nessessary
	 */
	private $controller_url = 'index.php?option=com_tournament&controller=tournamentsportevent';
	/**
	 * Display the list of racing tournaments
	 *
	 * @return void
	 */
	public function listView()
	{
		global $mainframe, $option;
		$sport_id		= JRequest::getVar('sportId', '');
		$competition_id	= JRequest::getVar('competitionId', '');

		$event_group_model	=& $this->getModel('TournamentEventGroup', 'TournamentModel');
		$sports_model		=& $this->getModel('TournamentSport', 'TournamentModel');
		$filter_prefix		= 'sportevent';

		$order = $mainframe->getUserStateFromRequest(
		$sport_id.$filter_prefix.$competition_id.'filter_order',
			'filter_order',
			'name'
		);

		$direction = strtoupper($mainframe->getUserStateFromRequest(
		$sport_id.$filter_prefix.$competition_id.'filter_order_Dir',
			'filter_order_Dir',
			'ASC'
		));

		$limit = $mainframe->getUserStateFromRequest(
			'global.list.limit',
			'limit',
			$mainframe->getCfg('list_limit')
		);

		$offset = $mainframe->getUserStateFromRequest(
		$sport_id.$filter_prefix.$competition_id.'limitstart',
			'limitstart',
			0
		);
		
		$params = array(
			'type'			=> 'sports',
			'order'			=> $order,
			'direction'		=> $direction,
			'limit'			=> $limit,
			'offset'		=> $offset
		);

		$sports_event_group_list 	= $event_group_model->getEventGroupListBySportIDAndCompetitionID($sport_id, $competition_id, $params);
		$sports_all 				= $sports_model->getTournamentSportAdminList('sports');
		
		$sport_competitions = null;
		if($sport_id){
			$competition_model 	=& $this->getModel('TournamentCompetition', 'TournamentModel');
			$sport_competitions = $competition_model->getTournamentCompetitionListBySportID($sport_id);
		}

		jimport('joomla.html.pagination');

		$total 		= $event_group_model->getTotalEventGroupCountBySportAndCompetitionID($sport_id, $competition_id, 'sport');
		$pagination = new JPagination($total, $offset, $limit);
		$view 		=& $this->getView('TournamentSportEvent', 'html', 'TournamentView');

		$view->assign('sports_all', $sports_all);
		$view->assign('sport_id', $sport_id);
		$view->assign('sport_competitions', $sport_competitions);
		$view->assign('competition_id', $competition_id);

		$view->assign('event_group_list', $sports_event_group_list);
		$view->assign('order', $order);
		$view->assign('direction', $direction);
		$view->assign('pagination', $pagination->getListFooter());

		$view->display();
	}
	/**
	 * method to make the cancel button work
	 */
	public function cancel()
	{
		$this->setRedirect($this->controller_url);
		$session =& JFactory::getSession();
	}
	/**
	 * To load the Add/Edit Sports form
	 */
	public function editSport()
	{
		$sports_model =& $this->getModel('TournamentSport', 'TournamentModel'); // setting the sport model object
		$ext_sports_model =& $this->getModel('ImportSport', 'TournamentModel'); // setting the external sport model object

		$view =& $this->getView('TournamentSportEvent', 'html', 'TournamentView');
		$view->setLayout('editsport');

		$sports_data = null;
		if( JRequest::getVar('sportId') > 0) {
			$sports_data = $sports_model->getTournamentSport(JRequest::getVar('sportId'));
		}
		$ext_sport_data = $ext_sports_model->getImportSportList();

		$view->assign('sports_data',  $sports_data);
		$view->assign('ext_sport_data',  $ext_sport_data);

		$view->display();
	}

	/**
	 * to Save the Sports data
	 */
	public function saveSport()
	{
		$this->setRedirect($this->controller_url);
		$session 		=& JFactory::getSession();
		$sport_name 	= JRequest::getVar('sportName', '');
		$ext_sport_id 	= JRequest::getVar('externalSportId', '');
		$id 			= JRequest::getVar('sportId','');

		if(empty($sport_name) || $ext_sport_id < 1) {
			JError::raiseWarning(0, 'The fields cannot be empty!');
			return false;
		}
		/**
		 * Get the sport model
		 */
		$sports_model 	=& $this->getModel('TournamentSport', 'TournamentModel');
		$sport 			= $sports_model->getTournamentSportByName($sport_name); // -- get the sport by name to ensure the name is unique in the system
		/**
		* checking the sport name already exists on insert & update
		* except the same sport that has been edited
		*/
		if ($sport->id != $id && $sport->name == $sport_name){
			JError::raiseWarning(0, 'The Sport name already exists!');
			return false;
		}

		$sport_params = array(
			'id'                    => $id,
			'name'                  => $sport_name,
			'description'           => $sport_name,
			'status_flag'           => 1,
		);

		$sport_id = $sports_model->store($sport_params);

		/**
		 * Adding data to mapping table
		 * Using SportMap Modal object
		 */
		$sport_map_params = array(
			'tournament_sport_id'   => $sport_id,
			'external_sport_id'     => $ext_sport_id,
		);

		$sports_map_model 	=& $this->getModel('SportMap', 'TournamentModel');
		$result 			= $sports_map_model->insertSportMap($sport_map_params);
	}

	/**
	 * to add.edit competition
	 */
	public function editCompetition()
	{
		$sports_model =& $this->getModel('TournamentSport', 'TournamentModel'); // setting the sport model object
		$sports_all = $sports_model->getTournamentSportAdminList('sports');

		$competition_model =& $this->getModel('TournamentCompetition', 'TournamentModel');
		$ext_competition_model =& $this->getModel('ImportCompetition', 'TournamentModel'); // setting the external sport model object

		$view =& $this->getView('TournamentSportEvent', 'html', 'TournamentView');
		$view->setLayout('editcompetition');
		$competition_id = JRequest::getVar('competitionId',null);

		$sport_id			= null;
		$competition_data	= null;
		$event_exists		= false;
		if (!is_null($competition_id)) {
			$competition_data = $competition_model->getTournamentCompetition($competition_id);
			$sport_id = $competition_data->tournament_sport_id;
			$event_exists = $competition_model->checkEventExistsByCompetitionID($competition_id);
		}

		$ext_competition_data = null;
		if(!is_null($sport_id)) {
			$sport = $sports_model->getTournamentSport($sport_id);
			$ext_competition_data = $ext_competition_model->getImportCompetitionListBySportID($sport->external_sport_id);

			$sort =& $this->getModel('ImportSport', 'TournamentModel');
			$ext_competition_data = $this->_sortArray((array)$ext_competition_data,'league_name');
		}
		$view->assign('sport_id',  $sport_id);
		$view->assign('sports_all',  $sports_all);
		$view->assign('competition_data',  $competition_data);
		$view->assign('ext_competition_data',  $ext_competition_data);
		$view->assign('event_exists',  $event_exists);

		$view->display();
	}


	public function saveCompetition()
	{
		$this->setRedirect($this->controller_url);
		$session =& JFactory::getSession();

		$id 						= JRequest::getVar('competitionId', '');
		$tournament_sport_id 		= JRequest::getVar('sportId', '');
		$name						= trim(JRequest::getVar('competitionName',''));
		$external_competition_id	= JRequest::getVar('externalCompetitionId','');

		$competition_model	=& $this->getModel('TournamentCompetition', 'TournamentModel');
		$event_exists		= 0;
		if($id > 0) {
			/* Check if any Event is assosiated with the competition */
			$event_exists = $competition_model->checkEventExistsByCompetitionID($id);
		}
		if (($event_exists > 0 && empty($name)) || ($event_exists < 1 && (empty($tournament_sport_id) || empty($name) || $external_competition_id < 1))){
			JError::raiseWarning(0, 'The fields cannot be empty!');
			return false;
		}
		/**
		 * Check Live tournament exists for the competition
		 */

		$tournament_bet_model=& $this->getModel('TournamentBet','TournamentModel');
		$betting_started = $tournament_bet_model->isBettingStartedByCompetitionId($id);
		if($betting_started){
			JError::raiseWarning(0, "Sorry! You can't change the competition as betting has already started.");
			return false;
		}
		/**
		 * Checking Unique Competition name
		 */
		$competition = $competition_model->getTournamentCompetitionByNameAndSportID(strtolower($name), $tournament_sport_id);
		
		if(!is_null($competition) && $competition->id != $id && strtolower($competition->name) == strtolower($name)){
			JError::raiseWarning(0, 'The competition name already exists for this sport!');
			return false;
		}

		if($event_exists > 0){
			$competition_params = array(
			'id'						=> $id,
			'name'                  	=> $name,
			);
			$this->setMessage(JText::_('Success! The competition name has been updated.'));
		} else {
			$competition_params = array(
			'id'						=> $id,
			'tournament_sport_id'   	=> $tournament_sport_id,
			'name'                  	=> $name,
			'external_competition_id' 	=> $external_competition_id,
			'status_flag'           	=> 1
			);
			$this->setMessage(JText::_('Success! The competition has been updated.'));
		}
		$result = $competition_model->store($competition_params);
	}
	/**
	 * Delete Competition
	 * @param integer $id
	 */
	public function deleteCompetition()
	{
		$this->setRedirect($this->controller_url);
		$session =& JFactory::getSession();
		$id 	 = JRequest::getVar('competitionId', '');
		if($id){
			$competition_model	=& $this->getModel('TournamentCompetition', 'TournamentModel');
			$id 			  	= JRequest::getVar('competitionId', '');
			$event_exists		= $competition_model->checkEventExistsByCompetitionID($id);
			if($event_exists > 0){
				JError::raiseWarning(0, "Sorry! This competition has events assigned to it so you can't remove it.");
				return false;
			}
			$tournament_bet_model=& $this->getModel('TournamentBet','TournamentModel');
			$betting_started = $tournament_bet_model->isBettingStartedByCompetitionId($id);
			if($betting_started){
				JError::raiseWarning(0, "Sorry! You can't change the competition as betting has already started.");
				return false;
			}
			$competition_model->id = $id;
			$competition_model->delete();
		}
	}


	/**
	 * Method to make the Add Event button work
	 */
	public function newEvent()
	{
		$this->setRedirect($this->controller_url . "&task=editEvent");
	}
	/**
	 * to add.edit Event
	 */

	public function editEvent()
	{
		$sports_model	=& $this->getModel('TournamentSport', 'TournamentModel'); // setting the sport model object
		$sports_all		= $sports_model->getTournamentSportAdminList('sports');

		$event_group_id		= JRequest::getVar('id',null);
		$sport_id			= null;
		$event_group_data	= null;
		$betting_started	= null;
		
		if ($event_group_id) {
			$event_group_model	=& $this->getModel('EventGroup', 'TournamentModel');
			$event_group_data	= $event_group_model->getEventGroup($event_group_id);
			
			if (!empty($event_group_data)) {
				$competition_model	=& $this->getModel('TournamentCompetition', 'TournamentModel');
				$competition = $competition_model->getTournamentCompetition($event_group_data->tournament_competition_id);
				
				$tournament_bet_model	=& $this->getModel('TournamentBet','TournamentModel');
				$betting_started		= $tournament_bet_model->isBettingStartedByEventGroupId($event_group_id);
				$sport_id				= $competition->tournament_sport_id;
				
				$event_group_data->tournament_sport_id = $competition->tournament_sport_id;
			}
		}

		$competition_model  =& $this->getModel('TournamentCompetition', 'TournamentModel');
		$competitions 		= $competition_model->getTournamentCompetitionListBySportID($sport_id);

		$view =& $this->getView('TournamentSportEvent', 'html', 'TournamentView');
		$view->setLayout('editevent');

		$ext_matchs				= array();
		$event_list				= array();
		$market_checkbox_list	= array();
		$market_type_list		= array();
		$market_list_defined	= array();
		$total_tournaments		= 0;
		if (!is_null($event_group_data) && $event_group_data->tournament_competition_id > 0) {

			$ext_match_model =& $this->getModel('ImportMatch', 'TournamentModel');
			$ext_matchs 	 = $ext_match_model->getImportMatchListByCompetitionID($event_group_data->tournament_competition_id);

			$tournament_model	=& $this->getModel('Tournament', 'TournamentModel');
			$total_tournaments	= $tournament_model->getTotalTournamenCountByEventGroupID($event_group_id);

			$event_group_market_type_model	=& $this->getModel('EventGroupMarketType', 'TournamentModel');
			$event_group_model				=& $this->getModel('EventGroup', 'TournamentModel');
			$event_model					=& $this->getModel('Event', 'TournamentModel');

			$event_list		= $event_model->getEventListByEventGroupID($event_group_id);
			
			$market_model		=& $this->getModel('Market', 'TournamentModel');
			//$market_type_list	= $event_group_market_type_model->getEventGroupMarketTypeListByEventGroupID($event_group_id);
			$market_list_defined	= $event_group_market_type_model->getEventGroupMarketListByEventGroupID($event_group_id);

			$i = 0;
			foreach ($event_list as $event) {
				$master_list = $market_checkbox_list;
				$market_list = $market_model->getMarketArrayByEventID($event->id);
				
				if ($i == 0) {
					$market_checkbox_list = $market_list;
				} else {
					$market_checkbox_list = array_intersect($market_checkbox_list, $market_list);
				}
				$i++;
			}
			
		}

		$view->assign('sports_all', $sports_all);
		$view->assign('competitions', $competitions);
		$view->assign('event_group_data', $event_group_data);
		$view->assign('ext_match_data',$ext_matchs);
		$view->assign('match_list', $event_list);
		//$view->assign('match_time', $match_time);
		$view->assign('market_list', $market_checkbox_list);
		//$view->assign('bet_type_list', $market_type_list);
		$view->assign('bet_type_list', $market_list_defined);
		$view->assign('betting_started', $betting_started);
		$view->assign('total_tournaments', $total_tournaments);

		$view->display();
	}


	public function saveEvent()
	{
		$this->setRedirect($this->controller_url);
		$session =& JFactory::getSession();
		
		$event_model =& $this->getModel('Event', 'TournamentModel');
				
		$event_group_id			= JRequest::getVar('eventId', '');
		$competition_id			= JRequest::getVar('competitionId', '');
		$tournament_sport_id	= JRequest::getVar('sportId', '');
		$name					= JRequest::getVar('eventName','');
		$total_match			= JRequest::getVar('totMatchs','');
		$event_start_date		= JRequest::getVar('eventStartDate','');
		$market_types			= JRequest::getVar('betTypes',array());
		$matchIds				= JRequest::getVar('matchIds',array());
		$matchStartDate			= JRequest::getVar('matchStartDate',array());
		$matchStartTime			= JRequest::getVar('matchStartTime',array());
		
		$match_count=count($matchIds);
		for($i=0; $i < $match_count; $i++)
		{
			$event_params = array(
						'id'						=> $matchIds[$i],
						'start_date'           		=> $matchStartDate[$i]." ".$matchStartTime[$i]
			);
			$event_model->store($event_params);
		}

		
		if ($tournament_sport_id < 1 || empty($name) || $competition_id < 1 || ($total_match < 1 && empty($event_start_date))){
			JError::raiseWarning(0, 'The fields cannot be empty!');
			return false;
		}

		
		$start_date = null;
		$event_group_model =& $this->getModel('TournamentEventGroup', 'TournamentModel');
		if ($event_group_id > 0) {
			$start_date				= $event_group_model->getEventGroupFirstAndLastEventTimeByEventGroupID($event_group_id)->first_match_time;
			$tournament_bet_model	=& $this->getModel('TournamentBet','TournamentModel');
			$betting_started		= $tournament_bet_model->isBettingStartedByEventGroupId($event_group_id);
			if ($betting_started){
				JError::raiseWarning(0, "Sorry! You can't change the event as betting has already started.");
				return false;
			}
		}

		if((!empty($start_date) && time() >= strtotime($start_date)) || empty($start_date) && strtotime("today") > strtotime($event_start_date)) {
			JError::raiseWarning(0, 'Sorry! This event has already started.');
			return false;
		}

		if (!empty($start_date) && strtotime($event_start_date) > strtotime($start_date)) {
			$event_start_date = $start_date;
		}
		
		$wagering_api_model =& $this->getModel('WageringApi', 'BettingModel');
		$unitab_wagering	= $wagering_api_model->getWageringApiByKeyword('unitab');

		$event_group_params = array(
			'name'        					=> $name,
			'tournament_competition_id'  	=> $competition_id,
			'start_date' 					=> $event_start_date,
			'wagering_api_id'				=> $unitab_wagering->id
		);

		if(!empty($event_group_id)) {
			$event_group_params['id'] = $event_group_id;
		}

		$result = $event_group_model->store($event_group_params);

		if ($event_group_id > 0) {
			/**
			 * Update all assosiate tournament start date
			 */
			if ($event_start_date) {
				$tournament_model	=& $this->getModel('Tournament', 'TournamentModel');
				$tournaments		= $tournament_model->getTournamentListByEventGroupID($event_group_id);

				if (!empty($tournaments)){
					foreach($tournaments as $tournament){
						if(strtotime($tournament->start_date) != strtotime($event_start_date)){
							$tournament_data 	= $tournament_model->getTournament($tournament->id);
							
							$tournament_param = array(
								'id'							=> $tournament_data->id,
								'start_date' 					=> $event_start_date,
							);
							$tournament_model->update_tournament($tournament_param);
						}
					}
				}
			}
			/**
			 * Assign the markets to an event
			 */
			$event_model	=& $this->getModel('Event', 'TournamentModel');
			$event_list		= $event_model->getEventListByEventGroupID($event_group_id);

			if (!empty($event_list)) {
				/*
				 * If there are matchs & no bet type selected
				 */
				if (empty($market_types)) {
					JError::raiseWarning(0, 'You must select at least one bet type');
					return false;
				} else {
					$event_group_market_type_model	=& $this->getModel('EventGroupMarketType', 'TournamentModel');
					$market_model 					=& $this->getModel('Market', 'TournamentModel');
					/**
					 * Check if there is already market imported for this event
					 */
					$existing_market_types = $event_group_market_type_model->getEventGroupMarketTypeListByEventGroupID($event_group_id);

					foreach ($event_list as $event) {
						$market_list = $market_model->getMarketListByEventId($event->id);

						foreach ($market_list as $market) {
							if (in_array($market->id, $market_types)) {
								//if (!$event_group_market_type_model->isEventGroupMarketTypeAdded($event_group_id, $market->market_type_id)) {
								if (!$event_group_market_type_model->isEventGroupMarketAdded($event_group_id, $market->id)) {	
									//$event_group_market_type_model->addEventGroupMarketType($event_group_id, $market->market_type_id);
									$event_group_market_type_model->addEventGroupMarket($event_group_id, $market->market_type_id, $market->id);
								}
							} else {
								//$event_group_market_type_model->removeEventGroupMarketType($event_group_id, $market->market_type_id);
								$event_group_market_type_model->removeEventGroupMarket($event_group_id, $market->market_type_id, $market->id);
							}
						}
					}
					/**
					 * If no bet types already added for this event then We'll need to get
					 * all assosiated Jackpot tournament entrants who registered atleast
					 * 48 hrs before the tournament starts & will send a reminder mail
					 */
					if (empty($existing_bet_types)) {
						$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
						$entrant_list = $ticket_model->getJackpotTournamentEntrantListByEventGroupID($event_group_id);
						if(!empty($entrant_list)){
							$this->_sendReminderEmail($entrant_list);
						}
					}
				}
			}
			$this->setMessage(JText::_('Success! The event has been updated.'));
		}
		else{
			$this->setRedirect($this->controller_url. "&task=editEvent&id=$result");
			$this->setMessage(JText::_('Success! The following event has been created.'));
		}
	}
	/**
	 * Delete Competition
	 * @param integer $id
	 */
	public function deleteEvent()
	{
		$id = JRequest::getVar('eventId', '');

		$tournament_model 	 =& $this->getModel('Tournament', 'TournamentModel');
		$tournament_count	 = $tournament_model->getTotalTournamenCountByEventGroupID($id);

		if ($tournament_count > 0) {
			JError::raiseWarning(0, "Sorry! This event has tournaments, so you can't remove it.");
			return false;
		}
		if ($id) {
			$event_group_model	=& $this->getModel('EventGroup', 'TournamentModel');
			$event_model		=& $this->getModel('Event', 'TournamentModel');

			$events = $event_model->getEventListByEventGroupID($id);
			/**
			 * Delete all matchs, offers, markets for this event
			 */
			if (!empty($events)) {
				foreach($events as $event){
					$this->deleteMatch($id, $event->id);
				}
			}
			
			$event_group_model->id = $id;
			$event_group_model->delete();
		}
		$this->setRedirect($this->controller_url, 'Event deleted');
	}
	/**
	 * Matches
	 */
	public function saveMatch()
	{
		$event_group_id  = JRequest::getVar('matchEventId', '');
		$competition_id  = JRequest::getVar('matchCompetitionId', '');
		$ext_event_info  = JRequest::getVar('extMatchInfo', '');
		//$ext_match_name	 = JRequest::getVar('extMatchName', '');

		$this->setRedirect($this->controller_url. "&task=editEvent&id=$event_group_id");

		if (!empty($event_group_id) && !empty($competition_id) && !empty($ext_event_info)) {
			$import_match_model =& $this->getModel('ImportMatch', 'TournamentModel');
			$event_model =& $this->getModel('Event', 'TournamentModel');
			if (!empty($ext_event_info)) {
				$ext_event 			= explode("_*_",$ext_event_info); //explode MeetingID & External MatchID
				$ext_event_id 		= $ext_event[1];
				$ext_event_group_id	= $ext_event[0];
				$ext_event_data		= null;
				if ($ext_event_group_id > 0 && $ext_event_id > 0) {
					$ext_event_data 	= $import_match_model->getImportMatchExternalMeetingIDAndExternalMatchID($ext_event_group_id, $ext_event_id);
				}
			}
			if ($event_group_id < 1 || $ext_event_group_id < 1) {
				JError::raiseWarning(0, "Sorry! You must fill up all the required fields.");
				return false;
			}

			if ($event_group_id > 0) {
				$tournament_bet_model	=& $this->getModel('TournamentBet','TournamentModel');
				$betting_started		= $tournament_bet_model->isBettingStartedByEventGroupId($event_group_id);
				
				if ($betting_started) {
					JError::raiseWarning(0, "Sorry! You can't add any match as betting has already started.");
					return false;
				}
			}
			/**
			 * Check if the same match is already in the database or not
			 * if exists then use that match Id else add as new match
			 */
			$event_data		= $event_model->getEventByExternalEventID($ext_event_id);
			$event_id		= null;
			$event_exists	= false;
			if (!is_null($event_data) && $event_data->id) {
				$event_id		= $event_data->id;
				$event_exists	= true;
			} else {
				$event_status_model	=& $this->getModel('EventStatus', 'TournamentModel');
				$event_status_id	= $event_status_model->getEventStatusByKeyword('selling')->id;
				
				$wagering_api_model =& $this->getModel('WageringApi', 'BettingModel');
				$unitab_wagering	= $wagering_api_model->getWageringApiByKeyword('unitab');

				$event_params = array(
					'id'						=> null,
					'tournament_competition_id'	=> $competition_id,
					'external_event_id'			=> $ext_event_id,
					'wagering_api_id'			=> $unitab_wagering->id,
					'event_status_id'			=> $event_status_id,
					'name' 						=> $ext_event_data['match_name'],
					'start_date'           		=> $ext_event_data['start_date']
				);

				$event_id = $event_model->store($event_params);
			}
			/**
			 * Check if the match exists for an event group
			 * if not then add that in the event group
			 */

			$event_group_event_model		=& $this->getModel('EventGroupEvent', 'TournamentModel');
			$event_exists_in_event_group	= $event_group_event_model->isEventExistsByEventGroupIDAndEventId($event_group_id, $event_id);

			if ($event_exists_in_event_group) {
				JError::raiseWarning(0, "Sorry! This match already exists in this event.");
				return false;
			}
			
			$event_group_model =& $this->getModel('EventGroup', 'TournamentModel');
			
			/**
			 * Add match to event group if not exists in the event group
			 */
			if ($event_id) {
				$event_group_event_params = array(
					'event_group_id'	=> $event_group_id,
					'event_id'			=> $event_id,
				);
				$result = $event_group_event_model->store($event_group_event_params);

				$event_start_date	 = $event_group_model->getEventGroupStartTimeByEventID($event_group_id);
				$result = $event_group_model->updateEventGroupDate($event_group_id, $event_start_date);

				$this->updateTournamentBettingClosedDateByEventGroupId($event_group_id);
			}
			
			/**
			 *  update external event group id in event group
			 */
			$event_group_params = array(
				'id'						=> $event_group_id,
				'external_event_group_id'	=> $ext_event_group_id
			);
			$event_group_model->store($event_group_params);
			
			/**
			 * Adding bettypes for the match if not exists already
			 */
			if (!$event_exists) {
				$import_market_model =& $this->getModel('ImportMarket', 'TournamentModel');
				$ext_markets 		 = $import_market_model->getImportMarketListByExternalMeetingIDAndExternalMatchID($ext_event_group_id, $ext_event_id);
				$added_market_list 	 = array();

				if(!empty($ext_markets)){
					$market_model		=& $this->getModel('Market', 'TournamentModel');
					$market_type_model	=& $this->getModel('MarketType', 'TournamentModel');
					$wagering_api_model	=& $this->getModel('WageringApi', 'BettingModel');
					
					$unitab_api	= $wagering_api_model->getWageringApiByKeyword('unitab');

					$added_market_list	= $market_model->getExternalMarketIDListByEvenGroupID($event_group_id);

					foreach ($ext_markets as $market) {
						/**
						 * Check if the Market exists in the DB
						 * If not Add new market
						 */
						if (!in_array($market['external_market_id'], $added_market_list)) {
							/**
							 * Get markety_type_id with the same name as the external market name
							 */
							$market_type_id = $market_type_model->addMarketTypeIfNotExist($market['name'], $market['description']);

							$market_params = array(
								'event_id'				=> $event_id,
								'market_type_id'		=> $market_type_id,
								'external_market_id'	=> $market['external_market_id'],
								'wagering_api_id'		=> $unitab_api->id,
							);
							$market_model->store($market_params);
						}
					}
				}
			}
		}
	}
	/**
	 *
	 */
	public function updateTournamentBettingClosedDateByEventGroupId($event_group_id = Null)
	{
		if ($event_group_id) {
			/**
			 * Update Tournament betting_closed_date on save
			 */
			$event_group_model 		=& $this->getModel('EventGroup', 'TournamentModel');
			$tournament_model 		=& $this->getModel('Tournament', 'TournamentModel');
			$tournaments 			= $tournament_model->getTournamentListByEventGroupID($event_group_id);

			$event_group	 		= $event_group_model->getEventGroupFirstAndLastEventTimeByEventGroupID($event_group_id);
			$tournament_start_date	= $event_group->first_match_time;
			$tournament_end_date	= $event_group->last_match_time;
			/**
			 * Udate Event start date if the matchs got changed
			 */
			$result = $event_group_model->updateEventGroupDate($event_group_id, $tournament_start_date);

			if(!empty($tournaments)){
				$tournament_model 		=& $this->getModel('Tournament', 'TournamentModel');
				/**
				 * Loop through the tournaments & update the bet closing date if needed
				 * and Update the tournament start & end date as well
				 */
				foreach($tournaments as $tournament){
					$betting_closed_date 				= $tournament->betting_closed_date;
					$closed_betting_on_first_match_flag = $tournament->closed_betting_on_first_match_flag;

					if($closed_betting_on_first_match_flag ==1){
						if(strtotime($betting_closed_date) != strtotime($tournament_start_date)){
							$new_betting_closed_date = $tournament_start_date;
						}
					} else {
						if(strtotime($betting_closed_date) != strtotime($tournament_end_date)){
							$new_betting_closed_date = $tournament_end_date;
						}
					}
					/**
					 * Updating Tournament start & end date assosiated with the event
					 */
					if(!empty($tournament_start_date) && ($tournament->start_date != strtotime($tournament_start_date) || $tournament->end_date != strtotime($tournament_end_date))){
						$tournament_params = array(
							'id'                    => $tournament->id,
							'start_date'            => $tournament_start_date,
							'end_date'              => $tournament_end_date
						);
						$result = $tournament_model->update_tournament($tournament_params);
					}
					/**
					 * if the date is different than the one already in the record
					 * Update that with new date
					 */
					if(!empty($new_betting_closed_date)){
						$betting_params = array(
							'id'   		=> $tournament->id,
							'betting_closed_date'	=> $new_betting_closed_date,
						);
						$result = $tournament_model->update_tournament($betting_params);
					}
				}
			}
		}
	}
	/**
	 * Delete Match
	 */
	public function deleteMatch($event_group_id= Null, $event_id = Null)
	{
		if (!$event_group_id) {
			$event_group_id = JRequest::getVar('eventId', '');
		}
		if (!$event_id) {
			$event_id = JRequest::getVar('matchId', '');
		}
		
		$this->setRedirect($this->controller_url . "&task=editEvent&id=" . $event_group_id);

		if ($event_group_id > 0) {
			$tournament_bet_model=& $this->getModel('TournamentBet','TournamentModel');
			$betting_started = $tournament_bet_model->isBettingStartedByEventGroupId($event_group_id);
			if($betting_started){
				JError::raiseWarning(0, "Sorry! You can't delete any match as betting has already started.");
				return false;
			}
		}
		
		if($event_id){
			$event_model 				=& $this->getModel('Event', 'TournamentModel');
			$event_group_event_model	=& $this->getModel('EventGroupEvent', 'TournamentModel');
			$event_group_model			=& $this->getModel('EventGroup', 'TournamentModel');
			$market_model 				=& $this->getModel('Market', 'TournamentModel');
			$offer_model 				=& $this->getModel('TournamentOffer', 'TournamentModel');

			$event_group_event_model->deleteByEventGroupIDAndEventID($event_group_id, $event_id);
			$this->updateTournamentBettingClosedDateByEventGroupId($event_group_id);

			$event_group_list = $event_group_model->getEventGroupListByEventId($event_id);

			if(count($event_group_list) == 0){
				$market_ids = $market_model->getMarketIDsByEventID($event_id);

				if(!empty($market_ids->markets)) {
					$offer_model->delete($market_ids->markets);
					$market_model->deleteMarketByID($market_ids->markets);
				}
				$event_model->id = $event_id;
				$event_model->delete();
			}
		}
	}
	/**
	 * Send mail to the entrants
	 * @param $entrants
	 * @param $replacement_list
	 */
	private function _sendReminderEmail($entrants)
	{
		$subject = JText::_("TopBetta - Betting is now open for your jackpot tournament");
		$mailer = new UserMAIL();
		$email_params	= array(
			'subject'	=> $subject,
			'ishtml'	=> true
		);

		foreach($entrants as $entrant){
			$email_params['mailto']	= $entrant->email;
			$display_identifier 	= $entrant->display_identifier ? $entrant->display_identifier : $entrant->tournament_id;
			$tournament_url			=  "<a href='https://www.topbetta.com/tournament/details/{$display_identifier}'>https://www.topbetta.com/tournament/details/{$display_identifier}</a>";

			$replacement_list = array(
				'username' => $entrant->username,
				'tournament name' => $entrant->name,
				'link to tournament' => $tournament_url
			);

			$result = $mailer->sendUserEmail('bettingReminderEmailText', $email_params, $replacement_list);
		}
		return;
	}
	/**
	 * Re-Import Markets
	 */
	public function reimportMarkets(){
		$event_group_id = JRequest::getVar('eventId', '');

		if($event_group_id > 0){
			$this->setRedirect($this->controller_url. "&task=editEvent&id=$event_group_id");

			$tournament_bet_model=& $this->getModel('TournamentBet','TournamentModel');
			$betting_started = $tournament_bet_model->isBettingStartedByEventGroupId($event_group_id);
			if($betting_started){
			//	JError::raiseWarning(0, "Sorry! You can't reimport bets for this event as betting has already started.");
			//	return false;
			}
			$added_market_list 	= array();

			$event_model	=& $this->getModel('Event', 'TournamentModel');
			$event_list 	= $event_model->getEventListByEventGroupID($event_group_id);

			$event_group_model	=& $this->getModel('EventGroup', 'TournamentModel');
			$event_group		= $event_group_model->getEventGroup($event_group_id);
			
			if(!empty($event_list)){
				$market_model  			=& $this->getModel('Market', 'TournamentModel');
				$import_market_model	=& $this->getModel('ImportMarket', 'TournamentModel');
				$market_type_model 		=& $this->getModel('MarketType', 'TournamentModel');

				$added_market_list		= $market_model->getExternalMarketIDListByEvenGroupID($event_group_id);

				foreach ($event_list as $event) {
					$ext_markets = $import_market_model->getImportMarketListByExternalMeetingIDAndExternalMatchID($event_group->external_event_group_id, $event->external_event_id);

					if (!empty($ext_markets)) {
						foreach($ext_markets as $market){
							/**
							 * Check if the Market exists in the DB
							 * If not Add new market
							 */
							if(!in_array($market['external_market_id'], $added_market_list)){
								/**
								 * Get bet_type_id with the same name as the external market name
								 */
								$market_type_id = $market_type_model->addMarketTypeIfNotExist($market['name'], $market['description']);

								$market_params = array(
									'event_id'				=> $event->id,
									'market_type_id'		=> $market_type_id,
									'external_market_id'	=> $market['external_market_id'],
								);
								$market_model->store($market_params);
							}
						}
					}
				}
			}
		}
	}
	/**
	 * Ajax Tasks
	 */

	/**
	 * Load Competitions by Sport IDstring
	 * Used for Ajax
	 */
	public function loadCompetitions(){
		$sport_id = JRequest::getVar('sportId');

		if ($sport_id) {
			$competition_model =& $this->getModel('TournamentCompetition', 'TournamentModel');
			$comp_list = $competition_model->getTournamentCompetitionListBySportID($sport_id);

			$str = '';
			foreach($comp_list as $competition) {
				$str .= "{$competition->id}_:_{$competition->name}_|_";
			}
			$str = substr($str, 0, -3);
			echo $str;
		}
	}

	/**
	 * Load External Competition by Sport ID
	 * Used for Ajax
	 */
	public function loadEvents(){
		$competition_id = JRequest::getVar('competitionId');
		if ($competition_id) {
			$event_model  =& $this->getModel('TournamentEvent', 'TournamentModel');
			$event_data = $event_model->getTournamentEventListByCompetitonID($competition_id);

			foreach($event_data as $event) {
				$str .= "{$event->id}_:_{$event->event_name}_|_";
			}
			$str = substr($str, 0, -3);
			echo $str;
		}
	}
	/**
	 * Load External Competition by Sport ID
	 * Used for Ajax
	 */
	public function loadExtCompetitions(){
		$sport_id = JRequest::getVar('sportId');
		if ($sport_id) {
			$sports_model =& $this->getModel('TournamentSport', 'TournamentModel');
			$sport = $sports_model->getTournamentSport($sport_id);

			$ext_competition_model =& $this->getModel('ImportCompetition', 'TournamentModel');
			$ext_competition_data = $ext_competition_model->getImportCompetitionListBySportID($sport->external_sport_id);

			/**
			 * Sort result
			 */
			$sort =& $this->getModel('ImportSport', 'TournamentModel');
			$ext_competition_data = $this->_sortArray($ext_competition_data,'league_name');
			
			$str = '';
			foreach($ext_competition_data as $competition) {
				$str .= "{$competition['league_id']}_:_{$competition['league_name']}_|_";
			}
			$str = substr($str, 0, -3);
			echo $str;
		}
	}
	/**
	 * Load External Competition by Sport ID
	 * Used for Ajax
	 */
	public function loadExtMatchs(){
		$competition_id = JRequest::getVar('competitionId');
		if ($competition_id) {
			$ext_match_model =& $this->getModel('ImportMatch', 'TournamentModel');
			$ext_match_data = $ext_match_model->getImportMatchListByCompetitionID($competition_id);

			/**
			 * Sort result
			 */
			$sort =& $this->getModel('ImportSport', 'TournamentModel');
			$ext_match_data = $this->_sortArray($ext_match_data,'event_name');

			foreach($ext_match_data as $match) {
				$str .= "{$match[meeting_id]}_*_{$match[ext_match_id]}_:_{$match[event_name]}_|_";
			}
			$str = substr($str, 0, -3);
			echo $str;
		}

	}
	/**
	 * Load Buy-ins
	 */
	public function loadBuyIns(){
		$type = JRequest::getVar('private');
		$selVal = JRequest::getVar('tournament_value');
		$buyin_model =& $this->getModel('TournamentBuyIn', 'TournamentModel');
		$buyins = $buyin_model->getTournamentBuyInListByPrivateFlag($type);

		foreach($buyins as $buyin) {
			//if($type > 0 && ($buyin->buy_in < 5 || $buyin->buy_in > 100)) {}
			$sel = '';
			if($buyin->id == $selVal) $sel="!selected";
			$str .= "{$buyin->id}_:_Buy-in:{$buyin->buy_in} , Entry-fee:{$buyin->entry_fee}{$sel}_|_";
		}
		$str = substr($str, 0, -3);
		echo $str;
	}

	/**
	 * Method to sort Array by column name
	 * @param array $array
	 * @param String on [the coulumn we want to sort]
	 * @param bool ASC. false = DESC
	 */
	private function _sortArray($array, $on="id", $order=SORT_ASC)
	{
		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}
}
