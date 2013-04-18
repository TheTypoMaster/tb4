<?php
/**
 * @version		$Id: racing.php  Michael Costa $
 * @package		API
 * @subpackage
 * @copyright	Copyright (C) 2012 Michael Costa. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
jimport('joomla.application.component.controller');
jimport('mobileactive.application.utilities.format');

class Api_Racing extends JController {

	function Api_Racing() {

	}

	/*
	 * MAPS TO: /com_tournament/tournamentracing.php->display
	 */
	public function getRacingTournamentsByType() {

		if ($type = RequestHelper::validate('type')) {

			$component_list = array('tournament', 'topbetta_user');
			foreach ($component_list as $component) {
				$path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
				$this -> addModelPath($path);
			}

			//$jackpot = JRequest::getVar('jackpot', false);
			$jackpot = ($type == 'jackpot') ? TRUE : FALSE;

			$sport_model = &$this -> getModel('TournamentSport', 'TournamentModel');
			$sport_name = RequestHelper::validate('competition_id');
			//$sport_name = JRequest::getVar('competition_id', null);

			$sport_id = null;
			if (!empty($sport_name)) {
				if ($sport = $sport_model -> getTournamentSportByName($sport_name)) {
					$sport_id = $sport -> id;
				}
			}
			
			$race_model4number = &$this -> getModel('Race', 'TournamentModel');

			//set up cookies for first visit, which will be used to display different banners when register
			//if ($user -> guest) {
			//setcookie("FirstVisit", 'racing', time() + 604800, '/');
			//}

			$racing_model = &$this -> getModel('TournamentRacing', 'TournamentModel');
			$tournament_model = &$this -> getModel('Tournament', 'TournamentModel');

			$list_params = array('sport_id' => $sport_id, 'jackpot' => $jackpot, 'private' => 0);
			$tournament_list = $racing_model -> getTournamentRacingActiveList($list_params);			

			$ticket_model = &$this -> getModel('TournamentTicket', 'TournamentModel');
			if (!empty($tournament_list)) {
				foreach ($tournament_list as $tournament) {
					$tournament -> entrants = $ticket_model -> countTournamentEntrants($tournament -> id);
					$tournament -> prize_pool = $tournament_model -> calculateTournamentPrizePool($tournament -> id);
					$tournament -> place_list = $tournament_model -> calculateTournamentPlacesPaid($tournament, $tournament -> entrants, $tournament -> prize_pool);

					$tournament4number = $racing_model->getTournamentRacingByTournamentID($tournament -> id);
					//TODO: is this broken - always returns 1?
					$tournament -> selected_race = $race_model4number -> getNextRaceNumberByMeetingID($tournament4number -> meeting_id);
					if (is_null($tournament -> selected_race)) {
						$tournament -> selected_race = $race_model4number -> getLastRaceNumberByMeetingID($tournament4number -> meeting_id);
					}										
				}
			}

			$ticket_list = array();
			//TODO: implement per user ticket
			//if (!$user -> guest) {
			//$ticket_start = strtotime('yesterday 12:00:00');
			//$ticket_list = $ticket_model -> getTournamentTicketActiveListByUserID($user -> id);
			//}

			$data = array('tournament_list' => $this -> sortTournamentList($tournament_list), 'ticket_list' => $ticket_list, 'jackpot' => $jackpot, 'tournament_type' => 'racing');
			/*
			 $view = &$this -> getView('Tournament', 'html', 'TournamentView');

			 $view -> assignRef('tournament_list', $tournament_list);
			 $view -> assignRef('ticket_list', $ticket_list);
			 $view -> assign('jackpot', $jackpot);
			 $view -> assign('tournament_type', 'racing');

			 $view -> setModel($sport_model);

			 $view -> display();
			 */

			$result = OutputHelper::json(200, $data);
		} else {
			$result = OutputHelper::json(500, array('error_msg' => 'Not a valid type!'));
		}

		return $result;

	}

	/*
	 * Sample: http://topbetta.com/tournament/details/86
	 */ 
	public function getTournamentDetails()
	{
		$id			= JRequest::getVar('id', null);
		$identifier	= JRequest::getVar('identifier', null);

		$component_list = array('tournament', 'topbetta_user', 'betting');
		foreach ($component_list as $component) {
			$path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
			$this -> addModelPath($path);
		}
		/**
		 * check private tournament with identifier
		 */
		 $password_protected = 0;
		if (!empty($identifier)) {
			$private_tournament_model	=& $this->getModel('TournamentPrivate', 'TournamentModel');
			$private_tournament			= $private_tournament_model->getTournamentPrivateByIdentifier($identifier);
			$id = $private_tournament->tournament_id;
			$password_protected = ($private_tournament->password) ? 1 : 0;
		}
		if (is_null($id)) {
			return OutputHelper::json(500, array('error_msg' => 'No tournament selected.'));
		}

		$sport_model 			=& $this->getModel('TournamentSport', 'TournamentModel');
		$is_racing_tournament 	= $sport_model->isRacingByTournamentId($id);

		if ($is_racing_tournament) {
			$racing_model	=& $this->getModel('TournamentRacing', 'TournamentModel');
			$tournament 	= $racing_model->getTournamentRacingByTournamentID($id);
		} else {
			$sport_event_model 	=& $this->getModel('TournamentSportEvent', 'TournamentModel');
			$tournament 		= $sport_event_model->getTournamentSportsByTournamentID($id);
		}

		if (is_null($tournament)) {
			return OutputHelper::json(500, array('error_msg' => 'Tournament not found.'));
		}
		$user =& JFactory::getUser();

		$private_tournament_link	= null;
		$private_tournament_url		= null;
		$shorten_url				= null;

		$event_group_id	= $tournament->meeting_id;
		if (empty($tournament->private_flag)) {
			$sport_id		= $tournament->tournament_sport_id;
			$competition_id	= $tournament->tournament_competition_id;
			if ($is_racing_tournament) {
				$event_group_id	= $tournament->meeting_id;
			} else {
				$event_group_id	= $tournament->event_group_id;
			}

			if (!$user->guest) {
				$private_tournament_link = '/index.php?option=com_tournament&task=privatetournament&format=raw&sport_id=' . $sport_id . '&competition_id=' . $competition_id . '&event_group_id=' . $event_group_id . '&from_tournament_id=' . $tournament->id;
			}
		} else {
			/**
			 * For Private tournament
			 * Only accessible with the identifier
			 */
			if (is_null($identifier)) {
				$private_tournament_model	=& $this->getModel('TournamentPrivate', 'TournamentModel');
				$private_tournament			= $private_tournament_model->getTournamentPrivateByTournamentID($id);
				$ticket_model				=& $this->getModel('TournamentTicket', 'TournamentModel');
				$ticket						= $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $id);

				if ($user->id < 1 || ($user->id != $private_tournament->user_id && $ticket->id < 1 )) {
					return OutputHelper::json(500, array('error_msg' => 'You need be invited to get in a private tournament'));
				}
			}

			$private_tournament_prize_format_model	=& $this->getModel('TournamentPrizeFormat', 'TournamentModel');
			$private_tournament_prize_format		= $private_tournament_prize_format_model->getTournamentPrizeFormat($private_tournament->tournament_prize_format_id);

			$tournament->prize_format_id = $private_tournament->tournament_prize_format_id;

			if ($user->id == $private_tournament->user_id) {
				$tournament_owner 		= $user->id;
				$private_tournament_url = JURI::base()."private/".$private_tournament->display_identifier;
				//$shorten_url 			= $this->shortenUrl($private_tournament_url);
			}
		}

		$ticket_model	=& $this->getModel('TournamentTicket', 'TournamentModel');
		$ticket			= $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id);

		if ($is_racing_tournament) {
			$race_model 				=& $this->getModel('Race', 'TournamentModel');
			$next_match 				= $race_model->getNextRaceNumberByTournamentID($tournament->id);
			$tournament_races 			= $race_model->getRaceListByTournamentID($tournament->id);
			$tournament_races_completed = false;
			$tournament_completed 		=& $tournament_races_completed;

			if (!empty($tournament_races) && is_null($next_match)) {
				$next_match = '';
				//checking if there is any unfinished race
				$tournament_races_completed = true;
				foreach ($tournament_races as $tournament_race) {
					if (($tournament_race->paid_flag == 0) && ($tournament_race->event_status_id !== 3)) { //status as Abandoned
						$tournament_races_completed = false;
						break;
					}
				}
			}
		} else {
			$event_model			=& $this->getModel('Event', 'TournamentModel');
			$next_match				= $event_model->getNextEventByEventGroupID($tournament->event_group_id);
			$tournament_completed 	= $tournament->paid_flag;
		}

		$player_list        = $ticket_model->getTournamentEntrantList($tournament->id);
		$leaderboard        = array();
		$leaderboard_rank   = null;

		$leaderboard_model =& $this->getModel('TournamentLeaderboard', 'TournamentModel');

		if (strtotime($tournament->start_date) < time()) {
			if ($tournament->paid_flag) {
				$leaderboard = $leaderboard_model->getLeaderBoardRank($tournament, 50, true);
			} else {
				$leaderboard = $leaderboard_model->getLeaderBoardRank($tournament, 50);
			}
			
			foreach($leaderboard as $id =>$val) $leaderboard[$id]->currency = Format::currency($leaderboard[$id]->currency);
			
			if (!is_null($ticket)) {
				$leaderboard_rank = $leaderboard_model->getLeaderBoardRankByUserAndTournament($user->id, $tournament);
				$leaderboard_rank->currency = Format::currency($leaderboard_rank->currency, true);
			}
		}

		$tournament_model 	=& $this->getModel('Tournament', 'TournamentModel');
		$prize_pool   		= $tournament_model->calculateTournamentPrizePool($tournament->id);
		$place_list   		= $tournament_model->calculateTournamentPlacesPaid($tournament, count($player_list), $prize_pool);

		$jackpot_link	= null;
		$parent_link	= null;
		if (!empty($tournament->jackpot_flag)) {
			$tournament_type 	= $is_racing_tournament ? 'racing' : 'sports';
			$jackpot_link 		= 'tournament/'.$tournament_type.'/component/jackpotmap/' . $tournament->id;
			$parent_link 		= 'tournament/details/'. $tournament->parent_tournament_id;
		}

		$unregister_allowed = false;
		$register_allowed 	= false;

		if (!$user->guest) {
			$unregister 		= $tournament_model->unregisterAllowed($tournament->id, $user->id);
			if (!is_null($unregister)) {
				$unregister_allowed = (floor($unregister->time) > 0 && $unregister->bet == 0 && !is_null($ticket));
			}
			$register_allowed 	= (is_null($ticket) && strtotime($tournament->end_date) > time() && empty($tournament->cancelled_flag));
		}
		/**
		 * Code for Comment box
		 */

		$tournament_comment_model 	=& $this->getModel('TournamentComment', 'TournamentModel');
		$tournament_comment_list	= $tournament_comment_model->getTournamentCommentListByTournamentId($tournament->id);

		$display_sledge_box 	= false;
		$allow_sledge_comment	= true;
		/**
		 * The Sledge box will appear
		 * Max 48 hrs after the tournament ends
		 */
		if ((time() - strtotime($tournament->end_date))/(60*60) <= 48) {
			$display_sledge_box = true;
			/**
			 * If the user is Normal user,
			 * he needs to be resigstered to that tournament
			 * To post a comment
			 */
			if (is_null($ticket) && ($user->usertype == "Registered" || $user->guest)) {
				$allow_sledge_comment = false;
			}
		}
		
			$race_model = &$this -> getModel('Race', 'TournamentModel');
			$number = JRequest::getVar('number', $race_model -> getNextRaceNumberByMeetingID($tournament -> meeting_id));
			if (is_null($number)) {
				$number = $race_model -> getLastRaceNumberByMeetingID($tournament -> meeting_id);
			}
			$race = $race_model -> getRaceByMeetingIDAndNumber($tournament -> meeting_id, $number);	
	
			$runner_model = &$this -> getModel('Runner', 'TournamentModel');
			$runner_list = $runner_model -> getRunnerListByRaceID($race -> id);

			$runner_list_by_id = array();
			$runner_list_by_number = array();
			$runner_ident_list = array();
			
			$image_root = getcwd();
			$image_root = str_replace('/api','',$image_root);
			
			foreach ($runner_list as $runner) {
				if($tournament->sport_name == 'greyhounds') { 
						$runner_list[$runner -> number]->silk_id = (file_exists($image_root.'/rugs/'.$runner->number .'.png')) ? "/rugs/".$runner->number . ".png" : "/rugs/default.png";
					} else {
						$runner_list[$runner -> number]->silk_id =  (file_exists($image_root.'/silks/'.$runner->silk_id .'.png')) ? "/silks/".$runner->silk_id .".png" : "/silks/default.png";
					}
				$runner_list_by_id[$runner -> id] = $runner;
				$runner_list_by_number[$runner -> number] = $runner;
				$runner_ident_list[] = $runner -> ident;
			}

			$rating_list = array();
			$rating_list = $runner_model -> getRunnerRatings($runner_ident_list);

			$runner_count = 0;

			foreach ($runner_list as $runner) {
				$runner -> rating = isset($rating_list[$runner -> ident]) ? $rating_list[$runner -> ident] -> rating : 0;

				if ($runner -> status == 'Not Scratched') {
					$runner_count++;
				}
			}
			
		/** 
		 * Author	    Nishad
		 * Description  Code for extra fields required in the method.
		 * Date		    24th sept 2012
		 */
		 $places_paid = 0;
		 if ($place_list) {
			foreach($place_list['place'] as $place => $prize) {
				$place_display[$place] = array();
				if(isset($prize['ticket']) && !empty($prize['ticket'])) {
					$place_display[$place][] = '1 Ticket (#' . $prize['ticket'] . ')';
				}

				if(isset($prize['cash']) && !empty($prize['cash'])) {
					$place_display[$place][] = Format::currency($prize['cash'], true);
				}

				$place_display[$place] = join(' + ', $place_display[$place]);
			}
			$places_paid    = count($place_display);						
		 }	
		 		
        $tournament->no_of_registrations	= count($player_list);
		$tournament->buy_in = Format::currency($tournament->buy_in, true);

		$tournament->value = Format::currency($tournament->buy_in, true) . ' + ' . Format::currency($tournament->entry_fee, true);

	    if($is_racing_tournament){
			$end_date						= strtotime($tournament->end_date);
		} else {
			$end_date			= strtotime($tournament->betting_closed_date ? $tournament->betting_closed_date : $tournament->end_date);
		}
		$betting_open						= $end_date > time();
		$tournament->betting_close	= ($betting_open) ? $this->formatCounterText($end_date) : 'Completed';

        $start_date						= strtotime($tournament->start_date);
		$tournament->start_time	= ($betting_open) ? $this->formatCounterText($start_date) : 'Completed';
		
		//Get user's betta bucks - Bala
		if ($user->guest || is_null($ticket)) {
			$available_currency = Format::currency(100000);
		} else {
			$available_currency = $ticket_model->getAvailableTicketCurrency($tournament->id, $user->id);
			$available_currency = Format::currency($available_currency);
		}		
	
		$races_and_results = $tournament_model->getRaceListAndResultsByTournamentIdForApi($event_group_id,$number);		
		
		//My Tournament bets
		$total_tournament_bet = 0;
		$total_tournament_win = 0;
		$tournament_bets = array();
		
		$tournament_races_bet = $race_model -> getRaceListByTournamentIDApi($tournament -> id);
		
		$bet_model =& $this->getModel('TournamentBet', 'TournamentModel');
		if(isset($tournament_races_bet))
		{
			foreach($tournament_races_bet as $race)
			{
				$tournament_bet_list = null;
				if (!is_null($ticket)) {
					$tournament_bet_list = $bet_model->getTournamentBetListByEventIDAndTicketID($race->id, $ticket->id);
				}
				
				if(is_null($tournament_bet_list)) {
					$tournament_bet_list = array();
				}
				
				foreach ($tournament_bet_list as $bet) {
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
						$bet->paid        = '-';
						$bet->result      = 'REFUNDED';
		
						$bet->row_class   = ' scratched';
						$bet->class       = 'paid';
					} else {
						$bet->row_class     = '';
						$bet->odds_tooltip  = (empty($bet->resulted_flag)) ? 'Current approximate odds' : 'Final payout dividend';
						
						$bet->paid_amount   = (empty($bet->resulted_flag)) ? bcmul($bet->display_dividend, $bet->bet_amount) : Format::currency($bet->win_amount);
						// formatting for wins and approximate wins
						$bet->paid    = ($bet->paid_amount > 0) ? Format::currency($bet->paid_amount) : '-';
						$bet->class   = 'paid';
						$bet->result  = (empty($bet->resulted_flag)) ? '-' : 'WIN';
		
						// formatting for losing bets
						if(intval($bet->paid_amount) == 0 && !empty($bet->resulted_flag)) {
							$bet->class   = 'notpaid';
							$bet->paid    = 'NIL';
							$bet->result  = 'LOSS';
						}
						// bet product
						if($bet->bet_product_id == 0) {
							$bet->product   = '';
						}
						else 
						{
							require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betproduct.php');
							$bet_product_model	= new BettingModelBetProduct();
							$bet->product = $bet_product_model->getBetProductByIdApi((int)$bet->bet_product_id);	
						}
						
						$bet->bet_amount = Format::currency($bet->bet_amount,true);
					}
		
					$total_tournament_bet = bcadd($total_tournament_bet, ($bet->bet_status == 'fully-refunded') ? 0 : Format::currency($bet->bet_amount,true));
					$total_tournament_win = bcadd($total_tournament_win, ($bet->bet_status == 'fully-refunded') ? 0 : Format::currency($bet->paid_amount,true));

				}
				
				foreach ($tournament_bet_list as $bet) {
					if($bet->display_bet) $tournament_bets[$race->number] = array('race_id' => $race->id, 'race_number' => $race->number, 'race_bets' => $tournament_bet_list);
				}
			}
		}
		$net_win = '-';
		$tournament_net_win_class = 'paid';
		
		if ($race->paid_flag) {
			$net_win = bcsub($total_tournament_win, $total_tournament_bet);
			$race->tournament_net_win_class = ($net_win < 0 ? 'notpaid' : 'paid');
			
			$net_win = Format::currency($net_win);
		}
		
		$total_tournament_bet	= ($total_tournament_bet ? Format::currency($total_tournament_bet) : '-');
		$total_tournament_win	= ($total_tournament_win ? Format::currency($total_tournament_win) : '-');
		
		if ($user->guest) {
			$tournament_bet_list = '';
		}
		else 
		{
			$tournament_bet_list = array(	'bets' => $tournament_bets, 
											'net_win' => $net_win, 
											'total_tournament_bet' => $total_tournament_bet, 
											'total_tournament_win' => $total_tournament_win);
		}
		
        
        $data = array(
			'tournament' => $tournament,
			'ticket' => $ticket,
			'place_list' => $place_list,
			'prize_pool' => Format::currency($prize_pool, true),
			'leaderboard_rank' => $leaderboard_rank,			
			'player_list' => $player_list,
			'leaderboard' => $leaderboard,
			'runner_list' => $runner_list_by_number,
			'selected_race' => $number,
			'tournament_bet_list' => $tournament_bet_list,
			'places_paid' => $places_paid,
			'races_and_results' => $races_and_results,
			'available_currency' => $available_currency,
			'private' => $tournament->private_flag,
			'password_protected' => $password_protected
			
		);
		
		$result = OutputHelper::json(200, $data);
		
		return $result;
		
		/*
		$view =& $this->getView('Tournament', 'html', 'TournamentView');
		$view->setLayout('details');

		$view->assignRef('tournament', $tournament);
		$view->assignRef('private_tournament', $private_tournament);
		$view->assignRef('private_tournament_prize_format', $private_tournament_prize_format);
		$view->assignRef('tournament_owner', $tournament_owner);

		$view->assignRef('ticket', $ticket);
		$view->assignRef('user', $user);

		$view->assign('place_list', $place_list);
		$view->assign('prize_pool', $prize_pool);

		$view->assign('unregister_allowed', $unregister_allowed);
		$view->assign('register_allowed', $register_allowed);
		$view->assign('next_match', $next_match);

		$view->assignRef('player_list', $player_list);
		$view->assignRef('leaderboard', $leaderboard);

		$view->assignRef('leaderboard_rank', $leaderboard_rank);
		$view->assign('jackpot_link', $jackpot_link);
		$view->assign('parent_link', $parent_link);
		$view->assign('private_tournament_link', $private_tournament_link);
		$view->assign('is_racing_tournament', $is_racing_tournament);

		$view->assign('tournament_completed', $tournament_completed);
		$view->assign('tournament_comment_list', $tournament_comment_list);
		$view->assign('display_sledge_box', $display_sledge_box);
		$view->assign('allow_sledge_comment', $allow_sledge_comment);

		$view->assign('private_tournament_url', $private_tournament_url);
		$view->assign('shorten_url', $shorten_url);
		$view->display();
		 * */
	}


	/**
	 * Sort a list of tournaments into meeting groups and format their display values
	 *
	 * @param array $unsorted
	 * @return array
	 */
	private function sortTournamentList($unsorted) {
		$user = &JFactory::getUser();

		$sorted_list = array();
		foreach ($unsorted as $tournament) {
			$event_group_id = $tournament -> event_group_id;

			if (!isset($sorted_list[$event_group_id])) {
				$sorted_list[$event_group_id] = array();
			}
			$tournament -> value = $this -> formatTournamentValue($tournament -> entry_fee, $tournament -> buy_in);
			$tournament -> gameplay = (empty($tournament -> jackpot_flag)) ? 'Single' : 'Jackpot';

			//this shows the time to go in days,mins etc
			$time = strtotime($tournament -> start_date);
			$tournament -> togo = ($time > time()) ? $this -> formatCounterText(strtotime($tournament -> start_date)) : 'In Progress';

			$tournament -> places_paid = count($tournament -> place_list['place']);
			$tournament -> display_pool = Format::currency($tournament -> prize_pool, true);
			//$tournament -> info_link_href = 'tournament/details/' . $tournament -> id;
			$tournament -> info_link_href = OutputHelper::api_link('getTournamentDetails', 'id=' . $tournament -> id);

			if (!empty($this -> ticket_list) && isset($this -> ticket_list[$tournament -> id])) {
				//MC $tournament -> entry_link_href = 'tournament/' . $this -> tournament_type . '/game/' . $tournament -> id;
				$tournament -> entry_link_text = 'Bet Now';
				$tournament -> entry_link_class = 'bet_link';
			} else {
				//MC $tournament -> entry_link_href = $user -> guest ? '/user/register' : 'tournament/' . $this -> tournament_type . '/confirmticket/' . $tournament -> id;
				$tournament -> entry_link_text = 'Enter';
				$tournament -> entry_link_class = $user -> guest ? 'guest_link' : 'register_link';
			}

			$sorted_list[$event_group_id][$tournament -> id] = $tournament;
		}
		return $sorted_list;
	}

	/**
	 * Format a tournament value string
	 *
	 * @param integer $entry_fee
	 * @param integer $buy_in
	 * @return string
	 */
	protected function formatTournamentValue($entry_fee, $buy_in) {
		$value = 'FREE';
		if (!empty($entry_fee) && !empty($buy_in)) {
			$value = Format::currency($buy_in, true);
			$value .= ' + ';
			$value .= Format::currency($entry_fee, true);
		}

		return $value;
	}

	/**
	 * Format the display of a countdown to a specified time
	 *
	 * @param integer $time
	 * @return string
	 */
	protected function formatCounterText($time) {
		if ($time < time()) {
			return 'PAST START TIME';
		}

		$remaining = $time - time();

		$days = intval($remaining / 3600 / 24);
		$hours = intval(($remaining / 3600) % 24);
		$minutes = intval(($remaining / 60) % 60);
		$seconds = intval($remaining % 60);

		$text = $seconds . ' sec';
		if ($minutes > 0) {
			$text = $minutes . ' min';
		}

		if ($hours > 0) {
			$min_sec_text = '';

			if ($days == 0) {
				$min_sec_text = $text;
			}

			$text = $hours . ' hr ' . $min_sec_text;
		}

		if ($days > 0) {
			$text = $days . ' d ' . $text;
		}
		return $text;
	}

}
?>
