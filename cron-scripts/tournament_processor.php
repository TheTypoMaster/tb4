<?php
require_once '../common/shell-bootstrap.php';

class TournamentProcessor extends TopBettaCLI
{
	/**
	 * Tournament model pointer
	 *
	 * @var TournamentModelTournament
	 */
	protected $tournament;

	/**
	 * TournamentTicket model pointer
	 *
	 * @var TournamentModelTournamentTicket
	 */
	protected $tournament_ticket;

	/**
	 * TournamentLeaderboard model pointer
	 *
	 * @var TournamentModelTournamentLeaderboard
	 */
	protected $leaderboard;

	/**
	 * BetType model pointer
	 *
	 * @var BettingModelBetType
	 */
	protected $bet_type;

	/**
	 * BetResultStatus model pointer
	 *
	 * @var BettingModelBetResultStatus
	 */
	protected $bet_result_status;

	/**
	 * Reusable DateTime object
	 *
	 * @var DateTime
	 */
	protected $date;

	/**
	 * Used to initialise all required database models
	 */
	final public function initialise(){

		$this->date = new DateTime();

		$this->addComponentModels('tournament');
		$this->addComponentModels('betting');
		$this->addComponentModels('payment');
		$this->addComponentModels('tournamentdollars');
		$this->addComponentModels('topbetta_user');

		$this->tournament			=& JModel::getInstance('Tournament', 'TournamentModel');
		$this->tournament_ticket 	=& JModel::getInstance('TournamentTicket', 'TournamentModel');
		$this->leaderboard			=& JModel::getInstance('TournamentLeaderboard', 'TournamentModel');
		$this->place				=& JModel::getInstance('TournamentPlacesPaid', 'TournamentModel');
		$this->payout_final 		=& JModel::getInstance('TournamentPayoutFinal', 'TournamentModel');

		$this->bet_type 			=& JModel::getInstance('BetType', 'BettingModel');
		$this->bet_result_status 	=& JModel::getInstance('BetResultStatus', 'BettingModel');

		$this->account_balance		=& JModel::getInstance('AccountTransaction', 'PaymentModel');
		$this->tournament_balance	=& JModel::getInstance('TournamentTransaction', 'TournamentDollarsModel');
		$this->user					=& JModel::getInstance('TopBettaUser', 'TopBettaUserModel');

		$this->tournament_sport 	=& JModel::getInstance('TournamentSport', 'TournamentModel');
		$this->tournament_bet 		=& JModel::getInstance('TournamentBet', 'TournamentModel');

		$this->event				=& JModel::getInstance('Event', 'TournamentModel');
		$this->event_group			=& JModel::getInstance('EventGroup', 'TournamentModel');
		$this->selection			=& JModel::getInstance('Selection', 'TournamentModel');
		$this->selection_result 	=& JModel::getInstance('SelectionResult', 'TournamentModel');

		$this->db =& $this->getDBO();
	}
	/**
	* Main script method
	*/
	public function execute()
	{
		$display_message = true;
		while($this->_checkForRunningInstance(basename(__FILE__))){
			if($display_message){
				$this->l('Tournament Processor already running. WAIT');
				$display_message = false;
			}
			time_nanosleep(0, 500000000);
		}
		
		$abandoned_list = $this->event->getAbandonedEventList();
		if(empty($abandoned_list)) {
			$this->l("No abandoned matches to process");
		} else {
			$this->_processAbandonedEventList($abandoned_list);
			$this->_updateLeaderboardList($abandoned_list);
		}
	
		// process paying match bets
		$paying_list = $this->event->getPayingEventList(true);
		
		if(empty($paying_list)){
			$this->l("No unresulted paying matches available to process");
		} else {
			$this->_processPayingEventList($paying_list);
			$this->_updateLeaderboardList($paying_list);
		}
		
		// payout tournaments
		$tournament_list = $this->tournament->getTournamentCompletedList();
		
		if(empty($tournament_list)) {
			$this->l("No completed tournaments to close");
		} else {
			$this->_processCompletedTournamentList($tournament_list);
		}
	}

	/**
	 * Update a single leaderboard record for a user and tournament
	 *
	 * @param integer $tournament_id
	 * @param integer $user_id
	 */
	protected function _updateLeaderboard($tournament_id, $user_id)
	{
		$total = $this->tournament_ticket->getLeaderboardTicketCurrency($tournament_id, $user_id);
        file_put_contents('/tmp/tournament-leaderboard-'.$tournament_id, 'UserId:'.$user_id.', Currency:'.$total, FILE_APPEND);
        $this->leaderboard->updateLeaderboardByUserAndTournamentID($user_id, $tournament_id, $total);
	}

	/**
	 * Loop over an array of paid race/match records and update the leaderboard for any users who bet on that race/match
	 *
	 * @param array $update_list
	 */
	protected function _updateLeaderboardList(array $update_list)
	{
		foreach($update_list as $event) {
			$ticket_list = $this->tournament_ticket->getTournamentTicketListByEventID($event->id);
			if(empty($ticket_list)) {
				continue;
			}

			foreach($ticket_list as $ticket) {
				if(!$this->tournament_bet->userhasBet($ticket->user_id, $ticket->tournament_id, $event->id)) {
					continue;
				}

				$this->_updateLeaderboard($ticket->tournament_id, $ticket->user_id);
			}
		}
	}

	/**
	 * Loop over and array of tournament records with their final race set to paid and result the tournaments
	 *
	 * @param array $tournament_list
	 */
	protected function _processCompletedTournamentList(array $tournament_list)
	{
		foreach($tournament_list as $tournament) {
			$qualified_list = $this->leaderboard->getLeaderBoardRank($tournament, null, true);
			$this->_resultTournament($tournament, $qualified_list);
		}
	}

	/**
	 * Result a single bet
	 *
	 * @param object $bet
	 */
	protected function _resultBet($bet)
	{
		static $resulted_status_id = null;

		if(is_null($resulted_status_id)) {
			$resulted_status = $this->bet_result_status->getBetResultStatusByName(BettingModelBetResultStatus::STATUS_PAID);
			$resulted_status_id = $resulted_status->id;
		}

		$bet->bet_result_status_id 	= $resulted_status_id;
		$bet->win_amount 			= (int) $this->_getWinAmount($bet);
		$bet->resulted_flag 		= 1;

		$this->_save($bet);
	}

	/**
	 * Refund a bet by setting the refund status, resulted flag and changing the win amount to the bet amount
	 *
	 * @param object $bet
	 */
	protected function _refundBet($bet)
	{
		static $refund_status_id = null;

		if(is_null($refund_status_id)) {
			$refund_status = $this->bet_result_status->getBetResultStatusByName(BettingModelBetResultStatus::STATUS_FULL_REFUND);
			$refund_status_id = $refund_status->id;
		}

		$bet->bet_result_status_id 	= $refund_status_id;
		$bet->win_amount 			= (int) $bet->bet_amount;
		$bet->resulted_flag 		= 1;

		$this->_save($bet);
	}

	/**
	 * Result a completed tournament
	 *
	 * @param object 	$tournament
	 * @param array 	$qualified_list
	 */
	private function _resultTournament($tournament, $qualified_list)
	{
		if($tournament->private_flag){
			$this->l("Tournament is marked as private.");
		}
	
		$qualified = count($qualified_list);
		$this->l("Found {$qualified} qualifier(s) for tournament {$tournament->name} ({$tournament->id})");

		if($qualified == 0) {
			$this->l("Tournament {$tournament->name} ({$tournament->id}) has no qualifiers and is being refunded");
			$this->_refundTournament($tournament);
			return;
		}

		$prize_list 		= $this->place->getPrizeDistribution($tournament);
		$formula			= $prize_list['formula'];
		$prize_place_list 	= $prize_list['place'];
		$rank_list			= $this->place->formatRankingList($qualified_list);
		
		if($tournament->free_credit_flag){
			$formula = "free";
		}

		$this->l("Using payout formula {$formula}");
		foreach($prize_place_list as $rank => $prize) {
			$this->l("Paying position {$rank}");

			foreach($rank_list[$rank] as $qualifier) {
				$prize_display = array();
				$payout_final = clone $this->payout_final;
				$payout_final->user_id = (int) $qualifier->id;
				$payout_final->position = $rank;
				$payout_final->tournament_id = $tournament->id;

				$ticket 				= (!empty($prize['ticket'])) ? $prize['ticket'] : null;
				$cash					= (!empty($prize['cash'])) ? $prize['cash'] : null;
				$prize['ticket_value'] 	= null;

				if(!is_null($ticket) && $ticket > 0) {
					if($existing_ticket = $this->tournament_ticket->getTournamentTicketByUserAndTournamentID($qualifier->id, $prize['ticket'])) {
						$this->l('User already has a ticket to this tournament, paying tournament dollars');

						if(is_null($cash)) {
							$cash = 0;
						}

						$cash 					+= $this->tournament_ticket->getTicketCost($existing_ticket->id);
						$ticket 				= null;
						$prize['ticket_value']	= $cash;

					} else {
						$prize_display[] 				= "Ticket to Tournament {$ticket}";
						$result_id 						= $this->_awardTicket($qualifier, $ticket);
					}
					$payout_final->win_amount = (int) $this->_getParentTournamentTicketValue($prize['ticket']);
					$payout_final->saveTournamentTicketPayout();
				}

				if(!is_null($cash) && $cash > 0) {
					$display_cash = '$' . number_format($cash / 100, 2);
					$payout_final->win_amount = (int) $prize['cash'];

					if($formula == 'cash') {
						$display_cash .= ' in cash';
						$result_id = $this->_awardCash($qualifier, $cash);
						$payout_final->saveCashPayout();
					}elseif($formula == 'free'){
						$display_cash .= ' in free credit';
						$result_id = $this->_awardTournamentDollars($qualifier, $cash);
						if(!is_null($prize['cash'])){
							$payout_final->saveTournamentDollarPayout();
						}
					} else {
						$display_cash .= ' in tournament dollars';
						$result_id = $this->_awardTournamentDollars($qualifier, $cash);
						if(!is_null($prize['cash'])){
							$payout_final->saveTournamentDollarPayout();
						}
					}
					$prize_display[] = $display_cash;
				}
				$prize_display = implode(' + ', $prize_display);
				$this->l("{$rank}. {$qualifier->username}: {$prize_display}");
				$this->_sendWinnerEmail($qualifier, $tournament, $prize);
				$this->_resultTicket($qualifier, $tournament->id, $result_id);
			}
		}

		$this->tournament_ticket->setResultedFlagByTournamentID($tournament->id);
		$this->tournament->setPaidFlagByTournamentID($tournament->id);
	}
	/**
	 * Payout the prizes using payout formula
	 *
	 * @param $prize_list
	 */
	/**
	 * Set the result transaction ID and resulted flag for a tournament ticket
	 *
	 * @param object 	$user
	 * @param integer 	$tournament_id
	 * @param integer 	$result_transaction_id
	 */
	private function _resultTicket($user, $tournament_id, $result_transaction_id) {
		$ticket = $this->tournament_ticket->getTournamentTicketByUserAndTournamentID($user->id, $tournament_id);

		$ticket->result_transaction_id 	= $result_transaction_id;
		$ticket->resulted_flag			= 1;

		$this->tournament_ticket->store((array)$ticket);
	}

	/**
	 * Perform the audit-trail filling transactions and create a new ticket for a user
	 *
	 * @param object 	$user
	 * @param integer 	$tournament_id
	 */
	private function _awardTicket($user, $tournament_id) {
		$tournament = $this->_getParentTournament($tournament_id);
		$value 		= $tournament->entry_fee + $tournament->buy_in;

		$increment_id 	= $this->_awardTournamentDollars($user, $value);
		$entry_fee_id 	= $this->_awardTournamentDollars($user, -$tournament->entry_fee, 'entry');
		$buy_in_fee_id 	= $this->_awardTournamentDollars($user, -$tournament->buy_in, 'buyin');

		$ticket = array(
			'tournament_id' 			=> $tournament->id,
			'user_id'					=> $user->id,
			'entry_fee_transaction_id' 	=> $entry_fee_id,
			'buy_in_transaction_id' 	=> $buy_in_fee_id,
			'resulted_flag'				=> 0,
			'refunded_flag'				=> 0
		);

		$this->tournament_ticket->store($ticket);
		$leaderboard = array(
			'user_id' 		=> $user->id,
			'tournament_id' => $tournament->id,
			'currency' 		=> $tournament->start_currency,
			'turned_over' 	=> 0
		);

		$this->leaderboard->store($leaderboard);
		return $increment_id;
	}

	/**
	 * Get parent tournament details
	 *
	 * @param integer $tournament_id
	 * @return object
	 */
	private function _getParentTournament($tournament_id){
		static $parent_tournament = null;

		if(is_null($parent_tournament) || $parent_tournament->id != $tournament_id){
			$parent_tournament = $this->tournament->getTournament($tournament_id);
		}

		return $parent_tournament;
	}

	/**
	 * Get parent tournament details
	 *
	 * @param integer $tournament_id
	 * @return integer
	 */
	private function _getParentTournamentTicketValue($tournament_id){
		$tournament = $this->_getParentTournament($tournament_id);
		return $tournament->entry_fee + $tournament->buy_in;
	}

	/**
	 * Increment a user's account balance
	 *
	 * @param object 	$user
	 * @param integer 	$amount
	 * @param string 	$keyword
	 */
	private function _awardCash($user, $amount, $keyword = 'tournamentwin') {
		$this->account_balance->setUserId($user->id);
		return $this->account_balance->increment($amount, $keyword);
	}

	/**
	 * Increment a user's tournament dollars balance
	 *
	 * @param object 	$user
	 * @param integer 	$amount
	 * @param string 	$keyword
	 */
	private function _awardTournamentDollars($user, $amount, $keyword = 'win') {
		$this->tournament_balance->setUserId($user->id);
		return $this->tournament_balance->increment($amount, $keyword);
	}

	/**
	 * Send a winner notification email
	 *
	 * @param integer 	$user_id
	 * @param integer 	$tournament_id
	 * @param mixed 	$prize
	 */
	private function _sendWinnerEmail($qualifier, $tournament, $prize) {
		$mailer = new UserMAIL();
		
		$user	=& $this->user->getUser($qualifier->id);
		
		$email_params	= array(
			'subject'	=> 'TopBetta Winner Notification',
			'mailto'	=> $user->email
		);
		$email_replacements = array(
			'name'				=> $user->name,
			'username'			=> $user->username,
			'tournament name'	=> ($tournament->private_flag ? $tournament->owner : 'Topbetta') . '\'s ' . $tournament->name . ' tournament',
			'prize text'		=> $this->_getPrizeText($tournament, $prize)
		);

		if($this->debug()) {
			$this->d('Sending email...');
		} else {
			$mailer->sendUserEmail('winnerNotificationEmail', $email_params, $email_replacements);
		}
	}

	/**
	 * Construct the prize text for a winner email
	 *
	 * @param object 	$tournament
	 * @param array 	$prize
	 * @return string
	 */
	private function _getPrizeText($tournament, $prize) {
		$nl   = "\n\n";
		if($tournament->private_flag && $tournament->buy_in == 0) {
			$text	= $nl;
			$text	.= 'Why not challenge everyone to a rematch? You can create your own Private Tournament on TopBetta.';
		} else {
			$text = ' You\'ve won';
	
			if(isset($prize['ticket']) && $prize['ticket'] > -1) {
				$parent = $this->tournament->getTournament($prize['ticket']);
				$text .= ' a ticket to the next tournament round: '. $parent->name . '.' ;
	
				if($prize['ticket_value'] > 0) {
					$text .= ' You\'re already registered for that tournament though, so we\'ve credited you with '. number_format($prize['ticket_value'] / 100, 2) . ' in Free Credit. ';
				}
				$text .=$nl;
	
				if(isset($prize['cash'])) {
					$text .= 'You\'ve also won';
				}
			}
	
			if(isset($prize['cash'])) {
				$text .= ' $' . number_format(floor($prize['cash']) / 100, 2);
				if(isset($prize['ticket'])) {
					$text .= ' in Free Credit.';
					$text .= ' These can be used to enter more tournaments on Topbetta and compete for cash prizes.';
				} elseif($tournament->free_credit_flag){
					$text .= ' free credit! This amount has been credited to your Free Credit Balance. ';
				} else {
					$text .= ' cash! This amount has been credited to your Account Balance. ';
					$text .= ' Remember, to withdraw your cash you need to provide us with the Identification Document.';
				}
			}
		}
		return $text;
	}

	/**
	 * Refund all tickets for a tournament
	 *
	 * @param object 	$tournament
	 * @param boolean 	$full
	 */
	private function _refundTournament($tournament, $full = false) {
		$ticket_list = $this->tournament_ticket->getTournamentTicketListByTournamentID($tournament->id);
		if(empty($ticket_list)) {
			$this->l("No tickets to refund for {$tournament->name} ({$tournament->id})");
		} else {
			foreach($ticket_list as $ticket) {
				$this->l("Refunding ticket {$ticket->id}");
				$this->tournament_ticket->refundTicketAnywhere($ticket, $full);
				$this->l("Removing leaderboard record for ticket {$ticket->id} - user {$ticket->user_id}");
				$this->leaderboard->deleteByUserAndTournamentID($ticket->user_id, $tournament->id);
			}
		}

		$this->tournament->setPaidFlagByTournamentID($tournament->id);
	}
	
	/**
	* process paying event list
	* @param array $paying_list
	*/
	private function _processPayingEventList(array $paying_list) {
		$this->l("Processing paying match list");
	
		foreach($paying_list as $event) {
			$bet_list = $this->tournament_bet->getUnresultedTournamentBetListByEventID($event->id);
			$this->selection_result_list = $this->selection_result->getSelectionResultListByEventID($event->id);

			//MC - only process this event if we have a dividend for the winner
			$found_winner = ($event->racing_flag == 1) ? 0 : 1;
	        foreach ($this->selection_result_list as $selection) {
	            if ($selection->win_dividend > 0 ) {
	                $found_winner++;
	                $this->l("Win Dividend: " . $selection->win_dividend . " ID: " . $selection->selection_id);
	            }	            
	        }
	        
	        if ($found_winner > 0) {
	        
                if(empty($bet_list)) {
                    $this->l("No bets to result for {$event->name}");
                } else {
                    foreach($bet_list as $bet) {
                        if($bet->refund_flag) {
                            $this->l("Refunding bet {$bet->id}: market: {$bet->market_name}");
                            $this->_refundBet($bet);
                            continue;
                        }
                        if($this->_isRacing($bet)){
                            if($this->selection->isScratched($bet->selection_status)) {
                                $this->l("Refunding bet {$bet->id}: runner {$bet->selection_name} status is {$bet->selection_status}");
                                $this->_refundBet($bet);
                                continue;
                            }
                        }
                        $this->_resultBet($bet);
                    }
                }
                
                $this->l("Setting {$event->external_event_id} to paid");
                $this->event->setEventToPaid($event->id);
			
			} else {
			    $this->l("No winner found for {$event->external_event_id}, skipping.");
			}			
		}
	}
	
	/**
	 * process paying match list
	 * @param array $paying_list
	 */
	private function _processAbandonedEventList(array $abandoned_list) {
		$this->l("Processing abandoned event list");
	
		foreach($abandoned_list as $event) {
			$bet_list = $this->tournament_bet->getUnresultedTournamentBetListByEventID($event->id);
	
			if(empty($bet_list)) {
				$this->l("No bets to refund for {$event->name}");
			} else {
				foreach($bet_list as $bet) {
					$this->l("Refunding bet {$bet->id}: market: {$bet->market_name}");
					$this->_refundBet($bet);
				}
			}
	
			$this->event->setAbandonedEventToPaid($event->id);
				
			if($this->_isRacing($event)){
				if($this->event_group->isEventGroupAbandoned($event->event_group_id)) {
					$this->l("Meeting ({$event->event_group_id}) has been abandoned");
					$this->_abandonMeeting($event->event_group_id);
				}
			}
		}
	}
	
	/**
	 * Method to get win amount (abstraction)
	 *
	 * @param object $bet
	 * @return int
	 */
	protected function _getWinAmount($bet){
		$win_amount = 0;
		if($dividend = $this->_getPayoutDividend($bet)){
			$win_amount = bcmul($dividend, $bet->bet_amount);
		}
	
		if($win_amount > 0) {
			$this->l("Paying bet {$bet->id}: amount: $win_amount position: {$bet->position} selection: {$bet->selection_name} for event: {$bet->event_name}");
		} else {
			$this->l("Resulting bet {$bet->id}: amount: $win_amount [ position: {$bet->position} bet_amount: {$bet->bet_amount} dividend: $dividend bet type: {$bet->bet_type} ] selection: {$bet->selection_name} for event: {$bet->event_name}");
		}
	
		return $win_amount;
	}
	
	/**
	 * Extract the correct payout odds from a bet based on the bet type
	 *
	 * @param object $bet
	 * @return mixed
	 */
	private function _getPayoutDividend($bet) {
		if(!$this->_isWinningBet($bet)) {
			return false;
		}
	
		if($this->_isSport($bet)){
			return $bet->fixed_odds;
		}
	
		return ($bet->bet_type == 'win') ? $bet->win_dividend : $bet->place_dividend;
	}
	
	/**
	 * Check a bet and determine if it's a winner
	 *
	 * @param object $bet
	 * @return bool
	 */
	private function _isWinningBet($bet) {
	
		if($this->_isSport($bet)) {
			$is_winning = (!is_null($bet->record_exists) && $bet->record_exists != '__UNDEFINED__');
			return $is_winning;
		}
	
		if(empty($bet->position) || $bet->position > 3) {
			return false;
		}
	
		if($bet->bet_type == 'win' && $bet->position != 1) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Abandon a meeting then refund all tickets for tournaments which were using that meeting
	 *
	 * @param integer $id
	 */
	private function _abandonMeeting($id) {
		$tournament_list = $this->tournament->getTournamentListByEventGroupID($id);
		if(empty($tournament_list)) {
			$this->l("No tournaments to abandon for abandoned meeting {$id}");
			return;
		}
	
		$count = count($tournament_list);
		$this->l("Found {$count} tournaments to cancel for abandoned meeting {$id}");
	
		foreach($tournament_list as $tournament) {
			$this->l("Cancelling {$tournament->name} ({$tournament->id})");
	
			$tournament->cancelled_flag 	= 1;
			$tournament->cancelled_reason	= "More than 50-% of the races for meeting {$id} have been abandoned";
	
			$this->_save($tournament);
		}
	
		$ticket_list = $this->tournament_ticket->getTournamentTicketListByEventGroupID($id);
		if(empty($ticket_list)) {
			$this->l("No tickets to refund for abandoned meeting {$id}");
			return;
		}
	
		$count = count($ticket_list);
		$this->l("Found {$count} tickets to refund for abandoned meeting {$id}");
	
		foreach($ticket_list as $ticket) {
			$this->l("Refunding ticket {$ticket->id}");
			$this->tournament_ticket->refundTicketAnywhere($ticket, true);
	
			$this->l("Removing leaderboard record for ticket {$ticket->id} - user {$ticket->user_id}");
			$this->leaderboard->deleteByUserAndTournamentID($ticket->user_id, $ticket->tournament_id);
		}
	}
	
	private function _isSport($object){
		return (!$object->racing_flag);
	}
	
	private function _isRacing($object){
		return ($object->racing_flag);
	}
}

$cron = new TournamentProcessor();
$cron->debug(false);
$cron->execute();
