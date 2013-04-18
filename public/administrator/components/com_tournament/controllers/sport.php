<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class SportController extends JController
{
	public function listView()
	{
		list($order, $direction, $limit, $offset) = ListViewHelper::getParameterList('sport');

		$sport_list 	= JModel::getInstance('TournamentSport', 'TournamentModel')
							->getTournamentSportAdminList($order, $direction, $limit, $offset);

		$sport_count 	= JModel::getInstance('TournamentSport', 'TournamentModel')
							->getTournamentSportCount();

		jimport('joomla.html.pagination');
		$pagination = new JPagination($sport_count, $limit, $offset);

		$view = $this->getView('Sport', 'html', 'TournamentView');
		$view->setLayout('listview');

		$view->assign('sport_list', $sport_list);
		$view->assign('pagination', $pagination->getListFooter());

		$view->assign('order', 		$order);
		$view->assign('direction', 	$direction);

		$view->assign('limit', 		$limit);
		$view->assign('offset', 	$offset);

		$view->display();
	}

	private function _getFieldList()
	{
		static $field_list = array(
			'id' 				=> null,
			'name' 				=> '',
			'description' 		=> '',
			'status_flag' 		=> 0,
			'external_sport_id' => -1
		);

		return $field_list;
	}

	public function edit()
	{
		$id 		= JRequest::getVar('id', null);
		$sport 		= JModel::getInstance('TournamentSport', 'TournamentModel')->load($id);
		$session 	=& JFactory::getSession();

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

		$default_list 			= FormHelper::getSelectedList($this->_getFieldList(), $sport, $submit_list);
		$external_sport_list 	= $this->_getExternalSportOptionList();

		$view = $this->getView('Sport', 'html', 'TournamentView');
		$view->setLayout('edit');

		$view->assign('sport', 					$sport);

		$view->assign('external_sport_list', 	$external_sport_list);
		$view->assign('default_list', 			$default_list);

		$view->assign('error_list', 			$error_list);
		$view->assign('submit_list', 			$submit_list);

		$view->display();
	}

	public function save()
	{
		$id 	= JRequest::getVar('id', null);
		$sport 	= JModel::getInstance('TournamentSport', 'TournamentModel')->load($id);

		$submit_list = array(
			'id' 				=> $id,
			'name' 				=> JRequest::getVar('name', null),
			'description' 		=> JRequest::getVar('description', null),
			'status_flag'		=> JRequest::getVar('status_flag', 0)
		);

		$sport_map = JModel::getInstance('SportMap', 'TournamentModel');
		$sport_map->external_sport_id = JRequest::getVar('external_sport_id', -1);

		$error_list = $sport_map->validate();
		if(!array_key_exists('external_sport_id', $error_list)) {
			$sport->setMembers($submit_list);
			$error_list += $sport->validate();
		}

		unset($error_list['tournament_sport_id']);
		$session =& JFactory::getSession();

		if(!empty($error_list)) {
			$session->set('error_list', $error_list);
			$session->set('submit_list', $submit_list);

			$this->setRedirect('index.php?option=com_tournament&controller=sport&task=edit&id=' . $id);
		} else {
			$session->set('error_list', array());
			$session->set('submit_list', array());

			$sport_map->tournament_sport_id = $sport->save();
			$sport_map->save();

			$this->setRedirect('index.php?option=com_tournament&controller=sport');
		}
	}

	private function _getExternalSportOptionList()
	{
		return JModel::getInstance('ImportSport', 'TournamentModel')
					->getImportSportList(true);
	}
}