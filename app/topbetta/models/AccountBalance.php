<?php namespace TopBetta;

class AccountBalance extends \Eloquent {
	
	protected $table = 'tbdb_account_transaction';
	protected $guarded = array();

    public static $rules = array();
    
    // table relationships
    
    
    
        
    const TYPE_BETWIN = 'betwin',
    TYPE_BETREFUND = 'betrefund',
    TYPE_BETENTRY = 'betentry';
    
    /**
     * Get users account balance.
     * @param $userID
     * @return int
     * - The account balance of the user
     */
    static public function getAccountBalance($userID) {
    	return AccountBalance::where('recipient_id', '=', $userID) -> sum('amount');
    }
    
    
   
    /**
     * change a user's balance
     *
     * @param  array increment params
     * @return int transaction id
     */
    private function newTransaction($params)
    {
    	// instansiate the model
    	$transaction = new TopBetta\AccountBalance;
    	
    	// add the transaction data
    	$transaction->recipient_id = $params['recipient_id'];
    	$transaction->giver_id = $params['giver_id'];
    	$transaction->session_tracking_id = $params['session_tracking_id'];
    	$transaction->account_transaction_type = $params['account_transaction_type'];
    	$transaction->amount = (int)$params['amount'];
    	$transaction->notes = $params['notes'];
    	
    	// save the model
    	$transaction->save();
    	
    	// return false if save fails
    	if($transaction->id) {
    		return false;
    	}
    	// return the new transaction ID
    	return $transaction->id;
    }
    
    /**
     * Add to a user's balance
     *
     * @param int transaction amount
     * @param keyword transaction type keyword
     * @param string transaction description
     * @return int transaction id
     */

    //TODO:  this needs to be looked at once the legacy API is removed. Passing in $userID at this stage
    static public function _increment($userID, $amount, $keyword, $desc = null)
    {
    	
    	// Grab the ID for the keyword
    	$transactionTypeId = AccountTransactionTypes::getTransactionTypeId($keyword);
       	$tracking_id = -1;
     	
      	// TODO: Changed to cater for laravel sessions
/*     	if(PHP_SAPI != 'cli') {
    		$session =& JFactory::getSession();
    		$tracking_id = $session->get('sessionTrackingId');
    	} */
    
    	if(!$transactionTypeId) {
    		return false;
    	}
    
    	if(null == $desc) {
    		$transactionTypeRec = AccountTransactionTypes::getTransactionType($keyword)->toArray();
    		
    		$t = print_r($transactionTypeRec,true);
    		
    		LogHelper::l("AccountBalance Desc: $t.");
    		$desc = $transactionTypeRec[0]['description'];
    	}
    
    	$giver_id = -1;
    	
    	
/*     	if(PHP_SAPI != 'cli') {
   		//TODO: userID to be passed in?
    		$loginUser =& JFactory::getUser();
     		$giver_id = $loginUser->id;
    	} */
    
    	$recipient_id = $userID;
    	if(null == $recipient_id) {
    		$recipient_id = $giver_id;
    	}
    
    	$params = array(
    			'recipient_id' 				=> $recipient_id,
    			'giver_id' 					=> $giver_id,
    			'session_tracking_id' 		=> $tracking_id,
    			'amount' 					=> $amount,
    			'notes' 					=> $desc,
    			'account_transaction_type' 	=> $keyword,
    	);
    
    	return AccountBalance::newTransaction($params);
    }
    
    /**
     * Deduct from a user's balance
     *
     * @param int transaction amount
     * @param string transaction type keyword
     * @param string transaction description
     * @return int transaction id
     */
    static public function _decrement($amount, $keyword, $desc = null)
    {
    	return TopBetta\AccountBalance::_increment(-$amount, $keyword, $desc);
    }
    
   
    
    /**
     * Validate if a deposit type is valid
     *
     * @param 	string		the keyword of deposit type
     * @return	boolean		true on valid deposit type
     */
    public function validateTransactionType($transactionType)
    {
    	return (bool)TopBetta\AccountTransactionTypes::getTransactionType($transactionType);
    }
    
    
    
    
}