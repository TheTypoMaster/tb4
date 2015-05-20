<?php namespace TopBetta\Models;

class FreeCreditTransactionTypes extends \Eloquent {
	protected $table = 'tbdb_tournament_transaction_type';
    protected $guarded = array();

    public static $rules = array();
    
    
    
    
    /**
     * Get transaction type id
     *
     * @param  string transaction type
     * @return integer
     */
    static public function getTransactionTypeId($keyword)
    {
    	return FreeCreditTransactionTypes::where('keyword', '=', $keyword) -> pluck('id');
    }
    
    /**
     * Get transaction type record
     *
     * @param  string transaction type
     * @return integer
     */
    static public function getTransactionType($transactionType)
    {
    	return FreeCreditTransactionTypes::where('keyword', '=', $transactionType) -> get();
    }
    
}