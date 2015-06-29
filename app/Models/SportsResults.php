<?php
namespace TopBetta\Models;

class SportsResults extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();

    public static function getResultsForEventId($eventId) {

		$query = "SElECT sr.id,s.name,sr.payout_flag, e.id as event_id, m.id as market_id, mt.name as market, s.id as selection_id
			FROM tbdb_event AS e
			INNER JOIN tbdb_market AS m ON m.event_id = e.id
			LEFT JOIN tbdb_market_type AS mt ON m.market_type_id = mt.id
			INNER JOIN tbdb_selection AS s ON m.id = s.market_id
			INNER JOIN tbdb_selection_result AS sr ON s.id = sr.selection_id
			WHERE e.id = $eventId";

		$result = \DB::select($query);

		return $result;

    }
}