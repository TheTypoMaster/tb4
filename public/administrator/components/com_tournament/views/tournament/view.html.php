<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );

class TournamentViewTournament extends JView
{
	public function display($tpl = null) {
		$task = JRequest::getVar('task', 'listView');
		
		if(method_exists($this, $task)) {
			$this->$task();
		} else {
			$this->listView();
		}
		
		$document = &JFactory::getDocument();
		$document->addScript('/media/system/js/tabs.js' );

		parent::display($tpl);
		
	}

	public function listView()
	{
		$this->is_private		= (bool)$this->private_flag;
		$this->form_action 		= 'index.php?option=com_tournament';

		$this->public_tab_link 	= $this->form_action . '&amp;private=0';
		$this->private_tab_link = $this->form_action . '&amp;private=1';

		foreach ($this->tournament_list as &$tournament) {
			$tournament->prize_formula 	= (!empty($tournament->jackpot_flag) && $tournament->parent_tournament_id > 0) ? 'Ticket' : 'Cash';
			$tournament->prize_pool		= $this->formatCurrency($tournament->prize_pool, true);
			$tournament->gameplay		= (!empty($tournament->jackpot_flag)) ? 'Jackpot' : 'Single';

			$tournament->status		= (empty($tournament->status_flag)) ? 'Unpub-' : 'Pub-';
			$tournament->cancelled 	= (empty($tournament->cancelled_flag)) ? 'Active' : 'Cancelled';

			$tournament->view_link 	= 'index.php?option=com_tournament&amp;task=view&amp;id=' . $tournament->id;
			$tournament->clone_link	= 'index.php?option=com_tournament&amp;task=clonetournament&amp;id=' . $tournament->id;

			$tournament->cancel_link = 'index.php?option=com_tournament&amp;task=cancelform&amp;id=' . $tournament->id;
			$tournament->delete_link = 'index.php?option=com_tournament&amp;task=delete&amp;id=' . $tournament->id;

			$tournament->edit_link = 'index.php?option=com_tournament&amp;task=edit&amp;id=' . $tournament->id;

			$tournament->buy_in 	= $this->formatCurrency($tournament->buy_in, true);
			$tournament->entry_fee	= $this->formatCurrency($tournament->entry_fee, true);
		}
		
		if (!empty($this->tod_list)) {
			foreach($this->tod_list as $tod) {
				$this->tod_flag_list[strtoupper($tod->keyword)] = JText::_($tod->venue);
			}
		}
		
		$this->tournament_type_list = array(
			0	=> 'Public Tournaments', 
			1	=> 'Private Tournaments'
		);
		
		$document =& JFactory::getDocument();
		$document->addScript('/media/system/js/datepicker.js' );
		
		$js = "window.addEvent('domready', function(){
			$$('input.DatePicker').each( function(el){
				new DatePicker(el);
				});
		}); ";
		$document->addScriptDeclaration($js);
		
		$js = "function isNumberKey(evt)
		      {
		         var charCode = (evt.which) ? evt.which : event.keyCode
		         if (charCode > 31 && (charCode < 48 || charCode > 57))
		            return false;
		         return true;
		      }
		";
		$document->addScriptDeclaration($js);
		
		
		$css = '/media/system/css/datepicker.css';
		$document->addStyleSheet($css);
	}

	public function edit()
	{
		$this->sport_option_list = array('-1' => JText::_('Select a Sport'));

		if (!empty($this->sport_list)) {
			foreach($this->sport_list as $sport) {
				$this->sport_option_list[$sport->id] = JText::_($sport->name);
			}
		}

		$this->sport_selected_list = FormHelper::getSelectedList($this->sport_option_list, $this->formdata['tournament_sport_id']);

		$this->competition_option_list = array('-1' => JText::_('Select a Competition'));
		if (!empty($this->competition_list)) {
			foreach($this->competition_list as $competition) {
				$this->competition_option_list[$competition->id] = JText::_($competition->name);
			}
		}

		$this->competition_selected_list = FormHelper::getSelectedList($this->competition_option_list, $this->formdata['tournament_competition_id']);

		$this->event_group_option_list = array('-1' => JText::_('Select an Event Group'));
		if (!empty($this->event_group_list)) {
			foreach($this->event_group_list as $eg_id => $eg) {
				$this->event_group_option_list[$eg_id] = $eg->name . ' - ' . $eg->start_date;
			}
		}

		$this->event_group_selected_list = FormHelper::getSelectedList($this->event_group_option_list, $this->formdata['event_group_id']);
		
		$this->buy_in_option_list = array('-1' => JText::_('Select the ticket value'));

		if (!empty($this->buy_in_list)) {
			foreach($this->buy_in_list as $buyin) {
				$this->buy_in_option_list[$buyin->id] = $this->_formatTicketValue($buyin->buy_in, $buyin->entry_fee);
			}
		}

		$this->buy_in_selected_list = FormHelper::getSelectedList($this->buy_in_option_list, $this->formdata['ticket_value']);

		$this->parent_tournament_option_list = array('-1' => JText::_('Select a Parent Tournament'));

		if (!empty($this->parent_tournament_list)) {
			foreach($this->parent_tournament_list as $parent) {
				$this->parent_tournament_option_list[$parent->id] = JText::_($parent->name);
			}
		}

		$this->parent_tournament_selected_list = FormHelper::getSelectedList($this->parent_tournament_option_list, $this->formdata['parent_tournament_id']);

		$this->venue_option_list = array('-1' => JText::_('Select a Venue of the future tournament'));
		
		if (!empty($this->venue_list)) {
			foreach($this->venue_list as $venue) {
				$this->venue_option_list[$venue->name] = $venue->name;
			}
		}
		$this->venue_selected_list = FormHelper::getSelectedList($this->venue_option_list, $this->formdata['future_meeting_venue']);
		
		
// 		// Build the tournament feature list
// 		$this->tournament_feature_option_list = array('-1' => JText::_('Select Tournament Feature'));
		
// 		if (!empty($this->tournament_feature_list)) {
// 			foreach($this->tournament_feature_list as $feature) {
// 				$this->tournament_feature_option_list[$feature->keyword] = JText::_($feature->keyword);
// 			}
// 		}
// 		$this->tournament_feature_selected_list = FormHelper::getSelectedList($this->tournament_feature_option_list, $this->formdata['tournament_feature_id']);
		
		
		if (empty($this->formdata['jackpot_flag'])) {
			$this->jackpot_yes_checked 	= '';
			$this->jackpot_no_checked	= ' checked="checked"';
		} else {
			$this->jackpot_yes_checked	= ' checked="checked"';
			$this->jackpot_no_checked	= '';
		}
		
		$this->tod_flag_list = array('' => JText::_('Select here'));
		if (!empty($this->tod_list)) {
			foreach($this->tod_list as $tod) {
				$this->tod_flag_list[strtoupper($tod->keyword)] = JText::_($tod->venue);
			}
		}
		
		$this->free_credit_flag = $this->formdata["free_credit_flag"];

		$this->style_list = array();
		foreach ($this->error_list as $field => $field_error_list) {
			$this->style_list[] = $field;
			foreach($field_error_list as $error) {
				JError::raiseNotice(69, $error);
			}
		}
		
		$this->disabled = ($this->entrants_disable ? ' disabled="disabled"' : '');

		$ajax_base = JURI::base() . 'index.php?option=com_tournament&controller=ajax';

		$competition_js_option_list = json_encode(array(
			'request_base' 		=> $ajax_base,
			'select_id'			=> 'tournament_competition_id',
			'callback'			=> 'getCompetitionListBySportID',
			'trigger_id'		=> 'tournament_sport_id',
			'complete_trigger'	=> array(
				'request_base' 	=> $ajax_base,
				'select_id'		=> 'event_group_id',
				'callback'		=> 'getEventGroupListByCompetitionID',
				'trigger_id'	=> 'tournament_competition_id'
			)
		));

		$eg_js_option_list = json_encode(array(
			'request_base' 	=> $ajax_base,
			'select_id'		=> 'event_group_id',
			'callback'		=> 'getEventGroupListByCompetitionID',
			'trigger_id'	=> 'tournament_competition_id'
		));
		
		$parent_js_option_list = json_encode(array(
			'request_base' 		=> $ajax_base,
			'select_id'			=> 'parent_tournament_id',
			'callback'			=> 'getParentTournamentListBySportID',
			'trigger_id'		=> 'tournament_sport_id'
		));

		$document =& JFactory::getDocument();
		$document->addScript('components/com_tournament/assets/form.js');

		$bind_js = <<<EOF
			window.addEvent('domready', function() {
				var competition_select	= new AjaxSelectInput({$competition_js_option_list});
				var event_group_select	= new AjaxSelectInput({$eg_js_option_list});
				var parent_tourn_select	= new AjaxSelectInput({$parent_js_option_list});	
			});
EOF;

		$document->addScriptDeclaration($bind_js);
		$document->addScript('components/com_tournament/assets/formfields.js');
		
		$document->addScript('/media/system/js/datepicker.js' );
			
		$js = "window.addEvent('domready', function(){
			$$('input.DatePicker').each( function(el){
			new DatePicker(el);
			});
		}); ";
		$document->addScriptDeclaration($js);
		
		$css = '/media/system/css/datepicker.css';
		$document->addStyleSheet($css);
	}

	private function _getSelectedList($field, $member)
	{
		$list = array();
		foreach($this->$member as $id => $name) {
			$list[$id] = ($this->default_list[$field] == $id) ? ' selected="selected"' : '';
		}

		return $list;
	}

	protected function _formatTicketValue($buy_in, $entry_fee)
	{
		return sprintf('%s + %s',
			$this->formatCurrency($buy_in * 100, true),
			$this->formatCurrency($entry_fee * 100, true));
	}

	/**
	 * View Tournament
	 */
	public function view()
	{
		$back_link = 'index.php?option=com_tournament&controller=tournament';
		JToolBarHelper::back('Back to Tournament List', $back_link);

		$this->tournament->value    = $this->formatCurrency($this->tournament->buy_in, true) . ' + ' . $this->formatCurrency($this->tournament->entry_fee, true);
		$this->tournament->entrants = count($this->player_list);

		$this->tournament->gameplay = (empty($this->tournament->jackpot_flag)) ? 'Single' : 'Jackpot';

		$start_date = strtotime($this->tournament->start_date);
		$this->tournament->betting_start  = ($start_date > time()) ? $this->formatCounterText($start_date) : 'Started';
		$end_date = strtotime($this->tournament->end_date);
		$this->tournament->betting_close  = ($end_date > time()) ? $this->formatCounterText($end_date) : 'Completed';
		if(!empty($this->tournament->cancelled_flag)) {
			$this->tournament->betting_start = 'Cancelled';
			$this->tournament->betting_close = 'Cancelled';
		}

		$this->tournament->started  = (strtotime($this->tournament->start_date) < time() && empty($this->tournament->cancelled_flag));
		$this->tournament->ended    = (strtotime($this->tournament->end_date) < time() || !empty($this->tournament->cancelled_flag));

		$this->prize_pool = $this->formatCurrency($this->prize_pool, true);

		$prize_formula  = $this->place_list['formula'];

		switch($prize_formula) {
			case 'ticket':
				$this->place_title = 'Tickets into Tournament No. ' . $this->tournament->parent_tournament_id;
				break;
			case 'cash':
			default:
				$this->place_title = $this->prize_pool . ' in Cash Prizes';
				break;
		}

		$place_display = array();
		$this->is_free_private_tournament = false;
		if($this->tournament->private_flag && $this->tournament->buy_in == 0) {
			$this->is_free_private_tournament = true;
			$place_display[1] 	= 'Top Betta';
		} else if(is_array($this->place_list['place']) && !empty($this->place_list['place'])) {
			foreach($this->place_list['place'] as $place => $prize) {
				$place_display[$place] = array();
				if(isset($prize['ticket']) && !empty($prize['ticket'])) {
					$place_display[$place][] = '1 Ticket (#' . $prize['ticket'] . ')';
				}

				if(isset($prize['cash']) && !empty($prize['cash'])) {
					$place_display[$place][] = $this->formatCurrency(round($prize['cash']), true);
				}

				$place_display[$place] = join(' + ', $place_display[$place]);
			}
		}
		$this->place_display  = $place_display;
		$this->places_paid    = count($place_display);

		if(!empty($this->leaderboard_rank)) {
			$this->leaderboard_rank->display_currency = $this->formatCurrency($this->leaderboard_rank->currency, true);
		}

		foreach($this->leaderboard as $leaderboard) {
			$leaderboard->display_currency = $this->formatCurrency($leaderboard->currency, true);
		}

		// button links and classes

		if($this->is_racing_tournament) {
			$unregister_link = 'tournament/racing/unregister/' . $this->tournament->id;
		} else {
			$unregister_link = 'tournament/sport/unregister/' . $this->tournament->id;
		}

		// page setup
		$document = & JFactory::getDocument();
		$document->setTitle(JText::_($this->is_racing_tournament ? 'TopBetta - Racing Tournaments' : 'TopBetta - Sport Tournaments'));

		$this->tournament_details_title = JText::_($this->is_racing_tournament ? 'Racing Tournament Details' : 'Sports Tournament Details');

		if($this->tournament->private_flag > 0) {
			$title_prefix 	 = "Private Tournament - ";
			$tournament_type = "Cash - Private";
			if(!empty($this->private_tournament->password)) {
				$tournament_type .= " - Password-protected";
			}
		} else {
			$tournament_type = $this->tournament->gameplay;
		}
		$this->tournament_type = $tournament_type;
		
		$entrants_export_link = null;
		if ($this->tournament->entrants > 0) {
			$entrants_export_link = JRoute::_('index.php?option=com_tournament&task=export_entrants&id='. $this->tournament->id);
		}
		$this->assign('entrants_export_link', $entrants_export_link);
	}
	
	public function cancelForm() {
		
		if (!$this->tournament->cancelled_flag) {
			JToolBarHelper::save('cancelsave', 'Cancel tournament');
		}
		JToolBarHelper::cancel('cancel', 'Back to Tournament List');
	}

	/**
	 * Formats an integer to be displayed as currency, optionally adding a dollar sign
	 *
	 * @param integer $amount
	 * @param boolean $add_dollar_sign
	 * @return string
	 */
	protected function formatCurrency($amount, $add_dollar_sign = false) {
		$text = ($add_dollar_sign) ? '$' : '';
		return $text . number_format($amount / 100, 2);
	}

	/**
	 * Format the display of a countdown to a specified time
	 *
	 * @param integer $time
	 * @return string
	 */
	protected function formatCounterText($time) {
		if($time < time()) {
			return 'PAST START TIME';
		}

		$remaining = $time - time();

		$days     = intval($remaining / 3600 / 24);
		$hours    = intval(($remaining / 3600) % 24);
		$minutes  = intval(($remaining / 60) % 60);
		$seconds  = intval($remaining % 60);

		$text = $seconds . ' sec';
		if($minutes > 0) {
			$text = $minutes . ' min';
		}

		if($hours > 0) {
			$min_sec_text = '';

			if( $days == 0 ) {
				$min_sec_text = $text;
			}

			$text = $hours . ' hr ' . $min_sec_text;
		}

		if( $days > 0) {
			$text = $days . ' d ' . $text;
		}

		return $text;
	}
}
