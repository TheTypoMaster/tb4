<?php
namespace TopBetta;

class SportsTypes extends \Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public function getTypes($eventId) {

		$query = "SELECT m.id AS id, mt.name AS bet_type, m.market_status as status, m.line as line, mt.id AS market_type_id, mt.ordering as ordering
					FROM tbdb_market_type AS mt 
					INNER JOIN tbdb_market AS m ON mt.id = m.market_type_id
					INNER JOIN tbdb_selection s ON s.market_id = m.id
					INNER JOIN tbdb_selection_price sp ON s.id = sp.select_id
					WHERE m.event_id = $eventId
					AND m.display_flag = '1'
					AND m.market_status NOT IN ('D', 'S')
					AND sp.win_odds > 1
					AND s.selection_status_id = '1'
					GROUP BY id
					ORDER BY -(ordering) DESC";

		$result = \DB::select($query);

		return $result;

	}

	public function getTournamentTypes($compId, $eventId) {

		$query = "SELECT DISTINCT(m.id) AS id, mt.name AS bet_type, m.market_status as status, m.line as line, mt.id AS market_type_id, mt.ordering as ordering
					FROM tbdb_market AS m
					INNER JOIN tbdb_event as e on e.id = m.event_id
					INNER JOIN tbdb_market_type AS mt ON mt.id = m.market_type_id
					INNER JOIN tbdb_event_group_market_type AS egmt ON egmt.market_type_id = mt.id
					INNER JOIN tbdb_event_group AS eg ON eg.id = egmt.event_group_id
					INNER JOIN tbdb_event_group_event AS ege ON ege.event_group_id = eg.id
					INNER JOIN tbdb_selection s ON s.market_id = m.id
					WHERE eg.id = '$compId' AND e.id = '$eventId' and m.display_flag = '1'
					AND m.market_status != 'D'
					GROUP BY id
					ORDER BY -(ordering) DESC";

		$result = \DB::select($query);

		return $result;

	}

}
