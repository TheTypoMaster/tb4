<?php

class AccountTransactionTypes extends \Eloquent {
	protected $table = 'tbdb_account_transaction_types';
    protected $guarded = array();

    public static $rules = array();
    
    
    
    
    /**
     * Get transaction type id
     *
     * @param  string transaction type
     * @return integer
     */
    function getTransactionTypeId($keyword)
    {
    	return AccountTransactionTypes::where('keyword', '=', $keyword) -> pluck('id');
    }
    
    /**
     * Get transaction type record
     *
     * @param  string transaction type
     * @return integer
     */
    function getTransactionType($transactionType)
    {
    	
    	return AccountTransactionTypes::where('keyword', '=', $keyword) -> get();
    	
    	
    }
    
}