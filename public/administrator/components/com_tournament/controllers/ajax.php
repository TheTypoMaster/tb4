<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class AjaxController extends JController
{
	public function getCompetitionListBySportID()
	{
		$sport_id = JRequest::getVar('value', null);

		$competition_list = JModel::getInstance('TournamentCompetition', 'TournamentModel')
								->getTournamentCompetitionListBySportID($sport_id);

		$option_list = array(
			array(
				'title' => 'Select a Competition',
				'value' => -1
			)
		);

		if(!is_null($competition_list)) {
			foreach($competition_list as $competition) {
				$option_list[] = array(
					'title' => $competition->name,
					'value' => $competition->id
				);
			}
		}

		$this->_sendResponse($option_list);
	}

	public function getEventGroupListByCompetitionID()
	{
		$competition_id = JRequest::getVar('value', null);

		$event_group_list = JModel::getInstance('EventGroup', 'TournamentModel')
								->getActiveEventGroupListByCompetitonID($competition_id);

		$option_list = array(
			array(
				'title' => 'Select an Event Group',
				'value' => -1
			)
		);

		if(!is_null($event_group_list)) {
			foreach($event_group_list as $event_group) {
				
				$check_abandoned = JModel::getInstance('EventGroup', 'TournamentModel')
								->isEventGroupAbandoned($event_group->id);
				if(empty($check_abandoned)){
				$option_list[] = array(
					'title' => $event_group->name . ' - ' . $event_group->start_date,
					'value' => $event_group->id
				);
				}
			}
		}

		$this->_sendResponse($option_list);
	}

	public function getExternalCompetitionListByExternalSportID()
	{
		$tournament_sport_id 	= JRequest::getVar('value', null);
		$external_sport_id 		= JModel::getInstance('SportMap', 'TournamentModel')
									->getExternalIDByTournamentSportID((int)$tournament_sport_id);

		$competition_list 		= JModel::getInstance('ImportCompetition', 'TournamentModel')
									->getImportCompetitionListBySportID($external_sport_id, true);

		$option_list = array(
			array(
				'title' => 'Select an external competition',
				'value' => -1
			)
		);

		if(!is_null($competition_list)) {
			foreach($competition_list as $competition_id => $competition_name) {
				$option_list[] = array(
					'title' => $competition_name,
					'value' => $competition_id
				);
			}
		}

		$this->_sendResponse($option_list);
	}
	
	public function getParentTournamentListBySportID()
	{
		$sport_id = JRequest::getVar('value', null);

		$sport = JModel::getInstance('TournamentSport', 'TournamentModel')
							->load($sport_id);
							
		$list_params = array(
			'type'	=> ($sport->racing_flag ? 'racing' : 'sports'),
			'order'	=> 'lower(t.name)'
		);
		
		$parent_tournament_list = JModel::getInstance('Tournament', 'TournamentModel')
										->getTournamentActiveList($list_params);

		$option_list = array(
			array(
				'title' => 'Select a Parent Tournament',
				'value' => -1
			)
		);

		if(!is_null($parent_tournament_list)) {
			foreach($parent_tournament_list as $tournament) {
				$option_list[] = array(
					'title' => $tournament->name,
					'value' => $tournament->id
				);
			}
		}

		$this->_sendResponse($option_list);
	}

	protected function _sendResponse($output)
	{
		header('application/json');
		print json_encode($output);
		exit();
	}
}