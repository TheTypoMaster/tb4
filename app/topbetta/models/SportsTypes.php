<?php
namespace TopBetta;

class SportsTypes extends \Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public function getTypes($eventId) {

		$query = "SELECT DISTINCT(mt.id) AS id, mt.name AS bet_type FROM tbdb_market_type AS mt INNER JOIN tbdb_market AS m ON mt.id = m.market_type_id WHERE m.event_id = $eventId";

		$result = \DB::select($query);

		return $result;

	}

}
