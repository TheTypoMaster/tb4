<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );
require_once JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'views' . DS . 'betting' . DS . 'view.html.php';


class TournamentViewTournament extends BettingViewBetting
{
	/**
	 * Retrieves the task from the request object and dispatches to the view method
	 *
	 * @param string $display
	 * @return void
	 */
	public function display($tpl = null)
	{
		$task = JRequest::getVar('task', null);

		switch($task) {
			case 'list':
				$this->listView();
				break;
			case 'list_tournaments':
				$this->listTournaments(true);
				break;
			case  'tournamentdetails':
				$this->tournamentDetails();
				break;
			case 'ajaxcall':
				$this->ajaxcall();
				break;
			case 'tournamenthistory':
				$this->tournamentHistory();
				break;
			case 'privatetournamenthistory':
				$this->privateTournamentHistory();
				break;
			case 'confirmpassword':
				$this->confirmPassword();
				break;
			case 'emailfriend':
				$this->emailFriend();
				break;
			case 'privatetournament':
				$this->privateTournament();
				break;
			case 'squeezeboxredirect':
				$this->squeezeboxRedirect();
				break;
			case 'confirmticket':
				$this->confirmTicket();
				break;
			case 'ticketerror':
				$this->ticketError();
				break;
			case 'jackpotmap':
				$this->jackpotMap();
				break;
			default:

				break;
		}

		parent::display($tpl, false);
	}

	/**
	 * Set up the display of the tournament list page
	 *
	 * @return void
	 */
	public function listView()
	{
		$this->listTournaments();
		$this->assign('is_tournament_list', false);
		$this->assign('right_col', TournamentHelper::getModule('right'));

	}

	/**
	 * Set up the display of the tournament list page
	 *
	 * @return void
	 */
	public function listTournaments($include_ajax_call_js = false)
	{
		$sorted_list	= array();
		$title_list		= array();
		$is_sports		= ('sports' == $this->tournament_type);

		$this->filters = array(
			'sport' => array('' => 'Select Sport ...'),
			'competition' => array('' => 'Select Competition ...'),
			'tournament_type' => array(
				''			=> 'Select Tournament Type ...',
				'cash'		=> 'Cash Tournaments',
				'jackpot'	=> 'Jackpot Tournaments'
			)
		);

		if ($is_sports) {
			$this->panel_title			= 'Sports Tournaments';
			$this->accord_title_class	= 'accord-title-sports';
			foreach($this->sport_list as $sport) {
				$this->filters['sport'][$sport->id] = $sport->name;
			}

			foreach($this->competition_list as $competition) {
				$this->filters['competition'][$competition->id] = $competition->name;
			}
		} else {
			$this->panel_title			= 'Racing Tournaments';
			$this->accord_title_class	= 'accord-title-racing';

			$this->filters['sport'] = array('' => 'Racing');
			$sport_model =& $this->getModel('TournamentSport');
			foreach($sport_model->excludeSports as $racing_type) {
				$this->filters['competition'][$racing_type] = ucwords($racing_type);
			}

			$selected_filter = '';
			if('0' === $this->jackpot) {
					$selected_filter = 'cash';
			} else if('1' == $this->jackpot) {
					$selected_filter = 'jackpot';
			}
			$this->selected_filter = $selected_filter;
		}

		if(!empty($this->tournament_list)) {
			$title_list   = $this->sortTitleList($this->tournament_list);
			$sorted_list  = $this->sortTournamentList($this->tournament_list);
		}

		$show_id = -1;
		$counter = 0;

		$this->assignRef('sorted_list', $sorted_list);
		$this->assignRef('title_list', $title_list);
		$this->assign('is_tournament_list', true);
		
		$config =& JFactory::getConfig();
		$this->assign('time_zone', $config->getValue('config.time_zone'));

		$this->setLayout('list');
		$document =& JFactory::getDocument();

		$document->addStyleSheet('components/com_tournament/assets/view.default.css');
		$document->addStyleSheet('components/com_tournament/assets/tourns.list.css');
		$document->addStyleSheet('components/com_tournament/assets/tourninfo.default.css');
		$document->addScript('components/com_tournament/assets/divlink.js');
		$document->addScript('components/com_tournament/assets/tournslist.js');
		$document->addScript('components/com_tournament/assets/common.js');

		if($is_sports) {
			$document->addScript('components/com_tournament/assets/sporttournslist.js');
		} else {
			$document->addScript('components/com_tournament/assets/racingtournslist.js');
		}

		$js_var = $this->formatAccordionJavascript($show_id);
		$document->addScriptDeclaration($js_var);

		$ajax_js = null;
		if($include_ajax_call_js) {
			$ajax_js =<<<EOF
			<script language='javascript'>
			$js_var
			$$('.register_link').each(function(el) {
				el.addEvent('click', function(e) {
				new Event(e).stop();
				SqueezeBox.fromElement(this.href, {handler: 'url', size: {x: 600, y: 220}});
					});
				});
		</script>
EOF;
		}
		$this->assign('ajax_js', $ajax_js);
	}


	/**
	 * Display tournament details page
	 *
	 * @return void
	 */
	public function tournamentDetails()
	{
		$this->tournament->value    		= Format::currency($this->tournament->buy_in, true) . ' + ' . Format::currency($this->tournament->entry_fee, true);
		$this->tournament->entrants			= count($this->player_list);

		$this->tournament->gameplay			= (empty($this->tournament->jackpot_flag)) ? 'Single' : 'Jackpot';

		if($this->is_racing_tournament){
			$end_date						= strtotime($this->tournament->end_date);
		} else {
			$end_date						= strtotime($this->tournament->betting_closed_date ? $this->tournament->betting_closed_date : $this->tournament->end_date);
		}
		$betting_open						= $end_date > time();
		$this->tournament->betting_close	= ($betting_open) ? $this->formatCounterText($end_date) : 'Completed';

		if(!empty($this->tournament->cancelled_flag)) {
			$this->tournament->betting_close	= 'Cancelled';
		}
		$user =& JFactory::getUser();

		$this->tournament->started  = (strtotime($this->tournament->start_date) < time() && empty($this->tournament->cancelled_flag));
		$this->tournament->ended    = (strtotime($this->tournament->end_date) < time() || !empty($this->tournament->cancelled_flag));

		$this->prize_pool = Format::currency($this->prize_pool, true);
        
        $this->free_credit_flag = $this->tournament->free_credit_flag;

		$prize_formula  = $this->place_list['formula'];
		
		if($this->free_credit_flag){
			$prize_formula = 'free';
		}

		$place_display = array();

		if( $this->ticket &&  $user->id > 0 && $betting_open ) {
			$this->tournament->bet_now_txt = "BET NOW";
		} else  {
			$this->tournament->bet_now_txt = "VIEW";
		}
		if($this->tournament->ended) {
			$this->tournament->bet_now_txt = "REVIEW";
		}

		switch($prize_formula) {
			case 'ticket':
				$this->place_title = 'Tickets into Tournament No. ' . $this->tournament->parent_tournament_id;
				break;
			case 'free':
				$this->place_title = $this->prize_pool . ' in Free Credit';
				break;
			case 'cash':
			default:
				$this->place_title = $this->prize_pool . ' in Cash Prizes';
				break;
				
		}

		$this->is_free_private_tournament = false;
		if($this->tournament->private_flag && $this->tournament->buy_in == 0) {
			$this->is_free_private_tournament = true;
			$place_display[1] 	= 'Top Betta';
			$this->place_title	= '';
		} else if(isset($this->place_list['place']) && is_array($this->place_list['place'])) {
			foreach($this->place_list['place'] as $place => $prize) {
				$place_display[$place] = array();
				if(isset($prize['ticket']) && !empty($prize['ticket'])) {
					$place_display[$place][] = '1 Ticket (#' . $prize['ticket'] . ')';
				}

				if(isset($prize['cash']) && !empty($prize['cash'])) {
					$place_display[$place][] = Format::currency($prize['cash'], true);
				}

				$place_display[$place] = join(' + ', $place_display[$place]);
				
				if($this->free_credit_flag){
					$place_display[$place] .= ' (FC)';
				}
			}
		}
		
		
		
		$this->place_display  = $place_display;
		$this->places_paid    = count($place_display);

		if(!empty($this->leaderboard_rank)) {
			$this->leaderboard_rank->display_currency = Format::currency($this->leaderboard_rank->currency, true);
		}

		foreach($this->leaderboard as $leaderboard) {
			$leaderboard->display_currency = Format::currency($leaderboard->currency, true);
		}


		$tournament_type = $this->is_racing_tournament ? 'racing' : 'sports';
		// button links and classes
		$unregister_link = 'tournament/'.$tournament_type.'/unregister/' . $this->tournament->id;
		$this->unregister_button_class  = ($this->unregister_allowed) ? 'greenButt' : 'gryButt';
		$this->unregister_button_link   = ($this->unregister_allowed) ? $unregister_link : '#';
		/**
		 * Check if the tournament is a protected private tournament or not
		 * if yes link will load the confirmPassword
		 * else the ticket
		 */

		$register_link = 'tournament/'. $tournament_type .'/confirmticket/'. $this->tournament->id;
		if (!empty($this->private_tournament->password)) {
			$register_link = 'tournament/confirmpassword/'. $this->tournament->id;
		}

		$this->register_button_class	= (is_null($this->ticket) && !$this->tournament->ended) ? 'greenButt' : 'gryButt';
		$this->register_button_link		= (!$this->tournament->ended) ? $register_link : '#';

		if($user->guest) {
			$this->register_button_link = '/user/register';
			$this->register_button_class .= ' registerLink';
		}

		if($this->is_racing_tournament){
			$next_match			= empty($this->next_race) ? '' : $this->next_race;
			$this->lobby_link	= '/tournament/racing';
		} else {
			$next_match = empty($this->next_match) ? '' : $this->next_match->id;
			$this->lobby_link	= '/tournament/sports';
		}

		$this->goto_button_link = 'tournament/'.$tournament_type.'/game/' . $this->tournament->id;
		if (!empty($next_match)) $this->goto_button_link .= '/' . $next_match;

		$this->goto_button_class  = 'greenButt';

		$tournament_type = ($this->tournament->private_flag) ? 'Private' : $tournament_type;

		// page setup
		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('TopBetta - '.ucwords($tournament_type . ' Tournament - ' . $this->tournament->name)));

		$document->addStyleSheet('components/com_tournament/assets/tourninfo.default.css');
		$document->addStyleSheet('components/com_tournament/assets/jackpotmap.css');

		// Add scripts to the document
		$document->addScript('components/com_tournament/assets/common.js');
		$document->addScript('components/com_tournament/assets/info.js');

		$unregister			= ($this->unregister_allowed) ? 'true' : 'false';
		$register			= ($this->register_allowed) ? 'true' : 'false';
		$user_logged_in		= ($user->guest) ? 'false' : 'true';
		
		$password_required	= 'false';
		if ($this->private_tournament) {
			$password_required	= ($this->private_tournament->password) ? 'true' : 'false';
		}
		
		$document->addScriptDeclaration("\tvar unregister = {$unregister}, register = {$register}, password_required = {$password_required}, user_logged_in = {$user_logged_in};");
	}
	/**
	 * Confirm password for Provate password protected tournaments
	 */
	public function confirmPassword(){
		JRequest::setVar('tmpl', 'component');
	}

	/**
	 * Email friend - promote private tournament
	 */
	public function emailFriend(){
		JRequest::setVar('tmpl', 'component');
		$this->user =& JFactory::getUser();
	}
	/**
	 * Set up private tournament form
	 *
	 * @return void
	 */
	public function privateTournament()
	{
		//init sport options
		foreach ($this->sport_list as $sport) {
			$sport_options[$sport->id] = ucwords($sport->name);
		}
		
		//init competition and event options
		$competition_options	= array();
		$event_group_options	= array();

		//set up prize format options
		$prize_format_options = array();
		foreach ($this->prize_format_list as $prize_format) {
			$prize_format_options[$prize_format->id] = $prize_format->name;
		}

		//set up buy-in options
		$buyin_options = array();
		foreach ($this->buyin_list as $buyin) {
			if(0 == $buyin->buy_in || ($buyin->buy_in >= 2 && $buyin->buy_in <= 100)) {
				$buyin_options[$buyin->id] = $this->formatTournamentValue($buyin->entry_fee * 100, $buyin->buy_in * 100);
			}
		}

		//init selected option
		$selected_prize_format	= isset($this->formdata['prize_format_id']) ? $this->formdata['prize_format_id'] : null;
		$selected_buyin			= isset($this->formdata['buyin_id']) ? $this->formdata['buyin_id'] : null;

		$selected_sport			= isset($this->formdata['sport_id']) ? $this->formdata['sport_id'] : null;
		$selected_competition	= isset($this->formdata['competition_id']) ? $this->formdata['competition_id'] : null;
		$selected_event			= isset($this->formdata['event_id']) ? $this->formdata['event_id'] : null;

		foreach ($this->competition_list as $competition) {
			$competition_options[$competition->id] = $competition->name;
		}

		foreach ($this->event_group_list as $event_group) {
			$event_group_options[$event_group->id] = $event_group->name;
			if(!empty($event_group->state) && $event_group->state != '__UNDEFINED__') {
				$event_group_options[$event_group->id] .= ' (' . $event_group->state . ')';
			}
		}

		$this->setLayout('privatetournament');

		//assign variables
		$this->assign('sport_options', $sport_options);
		$this->assign('competition_options', $competition_options);
		$this->assign('event_group_options', $event_group_options);
		$this->assign('prize_format_options', $prize_format_options);
		$this->assign('buyin_options', $buyin_options);

		$this->assign('selected_sport', $selected_sport);
		$this->assign('selected_competition', $selected_competition);
		$this->assign('selected_event', $selected_event);
		$this->assign('selected_prize_format', $selected_prize_format);
		$this->assign('selected_buyin', $selected_buyin);

		JRequest::setVar('tmpl', 'component');

	}

	public function squeezeboxRedirect()
	{
		$user =& JFactory::getUser();
		if ($user->guest) {
			$redirect_js =<<<EOF
				<script language='javascript'>
					loginAlert();
					window.top.location.reload();
				</script>
EOF;
		} else {
			$redirect_js =<<<EOF
				<script language='javascript'>
					window.top.location.href="{$this->redirect_url}";
				</script>
EOF;
		}
		$this->redirect_js = $redirect_js;
		$this->setLayout('privatetournamentredirect');
		JRequest::setVar('tmpl', 'component');
	}

	/**
	 * Set up ajax callbacks
	 *
	 * @return void
	 */
	public function ajaxcall()
	{
		switch($this->type) {
			case 'sport':
				$sport_options = array();

				foreach($this->sport_list as $sport) {
					$sport_options[$sport->id] = $sport->id . '_:_' . $sport->name;

					if($sport->id == $this->selected_sport_id) {
						$sport_options[$sport->id] .= '!selected';
					}
				}
				$this->assign('sport_options', $sport_options);

				break;
			case 'competition':
				$competition_options = array();

				foreach($this->competition_list as $competition) {
					$competition_options[] = $competition->id . '_:_' . $competition->name;
				}

				$this->assign('competition_options', $competition_options);
				break;
			case 'eventgroup':
				$event_group_options = array();
				foreach($this->event_group_list as $event_group) {
					$event_group_options[$event_group->id] = $event_group->id . '_:_' . $event_group->name;
				
					if (!empty($event_group->state) && $event_group->state != '__UNDEFINED__') {
						$event_group_options[$event_group->id] .= ' (' . $event_group->state . ')';
					}
				}
				$this->assign('event_group_options', $event_group_options);
				break;
		}

		$this->setLayout('ajax');

	}

	/**
	 * Display tournament history
	 *
	 * @return void
	 */
	public function tournamentHistory()
	{
		$sport_model		=& $this->getModel('TournamentSport');
		$sport_event_model	=& $this->getModel('TournamentSportEvent');
		$event_model		=& $this->getModel('TournamentEvent');

		foreach($this->tournament_list as $tournament) {
			$tournament->betta_bucks = Format::currency($tournament->betta_bucks, true);

			if(!empty($tournament->prize)) {
				$tournament->prize = Format::currency($tournament->prize, true);

				if($tournament->jackpot_flag &&  !empty($tournament->parent_tournament_id) && -1 != $tournament->parent_tournament_id) {
					$tournament->prize .= ' in Tournament Dollars';
				}
			}

			if('-' == $tournament->leaderboard_rank && !$tournament->bet_open) {
				$tournament->leaderboard_rank = '(Did Not Qualify)';
			} else if( empty($tournament->leaderboard_rank) || '-' == $tournament->leaderboard_rank ) {
				$tournament->leaderboard_rank = '&mdash;';
			} else {
				$tournament->leaderboard_rank = Format::ordinalNumber($tournament->leaderboard_rank);
			}

			if(in_array($tournament->sport_name, $sport_model->excludeSports)) {
				$tournament->sport_name			= 'Racing';
			}
		}

		$document = & JFactory::getDocument();
		//$currentLayout = $this->getLayout();
		$document->setTitle( JText::_('TopBetta - Tournament History') );
		//Add stylesheets to the document
		$document->addStyleSheet('components/com_payment/assets/accounttransaction.default.css');

		$this->assign('current_date', date('d/m/y'));

		$this->setLayout('history');
	}

	/**
	 * Display private tournament history
	 *
	 * @return void
	 */
	public function privateTournamentHistory()
	{
		$sport_model		=& $this->getModel('TournamentSport');
		$sport_event_model	=& $this->getModel('TournamentSportEvent');
		$event_model		=& $this->getModel('TournamentEvent');

		foreach($this->tournament_list as $tournament) {
			if(in_array($tournament->sport_name, $sport_model->excludeSports)) {
				$tournament->sport_name			= 'Racing';
			}

			$tournament->prize_pool_display = Format::currency($tournament->prize_pool, true);
		}

		$document = & JFactory::getDocument();
		//$currentLayout = $this->getLayout();
		$document->setTitle( JText::_('TopBetta - Private Tournament History') );
		//Add stylesheets to the document
		$document->addStyleSheet('components/com_payment/assets/accounttransaction.default.css');

		$this->assign('current_date', date('d/m/y'));

		$this->setLayout('privatehistory');
	}

	/**
	 * Format a tournament value string
	 *
	 * @param integer $entry_fee
	 * @param integer $buy_in
	 * @return string
	 */
	protected function formatTournamentValue($entry_fee, $buy_in)
	{
		$value = 'FREE';
		if(!empty($entry_fee) && !empty($buy_in)) {
			$value  = Format::currency($buy_in, true);
			$value .= ' + ';
			$value .= Format::currency($entry_fee, true);
		}

		return $value;
	}

	/**
	 * Formats an integer to be displayed as currency, optionally adding a dollar sign
	 *
	 * @param integer $amount
	 * @param boolean $add_dollar_sign
	 * @return string
	 */
	protected function formatCurrency($amount, $add_dollar_sign = false)
	{
		$text = ($add_dollar_sign) ? '$' : '';
		return $text . number_format(floor($amount) / 100, 2);

	}

	/**
	 * Format the display of a countdown to a specified time
	 *
	 * @param integer $time
	 * @return string
	 */
	protected function formatCounterText($time)
	{
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

			if( $days == 0 )
			{
				$min_sec_text = $text;
			}

			$text = $hours . ' hr ' . $min_sec_text;
		}

		if( $days > 0) {
			$text = $days . ' d ' . $text;
		}
		return $text;
	}

	/**
	 * Format the jackpot map
	 *
	 * @return void
	 */
	public function jackpotMap() {
		foreach($this->jackpot_map as $tournament) {
			$tournament->prize_pool     	= Format::currency($tournament->prize_pool, true);
			$tournament->display_value  	= Format::currency($tournament->buy_in, true) . '+ ' . Format::currency($tournament->entry_fee, true);
			$tournament->start_date_display	= date('d/m/y', strtotime($tournament->start_date));
		}

		$this->setLayout('jackpotmap');
	}

	/**
	 * Filter a list of tournaments into a meeting list
	 *
	 * @param array $unsorted
	 * @return array
	 */
	private function sortTitleList($unsorted)
	{
		$title_list = array();

		if('sports' == $this->tournament_type) {
			$title_list = array();
			$event_group_model =& $this->getModel('EventGroup');
			$competition_model =& $this->getModel('TournamentCompetition');
			foreach($unsorted as $tournament) {
				$event_group	= $event_group_model->getEventGroup($tournament->event_group_id);
				$competition	= $competition_model->getTournamentCompetition($event_group->tournament_competition_id);
				if(!isset($title_list[$tournament->event_group_id])) {
					$time = strtotime($tournament->start_date);
					$title_list[$tournament->event_group_id] = array(
						'image'           	=> $this->getSportIcon($tournament->sport_name),
						'competition_name'	=> $competition->name,
						'event_name'		=> $tournament->event_group_name,
						'time_class'     	 => ($time > time()) ? 'time' : 'timeP',
						'time'           	 => ($time > time()) ? $this->formatCounterText(strtotime($tournament->start_date)) : 'In Progress'
						);
				}
			}
		} else {
			foreach($unsorted as $tournament) {
				if(!isset($title_list[$tournament->event_group_id])) {
					$time = strtotime($tournament->start_date);
					$title_list[$tournament->event_group_id] = array(
						'image'           	=> $this->getSportIcon($tournament->sport_name),
						'competition_name'	=> $tournament->event_group_name,
						'event_name'		=> $tournament->sport_name,
						'track'           => (empty($tournament->track)) ? 'N/A' : $tournament->track,
						'weather'         => (empty($tournament->weather)) ? 'N/A' : $tournament->weather,
						'time_class'      => ($time > time()) ? 'time' : 'timeP',
						'time'            => ($time > time()) ? $this->formatCounterText(strtotime($tournament->start_date)) : 'In Progress',
					);
				}
			}
		}
		return $title_list;
	}

	/**
	 * Get the sport icons name
	 *
	 * @param string $name
	 * @return string
	 */
	protected function getSportIcon($name)
	{
		$name = preg_replace('/[^a-z0-9]/i', '', strtolower($name));
		return 'icon-' . $name;
	}

	/**
	 * Sort a list of tournaments into meeting groups and format their display values
	 *
	 * @param array $unsorted
	 * @return array
	 */
	private function sortTournamentList($unsorted)
	{
		$user =& JFactory::getUser();

		$sorted_list = array();
		foreach($unsorted as $tournament) {
			$event_group_id = $tournament->event_group_id;

			if(!isset($sorted_list[$event_group_id])) {
				$sorted_list[$event_group_id] = array();
			}
			$tournament->value    = $this->formatTournamentValue($tournament->entry_fee, $tournament->buy_in);
			$tournament->gameplay = (empty($tournament->jackpot_flag)) ? 'Single' : 'Jackpot';

			$tournament->places_paid  = count($tournament->place_list['place']);
			$tournament->display_pool = Format::currency($tournament->prize_pool, true);
			$tournament->info_link_href= 'tournament/details/' . $tournament->id;

			if(!empty($this->ticket_list) && isset($this->ticket_list[$tournament->id])) {
				$tournament->entry_link_href	= 'tournament/'. $this->tournament_type . '/game/' . $tournament->id;
				$tournament->entry_link_text	= 'Bet Now';
				$tournament->entry_link_class	= 'bet_link';
			} else {
				$tournament->entry_link_href	= $user->guest ? '/user/register' : 'tournament/' . $this->tournament_type . '/confirmticket/' . $tournament->id;
				$tournament->entry_link_text	= 'Enter';
				$tournament->entry_link_class	= $user->guest ? 'guest_link' : 'register_link';
			}

			$sorted_list[$event_group_id][$tournament->id] = $tournament;
		}
		return $sorted_list;
	}

	/**
	 * Format the accordion invocation javascript to open a particular meeting group
	 *
	 * @param integer $show_id
	 * @return string
	 */
	private function formatAccordionJavascript($show_id)
	{
		return
<<<EOD
window.addEvent('domready', function() {
	var accordion = new Accordion('div.atStart1', 'div.atStart2', {
		show: {$show_id},
		opacity: true,
		alwaysHide: true,
		onActive: function(toggler, element){
			toggler.getElement('.Aarrow').setStyle('background-position', '0 -98px');
		},
		onBackground: function(toggler, element){
			toggler.getElement('.Aarrow').setStyle('background-position', '0 -74px');
		}
	}, $('bettaWrap'));
	$$('.accListItm').each(function(el) {
		el.setStyle('display', 'block');
	});
});

EOD;
	}
	/**
	 * Sets up the display of the ticket confirmation box upon successful validation
	 *
	 * @return void
	 */
	public function confirmTicket()
	{
		JRequest::setVar('tmpl', 'component');
		$this->setLayout('confirmticket');

		$this->tournament->gameplay = (empty($this->tournament->jackpot_flag)) ? 'Single' : 'Jackpot';
		$time = strtotime($this->tournament->start_date);

		$this->tournament->display_time     = ($time > time()) ? date('g:i a', $time) : 'TOURNAMENT IS IN PROGRESS&hellip;';
		$this->tournament->display_counter  = ($time > time()) ? ' (' . $this->formatCounterText($time) . ')' : '';

		$this->tournament->display_date     = date('d / m / Y', $time);
		$this->tournament->display_value	= Format::currency($this->tournament->buy_in) . ' + ' . Format::currency($this->tournament->entry_fee);

		$this->tournament->place_count		= count($this->tournament->places_paid['place']);

		$this->tournament->prize_pool		= Format::currency($this->tournament->prize_pool);
		$this->tournament->image			= ($this->tournament->isRacing) ? $this->getTicketIcon('racing') : $this->getTicketIcon($this->tournament->sport_name);
		
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_tournament/assets/tourninfo.default.css');
	}

	/**
	 * Sets up display of the ticket confirmation box when there was a validation error
	 *
	 * @return void
	 */
	public function ticketError()
	{
		JRequest::setVar('tmpl', 'component');
		$this->setLayout('ticketerror');
		$this->tournament->image		= ($this->tournament->isRacing) ? $this->getTicketIcon('racing') : $this->getTicketIcon($this->tournament->sport_name);
		$this->tournament->gameplay		= (empty($this->tournament->jackpot_flag)) ? 'Single' : 'Jackpot';
	}


	/**
	 * Get the sport CSS class for display on the tournament game page
	 *
	 * @param string $name
	 * @return string
	 */
	public function getRacingIcon($name) {
		switch(strtolower($name)) {
			case 'galloping':
				$icon = 'jockeyIcon';
				break;
			case 'greyhounds':
				$icon = 'greyhoundsIcon';
				break;
			case 'harness':
				$icon = 'harnessIcon';
				break;
		}

		return $icon;
	}

	/**
	 * Get the ticket icons name
	 *
	 * @param string $name
	 * @return string
	 */
	protected function getTicketIcon($name)
	{
		$name = preg_replace('/[^a-z0-9]/i', '', strtolower($name));

		return 'ticket-' . $name;
	}

}
