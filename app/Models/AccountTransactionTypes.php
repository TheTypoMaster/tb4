<?php namespace TopBetta\Models;

class AccountTransactionTypes extends \Eloquent {
	protected $table = 'tbdb_account_transaction_type';
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
    	return AccountTransactionTypes::where('keyword', '=', $keyword) -> value('id');
    }
    
    /**
     * Get transaction type record
     *
     * @param  string transaction type
     * @return integer
     */
    static public function getTransactionType($transactionType)
    {
    	return AccountTransactionTypes::where('keyword', '=', $transactionType) -> get();
    }
    
}