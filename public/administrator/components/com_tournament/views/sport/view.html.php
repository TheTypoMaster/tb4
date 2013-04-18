<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class TournamentViewSport extends JView
{
	public function display($tpl = null)
	{
		$task = JRequest::getVar('task', 'listView');
		if(method_exists($this, $task)) {
			$this->$task();
		}

		parent::display($tpl);
	}

	public function listView()
	{
		$this->form_action = 'index.php?option=com_tournament&amp;controller=sport';
		foreach($this->sport_list as &$sport) {
			$sport->edit_link 	= 'index.php?option=com_tournament&amp;controller=sport&amp;task=edit&amp;id=' . $sport->id;
			$sport->status		= (empty($sport->status_flag)) ? 'Unpublished' : 'Published';
		}
	}

	public function edit()
	{
		$this->style_list = array();
		foreach($this->error_list as $field => $field_error_list) {
			$this->style_list[] = $field;
			foreach($field_error_list as $error) {
				JError::raiseNotice(69, $error);
			}
		}
	}
}