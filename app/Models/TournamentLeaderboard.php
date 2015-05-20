<?php
namespace TopBetta\Models;

class TournamentLeaderboard extends \Eloquent {
    protected $guarded = array();

	protected $table = 'tbdb_tournament_leaderboard';

    public static $rules = array();
	
	/**
	 * Rank users within a tournament for display.
	 *
	 * @param integer $tournament_id
	 * @return array
	 */
	public function getLeaderBoardRank($tournament, $limit = null, $onlyQualifiers = false) {

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
				tbdb_tournament_leaderboard as l1
			INNER JOIN
				tbdb_tournament_leaderboard as l2 ON l1.currency < l2.currency
			OR
				(l1.currency = l2.currency AND l1.user_id = l2.user_id)
			INNER JOIN
				tbdb_users AS u
			ON
				l1.user_id = u.id
			WHERE
				l2.tournament_id = "' . $tournament->id .'"
			AND
				l1.tournament_id = "' .$tournament->id .'"
			AND
				l1.turned_over >= "' . $tournament->start_currency . '"
			AND
				l2.turned_over >= "' . $tournament->start_currency .'"';

		$query .= '
			AND
				l1.currency > 0
			GROUP BY
				l1.user_id, l1.currency ';

		if(!$onlyQualifiers) {
			$query .=
				'UNION SELECT
					u.id,
					u.name,
					u.username,
					l1.currency,
					l1.turned_over,
					"-" as rank,
					0 as qualified
				FROM
					tbdb_tournament_leaderboard as l1
				INNER JOIN
					tbdb_tournament_leaderboard as l2
				ON
					l1.currency < l2.currency
				OR
					(l1.currency = l2.currency AND l1.user_id = l2.user_id)
				INNER JOIN
					tbdb_users AS u
				ON
					l1.user_id = u.id
				WHERE
					l2.tournament_id = "' . $tournament->id . '"
				AND
					l1.tournament_id = "' . $tournament->id . '"
				AND
					(
						l1.currency = 0
					OR
						(
							l1.turned_over < "' . $tournament->start_currency .'"
						AND
							l2.turned_over < "' . $tournament->start_currency .'"
						)
					)
				GROUP BY
					qualified,
					l1.user_id,
					l1.currency';
		}

		$query .= '
        	ORDER BY
          		qualified DESC,
          		currency DESC';

		if(isset($limit)){
			$query .= ' LIMIT 0,' . $limit;
		}

		$result = \DB::select($query);

		return $result;
	}	

	/**
	 * Get users individual rank
	 *
	 * @param integer $user_id
	 * @param object $tournament
	 * @return array
	 */
	public function getLeaderBoardRankByUserAndTournament($userId, $tournament) {

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
				tbdb_tournament_leaderboard as l1
			INNER JOIN
				tbdb_tournament_leaderboard as l2 ON l1.currency < l2.currency
			OR
				(l1.currency = l2.currency AND l1.user_id = l2.user_id)
			INNER JOIN
				tbdb_users AS u
			ON
				l1.user_id = u.id
			WHERE
				l2.tournament_id = "' . $tournament->id . '"
			AND
				l1.tournament_id = "' . $tournament->id . '"
			AND
				l1.turned_over >= "' . $tournament->start_currency . '"
			AND
				l2.turned_over >= "' . $tournament->start_currency . '"
			AND
				l1.currency > 0
			AND
				u.id = "' . $userId . '"
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
				tbdb_tournament_leaderboard as l1
			INNER JOIN
				tbdb_tournament_leaderboard as l2
			ON
				l1.currency < l2.currency
			OR
				(l1.currency = l2.currency AND l1.user_id = l2.user_id)
			INNER JOIN
				tbdb_users AS u
			ON
				l1.user_id = u.id
			WHERE
				l2.tournament_id = "' . $tournament->id . '"
			AND
				l1.tournament_id = "' . $tournament->id . '"
			AND
				(
					l1.currency = 0
				OR
					(
						l1.turned_over < "' . $tournament->start_currency . '"
					AND
						l2.turned_over < "' . $tournament->start_currency . '"
					)
				)
			AND
				u.id = "' . $userId . '"
			GROUP BY
				qualified,
				l1.user_id,
				l1.currency
			ORDER BY
				qualified DESC,
				currency DESC';

		$result = \DB::select($query);

		return $result[0];
	}

	/**
	 * Delete a record from the leaderboard when a user unregisters.
	 *
	 * @param integer $user_id
	 * @param integer $tournament_id
	 * @return boolean
	 */
	public function deleteByUserAndTournamentID($userId, $tournamentId) {

		return \DB::table('tbdb_tournament_leaderboard')
			->where('user_id', $userId)
			->where('tournament_id', $tournamentId)
			->delete();
	}

	public function tournament() {
		return $this->belongsTo('\TopBetta\Tournament', 'tournament_id');
	}

	public function scopeQualified($query) {
		return $query->where('turned_over', '>=', 'tbdb_tournament.start_currency');
	}
}