<?php
namespace TopBetta\Models;

class TournamentRace extends \Eloquent {
	protected $table = 'tbdb_event';

	protected $guarded = array();

	public static $rules = array();

	/**
	 * Get the next race number for a tournament
	 *
	 * @param integer $id
	 * @return integer
	 */
	public function getNextRaceNumberByTournamentID($id) {

		$query = 'SELECT
				e.number
			FROM
				tbdb_event AS e
			INNER JOIN
				tbdb_event_group_event AS ege
			ON
				e.id = ege.event_id
			INNER JOIN
				tbdb_event_group AS eg
			ON
				eg.id = ege.event_group_id
			INNER JOIN
				tbdb_tournament AS t
			ON
				t.event_group_id = eg.id
			WHERE
				t.id = ' . $id . '
			AND
				e.start_date > NOW()
			ORDER BY
				e.start_date ASC
			LIMIT 1';

		$result = \DB::select($query);

		return (empty($result)) ? 1 : $result[0] -> number;

	}
	
	/**
	 * Get the race id from race number for tournament id
	 *
	 * @param integer $tournamentId
	 * @param integer $raceNumber 
	 * @return integer
	 */
	public function getRaceIdForRaceNumber($tournamentId, $raceNumber) {

		$query = 'SELECT
				e.id
			FROM
				tbdb_event AS e
			INNER JOIN
				tbdb_event_group_event AS ege
			ON
				e.id = ege.event_id
			INNER JOIN
				tbdb_event_group AS eg
			ON
				eg.id = ege.event_group_id
			INNER JOIN
				tbdb_tournament AS t
			ON
				t.event_group_id = eg.id
			WHERE
				t.id = ' . $tournamentId . '
			AND
				e.number = ' . $raceNumber;

		$result = \DB::select($query);

		return (empty($result)) ? 1 : $result[0] -> id;

	}	

	/**
	 * Get a list of races by tournament ID
	 *
	 * @param integer $id
	 * @return array
	 */
	public function getRaceListByTournamentID($id) {

		$query = 'SELECT
				e.id,
				ege.event_group_id AS meeting_id,
				e.name,
				e.start_date,
				e.paid_flag,
				e.event_status_id,
				e.class,
				e.distance,
				e.number,
				e.created_date,
				e.updated_date,
				t.id AS tournament_id
			FROM
				tbdb_event AS e
			INNER JOIN
				tbdb_event_group_event AS ege
			ON
				e.id = ege.event_id
			INNER JOIN
				tbdb_tournament AS t
			ON
				t.event_group_id = ege.event_group_id
			WHERE
				t.id = ' . $id;

		$result = \DB::select($query);

		return $result;
	}

}
