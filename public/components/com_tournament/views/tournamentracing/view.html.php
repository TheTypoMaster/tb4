<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );
require_once JPATH_COMPONENT . DS . 'views' . DS . 'tournament' . DS . 'view.html.php';

class TournamentViewTournamentRacing extends TournamentViewTournament
{
	/**
	 * Retrieves the task from the request object and dispatches to the view method
	 *
	 * @param string $display
	 * @return void
	 */
	public function display($tpl = null) {
		$task = JRequest::getVar('task', null);

		switch($task) {
			case 'game':
				$this->game();
				break;
			case 'confirmbet':
				$this->confirmBet();
				break;
			case 'beterror':
				$this->betError();
				break;
			default:
				$this->home();
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
		$document =& JFactory::getDocument();
		$this->meeting($document);
		
		$price = (empty($this->tournament->buy_in)) ? 'FREE' : Format::currency($this->tournament->buy_in, true);
		$this->tournament->title      = $this->tournament->name . ' &mdash; ' . $this->tournament->sport_name . ' &mdash; ' . $price;

		$this->tournament->icon       = $this->getRacingIcon($this->tournament->sport_name);
		$this->tournament->info_link  = 'tournament/details/' . $this->tournament->id;

		$this->tournament->weather  = (empty($this->tournament->weather)) ? 'N/A' : $this->tournament->weather;
		$this->tournament->track    = (empty($this->tournament->track)) ? 'N/A' : $this->tournament->track;

		$this->tournament->available_currency   = Format::currency($this->tournament->available_currency);
		$this->tournament->turnover_currency    = Format::currency($this->tournament->turnover_currency);

		$this->tournament->started  = (strtotime($this->tournament->start_date) < time() && empty($this->tournament->cancelled_flag));
		$this->tournament->ended    = (strtotime($this->tournament->end_date) < time() || !empty($this->tournament->cancelled_flag));

		$this->tournament->associate_label	= $this->getAssociateLabel($this->tournament->sport_name);
		$this->race->tournament_betting_open = (strtotime($this->race->start_date) > time());


		$this->race->total_tournament_bet = 0;
		$this->race->total_tournament_win = 0;
		
		foreach ($this->tournament_bet_list as $bet) {
			$bet->display_bet = Format::currency($bet->bet_amount);

			if(empty($bet->resulted_flag) || (empty($bet->win_dividend) && empty($bet->place_dividend))) {
				$bet->display_dividend = ($bet->bet_type == 'win') ? $bet->win_odds : $bet->place_odds;
				$bet->display_dividend = Format::odds($bet->display_dividend);
			} else {
				if($bet->win_amount > 0 && $bet->bet_status != 'fully-refunded') {
				$bet->display_dividend = ($bet->bet_type == 'win') ? $bet->win_dividend : $bet->place_dividend;
					$bet->display_dividend = Format::odds($bet->display_dividend);
				} else {
					$bet->display_dividend = '&mdash;';
				}
			}

			if($bet->bet_status == 'fully-refunded') {
				$bet->paid        = '&mdash;';
				$bet->result      = 'REFUNDED';

				$bet->row_class   = ' scratched';
				$bet->class       = 'paid';
			} else {
				$bet->row_class     = '';
				$bet->odds_tooltip  = (empty($bet->resulted_flag)) ? 'Current approximate odds' : 'Final payout dividend';
				
				$bet->paid_amount   = (empty($bet->resulted_flag)) ? bcmul($bet->display_dividend, $bet->bet_amount) : $bet->win_amount;
				// formatting for wins and approximate wins
				$bet->paid    = ($bet->paid_amount > 0) ? Format::currency($bet->paid_amount) : '&mdash;';
				$bet->class   = 'paid';
				$bet->result  = (empty($bet->resulted_flag)) ? '&mdash;' : 'WIN';

				// formatting for losing bets
				if(intval($bet->paid_amount) == 0 && !empty($bet->resulted_flag)) {
					$bet->class   = 'notpaid';
					$bet->paid    = 'NIL';
					$bet->result  = 'LOSS';
				}
			}

			$this->race->total_tournament_bet = bcadd($this->race->total_tournament_bet, ($bet->bet_status == 'fully-refunded') ? 0 : $bet->bet_amount);
			$this->race->total_tournament_win = bcadd($this->race->total_tournament_win, ($bet->bet_status == 'fully-refunded') ? 0 : $bet->paid_amount);
		}
		
		$net_win = '&mdash;';
		$this->race->tournament_net_win_class = 'paid';
		
		if ($this->race->paid_flag) {
			$net_win = bcsub($this->race->total_tournament_win, $this->race->total_tournament_bet);
			$this->race->tournament_net_win_class = ($net_win < 0 ? 'notpaid' : 'paid');
			
			$net_win = Format::currency($net_win);
		}
		
		$this->race->tournament_net_win		= $net_win;
		$this->race->total_tournament_bet	= ($this->race->total_tournament_bet ? Format::currency($this->race->total_tournament_bet) : '&mdash;');
		$this->race->total_tournament_win	= ($this->race->total_tournament_win ? Format::currency($this->race->total_tournament_win) : '&mdash;');
		
		// page setup
		$document->setTitle(JText::_('TopBetta - Racing Tournaments'));

		// add javascript file
		$document->addScript('components/com_tournament/assets/game.js');
		// add variables for javascript
		$js_var = $this->formatTournamentObjectJavascript($this->tournament, $this->race, $this->tb_user);
		$document->addScriptDeclaration($js_var);

		$user =& JFactory::getUser();
		if ($user->guest) {
			$this->bet_tournament_link		= 'index.php?option=com_tournament&task=setUserRedirect&url=' . urldecode("/user/register") . '&text=' . urlencode('You must log in or register to bet on tournaments.');
			$this->bet_tournament_link_id	= null;
		} else if (is_null($this->ticket)) {
			$this->bet_tournament_link		= 'tournament/sports/confirmticket/' . $this->tournament->id;
			$this->bet_tournament_link_id	= 'regoButt';
			
			if ($this->password_protected_tournament) {
				$this->bet_tournament_link		= 'tournament/sports/confirmpassword/' . $this->tournament->id;
			}
			
			$unregister		= 'false';
			$register		= 'true';
			$user_logged_in	= ($user->guest) ? 'false' : 'true';
			$password_required	= ($this->password_protected_tournament) ? 'true' : 'false';
			$document->addScriptDeclaration("\tvar unregister = {$unregister}, register = {$register}, password_required = {$password_required}, user_logged_in = {$user_logged_in};");
			
			if ($this->password_protected_tournament) {
				$register_link = 'tournament/confirmpassword/'. $this->tournament->id;
			}
			
			$document->addScript('components/com_tournament/assets/info.js');
-			$document->addStyleSheet('components/com_tournament/assets/tourninfo.default.css');
			
		} else {
			$this->bet_tournament_link		= '#';
			$this->bet_tournament_link_id	= 'confirmTournBetsG';
		}
		
	}

	/**
	 * Use the scaled averages of the ratings and total bet amounts to generate a rating width
	 *
	 * @param integer $max_rating
	 * @param integer $max_bet
	 * @param integer $rating
	 * @param integer $total_bet
	 * @return integer
	 */
	private function calculateRatingWidth($max_rating, $rating) {
		$scaled = 0;

		if($max_rating > 0) {
			$scaled += floor(($rating / $max_rating) * 100);
		}

		return $scaled;
	}

	/**
	 * Generates the script output needed in the head of the gameplay page
	 *
	 * @param object  $tournament
	 * @param object  $race
	 * @param array   $bet_type_list
	 * @return string
	 */
	private function formatTournamentObjectJavascript($tournament, $race, $tb_user='no')
	{
		$base = new stdClass;
		
		foreach($this->bet_type_list as $bet_type) {
			$bet_type_list[$bet_type->id] = $bet_type->name;
		}

		$base->id       = (int)$tournament->id;
		$base->race_id  = (int)$race->id;

		$base->time     = strtotime($this->race->start_date) - time();
		$base->bet_url  = '/index.php?option=com_tournament&controller=tournamentracing&task=confirmbet';
		
		$base->bet_type_list	= $bet_type_list;
		$base->bet_type			= current(array_keys($bet_type_list));
		
		// Remove ' from tournament name
		$processed_tournament_name = str_replace("'","",$this->tournament->name);
		$processed_tournament_sport_name = str_replace("'","",$this->tournament->sport_name);
		
		//check the user account status
		$user =& JFactory::getUser();
		if(!$tb_user) $basic_user = 'yes';
		else $basic_user = 'no';
		
		$data = json_encode($base);
		$script =
<<<EOT
		window.addEvent('domready', function() {
			gameAccordion('mytournamentselections');
			// the rainmaker
			var Tournament = new TournamentController({$data});
			
			var basic_user = '{$basic_user}';

			// setup counter
			if(Tournament.options.time > 0) {
				// place bet button
				if($('confirmTournBetsG')){
					$('confirmTournBetsG').addEvent('click', function(e) {
						
							Tournament.placeBet();
							new Event(e).stop();
						
					});
				}
			}

			for(var index in Tournament.options.bet_type_list) {
					var button_id = Tournament.getButtonID(index);
					if($(button_id)) {
					$(button_id).bet_type = index;

					$(button_id).addEvent('click', function(e) {
						Tournament.setButtonStyle(this.bet_type);
						Tournament.options.bet_type = this.bet_type;
					});
				}
			}
			
			if($('print_racing_tournament_bets')) {
				$('print_racing_tournament_bets').addEvent('click', function(e) {
						new Event(e).stop();
						printBets('{$processed_tournament_name} - {$processed_tournament_sport_name}', 'racing_tournament_bets');
				});
			}
		});
EOT;

		return $script;
	}
	/**
	 * Sets up the display of the bet confirmation box upon successful validation
	 *
	 * @return void
	 */
	public function confirmBet() {
		JRequest::setVar('tmpl', 'component');
		$this->setLayout('confirmbet');

		$this->display_total = Format::currency($this->bet_total);
		$this->display_value = Format::currency($this->value);

		$selected = array();
		foreach ($this->runner_list as $runner) {
			$selected[] = $runner->id;
		}

		$this->selection = implode(',', $selected);
	}

	/**
	 * Sets up display of the bet confirmation box when there was a validation error
	 *
	 * @return void
	 */
	public function betError() {
		JRequest::setVar('tmpl', 'component');
		$this->setLayout('beterror');

		$tournament_name  = (is_null($this->tournament)) ? 'Unknown' : $this->tournament->name;
		$race_name        = (is_null($this->race)) ? 'Unknown' : $this->race->name;
		$race_number      = (is_null($this->race)) ? 'Unknown' : $this->race->number;

		$this->assign('tournament_name', $tournament_name);
		$this->assign('race_name', $race_name);
		$this->assign('race_number', $race_number);
	}

	/**
	 * Set up the display of the racing themed homepage
	 *
	 * @return void
	 */
	public function home() {
		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('Online race betting - TopBetta.com'));

		$this->setLayout('home');

		$document->addStyleSheet('components/com_tournament/assets/view.default.css');
		$document->addScript('components/com_tournament/assets/noobslide.js');

		$this->assign('right_col', TournamentHelper::getModule('right'));
		$this->assign('next_to_jump', TournamentHelper::getModule('nexttojump'));
		$this->assign('upcoming_tournaments', TournamentHelper::getModule('uctournaments'));

		//get banner script from tournament param
		$config =& JComponentHelper::getParams( 'com_tournament' );
		$this->assign('header_banner', $config->get('header_banner'));
		$this->assign('left_banner', $config->get('left_banner'));
		$this->assign('center_large_banner', $config->get('center_large_banner'));
		$this->assign('right_banner', $config->get('right_banner'));
		
		$banner_slider="";
		$banner_count=0;
		$banner_item="";
		if($config->get('slide_banner_1') != ""){ $banner_slider.="<span>".$config->get('slide_banner_1')."</span>";$banner_count++; $banner_item.= $banner_count.',';}
		if($config->get('slide_banner_2') != ""){ $banner_slider.="<span>".$config->get('slide_banner_2')."</span>";$banner_count++; $banner_item.= $banner_count.',';}
		if($config->get('slide_banner_3') != ""){ $banner_slider.="<span>".$config->get('slide_banner_3')."</span>";$banner_count++; $banner_item.= $banner_count.',';}
		if($config->get('slide_banner_4') != ""){ $banner_slider.="<span>".$config->get('slide_banner_4')."</span>";$banner_count++; $banner_item.= $banner_count.',';}
		if($config->get('slide_banner_5') != ""){ $banner_slider.="<span>".$config->get('slide_banner_5')."</span>";$banner_count++; $banner_item.= $banner_count.',';}

		$banner_item = substr($banner_item, 0, -1);
		
		$this->assign('banner_slider', $banner_slider);
		$this->assign('banner_count', $banner_count);
		$this->assign('banner_item', $banner_item);
		
	}

	/**
	 * Format the accordion invocation javascript to open a particular meeting group
	 *
	 * @param integer $show_id
	 * @return string
	 */
	private function formatAccordionJavascript($show_id) {
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
});
EOD;
	}

	/**
	 * Get the sport CSS class for display on the tournament list titles
	 *
	 * @param string $name
	 * @return string
	 */
	private function getSportClass($name) {
		switch(strtolower($name)) {
			case 'greyhounds':
				$class = 'dogimg';
				break;
			case 'harness':
				$class = 'trotimg';
				break;
			case 'galloping':
			default:
				$class = 'horseimg';
				break;
		}

		return $class;
	}

	/**
	 * Get the label for the associate field by sport
	 *
	 * @param string $name
	 * @return string
	 */
	private function getAssociateLabel($name) {
		switch (strtolower($name)) {
			case 'galloping':
				$label = 'Jockey';
				break;
			case 'greyhounds':
				$label = 'Trainer';
				break;
			case 'harness':
				$label = 'Driver';
				break;
		}

		return $label;
	}
}
