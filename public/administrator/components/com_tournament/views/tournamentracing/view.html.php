<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );
require_once JPATH_COMPONENT . DS . 'views' . DS . 'tournament' . DS . 'view.html.php';

class TournamentViewTournamentRacing extends TournamentViewTournament
{
	public function display($tpl = null) {
		$task = JRequest::getVar('task', 'list');

		switch($task) {
			case 'edit':
				$this->edit();
				break;
			case 'cancelform':
				$this->cancelForm();
				break;
			case 'list':
			default:
				$this->listView();
				break;
		}

		parent::display($tpl);
	}

	public function listView() {
		JToolBarHelper::addNew('edit');
		JToolBarHelper::preferences('com_tournament', '350');

		$tournament_list =& $this->get('tournament_list', array());
		// formatting for table items here
		foreach($tournament_list as $tournament) {
			$tournament->parent_name    = (empty($tournament->parent_name)) ? 'None' : $tournament->parent_name;
			$tournament->sport_name     = ucfirst($tournament->sport_name);

			$tournament->prize_formula  = (empty($tournament->jackpot_flag) || (int)$tournament->parent_tournament_id <= 0) ? 'Cash' : 'Ticket';
			$tournament->gameplay       = (empty($tournament->jackpot_flag)) ? 'Single' : 'Jackpot';

			$tournament->cancelled      = (empty($tournament->admin_cancelled_flag)) ? 'Active' : 'Cancelled';
			$tournament->status         = (empty($tournament->status_flag)) ? 'Unpublished' : 'Published';

			$tournament->buy_in         = (empty($tournament->buy_in)) ? 'Free' : number_format($tournament->buy_in / 100, 2);
			$tournament->entry_fee      = (empty($tournament->entry_fee)) ? 'Free' : number_format($tournament->entry_fee / 100, 2);
		}

		$this->assign('tournament_list', $tournament_list);
	}

	public function edit() {
		JToolBarHelper::save();
		JToolBarHelper::cancel();

		$tournament_id = $this->get('tournament_id');
		if(empty($tournament_id)) {
			$tournament_data = new stdClass;
		} else {
			$tournament_data = $this->get('TournamentRacingByTournamentID', 'TournamentRacing');
		}

		if(empty($tournament_data)) {
			JError::raiseWarning(0, 'The specified tournament ID could not be found');
		}

		$vars = get_object_vars($tournament_data);
		foreach($vars as $name => $value) {
			$this->assign($name, $value);
		}

		list(,$start_time) = explode(' ', (string)$vars['start_date']);
		$this->assign('start_time', $start_time);

		$buyin_list = $this->get('TournamentBuyInList', 'TournamentBuyIn');
		$this->assign('buyin_list', $buyin_list);

		$this->assign('current_buy_in', $this->current_buy_in);

		$meeting_list = $this->get('MeetingUpcomingList', 'Meeting');
		$this->assign('meeting_list', $meeting_list);

		$tournament_model =& $this->getModel('TournamentRacing');
		$list_params['jackpot'] = true;
		$active_list = $tournament_model->getTournamentRacingActiveList($list_params);
		$this->assign('active_list', $active_list);

		$betlimit_list = $this->get('BetLimitsByTournamentID', 'TournamentBetLimit');

		$betlimit_option_list = array();
		foreach($this->bettype_list as $bettype) {
			$betlimit_option_list[$bettype->id] = array(
        		'name'    => $bettype->name,
        		'value'   => $betlimit_list[$bettype->id]->value
			);
		}

		$this->assign('betlimit_option_list', $betlimit_option_list);
		
		if (empty($this->id)) {
			$document = & JFactory::getDocument();
			$js = <<<EOF
				window.addEvent('domready', function() {
					$('tab_meeting_id').addEvent('keyup', function(e) {
						if($('tab_meeting_id').getProperty('value')) {
							$('tournament_name').setStyle('display', 'block');
						} else {
							$('tournament_name').setStyle('display', 'none').setProperty('value','');
						}
					});
				});
EOF;
			$document->addScriptDeclaration($js);
		}
	}
}