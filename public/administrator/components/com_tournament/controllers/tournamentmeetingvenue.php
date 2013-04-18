<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class TournamentMeetingVenueController extends JController
{
	/**
	 * Controller URL is to reuse the same path to all the places nessessary
	 */
	private $controllerUrl = 'index.php?option=com_tournament&controller=tournamentmeetingvenue';
	/**
	 * Display the list of racing tournaments
	 *
	 * @return void
	 */
	public function listView()
	{
		global $mainframe, $option;
		$state_id		= JRequest::getVar('stateId', null);
		$territory_id	= JRequest::getVar('territoryId', null);
		$venue_name		= JRequest::getVar('venueName', null);

		$venue_model 		=& $this->getModel('MeetingVenue', 'TournamentModel');
		$state_model		=& $this->getModel('MeetingState', 'TournamentModel');
		$territory_model	=& $this->getModel('MeetingTerritory', 'TournamentModel');
		
		$filter_prefix = 'meetingvenue';

		$order = $mainframe->getUserStateFromRequest(
			$filter_prefix.'filter_order',
			'filter_order',
			'v.id'
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
		
		$venue_list		= $venue_model->getMeetingVenueListByStateIDTerritoryIDAndVenueName($state_id, $territory_id, $venue_name, $order, $direction, $limit, $offset);
		$state_list		= $state_model->getMeetingStateList();
		$territory_list	= $territory_model->getMeetingTerritoryList();
		
		jimport('joomla.html.pagination');

		$total = $venue_model->getTotalMeetingVenueCount($state_id, $territory_id, $venue_name);
		$pagination 	= new JPagination($total, $offset, $limit);
		$view 			=& $this->getView('TournamentMeetingVenue', 'html', 'TournamentView');

		$view->assign('state_list', $state_list);
		$view->assign('state_id', $state_id);
		
		$view->assign('territory_list', $territory_list);
		$view->assign('territory_id', $territory_id);
		
		$view->assign('venue_name', $venue_name);

		$view->assign('venue_list', $venue_list);
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
		$this->setRedirect($this->controllerUrl);
		return;
	}
	/**
	* To load the Edit Odds form
	*/
	public function editMeetingVenue()
	{
		$venue_model 		=& $this->getModel('MeetingVenue', 'TournamentModel');
		$state_model		=& $this->getModel('MeetingState', 'TournamentModel');
		$territory_model	=& $this->getModel('MeetingTerritory', 'TournamentModel');
		
		$id = JRequest::getVar('id', null);
		
		if(empty($id)) {
			$this->setRedirect($this->controllerUrl, JText::_('Venue Id is empty'), 'error');
			return;
		}
		
		$venue = $venue_model->getMeetingVenue($id);
		if(empty($venue)) {
			$this->setRedirect($this->controllerUrl, JText::_('Venue not found'), 'error');
			return;
		}
		
		$state_list		= $state_model->getMeetingStateList();
		$territory_list	= $territory_model->getMeetingTerritoryList();
		
		$view =& $this->getView('TournamentMeetingVenue', 'html', 'TournamentView');
		$view->setLayout('editmeetingvenue');
		
		$view->assign('venue', $venue);
		$view->assign('state_list', $state_list);
		$view->assign('territory_list', $territory_list);
		
				//get the validation msg and keep the value entered after validation
		$session =& JFactory::getSession();

		$formData = array(
			'state'		=> $venue->meeting_state_id,
			'territory'	=> $venue->meeting_territory_id,
		);
		if($sessFormData = $session->get('sessFormData', null, 'meetingvenue'))
		{
			//print_r($session->get('sessFormErrors', null, 'withdrawal'));exit;
			if($sessFormErrors = $session->get('sessFormErrors', null, 'meetingvenue') )
			{
				$view->assign( 'formErrors', $sessFormErrors);
				$session->clear('sessFormErrors', 'meetingvenue');
			}

			$formData = array(
		        'state' => stripslashes($sessFormData['stateId']),
		        'new_state' => stripslashes($sessFormData['new_state']),
		        'territory' => stripslashes($sessFormData['territoryId']),
		        'new_territory' => stripslashes($sessFormData['new_territory']),
			);
			$session->clear('sessFormData', 'meetingvenue');
		}

		$view->assignRef('formData', $formData);
		
		$view->display();
	}
	
	/**
	 * to Save meeting venue
	 */
	public function saveMeetingVenue()
	{
    	$session			=& JFactory::getSession();
		$venue_model 		=& $this->getModel('MeetingVenue', 'TournamentModel');
		$state_model		=& $this->getModel('MeetingState', 'TournamentModel');
		$territory_model	=& $this->getModel('MeetingTerritory', 'TournamentModel');
		
		$id				= JRequest::getVar('id', null);
		$state_id		= JRequest::getVar('stateId', null);
		$territory_id	= JRequest::getVar('territoryId', null);
		$new_state		= JRequest::getVar('new_state', null);
		$new_territory	= JRequest::getVar('new_territory', null);
		
		$error = array();
		if(empty($id)) {
			$error['venue'] = 'Empty venue Id';
		} else {
			$venue = $venue_model->getMeetingVenue($id);
			if(empty($venue)) {
				$error['venue'] = 'Invalid venue id';
			}
		}
		
		if(empty($new_state)) {
			$state = $state_model->getMeetingState($state_id);
			
			if(empty($state)) {
				$error['state'] = 'Invalid option';
			}
		} else if($state_model->getMeetingStateByName($new_state)) {
			$error['state'] = 'State already exists';
		}
		
		if(empty($new_territory)) {
			$territory = $territory_model->getMeetingTerritory($territory_id);
			
			if(empty($territory)) {
				$error['territory'] = 'Invalid option';
			}
		} else if($territory_model->getMeetingTerritoryByName($new_territory)) {
			$error['territory'] = 'Territory already exists';
		}
		
		if(!empty($new_territory) && empty($new_state)) {
			$error['state'] = 'You must enter a new state for the new territory';
		}
		
		if(count($error) > 0) {
			$failedRedirectTo = 'index.php?option=com_tournament&controller=tournamentmeetingvenue&task=editmeetingvenue&id=' . $id;
			
    		$session->set( 'sessFormErrors', $error, 'meetingvenue' );
    		$session->set( 'sessFormData', $_POST, 'meetingvenue');
    		$this->setRedirect($failedRedirectTo, 'There were some errors processing this form. See messages below.', 'error');
    		return false;
		}
		
		$venue_params = array(
			'id'			=> $id,
			'meeting_state_id'		=> $state_id,
			'meeting_territory_id'	=> $territory_id,
		);
		//store new state
		if(!empty($new_state)) {
			$state_params = array(
				'name' => $new_state
			);
			$new_state_id = $state_model->store($state_params);
			if(empty($new_state_id)) {
				$this->setRedirect($this->controllerUrl, JText::_('Failed to store new state'), 'error');
				return;
			}
			$venue_params['meeting_state_id'] = $new_state_id;
		}
		
		if(!empty($new_territory)) {
			$territory_params = array(
				'name' => $new_territory
			);
			$new_territory_id = $territory_model->store($territory_params);
			if(empty($new_territory_id)) {
				$this->setRedirect($this->controllerUrl, JText::_('Failed to store new territory'), 'error');
				return;
			}
			$venue_params['meeting_territory_id'] = $new_territory_id;
		}
		
		if(!$venue_model->store($venue_params)) {
			$this->setRedirect($this->controllerUrl, JText::_('Failed to store venue'), 'error');
			return;
		}
		
		$this->setRedirect($this->controllerUrl, JText::_('Venue updated'));
	}
}