<?php namespace TopBetta;

class FreeCreditBalance extends \Eloquent {

	protected $table = 'tbdb_tournament_transaction';
	protected $guarded = array();

	public static $rules = array();

	const TYPE_FREEBETREFUND = 'freebetrefund';
	
    // table relationships
    public function transactionType() {
		return $this->belongsTo('TopBetta\FreeCreditTransactionTypes', 'tournament_transaction_type_id');
	}
	
	public function giver() {
		return $this->belongsTo('User', 'giver_id');
	}
	
	public function recipient() {
		return $this->belongsTo('User', 'recipient_id');
	}	

	/**
	 * Get users free creit balance.
	 * @param $userID
	 * @return int
	 * - The free credit balance of the user
	 */
	static public function getFreeCreditBalance($userID) {
		return FreeCreditBalance::where('recipient_id', '=', $userID) -> sum('amount');
	}


	/**
	* Change a user’s tournament dollars
	*
	* @param  array parameters of a new transaction
	* @return int transaction id
	*/
	static public function newTransaction($params) {
			
		// Timestamp for created_date - legacy field
		$nowTime = date("Y-m-d H:i:s");
		
		// instansiate the model
		$transaction = new FreeCreditBalance;
			
		// get the id for the transaction type
		$transactionTypeId = FreeCreditTransactionTypes::getTransactionTypeId($params['tournament_transaction_type']);
			
		// add the transaction data
		$transaction->recipient_id = $params['recipient_id'];
		$transaction->giver_id = $params['giver_id'];
		$transaction->session_tracking_id = $params['session_tracking_id'];
		$transaction->tournament_transaction_type_id = $transactionTypeId;
		$transaction->amount = (int)$params['amount'];
		$transaction->notes = $params['notes'];
		$transaction->created_date = $nowTime;
			
		// save the model
		$transaction->save();
			
		// return false if save fails
		if(!$transaction->id) {
			return false;
		}
		// return the new transaction ID
		return $transaction->id;
	}



	//TODO:  this needs to be looked at once the legacy API is removed. Passing in $userID at this stage
	static public function _increment($userID, $amount, $keyword, $desc = null)
	{

		// Grab the ID for the keyword
		$transactionTypeId = FreeCreditTransactionTypes::getTransactionTypeId($keyword);
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
			$transactionTypeRec = FreeCreditTransactionTypes::getTransactionType($keyword);
			$desc = $transactionTypeRec[0]->description;
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
				'tournament_transaction_type' 	=> $keyword,
		);

		return FreeCreditBalance::newTransaction($params);
	}







	/**
	 * Add Tournament Dollars to a user's balance for promo code.
	 *
	 * @param int the amount in cents to add
	 * @param keyword the keyword identifying the type of transaction
	 * @param string an optional description for the transaction
	 * @return int transaction id
	 */
	static public function _increment_for_promo_code($amount, $keyword, $user_id, $desc = null) {
		$transactionTypeId = FreeCreditTransactionTypes::getTransactionTypeId($keyword);

		$tracking_id = -1;
		//if(PHP_SAPI != 'cli') {
		$session =& JFactory::getSession();
		$tracking_id = $session->get('sessionTrackingId');
		//}

		if(!$transactionTypeId) {
			return false;
		}

		if(null == $desc) {
			$transactionTypeRec = FreeCreditTransactionTypes::getTransactionType($keyword);
			$desc = $transactionTypeRec->description;
		}

		$params = array(
				'recipient_id' 					=> $user_id,
				'giver_id' 						=> $user_id,
				'session_tracking_id' 			=> $tracking_id,
				'amount' 						=> $amount,
				'notes' 						=> $desc,
				'tournament_transaction_type' 	=> $keyword,
		);

		returnFreeCreditBalance::newTransaction($params);
	}

	/**
	 * Deduct Tournament Dollars from a user's balance.
	 * If the requested amount is not available, an additional check will be made to determine
	 * if the user can make up the remaining amount from their Account Balance. The method will return false if both of these checks fail.
	 *
	 * @param int user id
	 * @param int transaction amount
	 * @param string transaction type keyword
	 * @param string transaction description
	 * @return int transaction id
	 */
	static public function _decrement($userId, $amount, $keyword, $desc = null) {
		$decrementAccountAmount = 0;
		$totalTournamentAmount 	= FreeCreditBalance::getFreeCreditBalance($userId);
		$transactionId 			= null;

		if($amount > $totalTournamentAmount) {
			// $accountModel = JModel::getInstance('AccountTransaction', 'PaymentModel');

			//$loginUser 	=& JFactory::getUser();
			//$giver_id 	= $loginUser->id;
			$recipient_id = $recipient_id = $userId;
			//if(null == $recipient_id) {
			//	$recipient_id = $giver_id;
			//}

			// $accountModel->setUserId($recipient_id);
			$totalAccountAmount = AccountBalance::getFreeCreditBalance($userId);

			$decrementAccountAmount =  $amount - $totalTournamentAmount;
			if($decrementAccountAmount > $totalAccountAmount) {
				return false;
			}
		}

		if($decrementAccountAmount > 0) {
			if( !AccountBalance::getTransactionTypeId($keyword)) {
				$keyword = 'tournamentdollars';
			}

			if(!AccountBalance::_decrement($decrementAccountAmount, $keyword, $desc)) {
				return false;
			}

			if(!FreeCreditBalance::_increment($decrementAccountAmount, 'purchase', 'Transferred from account balance')) {
				if(!AccountBalance::_increment($decrementAccountAmount, 'tournamentdollars', 'Failed to increase user\'s tournament dollars! Add the decrement back!')) {
					// :TODO: send email to tech!
				}
				return false;
			}
		}

		if(!$transactionId = FreeCreditBalance::_increment(-$amount, $keyword, $desc)) {
			if($decrementAccountAmount > 0) {
				if(!FreeCreditBalance::_increment(-$decrementAccountAmount, $keyword, 'Failed to decrease user\'s tournament dollars! Decrease the amount transferred from account balance')) {
					//TO DO : send email to tech!
				}

				//MC if(!$accountModel->increment( $decrementAccountAmount, 'tournamentdollars', 'Failed to decrease user\'s tournament dollars! Add the amount transferred back!')) {
				//TO DO: send email to tech!
				//MC }
			}

			return false;
		}
		return $transactionId;
	}

	public function listTransactions($userId = null, $transactionType = null, $limit = 25, $offset = false) {

		$selectQuery = "SELECT t.*, r.name as recipient, g.name as giver, tt.name as type, tt.description as description, "
		. " tk.id as ticket, tk.tournament_id, tk.buy_in_transaction_id, tk2.id as ticket2, tk2.tournament_id AS tournament_id2, tk2.entry_fee_transaction_id,"
		. " tk3.id as ticket3, tk3.tournament_id AS tournament_id3, tk3.result_transaction_id,"
		. " tourn.name as tournament, s.name as sport_name, tourn2.name as tournament2, s2.name as sport_name2,"
		. " tourn3.name as tournament3, s3.name as sport_name3, f.username as friend_username,  be.id as bet_entry_id, bw.id as bet_win_id, br.id as bet_refund_id";
		
		$selectCountQuery = "SELECT COUNT(*) AS total";
		
		$query = " FROM tbdb_tournament_transaction t"
		. " LEFT JOIN tbdb_users r ON r.id = t.recipient_id"
		. " LEFT JOIN tbdb_users g ON g.id = t.giver_id"
		. " LEFT JOIN tbdb_tournament_transaction_type tt ON tt.id = t.tournament_transaction_type_id"
		. " LEFT JOIN tbdb_tournament_ticket tk ON tk.buy_in_transaction_id = t.id"
		. " LEFT JOIN tbdb_tournament tourn ON tourn.id = tk.tournament_id"
		. " LEFT JOIN tbdb_tournament_sport s ON s.id = tourn.tournament_sport_id"
		. " LEFT JOIN tbdb_tournament_ticket tk2 ON tk2.entry_fee_transaction_id = t.id"
		. " LEFT JOIN tbdb_tournament tourn2 ON tourn2.id = tk2.tournament_id"
		. " LEFT JOIN tbdb_tournament_sport s2 ON s2.id = tourn2.tournament_sport_id"
		. " LEFT JOIN tbdb_tournament_ticket tk3 ON tk3.result_transaction_id = t.id"
		. " LEFT JOIN tbdb_tournament tourn3 ON tourn3.id = tk3.tournament_id"
		. " LEFT JOIN tbdb_tournament_sport s3 ON s3.id = tourn3.tournament_sport_id"
		. " LEFT JOIN tbdb_user_referral referral ON referral.tournament_transaction_id = t.id"
		. " LEFT JOIN tbdb_users f ON f.id = referral.friend_id"
		//for free bet
		. " LEFT JOIN tbdb_bet be ON be.bet_freebet_transaction_id = t.id"
		. " LEFT JOIN tbdb_bet bw ON bw.result_transaction_id = t.id"
		. " LEFT JOIN tbdb_bet br ON br.refund_freebet_transaction_id = t.id"
		. " WHERE t.recipient_id = " . $userId
		. " AND t.amount != 0";

		if($transactionType != null) {
			$query .= " AND tt.keyword = '" . $transactionType . "'";
		}

		$query .= " ORDER BY t.created_date DESC, t.id DESC";
		
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


}