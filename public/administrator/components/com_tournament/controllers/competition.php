<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class CompetitionController extends JController
{
	public function listView()
	{
		list($order, $direction, $limit, $offset) = ListViewHelper::getParameterList('competition');

		$competition_list 	= JModel::getInstance('TournamentCompetition', 'TournamentModel')
								->getTournamentCompetitionAdminList($order, $direction, $limit, $offset);

		$competition_count 	= JModel::getInstance('TournamentCompetition', 'TournamentModel')
								->getTournamentCompetitionCount();

		jimport('joomla.html.pagination');
		$pagination = new JPagination($competition_count, $limit, $offset);

		$view = $this->getView('Competition', 'html', 'TournamentView');
		$view->setLayout('listview');

		$view->assign('competition_list', 	$competition_list);
		$view->assign('pagination', 		$pagination->getListFooter());

		$view->assign('order', 				$order);
		$view->assign('direction', 			$direction);

		$view->assign('limit', 				$limit);
		$view->assign('offset', 			$offset);

		$view->display();
	}

	public function edit()
	{
		$id 			= JRequest::getVar('id', null);
		$competition 	= JModel::getInstance('TournamentCompetition', 'TournamentModel', $id);
		$session 		=& JFactory::getSession();

		$error_list = $session->get('error_list');
		if(is_null($error_list)) {
			$error_list = array();
		}

		$session->set('error_list', array());

		$submit_list = $session->get('submit_list');
		if(is_null($submit_list)) {
			$submit_list = array();
		}

		$session->set('submit_list', array());

		$sport_list = JModel::getInstance('TournamentSport', 'TournamentModel')
						->getTournamentSportList();

		$default_list = FormHelper::getDefaultList($this->_getFieldList(), $competition, $submit_list);

		$tournament_sport_id 		= (array_key_exists('tournament_sport_id', $submit_list)) ? $submit_list['tournament_sport_id'] : null;
		$external_competition_list 	= $this->_getExternalCompetitionOptionList($tournament_sport_id);

		$view = $this->getView('Competition', 'html', 'TournamentView');
		$view->setLayout('edit');

		$view->assign('competition', 				$competition);
		$view->assign('external_competition_list', 	$external_competition_list);

		$view->assign('default_list', 				$default_list);
		$view->assign('sport_list', 				$sport_list);

		$view->assign('error_list', 				$error_list);
		$view->assign('submit_list', 				$submit_list);

		$view->display();
	}

	public function _getFieldList()
	{
		static $field_list = array(
			'id' 						=> null,
			'external_competition_id' 	=> -1,
			'name' 						=> '',
			'tournament_sport_id' 		=> -1
		);

		return $field_list;
	}

	public function save()
	{
		$id 			= JRequest::getVar('id', null);
		$competition 	= JModel::getInstance('TournamentCompetition', 'TournamentModel')->load($id);

		$submit_list = array(
			'id' 						=> $id,
			'external_competition_id' 	=> JRequest::getVar('external_competition_id', -1),
			'tournament_sport_id'		=> JRequest::getVar('tournament_sport_id', -1),
			'name'						=> JRequest::getVar('name', '')
		);

		$competition->setMembers($submit_list);
		$error_list = $competition->validate();

		$session =& JFactory::getSession();
		if(!empty($error_list)) {
			$session->set('error_list', 	$error_list);
			$session->set('submit_list', 	$submit_list);

			$this->setRedirect('index.php?option=com_tournament&controller=competition&task=edit&id=' . $competition->id);
		} else {
			$session->set('error_list', 	array());
			$session->set('submit_list', 	array());

			$competition->save();
			$this->setRedirect('index.php?option=com_tournament&controller=competition');
		}
	}

	private function _getExternalCompetitionOptionList($sport_id)
	{
		if(!empty($sport_id)) {
			return JModel::getInstance('ImportCompetition', 'TournamentModel')
					->getImportCompetitionListBySportID($sport_id, true);
		}

		return array();
	}
}