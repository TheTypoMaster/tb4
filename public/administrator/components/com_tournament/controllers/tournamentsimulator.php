<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

require_once( JPATH_COMPONENT.DS.'helpers'.DS.'simulator.php' );

class TournamentSimulatorController extends JController
{
	/**
	 * Controller URL is to reuse the same path to all the places nessessary
	 */
	const CONTROLLER_URL = 'index.php?option=com_tournament&controller=tournamentsimulator';
	private static $allowed_environment_list = array('staging','development');
	/**
	 * Display the list of racing tournaments
	 *
	 * @return void
	 */
	public function display()
	{
		$reader = ConfigReader::getInstance();
		$environment = $reader->getEnvironment();
		
		if (!in_array($environment, self::$allowed_environment_list)){
			JError::raiseWarning(0, 'Simulator can not be used on production.');
			return false;
		}
		
		$form = null;
		$status = null;
		
		$view =& $this->getView('TournamentSimulator', 'html', 'TournamentView');

		$meeting_model =& $this->getModel('Meeting', 'TournamentModel');
		$active_meeting_list = $meeting_model->getActiveMeetingList();
			
		if ($running_simulation = SimulatorHelper::checkForRunningSimulation($active_meeting_list)){
			$view->setLayout('simulationstatus');
			$view->assign('meeting', $running_simulation);
		} else {
			SimulatorHelper::RemoveExistingSimulationFromMeetingList($active_meeting_list);
			$template_list = SimulatorHelper::getTemplateList();
			$view->assign('template_list', $template_list);
			$view->assign('meeting_list', $active_meeting_list);
		}
		
		$view->display();
	}
	/**
	 * Save the simulation...
	 * 
	 */
	public function save()
	{
		$start_in  				= JRequest::getVar('start_in', 20);
		$meeting_id 			= JRequest::getVar('meeting', 0);
		$simulator_template 	= JRequest::getVar('template', '');
		$this->setRedirect(self::CONTROLLER_URL);

		try{
			$simulaton = SimulatorHelper::getInstance();
			$simulaton->meeting_id = (int) $meeting_id;
			$simulaton->use_template = $simulator_template;
			$simulaton->save();
		} catch(Exception $e){
			JError::raiseWarning(0, $e->getMessage());
			return false;
		}		
	}
	/**
	 * Make changes to simulatin...
	 * 
	 */
	public function update()
	{
		$meeting_id = JRequest::getVar('meeting_id', 0);
		$this->setRedirect(self::CONTROLLER_URL);
		
		$race_model =& $this->getModel('Race', 'TournamentModel');
		$race_list = $race_model->getRaceListByMeetingID($meeting_id);
		
		foreach ($race_list as $race){
			if ($race->race_status_id !== SimulatorHelper::getRaceStatus(TournamentModelRace::STATUS_ABANDONED)->id){
				$race->race_status_id = SimulatorHelper::getRaceStatus(TournamentModelRace::STATUS_PAYING)->id;
				$race_model->store((array) $race);
			}
		}
	}
}
