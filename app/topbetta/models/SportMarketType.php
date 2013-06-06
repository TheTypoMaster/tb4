<?php namespace TopBetta;

class SportMarketType extends \Eloquent {

	protected $table = 'tbdb_market_type';
	
	/**
	 * Check if a market type exists.
	 *
	 * @return Integer
	 * - The record ID if a record is found
	 */
	static public function marketTypeExists($betType) {
		return SportMarketType::where('name', '=', $betType) -> pluck('id');
	}

}