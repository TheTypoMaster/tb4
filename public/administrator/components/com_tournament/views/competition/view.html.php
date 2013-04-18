<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class TournamentViewCompetition extends JView
{
	public function display()
	{
		$task = JRequest::getVar('task', 'listview');
		if(method_exists($this, $task)) {
			$this->$task();
		}

		parent::display();
	}

	public function listView()
	{
		$this->form_action = 'index.php?option=com_tournament&amp;controller=competition';
		foreach($this->competition_list as &$competition) {
			$competition->edit_link = 'index.php?option=com_tournament&amp;controller=competition&amp;task=edit&amp;id=' . $competition->id;
			$competition->status	= (empty($competition->status_flag)) ? 'Unpublished' : 'Published';
		}
	}

	public function edit()
	{
		$this->sport_option_list = array('-1' => 'Select a sport');
		foreach($this->sport_list as $sport) {
			$this->sport_option_list[$sport->id] = $sport->name;
		}

		$this->sport_selected_list = FormHelper::getSelectedList($this->sport_option_list, $this->default_list['tournament_sport_id']);

		$this->external_competition_option_list = array('-1' => 'Select an external competition');
		foreach($this->external_competition_list as $external_id => $external_name) {
			$this->external_competition_option_list[$external_id] = $external_name;
		}

		$this->external_competition_selected_list = FormHelper::getSelectedList($this->external_competition_option_list, $this->default_list['external_competition_id']);

		$this->status_flag_on 	= (empty($this->competition->status_flag)) ? '' : ' checked="checked"';
		$this->status_flag_off	= (empty($this->competition->status_flag)) ? ' checked="checked"' : '';

		$ajax_base = JURI::base() . 'index.php?option=com_tournament&controller=ajax';

		$competition_js_option_list = json_encode(array(
			'request_base' 	=> $ajax_base,
			'select_id'		=> 'external_competition_id',
			'callback'		=> 'getExternalCompetitionListByExternalSportID',
			'trigger_id'	=> 'tournament_sport_id'
		));

		$document =& JFactory::getDocument();
		$document->addScript('components/com_tournament/assets/form.js');

		$bind_js = <<<EOF
			window.addEvent('domready', function() {
				var competition_select = new AjaxSelectInput({$competition_js_option_list});
			});
EOF;

		$document->addScriptDeclaration($bind_js);
	}
}