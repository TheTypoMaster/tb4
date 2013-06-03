<?php
namespace TopBetta;

class Bet extends \Eloquent {

	protected $table = 'tbdb_bet';
	protected $guarded = array();

	public static $rules = array();

	/**
	 * Get bet transaction details.
	 * @param $transactionID
	 * @return int
	 * - The details of a bet transaction
	 */
	static public function getBetDetails($transactionID) {
		return Bet::where('invoice_id', '=', $transactionID) -> get();
	}

	/**
	 * Check if bet exists based on IGAS
	 * @param $transactionID
	 * @return int
	 * - ID of the bet transaction
	 */
	static public function getBetExists($transactionID) {
		return Bet::where('invoice_id', '=', $transactionID) -> pluck('id');
	}

	/**
	 * Get the data required to place a legacy bet from just the selection id
	 *
	 * @param $selectionId int
	 * @return array
	 */
	public function getLegacyBetData($selectionId) {
		return \DB::table('tbdb_selection AS s') 
		-> join('tbdb_market AS m', 's.market_id', '=', 'm.id') 
		-> join('tbdb_event_group_event AS e', 'm.event_id', '=', 'e.event_id')  
		-> where('s.id', '=', $selectionId) 
		-> select('s.market_id', 's.wager_id', 's.barrier', 's.number', 'm.event_id AS race_id', 'e.event_group_id AS meeting_id') -> get();

	}

}
