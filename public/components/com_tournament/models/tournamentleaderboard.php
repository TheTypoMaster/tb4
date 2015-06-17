<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelTournamentLeaderboard extends JModel
{
	/**
	 * Rank users within a tournament for display.
	 *
	 * @param integer $tournament_id
	 * @return array
	 */
	public function getLeaderBoardRank($tournament, $limit = null, $only_qualifiers = false) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				u.id,
				u.name,
				u.username,
				u.email,
				l1.currency,
				l1.turned_over,
				t.topup_currency,
				t.rebuy_currency,
				t.start_currency,
				1 as qualified
			FROM
				' . $db->nameQuote('#__tournament_leaderboard') . ' as l1
			INNER JOIN
				' . $db->nameQuote('#__users') . ' AS u
			ON
				l1.user_id = u.id
            INNER JOIN
                '. $db->nameQuote('#__tournament') .' AS t
            ON
                t.id = l1.tournament_id
            INNER JOIN
               '. $db->nameQuote('#__tournament_ticket') . ' AS tt
            ON
                tt.user_id = u.id AND tt.tournament_id = '. $db->quote($tournament->id) .'
            LEFT JOIN (
                tbdb_tournament_ticket_buyin_history tbh_rebuy
                INNER JOIN tbdb_tournament_buyin_type tbt_rebuy ON tbh_rebuy.tournament_buyin_type_id = tbt_rebuy.id AND tbt_rebuy.keyword=' . $db->quote("rebuy") . '
              ) ON tbh_rebuy.tournament_ticket_id = tt.id
            LEFT JOIN (
                tbdb_tournament_ticket_buyin_history tbh_topup
                INNER JOIN tbdb_tournament_buyin_type tbt_topup ON tbh_topup.tournament_buyin_type_id = tbt_topup.id AND tbt_topup.keyword=' . $db->quote("topup") . '
              ) ON tbh_topup.tournament_ticket_id = tt.id
			WHERE
				l1.tournament_id = ' . $db->quote($tournament->id) . '
            AND
                l1.currency > 0
            GROUP BY
                l1.id
			HAVING
				 l1.turned_over >= t.start_currency + IFNULL(COUNT(tbh_rebuy.id), 0) * t.rebuy_currency + IFNULL(COUNT(tbh_topup.id), 0) * t.topup_currency
        ';

		if(!$only_qualifiers) {
			$query .=
				'UNION
				SELECT
				u.id,
				u.name,
				u.username,
				u.email,
				l1.currency,
				l1.turned_over,
				t.topup_currency,
				t.rebuy_currency,
				t.start_currency,
				0 as qualified
                FROM
                    ' . $db->nameQuote('#__tournament_leaderboard') . ' as l1
                INNER JOIN
                    ' . $db->nameQuote('#__users') . ' AS u
                ON
                    l1.user_id = u.id
                INNER JOIN
                    '. $db->nameQuote('#__tournament') .' AS t
                ON
                    t.id = l1.tournament_id
                INNER JOIN
                   '. $db->nameQuote('#__tournament_ticket') . ' AS tt
                ON
                    tt.user_id = u.id AND tt.tournament_id = '. $db->quote($tournament->id) .'
                LEFT JOIN (
                    tbdb_tournament_ticket_buyin_history tbh_rebuy
                    INNER JOIN tbdb_tournament_buyin_type tbt_rebuy ON tbh_rebuy.tournament_buyin_type_id = tbt_rebuy.id AND tbt_rebuy.keyword=' . $db->quote("rebuy") . '
                  ) ON tbh_rebuy.tournament_ticket_id = tt.id
                LEFT JOIN (
                    tbdb_tournament_ticket_buyin_history tbh_topup
                    INNER JOIN tbdb_tournament_buyin_type tbt_topup ON tbh_topup.tournament_buyin_type_id = tbt_topup.id AND tbt_topup.keyword=' . $db->quote("topup") . '
                  ) ON tbh_topup.tournament_ticket_id = tt.id
                WHERE
                    l1.tournament_id = ' . $db->quote($tournament->id) . '
                GROUP BY
                    l1.id
                HAVING
                     l1.currency = 0 OR l1.turned_over < t.start_currency + IFNULL(COUNT(tbh_rebuy.id), 0) * t.rebuy_currency + IFNULL(COUNT(tbh_topup.id), 0) * t.topup_currency';
		}

		$query .= '
        	ORDER BY
          		qualified DESC,
          		currency DESC';

		if(isset($limit)){
			$query .= ' LIMIT 0,' . $limit;
		}

		$db->setQuery($query);
		$leaderboard = $db->loadObjectList();

        //get the ranks for the leaderboard
        $position = 1;
        $amount = 0;
        $count = 0;
        foreach($leaderboard as &$record)
        {
            if($record->qualified) {
                if($record->currency < $amount) {
                    $position += $count;
                    $count = 0;
                }

                $amount = $record->currency;
                $record->rank = $position;
                $count++;
            } else {
                $record->rank = '-';
            }
        }

        return $leaderboard;
	}

	/**
	 * Get users individual rank
	 *
	 * @param integer $user_id
	 * @param object $tournament
	 * @return array
	 */
	public function getLeaderBoardRankByUserAndTournament($user_id, $tournament) {
		$db =& $this->getDBO();

		$query =
			'SELECT
				u.id,
				u.name,
				u.username,
				l1.currency,
				l1.turned_over,
				count(l2.currency) as rank,
				1 as qualified
			FROM
				' . $db->nameQuote('#__tournament_leaderboard') . ' as l1
			INNER JOIN
				' . $db->nameQuote('#__tournament_leaderboard') . ' as l2 ON l1.currency < l2.currency
			OR
				(l1.currency = l2.currency AND l1.user_id = l2.user_id)
			INNER JOIN
				' . $db->nameQuote('#__users') . ' AS u
			ON
				l1.user_id = u.id
			WHERE
				l2.tournament_id = ' . $db->quote($tournament->id) . '
			AND
				l1.tournament_id = ' . $db->quote($tournament->id) . '
			AND
				l1.turned_over >= ' . $db->quote($tournament->start_currency) . '
			AND
				l2.turned_over >= ' . $db->quote($tournament->start_currency) . '
			AND
				l1.currency > 0
			AND
				u.id = ' . $db->quote($user_id) . '
			GROUP BY
				l1.user_id,
				l1.currency
			UNION SELECT
				u.id,
				u.name,
				u.username,
				l1.currency,
				l1.turned_over,
				"-" as rank,
				0 as qualified
			FROM
				' . $db->nameQuote('#__tournament_leaderboard') . ' as l1
			INNER JOIN
				' . $db->nameQuote('#__tournament_leaderboard') . ' as l2
			ON
				l1.currency < l2.currency
			OR
				(l1.currency = l2.currency AND l1.user_id = l2.user_id)
			INNER JOIN
				' . $db->nameQuote('#__users') . ' AS u
			ON
				l1.user_id = u.id
			WHERE
				l2.tournament_id = ' . $db->quote($tournament->id) . '
			AND
				l1.tournament_id = ' . $db->quote($tournament->id) . '
			AND
				(
					l1.currency = 0
				OR
					(
						l1.turned_over < ' . $db->quote($tournament->start_currency) . '
					AND
						l2.turned_over < ' . $db->quote($tournament->start_currency) . '
					)
				)
			AND
				u.id = ' . $db->quote($user_id) . '
			GROUP BY
				qualified,
				l1.user_id,
				l1.currency
			ORDER BY
				qualified DESC,
				currency DESC';

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Update the leaderboard for a user and tournament.
	 *
	 * @param integer $user_id
	 * @param integer $tournament_id
	 * @param integer $currency
	 * @return bool
	 */
	public function updateLeaderboardByUserAndTournamentID($user_id, $tournament_id, $currency) {
		$db =& $this->getDBO();
		$query =
			'UPDATE
				' . $db->nameQuote('#__tournament_leaderboard') . '
			SET
				currency = ' . $db->quote($currency) . ',
				updated_date = NOW()
			WHERE
				user_id = ' . $db->quote($user_id) . '
			AND
				tournament_id = ' . $db->quote($tournament_id);

		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Add to the amount a user has turned over for a tournament.
	 *
	 * @param integer $user_id
	 * @param integer $tournament_id
	 * @param integer $add_currency
	 * @return bool
	 */
	public function addTurnedOverByUserAndTournamentID($user_id, $tournament_id, $add_currency) {
		$db =& $this->getDBO();
		$query =
			'UPDATE
				' . $db->nameQuote('#__tournament_leaderboard') . '
			SET
				turned_over = turned_over + ' . $db->quote($add_currency) . '
			WHERE
				user_id = ' . $db->quote($user_id) . '
			AND
				tournament_id = ' . $db->quote($tournament_id);

		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Deduct from a user's current leaderboard
	 *
	 * @param integer $user_id
	 * @param integer $tournament_id
	 * @param integer $currency
	 * @return bool
	 */
	public function deductCurrencyByUserAndTournamentID($user_id, $tournament_id, $currency) {
		$db =& $this->getDBO();
		$query =
			'UPDATE
				' . $db->nameQuote('#__tournament_leaderboard') . '
			SET
				currency = currency - ' . $db->quote($currency) . '
			WHERE
				user_id = ' . $db->quote($user_id) . '
			AND
				tournament_id = ' . $db->quote($tournament_id);

		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Get the current amount a user has turned over in a tournament.
	 *
	 * @param integer $user_id
	 * @param integer $tournament_id
	 * @return bool
	 */
	public function getTurnedOverByUserAndTournamentID($user_id, $tournament_id) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				turned_over
			FROM
				' . $db->nameQuote('#__tournament_leaderboard') . '
			WHERE
				user_id = ' . $db->quote($user_id) . '
			AND
				tournament_id = ' . $db->quote($tournament_id);

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Get the current amount a user has available in a tournament.
	 *
	 * @deprecated Currency on the leaderboard is now based on last race result, not current currency.
	 *
	 * @param integer $user_id
	 * @param integer $tournament_id
	 * @return bool
	 */
	public function getCurrencyByUserAndTournamentID($user_id, $tournament_id) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				currency
			FROM
				' . $db->nameQuote('#__tournament_leaderboard') . '
			WHERE
				user_id = ' . $db->quote($user_id) . '
			AND
				tournament_id = ' . $db->quote($tournament_id);

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Delete a record from the leaderboard when a user unregisters.
	 *
	 * @param integer $user_id
	 * @param integer $tournament_id
	 * @return boolean
	 */
	public function deleteByUserAndTournamentID($user_id, $tournament_id) {
		$db =& $this->getDBO();
		$query =
			'DELETE FROM
				' . $db->nameQuote('#__tournament_leaderboard') . '
			WHERE
				user_id = ' . $db->quote($user_id) . '
			AND
				tournament_id = ' . $db->quote($tournament_id);

		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Save a leaderboard entry.
	 *
	 * @param array $params
	 * @return mixed
	 */
	public function store($params) {
		$db =& $this->getDBO();

		if(empty($params['id'])) {
			$result = $this->_insert($params, $db);
		} else {
			$result = $this->_update($params, $db);
		}

		return $result;
	}

	/**
	 * Insert a leaderboard record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return integer
	 */
	private function _insert($params, $db = null) {
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'INSERT INTO ' . $db->nameQuote('#__tournament_leaderboard') . ' (
				tournament_id,
				user_id,
				currency,
				turned_over,
				balance_to_turnover
			) VALUES (
				' . $db->quote($params['tournament_id']) . ',
				' . $db->quote($params['user_id']) . ',
				' . $db->quote($params['currency']) . ',
				' . $db->quote($params['turned_over']) . ',
				' . $db->quote($params['balance_to_turnover']) . '
			)';

		$db->setQuery($query);
		$db->query();

		return $db->insertId();
	}

	/**
	 * Update a leaderboard record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _update($params, $db = null) {
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'UPDATE
				' . $db->nameQuote('#__tournament_leaderboard') . '
			SET
				tournament_id = ' . $db->quote($params['tournament_id']) . ',
				user_id = ' . $db->quote($params['user_id']) . ',
				currency = ' . $db->quote($params['currency']) . ',
				turned_over = ' . $db->quote($params['turned_over']) . ',
				updated_date = NOW()
			WHERE
				id = ' . $db->quote($params['id']);

		$db->setQuery($query);
		return $db->query();
	}
}