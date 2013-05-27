<?php
namespace TopBetta;

class TournamentTicket extends \Eloquent {
	protected $table = 'tbdb_tournament_ticket';

	protected $guarded = array();

	public static $rules = array();

	/**
	 * Count the number of entrants in a tournament using tournament tickets.
	 *
	 * @param integer $tournament_id
	 * @return integer
	 */
	public static function countTournamentEntrants($tournamentId) {

		return TournamentTicket::where('tournament_id', '=', $tournamentId) -> where('refunded_flag', '=', 0) -> count();

	}

	/**
	 * Get a single tournament ticket record by tournament and user ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public static function getTournamentTicketByUserAndTournamentID($userId, $tournamentId, $includeRefunded = false) {

		$query = 'SELECT
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
				tbdb_tournament_ticket
			WHERE
				user_id = ' . $userId . '
			AND
				tournament_id = ' . $tournamentId;

		if (!$includeRefunded) {
			$query .= ' AND refunded_flag != 1';
		}

		$result = \DB::select($query);

		return $result;
	}

	/**
	 * Get a list of all entrants to a tournament
	 *
	 * @param integer $tournament_id
	 */
	public function getTournamentEntrantList($tournamentId) {

		$query = 'SELECT
				tt.user_id,
				us.username,
				tu.city
			FROM
				tbdb_tournament_ticket AS tt
			INNER JOIN
				tbdb_users AS us
			ON
				tt.user_id = us.id
			LEFT JOIN
				tbdb_topbetta_user AS tu
			ON
				us.id = tu.user_id
			WHERE
				tt.tournament_id = ' . $tournamentId . '
			AND
				tt.refunded_flag != 1';

		$result = \DB::select($query);

		return $result;
		//return $db -> loadObjectList('user_id');
	}

}
