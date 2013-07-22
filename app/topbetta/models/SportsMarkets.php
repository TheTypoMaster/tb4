<?php namespace TopBetta;

class SportsMarket extends \Eloquent {

	protected $table = 'tbdb_market';
	
	/**
	 * Check if a meeting exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function marketExists($marketID){
		return SportsMarket::where('external_market_id', '=', $marketID )->pluck('id');
	}
	
	static public function sportMarketExists($marketID, $eventID){
		return SportsMarket::where('external_market_id', '=', $marketID )
							->where('event_id','=',$eventID)
							->pluck('id');
	}

}