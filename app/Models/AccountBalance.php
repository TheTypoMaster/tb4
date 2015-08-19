<?php namespace TopBetta\Models;

use Log;

class AccountBalance extends \Eloquent {
	
	protected $table = 'tbdb_account_transaction';
	protected $guarded = array();

    public static $rules = array();
    
    // table relationships
    public function transactionType() {
		return $this->belongsTo('TopBetta\Models\AccountTransactionTypes', 'account_transaction_type_id');
	}
	
	public function giver() {
		return $this->belongsTo('TopBetta\Models\UserModel', 'giver_id');
	}
	
	public function recipient() {
		return $this->belongsTo('TopBetta\Models\UserModel', 'recipient_id');
	}
    
    
        
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
    
    
 	public function listTransactions($userId = null, $transactionType = null, $limit = 25, $offset = false) {

		$selectQuery = "SELECT t.*, r.name as recipient, r.id as recipient_id, g.name as giver, g.id as giver_id, tt.name as type,"
		. " tt.description as description, tourn.id as tournament_id, tourn.name as tournament, s.name as sport_name,"
		. " tk.refunded_flag as ticket_refunded_flag, be.id as bet_entry_id, bw.id as bet_win_id, br.id as bet_refund_id";
		
		$selectCountQuery = "SELECT COUNT(*) AS total";
		
		$query = " FROM tbdb_account_transaction t"
		. " LEFT JOIN tbdb_users r ON r.id = t.recipient_id"
		. " LEFT JOIN tbdb_users g ON g.id = t.giver_id"
		. " LEFT JOIN tbdb_account_transaction_type tt ON tt.id = t.account_transaction_type_id"
		. " LEFT JOIN tbdb_tournament_ticket tk ON tk.result_transaction_id = t.id"
		. " LEFT JOIN tbdb_tournament tourn ON tk.tournament_id = tourn.id"
		. " LEFT JOIN tbdb_tournament_sport s ON s.id = tourn.tournament_sport_id"
		. " LEFT JOIN tbdb_bet be ON be.bet_transaction_id = t.id"
		. " LEFT JOIN tbdb_bet bw ON bw.result_transaction_id = t.id"
		. " LEFT JOIN tbdb_bet br ON br.refund_transaction_id = t.id"
		. $this->_buildQueryWhere($userId, $transactionType)
		. " ORDER BY t.created_date DESC, t.id DESC";	
		
		$countQuery = $selectCountQuery . $query;
		
		if ($offset) {
			$query .= ' LIMIT ' . $offset . ',' . $limit;	
		} else {
			$query .= ' LIMIT ' . $limit;
		}				
		
		// handle our normal query with results
		$fullQuery = $selectQuery . $query;
		
		$result = \DB::select($fullQuery);
		
		// handle our total count for this full query excluding page limits
		$numRows = \DB::select($countQuery);

		return array('result' => $result, 'num_rows' => $numRows[0]);					
		
 	}
 
 
	/**
	* Builds the WHERE part of a query
	*
	* @return string Part of an SQL query
	*/
	private function _buildQueryWhere($userId = null, $transactionType = null)
	{

		if(!$userId) {
			return false;
		}
		
		// Get the filter values
		//$filter_from_date	= $mainframe->getUserStateFromRequest($option.'filter_transaction_from_date', 'filter_transaction_from_date');
		//$filter_to_date		= $mainframe->getUserStateFromRequest($option.'filter_transaction_to_date', 'filter_transaction_to_date');
		
		// Prepare the WHERE clause
		$where = array();

		/*
		if( $filter_from_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_from_date, $m) )
		{
			$where[] = ' t.created_date >= FROM_UNIXTIME(' . $db->quote(strtotime($filter_from_date)) . ')';
		}
		
		if( $filter_to_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_to_date, $m) )
		{
			$where[] = ' t.created_date <= FROM_UNIXTIME(' . $db->quote(strtotime($filter_to_date)) . ')';
		}
		*/ 
		
		switch ($transactionType) {
			case 'deposits_withdrawals' :
				$where[] = ' tt.keyword IN ("paypaldeposit", "ewaydeposit", "bankdeposit", "bpaydeposit","moneybookersdeposit")';
				break;
			case 'bets' :
				$where[] = ' tt.keyword IN ("betentry", "betwin", "betrefund")';
				break;
			case 'tournaments' :
				$where[] = ' tt.keyword IN ("tournamentdollars", "tournamentwin", "entry", "buyin")';
				break;
		}

        $where[] = "t.amount != '0'";
		$where[] = 't.recipient_id = "' . $userId . '"';
		$where[] = '(tourn.parent_tournament_id IS NULL OR tourn.parent_tournament_id <= 0)';
		$where[] = '(br.bet_result_status_id NOT IN (6,7) OR br.bet_result_status_id IS NULL)';
		
		//$where[] = '(tk.refunded_flag IS NULL OR tk.refunded_flag = 0)';

		// return the WHERE clause
		return (count($where)) ? ' WHERE '.implode(' AND ', $where) : '';
	} 
   
    /**
     * change a user's balance
     *
     * @param  array increment params
     * @return int transaction id
     */
    static private function newTransaction($params)
    {
    	// Timestamp for created_date - legacy field
    	$nowTime = date("Y-m-d H:i:s");
    	
    	// instansiate the model
    	$transaction = new AccountBalance;
    	
    	// get the id for the transaction type
    	$transactionTypeId = AccountTransactionTypes::getTransactionTypeId($params['account_transaction_type']);
    	
    	// add the transaction data
    	$transaction->recipient_id = $params['recipient_id'];
    	$transaction->giver_id = $params['giver_id'];
    	$transaction->session_tracking_id = $params['session_tracking_id'];
    	$transaction->account_transaction_type_id = $transactionTypeId;
    	$transaction->amount = (int)$params['amount'];
    	$transaction->notes = $params['notes'];
    	$transaction->created_date = $nowTime;
    	
     	// save the model
    	$transaction->save();
     	
    	// debugging
    	$o = print_r($transaction,true);
    	Log::debug("AccountBalance newTransaction: About to save transaction:  Object:$o");
    	
    	// return false if save fails
    	if(!$transaction->id) {
    		Log::debug("AccountBalance newTransaction: Save Failed: ID:$transaction->id.");
    		return false;
    	}
    	// return the new transaction ID
     	Log::debug("AccountBalance newTransaction: Saved: ID:$transaction->id.");

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
    		
    		Log::debug("AccountBalance _increment: Desc: $t.");
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
    	return AccountBalance::_increment(-$amount, $keyword, $desc);
    }
    
   
    
    /**
     * Validate if a deposit type is valid
     *
     * @param 	string		the keyword of deposit type
     * @return	boolean		true on valid deposit type
     */
    public function validateTransactionType($transactionType)
    {
    	return (bool)AccountTransactionTypes::getTransactionType($transactionType);
    }
    
    
    
    
}
