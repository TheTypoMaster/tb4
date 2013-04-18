<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('mobileactive.application.component.view');

class BettingViewBetting extends View
{
	/**
	 * Retrieves the task from the request object and dispatches to the view method
	 *
	 * @param string $display
	 * @return void
	 */
	public function display($tpl = null, $implement_task = true)
	{
		if ($implement_task) {
			$task = JRequest::getVar('task', null);
	
			switch($task) {
				case 'meeting':
					$this->meeting();
					break;
				case 'confirmbet':
					$this->confirmBet();
					break;
				case 'bettinghistory':
					$this->bettingHistory();
					break;
				case 'printbet':
					break;
				case 'showOpenTournamentsForTopMenu':
					break;
				case 'showRecentTournamentsForTopMenu':
					break;
				case 'showUnresultedBetsForTopMenu':
					break;
				case 'showRecentBetsForTopMenu':
					break;
				case 'showAllBalanceForTopMenu':
					break;
				case 'list':
				default:
					$this->listView();
					break;
			}
		}
		parent::display($tpl);
	}

	/**
	 * Set up the display of the tournament list page
	 *
	 * @return void
	 */
	public function listView()
	{
		$this->setLayout('list');
		
		$meeting_race_list	= array(
			'galloping' 	=> array(),
			'greyhounds'	=> array(),
			'harness'		=> array(),
		);
		$meeting_list		= $this->meeting_list;
		
		$race_status_model	=& $this->getModel('EventStatus');
		$result_model		=& $this->getModel('SelectionResult');
		$runner_model		=& $this->getModel('Runner');
		
		$abandoned_status	= $race_status_model->getEventStatusByKeyword('abandoned');
		$selling_status		= $race_status_model->getEventStatusByKeyword('selling');
		
		$meeting_race_limit = array(
			'galloping' 	=> 10,
			'harness'		=> 10,
			'greyhounds'	=> 12,
		);
		
		if (!empty($meeting_list)) {
			foreach ($meeting_list as $meeting) {
				$race_list			= array();
				$next_race_marked	= false;
				if (!empty($meeting->race_list)) {
					foreach ($meeting->race_list as $race) {
						
						$start_time	= strtotime($race->start_date);
						$abandoned	= ($race->event_status_id == $abandoned_status->id);
						
						$tips_title = 'Race No. ' . $race->number . ' &ndash; ' . date('g:ia', $start_time);
						$tips_body	= $race->name;
						
						if ($abandoned) {
							$label	= '--';
							$class	= ($start_time > time() ? 'raceFuture' : 'racePast');
						} else if ($start_time > time()) {
							$label				= Format::counterText($start_time);
							//Format time 
							$label	  = str_replace(' hr', 'h', $label);
							$label	  = str_replace(' min', 'm', $label); 
							$label	  = str_replace(' sec', 's', $label); 							
							$class				= ($next_race_marked ? 'raceFuture' : 'racePresent');
							$next_race_marked	= true;
						} else if ($race->event_status_id != $selling_status->id ) {
							
							$label = 'pending';
							$class	= 'racePast';
							
							$result_list = $result_model->getSelectionResultListByEventID($race->id);
							
							if (!empty($result_list)) {
								$runner_list		= $runner_model->getRunnerListByRaceID($race->id);
								$runner_list_by_id	= array();
								foreach($runner_list as $runner) {
									$runner_list_by_id[$runner->id]	= $runner;
								}
								$result_display_list = $this->_getResultDisplayList($result_list, $runner_list_by_id, $race);
								
								$rank_display = array();
								foreach ($result_display_list['rank'] as $result) {
									if ($result['rank_no'] < 4) {
										$rank_display[$result['rank_no']][] = $result['number'];
									}
								}
								ksort($rank_display);
								
								$j = 0;
								foreach ($rank_display as $rank => $numbers) {
									$rank_count = count($numbers);
									
									if ($rank_count > 1) {
										$rank_display[$rank] = '(' . implode(', ', $numbers) . ')';
									} else {
										$rank_display[$rank] = $numbers[0];
									}
									$j += count($numbers);
									
									if ($j > 3) {
										break;
									}
								} 
								
								$label	= implode(', ', $rank_display);
								$class	= 'racePast';
								
								$tips_body .= $this->_formatResultTipsBody($result_display_list);
								
								//$tips_body .= '<table class="bet_link_tips-result"><tr><td>col1</td><td>col2</td></tr></table>';
								
							}
						} else {
							$label = 'pending';
							$class	= 'racePast';
						}
						
						$race_list[$race->number] = array(
							'link'			=> '/betting/racing/meeting/' . $meeting->id . '/' . $race->number,
							'label'			=> $label,
							'class'			=> $class,
							'tips_title'	=> $tips_title,
							'tips_body'		=> $tips_body,
						);
					}
				}
				$competition_name = strtolower($meeting->competition_name);
				$meeting_race_list[$competition_name][$meeting->id] = array(
					'meeting_name'	=> $meeting->name . ' (' . $meeting->state . ')',
					'race_list'		=> $race_list
				);
				
				$race_count = count($race_list);
				if ($race_count > $meeting_race_limit[$competition_name]) {
					$meeting_race_limit[$competition_name] = $race_count;
				}
			}
		}
		
		$this->assign('header', 'TODAY\'S RACING &ndash; ' . strtoupper(date('l jS F Y')));
		$this->assign('meeting_race_list', $meeting_race_list);
		$this->assign('meeting_race_limit', $meeting_race_limit);
		
		$document =& JFactory::getDocument();
		
		$document->addStyleSheet('components/com_betting/assets/bettinglist.css');
		
		$accordion_js = $this->formatAccordionJavascript($this->open_id);
		$document->addScriptDeclaration($accordion_js);
		
		$hit_js = <<<EOF
			window.addEvent('domready', function() {
							var linktips = new Tips($$('a.bet_link'), {
								className: 'bet_link_tips'
							});
			});
EOF;

		$document->addScriptDeclaration($hit_js);

		jimport( 'joomla.application.module.helper' );
		$next_to_jump_module = JModuleHelper::getModule('nexttojump');
		$this->assign('next_to_jump', JModuleHelper::renderModule($next_to_jump_module));
		
		$config =& JFactory::getConfig();
		$this->assign('time_zone', $config->getValue('config.time_zone'));
	}

	/**
	 * Set up the display of meeting (game play) page
	 *
	 * @return void
	 */
	public function meeting($document=null)
	{
		if ($this->meeting->state != '') {
			$this->meeting->title	= $this->meeting->name . ' (' . $this->meeting->state . ') ' . '&mdash; ' . $this->meeting->type . ' &mdash; RACE BETTING';
		} else {
			$this->meeting->title	= $this->meeting->name . '&mdash; ' . $this->meeting->type . ' &mdash; RACE BETTING';
		}
		$this->meeting->icon	= $this->getRacingIcon($this->meeting->type);
		
		$this->race->weather	= (empty($this->race->weather) ? 'N/A' : $this->race->weather);
		$this->race->track_condition	= (empty($this->race->track_condition) ? 'N/A' : $this->race->track_condition);
		
		$this->meeting->started	= strtotime($this->meeting->start_date) < time();
		
		$this->meeting->associate_label = $this->getAssociateLabel($this->competition->name);
		
		$this->race->betting_open	= (strtolower($this->race->status) == 'selling');
		$this->race->title			= (empty($this->race->name) ? ((empty($this->race->number)) ? '' : 'Race No.' . $this->race->number) : (FORMAT::cutString($this->race->name, 50, '...') . ' &mdash; ' . $this->race->distance . 'm'));
		$this->race->paid_status 	= ($this->race->betting_open) ? 'Approx. Win' : 'Paid';
		
		$this->race->time         = date('H:i d/m/y', strtotime($this->race->start_date));
		$this->race->counter      = Format::counterText(strtotime($this->race->start_date));
		
		$this->race->start_label  = (strtotime($this->race->start_date) > time()) ? 'Race starts in: ' : '';
		
		$display_result_list = array();
		if ($this->result_list) {
			$display_result_list = $this->_getResultDisplayList($this->result_list, $this->runner_list_by_id);
		}
		$this->assign('display_result_list', $display_result_list);
		$this->assign('dividend_label', isset($display_result_list['dividend_field']) ? ucwords($display_result_list['dividend_field']) : '');
		
		$total_bet_amount	= 0;
		$total_paid_amount	= null;
		$display_bet_list	= $this->_getDisplayBetDetailsList($total_bet_amount, $total_paid_amount);
		
		$this->assign('display_bet_list', $display_bet_list);
		
		$this->race->total_bet_amount	= Format::Currency($total_bet_amount);
		$this->race->total_paid_amount	= is_null($total_paid_amount) ? '&mdash;' : Format::Currency($total_paid_amount);
		$this->race->total_win			= is_null($total_paid_amount) ? '&mdash;' : Format::Currency(bcsub($total_paid_amount, $total_bet_amount));
		$this->race->total_win_class	= $this->race->total_win < 0 ? 'notpaid' : 'paid';
		
		$event_list = array();
		foreach ($this->race_time_list as $number => $time) {
			$suffix = ($time->time < time()) ? 'G' : '';
			$event_list[$number] = array(
						'time'          => Format::counterText($time->time),
						'class_suffix'  => $suffix,
						'class'         => ($number == $this->race->number) ? ' active' . $suffix : ''
					);
		}
		$this->meeting->event_list = $event_list;
		
		$max_rating = 0;
		foreach($this->runner_list as $runner) {
			if($runner->rating > $max_rating) {
				$max_rating = $runner->rating;
			}
		}
		
		foreach ($this->runner_list as $runner) {
			$runner->enabled      = (strtolower($runner->status) == 'not scratched' && $runner->name != '....');
			$runner->class        = ($runner->enabled) ? '' : ' scratched';

			$runner->win_odds     = ($runner->enabled) ? Format::odds($runner->win_odds) : 'scr';
			$runner->place_odds   = ($runner->enabled) ? Format::odds($runner->place_odds) : 'scr';

			$runner->rating_width = $this->calculateRatingWidth($max_rating, $runner->rating);
		}
		
		$bet_type_list = array();
		foreach ($this->bet_type_list as $bet_type) {
			$bet_type_list[$bet_type->id] = $bet_type->name;
		}

		$bet_type_list = array();
		foreach ($this->bet_type_list as $bet_type) {
			$bet_type_list[$bet_type->id] = $bet_type->name;
		}
		
		if(!$this->race->betting_open && strtolower($this->race->status) == 'selling') {
			$this->race->status = 'pending';
		}
		
		$this->assign('event_list', $event_list);
		
		$user =& JFactory::getUser();
		$this->bet_link_class = 'cnfrmBetButt';
		
		if ($user->guest) {
			$this->bet_link		= 'index.php?option=com_tournament&task=setUserRedirect&url=' . urldecode("/user/register") . '&text=' . urlencode('You must log in or register to make a bet.');
			$this->bet_link_id	= null;
			
			$this->bet_link_class .= ' guest_link';
		} else {
			$this->bet_link		= '#';
			$this->bet_link_id	= 'confirmBetsG';
		}
		
		if (is_null($document)) {
			$document =& JFactory::getDocument();
		}
		
		$document->addStyleSheet('components/com_tournament/assets/racebetting.common.css');
		$document->addStyleSheet('components/com_tournament/assets/racetourns.playgame.css');
		
		$document->addScript('components/com_betting/assets/meeting.js');
		$document->addScript('components/com_tournament/assets/common.js');

		// add variables for javascript
		$js_var = $this->formatMeetingObjectJavascript($this->meeting, $this->race, $bet_type_list, $this->tb_user);
		$document->addScriptDeclaration($js_var);
		
		jimport( 'joomla.application.module.helper' );
		$next_to_jump_module = JModuleHelper::getModule('nexttojump');
		$this->assign('next_to_jump', JModuleHelper::renderModule($next_to_jump_module));
		
		$config =& JFactory::getConfig();
		$this->assign('time_zone', $config->getValue('config.time_zone'));
	}
	
	/**
	 * Generates the script output needed in the head of the gameplay page
	 *
	 * @param object  $tournament
	 * @param object  $race
	 * @param array   $bet_type_list
	 * @return string
	 */
	private function formatMeetingObjectJavascript($meeting, $race, $bet_type_list, $tb_user='no')
	{
		$base = new stdClass;

		$base->id			= $meeting->id;
		$base->race_id		= $race->id;
		$base->race_number	= $race->number;
		$base->race_status	= strtolower($race->status);

		$base->time			= strtotime($this->race->start_date) - time();
		$base->base_url 	= '/index.php?option=com_betting';

		$base->bet_type_list  = $bet_type_list;
		$base->bet_type       = current(array_keys($bet_type_list));
		
		//check the user account status
		$user =& JFactory::getUser();
		if(!$tb_user) $basic_user = 'yes';
		else $basic_user = 'no';

		$data = json_encode($base);
		$script =
<<<EOT
		window.addEvent('domready', function() {
			gameAccordion('myselections');
			gameAccordion('results');
		
			// the rainmaker
			var Meeting = new MeetingController({$data});

			// setup counter
			if(Meeting.options.race_status == 'selling') {
				// start the counter
				var Timer = new TimerController({
					'time': Meeting.options.time,
					'timeout': 1000
				});

				Timer.addController(new CounterController());
				Timer.addController(new BetFormController());

				Timer.start();
				
				var basic_user = '{$basic_user}';

				// place bet button
				if($('confirmBetsG')){
					$('confirmBetsG').addEvent('click', function(e) {
						if(basic_user == 'yes')
						{
							alert('You have a basic account. Please upgrade it to place the bet.');
							window.location = '/user/upgrade';
						}
						else 
						{
						Meeting.placeBet();
						new Event(e).stop();
						}
					});
				}
				window.addEvent('domready', function() {
					for(var index in Meeting.options.bet_type_list) {
						// reset bet selection buttons
						Meeting.clearAllButtonStyle();
					}
					Meeting.setButtonStyle();
					Meeting.updateCheckBoxes();
				});

				for(var index in Meeting.options.bet_type_list) {
					var button_id = Meeting.getButtonID(index);
					if($(button_id)) {
						$(button_id).bet_type = index;
	
						$(button_id).addEvent('click', function(e) {
							Meeting.clearAllButtonStyle();
							Meeting.setButtonStyle(this.bet_type);
							Meeting.options.bet_type = this.bet_type;
							
							Meeting.updateCheckBoxes(this.bet_type);
						});
					}
				}

				$('refreshButtID').addEvent('click', function(e) {
					Meeting.refresh(Meeting.options.bet_type);
				});
				
				// select field checkbox
				$$('.selectA').addEvent('click', function(e) {
					Meeting.selectField(this);
				});
			} else {
				$$('.fourthP').each(function(el) {
					el.setProperty('disabled', 'disabled');
				});
			}
			
			if($('print_racing_bets')) {
				$('print_racing_bets').addEvent('click', function(e) {
					new Event(e).stop();
					printBets('{$this->meeting->name} - {$this->meeting->type}', 'racing_bets');
				});
			}
		});
		

EOT;

		return $script;
	}
	
	/**
	 * Get the result list for display
	 *
	 * @param $result_list
	 * @param $runner_list
	 * @return void
	 */
	protected function _getResultDisplayList($result_list, $runner_list, $race = null)
	{
		if (is_null($race)) {
			$race = $this->race;
		}
		
		$display_result_list = array(
			'dividend_field'	=> 'odds', // for old data before dividends fields introduced
			'has_exotics'		=> false,
			'rank'				=> array(),
			'exotic'			=> array(
				'quinella'		=> array(),
				'exacta'		=> array(),
				'trifecta'		=> array(),
				'firstfour'		=> array(),
			),
		);
		foreach ($result_list as $result) {
			$runner			= $runner_list[$result->selection_id];
			$runner_number	= $runner->number;
			$win_odds		= null;
			$place_odds		= null;
			$win_dividend	= null;
			$place_dividend	= null;
					
			if ($result->position < 4 ) {
				$place_odds		= $runner->place_odds;
				$place_dividend = $result->place_dividend;
			}
			
			if (1 == $result->position) {
				$win_odds		= $runner->win_odds;
				$win_dividend	= $result->win_dividend;
				
				if($win_dividend > 0) {
					$display_result_list['dividend_field'] = 'dividend';
				}
			}
			
			$display_result_list['rank'][] = array(
				'rank_no'			=> $result->position,
				'position'			=> Format::ordinalNumber($result->position),
				'number'			=> $runner->number,
				'name'				=> $runner->name,
				'win_odds'			=> $win_odds,
				'place_odds'		=> $place_odds,
				'win_dividend'		=> $win_dividend,
				'place_dividend'	=> $place_dividend
			);
		}
		
		$wagering_bet	= WageringBet::newBet();
		foreach ($display_result_list['exotic'] as $exotic_type => $exotic_list) {
			$dividends = unserialize($race->{$exotic_type . '_dividend'});
			$display_result_list['exotic'][$exotic_type] = $dividends;
			
			if ($dividends > 0) {
				$display_result_list['has_exotics'] = true;
			}
		}
		return $display_result_list;
	}
	
	/**
	 * Get race result html used in tips
	 *
	 * @param $display_result_list
	 * @return void
	 */
	private function _formatResultTipsBody($display_result_list)
	{
		
		$result_tips = '<table class="bet_link_tips-result"><tr><td class="bet_link_tips-result-left">';
		$result_tips .= '<table>';
		foreach ($display_result_list['rank'] as $result) {

			$result_tips .= '<tr>';
			$result_tips .= '<td>' . $result['number'] . '- ';
			if ($result['win_dividend']) {
				$result_tips .= $result['win_dividend'] . ',&nbsp;&nbsp;';
			}
			$result_tips .= '</td>';
			
			$result_tips .= '<td>';
			$result_tips .= $result['place_dividend'];
			$result_tips .= '</td>';
			$result_tips .= '</tr>';
		}
		$result_tips .= '</table>';
		$result_tips .= '</td><td>';
		foreach ($display_result_list['exotic'] as $type => $exotic_result) {
			if (!empty($exotic_result) && is_array($exotic_result)) {
				foreach ($exotic_result as $combos => $dividend) {
					$result_tips .= $combos;
					$result_tips .= '&nbsp;&nbsp;' . $dividend;
					$result_tips .= '<br />';
				}
			}
		}
		$result_tips .= '</td></table>';
		
		return $result_tips;
	}
	
	/**
	 * Sets up the display of the bet confirmation box
	 *
	 * @return void
	 */
	public function confirmBet()
	{
		JRequest::setVar('tmpl', 'component');
		$this->setLayout('confirmbet');
		
		$this->display_total = Format::currency($this->bet_total);

		$display_bet_list = $this->_getDisplayBetList();
		
		$this->assign('display_bet_list', $display_bet_list);
	}
	
	/**
	 * Get the display bet list
	 *
	 * @return array
	 */
	protected function _getDisplayBetList()
	{
		$display_bet_list = array();
		
		$bet_list = $this->wagering_bet_list;
		
		foreach ($this->wagering_bet_list as $k => $bet) {
			$flexi				= null;
			$combination_count	= 1;
			
			if ($bet->isCombinationBetType()) {
				$combination_count = $bet->getCombinationCount();
			}
			
			if ($bet->isFlexiBetType() && $bet->isFlexiBet()) {
				$flexi = Format::percentage($bet->getFlexiPercentage());
			}
			
			$selection			= $bet->displayBetSelections();
			$selection_display	= $selection;
			if ($bet->isStandardBetType()) {
				$selection_display =  $selection . '. ' . $this->runner_list_by_number[$selection]->name;
			} else if ($combination_count > 1) {
				$selection_display = $selection . ' - ' . $combination_count . ' combos';
			}
			$display_bet_list[$k] = array(
				'selection'		=> $selection_display,
				'bet_type'		=> $bet->getBetTypeDisplayName(),
				'bet_amount'	=> Format::currency($bet->getBetAmount()),
				'total_amount'	=> Format::currency($bet->getTotalBetAmount()),
				'flexi'			=> $flexi,
				'runner_number'		=> $selection,
			);
		}
		
		return $display_bet_list;
	}
	
	/**
	 * Get the display bet list
	 *
	 * @return array
	 */
	protected function _getDisplayBetDetailsList(&$total_bet_amount, &$total_paid_amount)
	{
		$display_bet_list = array();
		$tmp_display_list = $this->_getDisplayBetList();
		
		$i = 0;
		//$total_bet_amount	= 0;
		//$total_paid_amount	= null;
		foreach ($tmp_display_list as $bet_id => $display_bet) {
			$display_bet_list[$i] = $display_bet;
			
			$bet			= $this->bet_list[$bet_id];
			$wagering_bet	= $this->wagering_bet_list[$bet_id];
		
			
			$display_bet_list[$i]['id']				= $bet_id;
			$display_bet_list[$i]['odds']			= '&mdash;';
			$display_bet_list[$i]['paid_amount']	= 'N/A';
			$display_bet_list[$i]['result']			= 'CONFIRMED';
			$display_bet_list[$i]['row_class']		= '';
			$display_bet_list[$i]['class']			= 'paid';
			$display_bet_list[$i]['odds_tooltip']	= (empty($bet->resulted_flag)) ? 'Current approximate odds' : 'Final payout dividend';
			$display_bet_list[$i]['result_tooltip'] = $bet->external_bet_error_message ;
			
			$selection	= $wagering_bet->displayBetSelections();
			if ($wagering_bet->isStandardBetType()) {
				if ($bet->bet_result_status == 'unresulted') {
					$win_odds	= $this->runner_list_by_number[$selection]->win_odds;
					$place_odds	= $this->runner_list_by_number[$selection]->place_odds;
				} else {
					$win_odds	= $this->runner_list_by_number[$selection]->win_dividend;
					$place_odds	= $this->runner_list_by_number[$selection]->place_dividend;
				}
			}
			
			switch ($bet->bet_type) {
				case WageringBet::BET_TYPE_WIN:
					$display_bet_list[$i]['odds'] = Format::odds($win_odds);
					break;
				case WageringBet::BET_TYPE_PLACE:
					$display_bet_list[$i]['odds'] = Format::odds($place_odds);
					break;
				case WageringBet::BET_TYPE_EACHWAY:
					$display_bet_list[$i]['odds']  = Format::odds($win_odds);
					$display_bet_list[$i]['odds'] .= '/';
					$display_bet_list[$i]['odds'] .= Format::odds($place_odds);
					break;
			}
			
			$bet_amount	= $wagering_bet->getTotalBetAmount();
			
			if ($bet->bet_result_status == 'partially-refunded') {
				$display_bet_list[$i]['result']			= 'PARTIAL REFUND';
				
				$paid_text = array();
				if (intval($bet->win_amount) > 0) {
					$paid_text[] = 'Win: ' . Format::Currency($bet->win_amount);
				}
				
				if (intval($bet->refund_amount) > 0) {
					$paid_text[] = 'Refund: ' . Format::Currency($bet->refund_amount);
				}
				
				if (!empty($paid_text)) {
					$display_bet_list[$i]['paid_amount']	= implode(' <br />', $paid_text);
				} else {
					$display_bet_list[$i]['paid_amount']	= 'NIL';
				}
				
				$display_bet_list[$i]['class']			= (intval($bet->win_amount) == 0 && intval($bet->refund_amount) == 0) ? 'notpaid' :  'paid';
				
				$total_paid_amount = bcadd($total_paid_amount, $bet->win_amount);
				$total_paid_amount = bcadd($total_paid_amount, $bet->refund_amount);
			} else if ($bet->bet_result_status == 'fully-refunded' || $bet->refunded_flag == 1) {
				$bet_amount = 0;
				
				$display_bet_list[$i]['odds']			= '&mdash;';
				$display_bet_list[$i]['result']			= 'REFUNDED';
				$display_bet_list[$i]['paid_amount']	= '&mdash;';
				$display_bet_list[$i]['row_class']		= ' scratched';
			} else if ($bet->resulted_flag) {
				$display_bet_list[$i]['result']			= intval($bet->win_amount) == 0 ? 'LOSS' : 'WIN';
				$display_bet_list[$i]['paid_amount']	= intval($bet->win_amount) == 0 ? 'NIL' : Format::Currency($bet->win_amount);
				$display_bet_list[$i]['class']			= intval($bet->win_amount) == 0 ? 'notpaid' :  'paid';
				
				$total_paid_amount = bcadd($total_paid_amount, $bet->win_amount);
				
			} 
			else if($bet->bet_result_status == 'pending')
			{
				$display_bet_list[$i]['result']			= 'PENDING';
			} 
			else {
				switch ($bet->bet_type) {
					case WageringBet::BET_TYPE_WIN:
						$display_bet_list[$i]['paid_amount'] = Format::Currency(bcmul($win_odds, $bet->bet_amount));
						break;
					case WageringBet::BET_TYPE_PLACE:
						$display_bet_list[$i]['paid_amount'] = Format::Currency(bcmul($place_odds, $bet->bet_amount));
						break;
				}
			}
			$total_bet_amount = bcadd($total_bet_amount, $bet_amount);
			$i++;
		}
		
		return $display_bet_list;
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
	});
	$$('.todaysRacesRow').each(function(el) {
		el.setStyle('display', 'block');
	});
});

EOD;
	}
	
	/**
	 * Get the sport CSS class for display on the tournament game page
	 *
	 * @param string $name
	 * @return string
	 */
	public function getRacingIcon($name)
	{
		switch (strtolower($name)) {
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
	
	/**
	 * Set up the display of betting history page
	 *
	 * @return void
	 */
	public function bettingHistory()
	{
		$bet_display_list = array();
		
		$bet_selection_model	=& $this->getModel('BetSelection');
		$selection_result_model	=& $this->getModel('SelectionResult');
		$meeting_model			=& $this->getModel('Meeting');
		
		$wagering_bet = WageringBet::newBet();
		
		$i = 1;
		foreach ($this->bet_list as $bet) {
			$label		= BettingHelper::getBetTicketDisplay($bet->id);
			$meeting	= $meeting_model->getMeetingByRaceID($bet->event_id);
			
			$bet_display_list[$bet->id] = array(
				'link'			=> '/betting/racing/meeting/' . $meeting->id . '/' . $bet->event_number,
				'row_class'		=> $i % 2 == 0 ? 'odds' : 'even',
				'bet_time'		=> $bet->created_date,
				'label'			=> $label,
				'bet_type'		=> $wagering_bet->getBetTypeDisplayName($bet->bet_type),
				'amount'		=> FORMAT::currency($bet->bet_amount),
				'bet_total'		=> FORMAT::currency(abs($bet->bet_total)),
				'bet_freebet_amount'		=> FORMAT::currency(abs($bet->bet_freebet_amount)),
				'dividend'		=> '&mdash;',
				'paid'			=> '&mdash;',
				'result'		=> 'CONFIRMED',
				'half_refund'	=> false
			);
			 
			if ($bet->refunded_flag && !$bet->win_amount) {
				$bet_display_list[$bet->id]['result']	= 'REFUNDED';
				if ($bet->refund_amount > 0) {
					$bet_display_list[$bet->id]['paid']	= Format::currency($bet->refund_amount);  
				}
				
			}
			else if($bet->bet_result_status == 'pending')
			{
				$bet_display_list[$bet->id]['result']			= 'PENDING';
			}
			else if ($bet->resulted_flag && empty($bet->win_amount)) {
				$bet_display_list[$bet->id]['result']	= 'LOSS';
				$bet_display_list[$bet->id]['paid']		= 'NIL';
			} else if ($bet->resulted_flag) {
				$bet_display_list[$bet->id]['result']	= 'WIN';
				$bet_display_list[$bet->id]['paid']		= Format::currency($bet->win_amount);
				
				if ($wagering_bet->isStandardBetType($bet->bet_type)) {
					$selection_result	= $selection_result_model->getSelectionResultBySelectionID($bet->selection_id);
					$win_dividend		= $selection_result->win_dividend;
					$place_dividend		= $selection_result->place_dividend;
					
					switch ($bet->bet_type) {
						case WageringBet::BET_TYPE_WIN:
							$bet_display_list[$bet->id]['dividend'] = Format::odds($win_dividend);
							break;
						case WageringBet::BET_TYPE_PLACE:
							$bet_display_list[$bet->id]['dividend'] = Format::odds($place_dividend);
							break;
						case WageringBet::BET_TYPE_EACHWAY:
							$bet_display_list[$bet->id]['dividend']  = Format::odds($win_dividend);
							$bet_display_list[$bet->id]['dividend'] .= '/';
							$bet_display_list[$bet->id]['dividend'] .= Format::odds($place_dividend);
							break;
					}
				} else {
					$bet_dividends = unserialize($bet->{$bet->bet_type . '_dividend'});
					
					$bet_display_list[$bet->id]['dividend'] = '&mdash;';
					$dividends_count = count($bet_dividends);
					
					if ($dividends_count == 1) {
						$bet_display_list[$bet->id]['dividend'] = Format::odds(array_shift($bet_dividends));
					} else if ($dividends_count > 1) {
						$bet_display_list[$bet->id]['dividend'] = array();
						foreach ($bet_dividends as $combination => $bet_dividend) {
							$bet_display_list[$bet->id]['dividend'][] = $combination . ': ' . Format::odds($bet_dividend); 
						}
						$bet_display_list[$bet->id]['dividend'] = implode('<br />', $bet_display_list[$bet->id]['dividend']);
					}
				}
				
				if ($bet->refunded_flag) {
					$scrached_list = $bet_selection_model->getBetSelectionListByBetIDAndSelectionStatus($bet->id, 'scratched');
					$scrached_display = array();
					foreach ($scrached_list as $scrached) {
						$scrached_display[] = $scrached->number . '. ' . $scrached->name;
					}
					
					$bet_display_list[$bet->id]['half_refund'] = array(
						'label'		=> implode(', ', $scrached_display),
						'bet_type'	=> $wagering_bet->getBetTypeDisplayName($bet->bet_type),
						'amount'	=> '&mdash;',
						'bet_total'	=> '&mdash;',
						'dividend'	=> '&mdash;',
						'paid'		=> Format::currency($bet->refund_amount),
						'result'	=> 'REFUND'
					);
				}
			}
			$i++;
		}
		
		$nav_list		= array('all', 'unresulted', 'winning', 'losing', 'refunded');
		$current_nav	= in_array($this->result_type, $nav_list) ? $this->result_type : 'all';
		
		$this->assign('bet_display_list', $bet_display_list);
		$this->assign('nav_list', $nav_list);
		$this->assign('current_nav', $current_nav);
		$this->assign('current_date', date('d / m / Y'));
		
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TopBetta - Betting History') );
		//Add stylesheets to the document
		$document->addStyleSheet('components/com_payment/assets/accounttransaction.default.css');

		$document->addScript('/media/system/js/datepicker.js' );
			
		$js = "window.addEvent('domready', function(){
			$$('input.DatePicker').each( function(el){
			new DatePicker(el);
			});
		}); ";
		$document->addScriptDeclaration($js);
		
		$css = '/media/system/css/datepicker.css';
		$document->addStyleSheet($css);

		$this->setLayout('history');
	}
	
	/**
	 * Use the scaled averages of the ratings and total bet amounts to generate a rating width
	 *
	 * @param integer $max_rating
	 * @param integer $rating
	 * @return integer
	 */
	private function calculateRatingWidth($max_rating, $rating) {
		$scaled = 0;

		if($max_rating > 0) {
			$scaled += floor(($rating / $max_rating) * 100);
		}

		return $scaled;
	}
}
