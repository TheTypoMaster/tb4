<?php namespace TopBetta;
class RaceMarket extends \Eloquent {

	protected $table = 'tbdb_market';
	
	/**
	 * Check if a meeting exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function marketExists($eventID, $marketTypeID){
		return RaceMarket::where('event_id', '=', $eventID)
							->where('market_type_id', '=', $marketTypeID)->pluck('id');
	}

}