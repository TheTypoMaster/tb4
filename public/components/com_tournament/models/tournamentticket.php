<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelTournamentTicket extends JModel
{
	/**
	 * Get a single tournament ticket record.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentTicket($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				tournament_id,
				user_id,
				entry_fee_transaction_id,
				buy_in_transaction_id,
				result_transaction_id,
				refunded_flag,
				resulted_flag,
				created_date,
				resulted_date
			FROM
				' . $db->nameQuote( '#__tournament_ticket' ) . '
			WHERE
				id = ' . $db->quote( $id );

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Get a single tournament ticket record by tournament and user ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentTicketByUserAndTournamentID($user_id, $tournament_id, $include_refunded = false)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				tournament_id,
				user_id,
				entry_fee_transaction_id,
				buy_in_transaction_id,
				result_transaction_id,
				refunded_flag,
				resulted_flag,
				created_date,
				resulted_date
			FROM
				' . $db->nameQuote( '#__tournament_ticket' ) . '
			WHERE
				user_id = ' . $db->quote( $user_id ) . '
			AND
				tournament_id = ' . $db->quote( $tournament_id );

		if(!$include_refunded) {
			$query .= ' AND refunded_flag != 1';
		}

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Get a user's tournament tickets
	 *
	 * @param integer $user_id
	 * @return object
	 */
	public function getTournamentTicketActiveListByUserID($user_id, $order = false, $include_refunded = false)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				tk.id,
				tk.tournament_id,
				tk.result_transaction_id,
				t.buy_in,
				t.entry_fee,
				s.name AS sport_name,
				t.start_date,
				t.end_date,
				t.cancelled_flag,
				t.name AS tournament_name
			FROM
				' . $db->nameQuote('#__tournament_ticket') . ' AS tk
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.id = tk.tournament_id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				t.event_group_id = eg.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			ON
				t.tournament_sport_id = s.id
			WHERE
				user_id = ' . $db->quote($user_id) . '
				AND t.paid_flag <> 1
				AND t.cancelled_flag = 0';

		if(!$include_refunded) {
			$query .= ' AND tk.refunded_flag != 1';
		}

		if($order) {
			$query .= ' ORDER BY ' . $order;
		} else {
			$query .= ' ORDER BY t.start_date ASC, tk.created_date DESC';
		}

		$db->setQuery($query);
		return $db->loadObjectList('tournament_id');
	}

	/**
	 * Get a user's tournament tickets
	 *
	 * @param integer $user_id
	 * @return object
	 */
	public function getTournamentTicketRecentListByUserID($user_id, $from_time = false, $end_time = false, $paid_flag = null, $order = false, $include_refunded = false)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				tk.id,
				tk.tournament_id,
				tk.result_transaction_id,
				tk.winner_alert_flag,
				t.buy_in,
				t.entry_fee,
				s.name AS sport_name,
				t.jackpot_flag,
				t.start_date,
				t.end_date,
				t.cancelled_flag,
				t.name AS tournament_name
			FROM
				' . $db->nameQuote('#__tournament_ticket') . ' AS tk
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.id = tk.tournament_id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				t.event_group_id = eg.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			ON
				t.tournament_sport_id = s.id
			WHERE
				user_id = ' . $db->quote($user_id);

		if($from_time) {
			$query.= ' AND t.end_date > FROM_UNIXTIME(' . $db->quote($from_time) . ')';
		}

		if($end_time) {
			$query.= ' AND t.end_date < FROM_UNIXTIME(' . $db->quote($end_time) . ')';
		}

		if(!is_null($paid_flag)) {
			$query.= ' AND t.paid_flag = ' . $db->quote($paid_flag);
		}

		if(!$include_refunded) {
			$query .= ' AND tk.refunded_flag != 1';
		}

		if($order) {
			$query .= ' ORDER BY ' . $order;
		} else {
			$query .= ' ORDER BY t.start_date ASC, tk.created_date DESC';
		}

		$db->setQuery($query, 0, 15);
		return $db->loadObjectList('tournament_id');
	}

	/**
	 * Get a user's tournament list
	 *
	 * @param integer $user_id
	 * @return object
	 */
	public function getUserTournamentList($user_id, $order = null, $direction = null, $limit = null, $offset = null)
	{
		if(is_null($order)) {
			$order = (empty($this->order)) ? 't.id' : $this->order;
		}

		if(is_null($direction)) {
			$direction = (empty($this->direction)) ? 'ASC' : $this->direction;
		}

		if(is_null($limit)) {
			$limit = (empty($this->limit)) ? 0 : $this->limit;
		}

		if(is_null($offset)) {
			$offset = (empty($this->offset)) ? 0 : $this->offset;
		}

		$db =& $this->getDBO();
		$query =
			'SELECT
				t.id,
				tk.result_transaction_id,
				tk.created_date,
				t.buy_in,
				t.entry_fee,
				s.name AS sport_name,
				t.start_date,
				t.end_date,
				t.cancelled_flag,
				t.paid_flag,
				t.start_currency,
				t.name AS tournament_name,
				t.jackpot_flag,
				t.parent_tournament_id,
				c.name AS competition_name
			FROM
				' . $db->nameQuote('#__tournament_ticket') . ' AS tk
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.id = tk.tournament_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			ON
				t.tournament_sport_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				eg.id = t.event_group_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_competition') . ' AS c
				ON c.id = eg.tournament_competition_id
			WHERE
				user_id = ' . $db->quote($user_id) . '
			AND
				tk.refunded_flag != 1
			AND
				t.cancelled_flag != 1';

		if(!is_null($order)) {
			$query .= ' ORDER BY ' . $db->nameQuote($order);
		}

		if(!is_null($direction)) {
			$query .= ' ' . $direction;
		}

		$db->setQuery($query, $offset, $limit);
		return $db->loadObjectList();
	}

	/**
	 * Count the total number of racing tournaments.
	 *
	 * @return integer
	 */
	public function getUserTournamentCount($user_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				COUNT(tk.id)
			FROM
				' . $db->nameQuote('#__tournament_ticket') . ' AS tk
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.id = tk.tournament_id
			WHERE
				tk.user_id = ' . $db->quote($user_id) . '
			AND
				tk.refunded_flag != 1
			AND
				t.cancelled_flag != 1';

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Get a list of tournament ticket records by tournament ID.
	 *
	 * @param integer $tournament_id
	 * @return object
	 */
	public function getTournamentTicketListByTournamentID($tournament_id, $include_refunded = false)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				tournament_id,
				user_id,
				entry_fee_transaction_id,
				buy_in_transaction_id,
				result_transaction_id,
				refunded_flag,
				resulted_flag,
				created_date,
				resulted_date
			FROM
				' . $db->nameQuote( '#__tournament_ticket' ) . '
			WHERE
				tournament_id = ' . $db->quote( $tournament_id );

		if(!$include_refunded) {
			$query .= ' AND refunded_flag = 0';
		}

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Get a list of all entrants to a tournament
	 *
	 * @param integer $tournament_id
	 */
	public function getTournamentEntrantList($tournament_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				tt.user_id,
				us.username,
				tu.city
			FROM
				' . $db->nameQuote( '#__tournament_ticket' ) . ' AS tt
			INNER JOIN
				' . $db->nameQuote( '#__users' ) . ' AS us
			ON
				tt.user_id = us.id
			LEFT JOIN
				' . $db->nameQuote( '#__topbetta_user' ) . ' AS tu
			ON
				us.id = tu.user_id
			WHERE
				tt.tournament_id = ' . $db->quote($tournament_id) . '
			AND
				tt.refunded_flag != 1';

		$db->setQuery($query);
		return $db->loadObjectList('user_id');
	}

	/**
	 * Count the number of entrants in a tournament using tournament tickets.
	 *
	 * @param integer $tournament_id
	 * @return integer
	 */
	public function countTournamentEntrants($tournament_id)
	{
		$db = $this->getDBO();
		$query =
			'SELECT
				COUNT(user_id)
			FROM
				' . $db->nameQuote('#__tournament_ticket') . '
			WHERE
				tournament_id = ' . $db->quote($tournament_id) . '
			AND
				refunded_flag = 0';

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Use the number of tickets purchased for a tournament to determine the current prize pool
	 * in cents.
	 *
	 * @param integer $tournament_id
	 * @return integer
	 */
	public function calculateTournamentPrizePool($tournament_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				SUM(t.entry_fee) AS entry_fee,
				SUM(t.buy_in) AS buy_in
			FROM
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				tt.tournament_id = t.id
			WHERE
				tt.tournament_id = ' . $db->quote($tournament_id) . '
			AND
				tt.refunded_flag = 0
			GROUP BY
				tt.tournament_id';

		$db->setQuery($query);
		$data = $db->loadObject();

		return (is_null($data)) ? 0 : ($data->entry_fee + $data->buy_in);
	}

	/**
	 * Calculate the cost of a ticket by adding the entry-fee and buy-in
	 *
	 * @param integer $ticket_id
	 * @return integer
	 */
	public function getTicketCost($ticket_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				t.entry_fee + t.buy_in
			FROM
				' . $db->nameQuote('#__tournament') . ' AS t
			INNER JOIN
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			ON
				tt.tournament_id = t.id
			WHERE
				tt.id = ' . $db->quote($ticket_id);

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Get the buy-in cost of a purchased ticket.
	 *
	 * @param integer $ticket_id
	 * @return integer
	 */
	public function getTicketBuyIn($ticket_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				t.buy_in
			FROM
				' . $db->nameQuote('#__tournament') . ' AS t
			INNER JOIN
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			ON
				tt.tournament_id = t.id
			WHERE
				tt.id = ' . $db->quote($ticket_id);

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Get a list of a user's tournament tickets records.
	 *
	 * @param integer $user_id
	 * @return object
	 */
	public function getTournamentTicketsByUserID($user_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				tournament_id,
				user_id,
				entry_fee_transaction_id,
				buy_in_transaction_id,
				result_transaction_id,
				refunded_flag,
				resulted_flag,
				created_date,
				resulted_date
			FROM
				' . $db->nameQuote( '#__tournament_ticket' ) . '
			WHERE
				user_id = ' . $db->quote( $user_id );

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Calculate how much remaining currency a user has to spend by taking unresulted bets and subtracting from
	 * the current leaderboard.
	 *
	 * @param integer $tournament_id
	 * @param integer $user_id
	 * @return integer
	 */
	public function getAvailableTicketCurrency($tournament_id, $user_id)
	{
		$ticket = $this->getTournamentTicketByUserAndTournamentID($user_id, $tournament_id);
		if(is_null($ticket)) {
			return -1;
		}

		$tournament_model =& JModel::getInstance('Tournament', 'TournamentModel');
		$tournament_sport_model =& JModel::getInstance('TournamentSport', 'TournamentModel');
		$tournament = $tournament_model->getTournament($tournament_id);
		$tournament_sport = $tournament_sport_model->getTournamentSport($tournament->tournament_sport_id);

		$db =& $this->getDBO();
		$query =
			'SELECT
				SUM(IF(b.resulted_flag=0,b.bet_amount,0)) AS unresulted,
				l.currency AS current
			FROM
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			LEFT JOIN
				' . $db->nameQuote('#__tournament_bet') . ' AS b
			ON
				b.tournament_ticket_id = tt.id
			INNER JOIN
				' . $db->nameQuote('#__tournament_leaderboard') . ' AS l
			ON
				tt.tournament_id = l.tournament_id
			AND
				tt.user_id = l.user_id
			WHERE
				tt.tournament_id = ' . $db->quote($tournament_id) . '
			AND
				tt.refunded_flag <> 1
			AND
				tt.user_id = ' . $db->quote($user_id) . '
			GROUP BY
				b.tournament_ticket_id';

		$db->setQuery($query);
		$total = $db->loadObject();

		return $total->current - $total->unresulted;
	}

	/**
	 * Calculate how much currency a user has for leaderboard display by taking the starting value from tournament,
	 * subtracting resulted bet amounts and adding resulted bet winnings.
	 *
	 * @param integer $tournament_id
	 * @param integer $user_id
	 * @return integer
	 */
	public function getLeaderboardTicketCurrency($tournament_id, $user_id)
	{
		$tournament_model =& JModel::getInstance('Tournament', 'TournamentModel');
		$tournament_sport_model =& JModel::getInstance('TournamentSport', 'TournamentModel');

		$tournament = $tournament_model->getTournament($tournament_id);

		$ticket = $this->getTournamentTicketByUserAndTournamentID($user_id, $tournament_id);
		if (is_null($ticket)) {
			return -1;
		}
		
		$db =& $this->getDBO();
		$query =
			'SELECT
				SUM(bet_amount) AS bet_amount,
				SUM(win_amount) AS win_amount
			FROM
				' . $db->nameQuote('#__tournament') . ' AS t
			INNER JOIN
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			ON
				t.id = tt.tournament_id
			LEFT JOIN
				' . $db->nameQuote('#__tournament_bet') . ' AS tb
			ON
				tt.id = tb.tournament_ticket_id
			WHERE
				tournament_ticket_id = ' . $db->quote($ticket->id) . '
			AND
				tb.resulted_flag <> 0
			GROUP BY
				tournament_ticket_id';

		$db->setQuery($query);

		$data = $db->loadObject();
		
		return $tournament->start_currency - $data->bet_amount + $data->win_amount;
	}

	/**
	 * Get a list of tickets for tournaments using a particular meeting. Used for bulk
	 * refunds on abandoned meetings, so this will also load the entry_fee and buy_in
	 * from the target Tournament.
	 *
	 * @param integer $id
	 * @return array
	 */
	public function getTournamentTicketListByEventGroupID($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				tt.id,
				tt.tournament_id,
				tt.user_id,
				tt.entry_fee_transaction_id,
				tt.buy_in_transaction_id,
				tt.result_transaction_id,
				tt.refunded_flag,
				tt.resulted_flag,
				tt.created_date,
				tt.resulted_date,
				t.buy_in,
				t.entry_fee
			FROM
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.id = tt.tournament_id
			WHERE
				t.event_group_id = ' . $db->quote($id) . '
			AND
				tt.refunded_flag = 0
			AND
				tt.resulted_flag = 0';

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTournamentTicketListByRaceID($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				tt.id,
				tt.tournament_id,
				tt.user_id,
				tt.entry_fee_transaction_id,
				tt.buy_in_transaction_id,
				tt.result_transaction_id,
				tt.refunded_flag,
				tt.resulted_flag,
				tt.created_date,
				tt.resulted_date
			FROM
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			INNER JOIN
				' . $db->nameQuote('#__tournament_racing') . ' AS tr
			ON
				tt.tournament_id = tr.tournament_id
			INNER JOIN
				' . $db->nameQuote('#__meeting') . ' AS m
			ON
				m.id = tr.meeting_id
			INNER JOIN
				' . $db->nameQuote('#__race') . ' AS r
			ON
				m.id = r.meeting_id
			WHERE
				r.id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTournamentTicketListByEventID($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				tt.id,
				tt.tournament_id,
				tt.user_id,
				tt.entry_fee_transaction_id,
				tt.buy_in_transaction_id,
				tt.result_transaction_id,
				tt.refunded_flag,
				tt.resulted_flag,
				tt.created_date,
				tt.resulted_date
			FROM
				' . $db->nameQuote('#__tournament_ticket') . ' AS tt
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.id = tt.tournament_id
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				ege.event_group_id = t.event_group_id
			WHERE
				ege.event_id = ' . $db->quote($id) . '
			GROUP BY
				tt.id'
			;

		$db->setQuery($query);
		return $db->loadObjectList();
	}
	/**
	 * Get a list of all entrants to a Jackpot tournament
	 * Who got registered before 48hrs
	 *
	 * @param integer $tournament_id
	 * @param integer $hours
	 * @param integer $reminder_flag
	 * @return array
	 */
	public function getJackpotTournamentEntrantListByEventGroupID($event_group_id, $hours = 48, $reminder_flag = 1)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				tt.user_id,
				tt.tournament_id,
				t.name,
				tp.display_identifier,
				u.username,
				u.email
			FROM
					' . $db->nameQuote( '#__tournament_ticket' ) . ' AS tt
				INNER JOIN
					' . $db->nameQuote( '#__tournament' ) . ' AS t
				ON
					t.id = tt.tournament_id
				INNER JOIN
					' . $db->nameQuote( '#__users' ) . ' AS u
				ON
					tt.user_id = u.id
				INNER JOIN
					' . $db->nameQuote( '#__topbetta_user' ) . ' AS tu
				ON
					tu.user_id = tt.user_id
				LEFT JOIN
				' . $db->nameQuote('#__tournament_private') . ' AS tp
				ON
					t.id = tp.tournament_id
			WHERE
					t.event_group_id = ' . $db->quote($group_event_id) . '
				AND
					tt.refunded_flag != 1
				AND
					t.jackpot_flag = 1
				AND
					tu.email_jackpot_reminder_flag = ' . $db->quote($reminder_flag) . '
				AND
					HOUR( TIMEDIFF(t.start_date, tt.created_date) ) >= ' . $db->quote($hours);

		$db->setQuery($query);
		return $db->loadObjectList('user_id');
	}


	/**
	 * Get a list of all entrants to a Jackpot tournament
	 * Who got registered before the specified hours
	 *
	 * @param integer $tournament_id
	 * @param integer $hours
	 */
	public function getJackpotTournamentEntrantListByMeetingIDAndRegistrationHours($meeting_id, $hours)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				tt.user_id,
				tt.tournament_id,
				t.name,
				tp.display_identifier,
				u.username,
				u.email,
				tu.email_jackpot_reminder_flag
			FROM
					' . $db->nameQuote( '#__tournament_ticket' ) . ' AS tt
				INNER JOIN
					' . $db->nameQuote( '#__tournament' ) . ' AS t
				ON
					tt.tournament_id = t.id
				INNER JOIN
					' . $db->nameQuote( '#__users' ) . ' AS u
				ON
					tt.user_id = u.id
				INNER JOIN
					' . $db->nameQuote( '#__topbetta_user' ) . ' AS tu
				ON
					tu.user_id = u.id
				LEFT JOIN
				' . $db->nameQuote('#__tournament_private') . ' AS tp
				ON
					t.id = tp.tournament_id
			WHERE
					t.event_group_id = ' . $db->quote($meeting_id) . '
				AND
					tt.refunded_flag != 1
				AND
					t.jackpot_flag = 1
				AND
					HOUR( TIMEDIFF(t.start_date, tt.created_date) ) >= ' . $db->quote($hours);

		$db->setQuery($query);
		return $db->loadObjectList('user_id');
	}
	/**
	 * Refund a tournament ticket.
	 *
	 * @param integer $ticket_id
	 * @param boolean $full
	 * @return bool
	 */
	public function refundTicket($ticket_id, $full = false)
	{
		$ticket       = $this->getTournamentTicket($ticket_id);
		$cost_method  = ($full) ? 'getTicketCost' : 'getTicketBuyIn';
		$cost         = $this->$cost_method($ticket->id);

		if(!empty($cost)) {
			$user =& JFactory::getUser();
			if(!empty($user->tournament_dollars)) {
				$refund_id = $user->tournament_dollars->increment($cost, 'refund');
			} else {
				return false;
			}

			$ticket->result_transaction_id = $refund_id;
		}

		$ticket->refunded_flag = 1;
		return $this->store((array)$ticket);
	}

	/**
	 * Refund a tournament ticket.
	 *
	 * @param integer $ticket_id
	 * @param boolean $full
	 * @return bool
	 */
	public function refundTicketAdmin($tournament_dollars, $ticket_id, $full = false)
	{
		$ticket       = $this->getTournamentTicket($ticket_id);
		$cost_method  = ($full) ? 'getTicketCost' : 'getTicketBuyIn';
		$cost         = $this->$cost_method($ticket->id);

		if(!empty($cost)) {
			if(!empty($tournament_dollars)) {
				$tournament_dollars->setUserId($ticket->user_id);
				$refund_id = $tournament_dollars->increment($cost, 'refund');
			} else {
				return false;
			}

			$ticket->result_transaction_id = $refund_id;
		}

		$ticket->refunded_flag = 1;
		return $this->store((array)$ticket);
	}

	/**
	 * A super magical method which can actually refund a ticket anywhere! ZOMG!
	 *
	 * @param object 	$ticket
	 * @param bool		$full
	 * @return bool
	 */
	public function refundTicketAnywhere($ticket, $full = false)
	{
		$transaction_model =& JModel::getInstance('TournamentTransaction', 'TournamentDollarsModel');

		if($full) {
			$cost = $this->getTicketCost($ticket->id);
		} else {
			$cost = $this->getTicketBuyIn($ticket->id);
		}

		if(!empty($cost)) {
			$transaction_model->setUserId($ticket->user_id);
			$refund_id = $transaction_model->increment($cost, 'refund');

			if(!$refund_id) {
				return false;
			}

			$ticket->result_transaction_id = $refund_id;
		}

		$ticket->refunded_flag = 1;
		return $this->store((array)$ticket);
	}

	/**
	 * Set all tickets for a tournament as resulted. Scripts will flag winners, this is for the losers.
	 *
	 * @param integer $tournament_id
	 * @return bool
	 */
	public function setResultedFlagByTournamentID($tournament_id)
	{
		$db =& $this->getDBO();
		$query =
			'UPDATE
				' . $db->nameQuote('#__tournament_ticket') . '
			SET
				resulted_flag = 1
			WHERE
				tournament_id = ' . $db->quote($tournament_id);

		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Set all tickets for a tournament as resulted. Scripts will flag winners, this is for the losers.
	 *
	 * @param integer $tournament_id
	 * @return bool
	 */
	public function setWinnerAlertFlagByTournamentID($tournament_id)
	{
		$user = JFactory::getUser(); 
		$db =& $this->getDBO();
		$query =
			'UPDATE
				' . $db->nameQuote('#__tournament_ticket') . '
			SET
				winner_alert_flag = 1
			WHERE
				tournament_id = ' . $db->quote($tournament_id) . ' AND user_id=' . $user->id . ' LIMIT 1';

		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Store a tournament ticket record. Will determine whether to insert or update based on the
	 * presence of an ID.
	 *
	 * @param array $params
	 * @return bool
	 */
	public function store($params)
	{
		$db =& $this->getDBO();

		if(empty($params['id'])) {
			$result = $this->_insert($params, $db);
		} else {
			$result = $this->_update($params, $db);
		}

		return $result;
	}

	/**
	 * Insert a new tournament ticket record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _insert($params, $db = null)
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'INSERT INTO ' . $db->nameQuote('#__tournament_ticket') . ' (
				tournament_id,
				user_id,
				entry_fee_transaction_id,
				buy_in_transaction_id,
				result_transaction_id,
				refunded_flag,
				resulted_flag,
				created_date
			) VALUES (
				' . $db->quote($params['tournament_id']) . ',
				' . $db->quote($params['user_id']) . ',
				' . $db->quote($params['entry_fee_transaction_id']) . ',
				' . $db->quote($params['buy_in_transaction_id']) . ',
				' . $db->quote($params['result_transaction_id']) . ',
				' . $db->quote($params['refunded_flag']) . ',
				' . $db->quote($params['resulted_flag']) . ',
				NOW()
			)';

		$db->setQuery($query);
		return ($db->query() ? $db->insertid() : false);
	}

	/**
	 * Update an existing tournament ticket record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _update($params, $db = null)
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'UPDATE
				' . $db->nameQuote('#__tournament_ticket') . '
			SET
				tournament_id = ' . $db->quote($params['tournament_id']) . ',
				user_id = ' . $db->quote($params['user_id']) . ',
				entry_fee_transaction_id = ' . $db->quote($params['entry_fee_transaction_id']) . ',
				buy_in_transaction_id = ' . $db->quote($params['buy_in_transaction_id']) . ',
				result_transaction_id = ' . $db->quote($params['result_transaction_id']) . ',
				refunded_flag = ' . $db->quote($params['refunded_flag']) . ',
				resulted_flag = ' . $db->quote($params['resulted_flag']) . ',
				resulted_date = NOW()
			WHERE
				id = ' . $db->quote($params['id']);

		$db->setQuery($query);
		return $db->query();
	}
}
