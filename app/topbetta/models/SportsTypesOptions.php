<?php
namespace TopBetta;

class SportsTypesOptions extends \Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public function getTypesAndOptions($eventId) {

		$query = ' SELECT mt.id AS type_id, mt.name AS bet_type, s.name AS bet_selection, sp.place_bet_dividend AS odds ';
		$query .= ' , s.bet_place_ref, s.bet_type_ref, s.external_selection_id, s.id AS selection_id ';

		$query .= ' FROM tbdb_market_type AS mt ';
		$query .= ' INNER JOIN tbdb_market AS m ON mt.id = m.market_type_id  ';
		$query .= ' INNER JOIN tbdb_selection AS s ON m.id = s.market_id ';
		$query .= ' INNER JOIN tbdb_selection_price AS sp ON s.id = sp.selection_id ';
		$query .= ' WHERE m.event_id = ' . $eventId;

		$result = \DB::select($query);

		return $result;

	}

}
