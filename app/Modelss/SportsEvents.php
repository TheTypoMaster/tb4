<?php
namespace TopBetta;

class SportsEvents extends \Eloquent {

	protected $table = 'tbdb_event_group_event';

	protected $guarded = array();

	public static $rules = array();

	static public function eventExists($eventId) {
		$query = "SELECT e.id FROM tbdb_event AS e
				INNER JOIN tbdb_event_group_event AS ege ON e.id = ege.event_id
				INNER JOIN tbdb_event_group AS eg ON ege.event_group_id = eg.id
				WHERE e.external_event_id = '$eventId'";

		$result = \DB::select($query);

		return $result;


		// return SportsEvents::where('external_event_id', '=', $eventId) -> pluck('id');

	}

	public function getEvents($limit = 0, $cid = 0, $date = NULL, $tournamentFlag = false) {

		//get the comp id if not set
		$compQuery = ($cid != 0) ? ' AND ege.event_group_id = "' . $cid . '"' : false;

		if (!$compQuery) {
			return array();
		}

		//add limit if set
		$limitQuery = ($limit) ? ' LIMIT ' . $limit : '';

		$dateQuery = $this -> mkDateQuery($date, 'e.start_date');

		//get events/matches
		$query = ' SELECT e.id AS id, e.start_date AS event_start_time, sp.win_odds, sp.override_odds, sp.override_type ';
		$query .= ', e.name AS event_name, e.event_id AS ext_event_id ';
		//$query.= ', ege.event_group_id AS compID, AS compName ';
		$query .= ' FROM tbdb_event AS e ';
		$query .= ' INNER JOIN tbdb_market AS m ON m.event_id = e.id';
		$query.= ' INNER JOIN tbdb_selection s ON s.market_id = m.id';
		$query.= ' INNER JOIN tbdb_selection_price sp ON s.id = sp.selection_id';
		if ($cid) { $query .= ' INNER JOIN tbdb_event_group_event AS ege ON e.id = ege.event_id ';
		}
		// TODO: is this actually needed?

		$query .= " WHERE e.display_flag = '1' ";
		$query .= $tournamentFlag ? "" : $dateQuery;
		$query .= " AND m.market_status NOT IN ('D', 'S') ";
		$query .= " AND ((sp.win_odds > 1 AND sp.override_type IS NULL) OR (sp.override_odds > 1 AND sp.override_type = 'price') OR (sp.override_odds * sp.win_odds > 1 AND sp.override_type='percentage'))";
		$query .= " AND s.selection_status_id = '1'";
		$query .= $compQuery;
		$query .= ' GROUP BY id';
		$query .= ' ORDER BY e.start_date ASC ';
		$query .= $limitQuery;

		$result = \DB::select($query);

        return $result;
	}

	private function mkDateQuery($date = NULL, $time_field) {
		if ($date && date('Y-m-d') != $date) {
			if (strtotime($date) < time()) {
				//date is in the past >> returns just on that date
				$dateQuery = ' AND ' . $time_field . ' LIKE "' . $date . '%" ';
			} else {
				//date is in the future >> returns from date to future
				$dateQuery = ' AND UNIX_TIMESTAMP(' . $time_field . ') > ' . strtotime($date);
			}
		} else {
			//no date or date is today >> returns from now to future
			$dateQuery = ' AND UNIX_TIMESTAMP(' . $time_field . ') > ' . time();
		}
		return $dateQuery;
	}

	public static function getNextEventsToJump($limit = 25, $sportId = false) {

		$query = "select ege.event_group_id AS comp_id, eg.name AS comp_name, e.*, ts.name AS sport_name, sp.win_odds, sp.override_odds, sp.override_type
		FROM tbdb_event AS e
		INNER JOIN tbdb_event_group_event AS ege ON e.id = ege.event_id
		INNER JOIN tbdb_event_group AS eg ON ege.event_group_id = eg.id
		INNER JOIN tbdb_tournament_sport AS ts ON ts.id = eg.sport_id
		INNER JOIN tbdb_market AS m ON m.event_id = e.id
		INNER JOIN tbdb_selection s ON s.market_id = m.id
		INNER JOIN tbdb_selection_price sp ON s.id = sp.selection_id
		WHERE e.start_date > NOW()
		AND eg.type_code IS NULL
		AND e.display_flag = '1'
		AND m.market_status NOT IN ('D', 'S')
	 	AND ((sp.win_odds > 1 AND sp.override_type IS NULL) OR (sp.override_odds > 1 AND sp.override_type = 'price') OR (sp.override_odds * sp.win_odds > 1 AND sp.override_type='percentage'))
		AND s.selection_status_id = '1' ";
		
		if ($sportId) {
			$query .= "AND eg.sport_id = $sportId";
		}

		$query .= " GROUP BY e.id ";
		$query .= " ORDER BY e.start_date ASC
		LIMIT $limit";

		$result = \DB::select($query);

        return array_filter($result, function($value) {
            return \App::make('TopBetta\Services\Betting\SelectionService')->calculatePrice($value->win_odds, $value->override_odds, $value->override_type) > 1;
        });

	}

}
