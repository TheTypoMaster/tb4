<?php namespace TopBetta;

class FreeCreditBalance extends \Eloquent {

	protected $table = 'tbdb_tournament_transaction';
	protected $guarded = array();

	public static $rules = array();

	const TYPE_FREEBETREFUND = 'freebetrefund';

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
	function newTransaction($params) {
			
		// instansiate the model
		$transaction = new TopBetta\FreeCreditBalance;
			
		// get the id for the transaction type
		$transactionTypeId = FreeCreditTransactionTypes::getTransactionTypeId($params['tournament_transaction_type']);
			
		// add the transaction data
		$transaction->recipient_id = $params['recipient_id'];
		$transaction->giver_id = $params['giver_id'];
		$transaction->session_tracking_id = $params['session_tracking_id'];
		$transaction->tournament_transaction_type_id = $transactionTypeId;
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



	//TODO:  this needs to be looked at once the legacy API is removed. Passing in $userID at this stage
	function _increment($userID, $amount, $keyword, $desc = null)
	{

		// Grab the ID for the keyword
		$transactionTypeId = TopBetta\FreeCreditTransactionTypes::getTransactionTypeId($keyword);
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
			$transactionTypeRec = TopBetta\FreeCreditTransactionTypes::getTransactionType($keyword);
			$desc = $transactionTypeRec->description;
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

		return TopBetta\FreeCreditBalance::newTransaction($params);
	}







	/**
	 * Add Tournament Dollars to a user's balance for promo code.
	 *
	 * @param int the amount in cents to add
	 * @param keyword the keyword identifying the type of transaction
	 * @param string an optional description for the transaction
	 * @return int transaction id
	 */
	function _increment_for_promo_code($amount, $keyword, $user_id, $desc = null) {
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
	function _decrement($userId, $amount, $keyword, $desc = null) {
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

	


}