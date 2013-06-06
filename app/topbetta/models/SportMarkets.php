<?php namespace TopBetta;

class SportMarket extends \Eloquent {

	protected $table = 'tbdb_market';
	
	/**
	 * Check if a meeting exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function marketExists($marketID){
		return SportMarket::where('external_market_id', '=', $marketID )->pluck('id');
	}

}