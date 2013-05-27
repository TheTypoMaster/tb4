<?php namespace TopBetta;

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
    	return Bet::where('invoice_id', '=', $transactionID)->get();
    }
    
    /**
     * Check if bet exists based on IGAS
     * @param $transactionID
     * @return int
     * - ID of the bet transaction
     */
    static public function getBetExists($transactionID) {
    	return Bet::where('invoice_id', '=', $transactionID)->pluck('id');
    }
}