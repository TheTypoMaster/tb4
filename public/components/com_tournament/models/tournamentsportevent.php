<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
require_once 'tournament.php';

class TournamentModelTournamentSportEvent extends TournamentModelTournament
{
	/**
	 * Load a full tournament data record including joined tables
	 * (tournament, tournament_sport, tournament_event)
	 *
	 * @param integer $tournament_id
	 * @return object
	 */
	public function getTournamentSportEventByTournamentID($tournament_id = null)
	{
		$db =& $this->getDBO();
		$query =
      		'SELECT
        		id,
		        event_group_id,
		        name,
		        start_currency,
		        start_date,
		        end_date,
		        jackpot_flag,
		        buy_in,
		        entry_fee,
		        minimum_prize_pool,
		        paid_flag,
		        auto_create_flag,
		        cancelled_flag,
		        cancelled_reason,
		        private_flag,
		        closed_betting_on_first_match_flag,
		        betting_closed_date,
		        reinvest_winnings_flag,
		        bet_limit_flag,
		        entries_close,
		        status_flag,
		        created_date,
		        updated_date
		      FROM
		        ' . $db->nameQuote( '#__tournament' ) . '
		      WHERE
		        id = ' . $db->quote($tournament_id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	/**getTournamentListByTournamentSportEventID
	 *
	 * @param $event_id
	 */
	public function getTournamentListByTournamentSportEventGroupID($event_group_id = null)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
        		t.id,
		        t.event_group_id,
		        t.name,
		        t.start_currency,
		        t.start_date,
		        t.end_date,
		        t.jackpot_flag,
		        t.buy_in,
		        t.entry_fee,
		        t.minimum_prize_pool,
		        t.paid_flag,
		        t.auto_create_flag,
		        t.cancelled_flag,
		        t.cancelled_reason,
		        t.status_flag,
		        t.private_flag,
		        t.closed_betting_on_first_match_flag,
		        t.betting_closed_date,
		        t.reinvest_winnings_flag
		        t.created_date,
		        t.updated_date,
		        t.private_flag,
			FROM
		        ' . $db->nameQuote('#__tournament') . ' AS t
			WHERE
		        t.event_group_id = ' . $db->quote($event_group_id);
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**get total number of Tournament from event ID
	 *
	 * @param $event_id
	 */
	public function getTotalTournamenByEventID($event_id = null)
	{
		$db =& $this->getDBO();
		$query =
      		'SELECT
        		count(t.id)
		      FROM
		        ' . $db->nameQuote( '#__tournament' ) . ' AS t
		      INNER JOIN
		        ' . $db->nameQuote( '#__tournament_sport_event' ) . ' AS te
		      ON
		        te.tournament_id = t.id
		      WHERE
		        te.tournament_event_id = ' . $db->quote($event_id);
		$db->setQuery($query);
		return $db->loadResult();
	}
	/**
	 * Get Tournament Sport Event list By Competition ID
	 * @param unknown_type $order
	 * @param unknown_type $direction
	 * @param unknown_type $limit
	 * @param unknown_type $offset
	 */

	public function  getTournamentSportEventListByCompetitionID($competition_id=null, $order = null, $direction = null, $limit = null, $offset = null){
		if(is_null($order)) {
			$order = (empty($this->order)) ? 'te.name' : $this->order;
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

		$query ='
  			SELECT
  				tm.tournament_competition_id,
  				te.name,
  				te.start_date
			FROM
				' . $db->nameQuote( '#__tournament_match' ) . ' AS tm
			INNER JOIN
				' . $db->nameQuote( '#__tournament_event_match' ) . ' AS tem
			ON
				tm.id = tem.tournament_match_id
			INNER JOIN
				' . $db->nameQuote( '#__tournament_event' ) . ' AS te
			ON
				te.id = tem.tournament_event_id
			WHERE
				tm.tournament_competition_id =  ' . $db->quote($competition_id);

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
	 * Get Tournament Sport Event list By Sport ID
	 * @param unknown_type $order
	 * @param unknown_type $direction
	 * @param unknown_type $limit
	 * @param unknown_type $offset
	 */

	public function getTournamentSportEventListBySportID($sport_id=null, $order = null, $direction = null, $limit = null, $offset = null)
	{
		if(is_null($order)) {
			$order = (empty($this->order)) ? 'te.name' : $this->order;
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
        		te.id,
		        te.name,
		        te.start_date,
		        tse.tournament_event_id,
		        tse.closed_betting_on_first_match_flag,
		        tse.betting_closed_date,
		        tse.reinvest_winnings_flag
		      FROM
		        ' . $db->nameQuote( '#__tournament' ) . ' AS t
		      INNER JOIN
		        ' . $db->nameQuote( '#__tournament_sport_event' ) . ' AS tse
		      ON
		        tse.tournament_id = t.id
		      INNER JOIN
		        ' . $db->nameQuote( '#__tournament_event' ) . ' AS te
		      ON
		        tse.tournament_event_id = te.id';

		if($sport_id > 0){
			$query .='
	    		WHERE
		      		t.tournament_sport_id = ' . $db->quote($sport_id);
		}

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
	 * Load a list of sport tournaments which are still active and taking bets
	 *
	 * @param int $sport_id
	 * @param int $competition_id
	 * @param int $limit
	 * @param int $jackpot
	 * @return array
	 */
	public function getTournamentSportActiveList($list_params = array())
	{
		$list_params['type'] = 'sports';
		return $this->getTournamentActiveList($list_params);
	}


	/**
	 * Load a full tournament data record including joined tables (tournament, tournament_sport)
	 *
	 * @param integer $tournament_id
	 * @return object
	 */
	public function getTournamentSportsByTournamentID($tournament_id = null)
	{
		if(empty($tournament_id)) {
			$tournament_id = $this->tournament_id;
		}

		$db =& $this->getDBO();
		$query =
	      'SELECT
	        t.id,
	        t.tournament_sport_id,
	        t.parent_tournament_id,
			t.event_group_id,
	        t.name,
	        t.description,
	        t.start_currency,
	        t.start_date,
	        t.end_date,
	        t.jackpot_flag,
	        t.buy_in,
	        t.entry_fee,
	        t.minimum_prize_pool,
			t.free_credit_flag,
			t.tod_flag,
	        t.paid_flag,
	        t.auto_create_flag,
	        t.cancelled_flag,
	        t.cancelled_reason,
	        t.status_flag,
	        t.created_date,
	        t.updated_date,
	        t.private_flag,
	        s.name AS sport_name,
	        s.description AS sport_description,
	        t.reinvest_winnings_flag,
	        t.bet_limit_flag,
	        t.closed_betting_on_first_match_flag,
	        t.betting_closed_date,
	        c.name AS competition_name,
	        c.id AS tournament_competition_id,
	        eg.name AS event_group_name
	      FROM
	        ' . $db->nameQuote('#__tournament') . ' AS t
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
	      ON
	        c.id = eg.tournament_competition_id
	      WHERE
	        t.id = ' . $db->quote($tournament_id);

		$db->setQuery($query);
		return $db->loadObject();
	}
	/**
	 * Get All active tournament Sport list
	 */
 	public function getActiveTournamentSportList($private = false, $racing_flag = null)
    {
        $db =& $this->getDBO();
        $query =
          'SELECT
            s.id,
            s.name
          FROM
            ' . $db->nameQuote('#__tournament_sport') . ' AS s
          INNER JOIN
            ' . $db->nameQuote('#__tournament_competition') . ' AS c
            ON
            	c.tournament_sport_id = s.id
          INNER JOIN
          	' . $db->nameQuote('#__event_group') . ' AS eg
          	ON
          		eg.tournament_competition_id = c.id
          INNER JOIN
          	' . $db->nameQuote('#__tournament') . ' AS t
          	ON
          		t.event_group_id = eg.id
          WHERE
			t.end_date > NOW()
			';
        if (!is_null($racing_flag)) {
        	$query .='
        	AND
        		racing_flag = ' . $db->quote($racing_flag);
        }
        if ($private !== false) {
        	$query .= '
        	AND t.private_flag = ' . $db->quote($private);
        }
        $query .='
		  GROUP BY
			s.id
         	ORDER BY s.name ASC';
        $db->setQuery($query);
        return $db->loadObjectList();
    }

	/**
	 * Clone Sport Tournament Event
	 */
	public function cloneTournamentSportEvent( $clone_id, $cloned_tournament_id){
		$tournament_event = $this->getTournamentSportEventByTournamentID($cloned_tournament_id);
		$tournament_event->tournament_id = $clone_id;
		return $this->store((array)$tournament_event);
	}
}
