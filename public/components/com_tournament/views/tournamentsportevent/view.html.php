<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
require_once JPATH_COMPONENT . DS . 'views' . DS . 'tournament' . DS . 'view.html.php';

class TournamentViewTournamentSportEvent extends TournamentViewTournament
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
			case 'confirmticket':
				$this->confirmTicket();
				break;
			case 'ticketerror':
				$this->ticketError();
				break;
			case 'confirmbet':
				$this->confirmBet();
				break;
			case 'beterror':
				$this->betError();
				break;
			case 'game':
				$this->game();
				break;
			case 'listoffers':
				$this->listOffers(true);
				break;
		}

		parent::display($tpl);
	}

	/**
	 * Sets up the display of the gameplay page
	 *
	 * @return void
	 */
	public function game()
	{
		$price = (empty($this->tournament->buy_in)) ? 'FREE' : Format::currency($this->tournament->buy_in, true);
		$this->tournament->title      = $this->tournament->name . ' - ' . $this->tournament->sport_name . ' - ' . $price;

		$this->tournament->icon       = $this->getSportIcon($this->tournament->sport_name);
		$this->tournament->info_link  = 'tournament/details/' . $this->tournament->id;

		$this->tournament->available_currency   = Format::currency($this->tournament->available_currency);
		$this->tournament->turnover_currency    = Format::currency($this->tournament->turnover_currency);

		$betting_end_date = $this->match->start_date;

		if(!empty($this->tournament->betting_closed_date) && $this->tournament->betting_closed_date < $betting_end_date) {
			$betting_end_date = $this->tournament->betting_closed_date;
		}

		$this->match->paid_status = $this->match->paid_flag ? 'Paid' : 'Approx. Win';

		$bet_model =& $this->getModel('TournamentBet');
		foreach($this->match_list as $match) {
			$match->class		= ($match->id == $this->match->id) ? ' class="selected"' : '';
			$match->link		= 'tournament/sports/game/' . $this->tournament->id . '/' . $match->id;
			
			$bet_total	= 0;
			if (!is_null($this->ticket)) {
				$bet_total			= $bet_model->getTournamentBetTotalsByEventIDAndTicketID($match->id, $this->ticket->id);
			}

			$match->bet_total	= ($bet_total ? ('<span class="bet-amount bet-amount-cell">' . Format::currency($bet_total) . '</span>') : '-');
		}

		foreach($this->market_list as $market) {
			$market->class		= ($market->id == $this->market->id) ? ' class="selected"' : '';
			$market->link		= 'tournament/sports/game/' . $this->tournament->id . '/' . $this->match->id . '/' . $market->id;
			
			$bet_total = 0;
			if (!is_null($this->ticket)) {
				$bet_total			= $bet_model->getTournamentBetTotalsByMarketIDAndTicketID($market->id, $this->ticket->id);
			}
			$market->bet_total	= ($bet_total ? ('<span class="bet-amount">' . Format::currency($bet_total) . '</span>') : '-');
		}

		$this->match->total_bet = 0;
		$this->match->total_win = 0;
		foreach($this->bet_list as $bet) {
			$offer_model			=& $this->getModel('Selection');
			$bet_selection_model	=& $this->getModel('TournamentBetSelection');
			$bet_selection			= $bet_selection_model->getTournamentBetSelectionListByBetID($bet->id);
			$offer					= $offer_model->getSelection($bet_selection[0]->selection_id);

			$bet->market_name			= $this->market_list[$offer->market_id]->name;
			$bet->offer_name			= $offer->name;
			$bet->display_bet			= Format::currency($bet->bet_amount);

			$bet->display_odds			= Format::odds($bet->fixed_odds);
			if($bet->bet_status == 'fully-refunded' || ($bet->resulted_flag && $bet->win_amount == 0 )) {
				$bet->display_odds		= '&mdash;';
			}

			if($bet->bet_status == 'fully-refunded') {
				$bet->result	= 'REFUNDED';
				$bet->row_class	= ' scratched';
				$bet->class		= 'paid';
				$bet->paid		= '&mdash;';
			} else {
				$bet->row_class		= '';
				$bet->paid_amount   = (empty($bet->resulted_flag)) ? bcmul($bet->display_odds, $bet->bet_amount) : $bet->win_amount;
				
				// formatting for wins and approximate wins
				$bet->paid			= ($bet->paid_amount > 0) ? Format::currency($bet->paid_amount) : '&mdash;';
				$bet->class			= 'paid';
				$bet->result		= (empty($bet->resulted_flag)) ? '&mdash;' : 'WIN';

				// formatting for losing bets
				if(intval($bet->paid_amount) == 0 && !empty($bet->resulted_flag)) {
					$bet->class   = 'loss';
					$bet->paid    = 'NIL';
					$bet->result  = 'LOSS';
				}
			}

			$this->match->total_bet = bcadd($this->match->total_bet, ($bet->bet_status == 'fully-refunded') ? 0 : $bet->bet_amount);
			$this->match->total_win = bcadd($this->match->total_win, ($bet->bet_status == 'fully-refunded') ? 0 : $bet->paid_amount);
		}

		$net_win = '&mdash;';
		$this->match->net_win_class	= '';
		if($this->match->paid_flag) {
			$net_win = $this->match->total_win - $this->match->total_bet;
			if($net_win < 0) {
				$this->match->net_win_class	= 'loss';
			}

			$net_win = Format::currency($net_win);
		}

		$this->match->net_win	= $net_win;
		$this->match->total_bet = ($this->match->total_bet ? Format::currency($this->match->total_bet) : '&mdash;');
		$this->match->total_win = ($this->match->total_win ? Format::currency($this->match->total_win) : '&mdash;');

		$is_abandoned_match		= ($this->match->event_status_id == $this->match_status_abandoned_id);

		if($is_abandoned_match) {
			$this->tournament->betting_end_date	= time()-1;
			$this->match->betting_open			= false;

			$this->match->time					= date('H:i d/m/y', strtotime($this->match->start_date));
			$this->match->counter				= 'Match Abandoned';

			$this->match->start_label			= '';
		} else {

			$this->tournament->betting_end_date	= $betting_end_date;
			$this->match->betting_open			= (strtotime($betting_end_date) > time());

			$this->match->time					= date('h:i d/m/y', strtotime($this->match->start_date));
			$this->match->counter				= $this->formatCounterText(strtotime($betting_end_date));

			$this->match->start_label			= (strtotime($betting_end_date) > time()) ? 'Betting closes in: ' : '';
		}

		$this->listOffers();

		$this->result_display = array(
			'col0' => array(),
			'col1' => array(),
			'col2' => array(),
		);

		$i = 0;
		foreach($this->result_list as $result) {
			$col_num = $i % 3;
			$this->result_display['col' . $col_num][] = array(
				'market_name'	=> $result->market_name,
				'offer_name'	=> $result->selection_name,
			);
			$i++;
		}
		$result_row_number = ceil(count($this->result_list) / 3 );
		foreach($this->result_display as $col => $result_display) {
			$row_count = count($result_display);
			while($row_count < $result_row_number) {
				$this->result_display[$col][] = array(
					'market_name'	=> '-',
					'offer_name'	=> '-',
				);
				$row_count++;
			}
		}

		// page setup
		$document =& JFactory::getDocument();
		$document->setTitle(JText::_('TopBetta - Sports Tournaments'));

		// add stylesheets
		$document->addStyleSheet('components/com_tournament/assets/sportstourns.default.css');

		// add javascript file
		$document->addScript('components/com_tournament/assets/common.js');
		$document->addScript('components/com_tournament/assets/sportmatch.js');
		
		// Remove ' from tournament name
		$processed_tournament_name = str_replace("'","",$this->tournament->name);
		$processed_tournament_sport_name = str_replace("'","",$this->tournament->sport_name);
		
		$js_var =<<<EOF
			window.addEvent('domready', function() {
					gameAccordion('results');
					gameAccordion('myselections');
					
					if($('print_sports_bets')) {
						$('print_sports_bets').addEvent('click', function(e) {
							new Event(e).stop();
							printBets('{$processed_tournament_name} - {$processed_tournament_sport_name}', 'sports_bets');
						});
					}
				});
EOF;
		$document->addScriptDeclaration($js_var);

		if($this->bet_refresh) {
			$bet_refresh_js =<<<EOF
						window.addEvent('domready', function() {
							$('confirmBetsG').fireEvent('click');
						});
EOF;
			$document->addScriptDeclaration($bet_refresh_js);
		}

		$user =& JFactory::getUser();
		$this->bet_link_class = 'cnfrmBetButt';
		if($user->guest) {
			$this->bet_link		= 'index.php?option=com_tournament&task=setUserRedirect&url=' . urldecode("/user/register") . '&text=' . urlencode('You must log in or register to bet on tournaments.');
			$this->bet_link_id	= null;
			
			$this->bet_link_class .= ' guest_link';
		} else if(is_null($this->ticket)) {
			$this->bet_link		= 'tournament/sports/confirmticket/' . $this->tournament->id;
			$this->bet_link_id	= 'regoButt';
			
			if($this->password_protected_tournament) {
				$this->bet_link		= 'tournament/sports/confirmpassword/' . $this->tournament->id;
				$this->tournament->info_link  = 'private/' . $this->private_tournament_ident;
			}
			
			$unregister		= 'false';
			$register		= 'true';
			$user_logged_in	= ($user->guest) ? 'false' : 'true';
			$password_required	= ($this->password_protected_tournament) ? 'true' : 'false';
			$document->addScriptDeclaration("\tvar unregister = {$unregister}, register = {$register}, password_required = {$password_required}, user_logged_in = {$user_logged_in};");
			
			if($this->password_protected_tournament) $register_link = 'tournament/confirmpassword/'. $this->tournament->id;
			
			$document->addScript('components/com_tournament/assets/info.js');
			
		} else {
			$this->bet_link		= '#';
			$this->bet_link_id	= 'confirmBetsG';
		}
-		$document->addStyleSheet('components/com_tournament/assets/tourninfo.default.css');
		$document->addStyleSheet('components/com_tournament/assets/racetourns.playgame.css');
	}

	/**
	 * List tournament offers
	 *
	 * @param $ajax_call
	 * @return void
	 */
	public function listOffers($ajax_call=false)
	{
		foreach($this->offer_list as &$offer) {
			$odds = $offer->win_odds;
			if(!empty($offer->override_odds) && ($offer->override_odds < $odds)) {
				$odds = $offer->override_odds;
			}
			$offer->odds				= Format::odds($odds);
			$offer->updated_timestamp	= strtotime($offer->updated_date);
			$offer->win					= '-';
			if(isset($this->pending_bet_list[$offer->id]) && !empty($this->pending_bet_list[$offer->id])) {
				$offer->win	= number_format($this->pending_bet_list[$offer->id] * $offer->odds, 2);
			}
		}

		$offer_count = count($this->offer_list);
		if(isset($this->offer_market_limit[$offer_count])) {
			$bet_limit = $this->offer_market_limit[$offer_count];
		} else {
			$bet_limit = $this->offer_market_limit[9];
		}

		$this->bet_limit_text = '';
		
		if ($this->tournament->bet_limit_flag) {
			if('unlimited' == $offer_count) {
				$bet_limit = $this->tournament->start_currency;
			}
			$this->bet_limit_text = ($bet_limit ? (' (LIMIT: ' . Format::currency($bet_limit, true).')') : '');
		}

		$js_var = $this->formatTournamentObjectJavascript($this->tournament, $this->match, $this->market, $this->tb_user);

		if($ajax_call) {
			$base = new stdClass;

			$base->match_id	= (int)$this->match->id;
			$base->bet_url  = '/index.php?option=com_tournament&controller=tournamentsportevent&task=confirmbet';

			$data = json_encode($base);
			$js_var =<<<EOF
			var Tournament = new TournamentController({$data});
			Tournament.updateOfferList();
			Tournament.updateTotal();
EOF;

			$this->assign('js_var', $js_var);

			//set up check if the match is open for betting
			$betting_end_date = $this->match->start_date;

			if(!empty($this->tournament->betting_closed_date) && $this->tournament->betting_closed_date < $betting_end_date) {
				$betting_end_date = $this->tournament->betting_closed_date;
			}

			$is_abandoned_match			= ($this->match->match_status_id == $this->match_status_abandoned_id);
			$this->match->betting_open	= ($is_abandoned_match ? false : (strtotime($betting_end_date) > time()));
			
		} else {
			$document =& JFactory::getDocument();
			// add variables for javascript
			$document->addScriptDeclaration($js_var);
		}
		$this->assign('ajax_call', $ajax_call);
	}

	/**
	 * Sets up the display of the bet confirmation box upon successful validation
	 *
	 * @return void
	 */
	public function confirmBet()
	{
		JRequest::setVar('tmpl', 'component');
		$this->setLayout('confirmbet');

		$offer_model =& $this->getModel('Selection');

		$bet_rows		= array();
		$odds_updated	= false;

		foreach($this->bet_list as $offer_id => $bet_value) {
			$offer	= $offer_model->getSelectionDetails($offer_id);
			$odds	= $offer->win_odds;
			
//			if(!empty($offer->override_odds) && ($offer->override_odds < $odds)) {
//				$odds = $offer->override_odds;
//			}

			$row_odds_updated = false;
			if(isset($this->offer_updated_list[$offer->market_id][$offer->id])) {
				$row_odds_updated = true;
				$odds_updated = true;
			}

			$bet_rows[] = array(
				'offer_name'		=> $offer->name,
				'market_name'		=> $offer->market_type,
				'amount'			=> Format::currency($bet_value, true),
				'odds'				=> Format::odds($odds),
				'win'				=> Format::currency(bcmul($bet_value, $odds), true),
				'odds_text'			=> $row_odds_updated ? ' * NEW!' : '',
				'odds_class'		=> $row_odds_updated ? ' class="bettixNotes"' : '',
			);

		}

		$ticket_buyin	= (!empty($this->tournament->entry_fee) && !empty($this->tournament->buy_in)) ? Format::currency($this->tournament->buy_in, true) : 'FREE';
		$header			= $this->match->name . ' ' . $this->tournament->competition_name . ' ' . $ticket_buyin . ' - ' . $this->tournament->sport_name;

		$this->header			= $header;
		$this->bet_rows			= $bet_rows;
		$this->display_total	= Format::currency($this->bet_total, true);
		$this->odds_updated		= $odds_updated;
	}

	/**
	 * Sets up display of the bet confirmation box when there was a validation error
	 *
	 * @return void
	 */
	public function betError()
	{
		JRequest::setVar('tmpl', 'component');
		$this->setLayout('beterror');

		$tournament_name  = (is_null($this->tournament)) ? 'Unknown' : $this->tournament->name;
		$match_name        = (is_null($this->match)) ? 'Unknown' : $this->match->name;

		$this->assign('tournament_name', $tournament_name);
		$this->assign('match_name', $match_name);
	}

	/**
	 * Generates the script output needed in the head of the gameplay page
	 *
	 * @param object  $tournament
	 * @param object  $race
	 * @param array   $bet_type_list
	 * @return string
	 */
	private function formatTournamentObjectJavascript($tournament, $match, $market, $tb_user='no')
	{
		$base = new stdClass;

		$base->id		= (int)$tournament->id;
		$base->match_id	= (int)$match->id;

		$base->time     = isset($this->tournament->betting_end_date) ? (strtotime($this->tournament->betting_end_date) - time()) : null;
		$base->bet_url  = '/index.php?option=com_tournament&controller=tournamentsportevent&task=confirmbet';
		
		//check the user account status
		$user =& JFactory::getUser();
		if(!$tb_user && $tournament -> buy_in > 0) $basic_user = 'yes';
		else $basic_user = 'no';

		$data = json_encode($base);
		$script =
<<<EOT
		window.addEvent('domready', function() {
			// the rainmaker
			var Tournament = new TournamentController({$data});

			// setup counter
			if(Tournament.options.time > 0) {
				// start the counter
				var Timer = new TimerController({
					'time': Tournament.options.time,
					'timeout': 1000
				});

				Timer.addController(new CounterController());
				Timer.addController(new BetFormController());

				Timer.start();
				
				var basic_user = '{$basic_user}';

				// place bet button
				if($('confirmBetsG')) {
					$('confirmBetsG').addEvent('click', function(e) {
					if(basic_user == 'yes')
						{
							alert('You have a basic account. Please upgrade it to place the bet.');
							window.location = '/user/upgrade';
						}
						else 
						{
							Tournament.placeBet();
							new Event(e).stop();
						}
					});
			}
				Tournament.updateTotal();
			}

			Tournament.changeMarket();
		});
EOT;

		return $script;
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
	}, $('sports-tourns-wrap'));
});
EOD;
	}
}
