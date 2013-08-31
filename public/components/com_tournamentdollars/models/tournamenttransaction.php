<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: payment.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage tournamentdollars
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

// :TODO: indentation and bracing are fixed but the variables here need to be converted from studly caps to underscores

class TournamentdollarsModelTournamenttransaction extends JModel
{
	/**
	 * Current user ID when not operating in injected mode
	 *
	 * @var int user_id
	 */
	public $user_id = null;

	/**
	 * Loaded transaction records
	 *
	 * @var array
	 */
	private $_transactions = null;

	/**
	 * Total number of requests
	 *
	 * @var int
	 */
	private $_total = null;

	/**
	 * A pagination object
	 *
	 * :TODO: This should not be here, please move it to the controller and view.
	 *
	 * @var JPagination
	 */
	private $_pagination = null;

	const TYPE_FREEBETREFUND = 'freebetrefund';

	/**
	 * Constructor
	 *
	 * @return void
	 */
	function __construct() {
		global $mainframe, $option;

		parent::__construct();

		// Get the pagination request variables if available
		if($mainframe) {
			$this->setState('limit', $mainframe->getUserStateFromRequest('com_tournamentdollars.limit', 'limit', 50, 'int'));
			$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
		}
	}

	/**
	* Get a single tournament dollars transaction record.
	*
	* @param integer $id
	* @return object
	*/
	function getTournamentTransaction($id) {
	    $db =& $this->getDBO();
	    $query =
	      'SELECT
	        id,
	        recipient_id,
	        giver_id,
	        session_tracking_id,
	        tournament_transaction_type_id,
	        amount,
	        notes,
	        created_date
	      FROM
	        ' . $db->nameQuote('#__tournament_transaction') . '
	      WHERE
	        id = ' . $db->quote($id);

	    $db->setQuery($query);
	    return $db->loadObject();
	}

	/**
	 * Get a pagination object
	 *
	 * @return pagination object
	 */
	function getPagination() {
		if (empty($this->_pagination)) {
			// Import the pagination library
			jimport('joomla.html.pagination');

			// Prepare the pagination values
			$total 		= $this->getTotalPage();
			$limitstart = $this->getState('limitstart');
			$limit 		= $this->getState('limit');

			// Create the pagination object
			$this->_pagination = new JPagination($total,$limitstart,$limit);
		}
		return $this->_pagination;
	}

	/**
	 * Get number of requests
	 *
	 * @return integer
	 */
	function getTotalPage() {
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * change a user’s trounament dollars
	 *
	 * @param  array parameters of a new transaction
	 * @return int transaction id
	 */
	function newTransaction($params) {
		$db =& Jfactory::getDBO();
		$transactionId = null;

		$recipientId 		= $db->quote($params['recipient_id']);
		$giverId 			= $db->quote($params['giver_id']);
		$sessionTrackingId 	= $db->quote($params['session_tracking_id']);
		$amount 			= $db->quote((int)$params['amount']);
		$notes 				= $db->quote($params['notes']);

		$transactionTypeId = $db->quote($this->getTransactionTypeId($params['tournament_transaction_type']));

		$table = $db->nameQuote('#__tournament_transaction');
		$insertQuery =
			"INSERT INTO $table (
				recipient_id,
				giver_id,
				session_tracking_id,
				tournament_transaction_type_id,
				amount,
				notes,
				created_date
			) VALUES (
				$recipientId,
				$giverId,
				$sessionTrackingId,
				$transactionTypeId,
				$amount,
				$notes,
				now()
			)";

		$db->setQuery($insertQuery);
		if(!$db->query()) {
			return false;
		}

		return $db->insertid();
	}

	/**
	 * Add Tournament Dollars to a user's balance.
	 *
	 * @param int the amount in cents to add
	 * @param keyword the keyword identifying the type of transaction
	 * @param string an optional description for the transaction
	 * @return int transaction id
	 */
	function increment($amount, $keyword, $desc = null, $user_id = null) {
		$transactionTypeId = $this->getTransactionTypeId($keyword);

		$tracking_id = -1;
		if(PHP_SAPI != 'cli') {
			$session =& JFactory::getSession();
			$tracking_id = $session->get('sessionTrackingId');
		}

		if(!$transactionTypeId) {
			return false;
		}

		if(null == $desc) {
			$transactionTypeRec = $this->getTransactionType($keyword);
			$desc = $transactionTypeRec->description;
		}

		$giver_id = -1;
		if(PHP_SAPI != 'cli') {
			if($user_id != null){
				$giver_id = $user_id;
			}else{
				$loginUser =& JFactory::getUser();
				$giver_id = $loginUser->id;
			}
		}

		if($user_id != null){
			$recipient_id = $user_id;
		}else{
			$recipient_id = $user->id;
		}
		
		if(null == $recipient_id) {
			$recipient_id = $giver_id;
		}

		$params = array(
			'recipient_id' 					=> $recipient_id,
			'giver_id' 						=> $giver_id,
			'session_tracking_id' 			=> $tracking_id,
			'amount' 						=> $amount,
			'notes' 						=> $desc,
			'tournament_transaction_type' 	=> $keyword,
		);

		return $this->newTransaction($params);
	}
	
	/**
	 * Add Tournament Dollars to a user's balance for promo code.
	 *
	 * @param int the amount in cents to add
	 * @param keyword the keyword identifying the type of transaction
	 * @param string an optional description for the transaction
	 * @return int transaction id
	 */
	function increment_for_promo_code($amount, $keyword, $user_id, $desc = null) {
		$transactionTypeId = $this->getTransactionTypeId($keyword);

		$tracking_id = -1;
		//if(PHP_SAPI != 'cli') {
			$session =& JFactory::getSession();
			$tracking_id = $session->get('sessionTrackingId');
		//}

		if(!$transactionTypeId) {
			return false;
		}

		if(null == $desc) {
			$transactionTypeRec = $this->getTransactionType($keyword);
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

		return $this->newTransaction($params);
	}

	/**
	 * Deduct Tournament Dollars from a user's balance.
	 * If the requested amount is not available, an additional check will be made to determine
	 * if the user can make up the remaining amount from their Account Balance. The method will return false if both of these checks fail.
	 *
	 * @param int transaction amount
	 * @param string transaction type keyword
	 * @param string transaction description
	 * @return int transaction id
	 */
	function decrement($amount, $keyword, $desc = null, $user_id = null) {
		$decrementAccountAmount = 0;
		$totalTournamentAmount 	= $this->getTotal();
		$transactionId 			= null;

		if($amount > $totalTournamentAmount) {
			$accountModel = JModel::getInstance('AccountTransaction', 'PaymentModel');

			if($user_id != null){
				$giver_id = $user_id;
				$recipient_id = $user_id;
			}else{
				$loginUser 	=& JFactory::getUser();
				$giver_id 	= $loginUser->id;
				$recipient_id = $this->user_id;
			}
			
			if(null == $recipient_id) {
				$recipient_id = $giver_id;
			}

			$accountModel->setUserId($recipient_id);
			$totalAccountAmount = $accountModel->getTotal();

			$decrementAccountAmount =  $amount - $totalTournamentAmount;
			if($decrementAccountAmount > $totalAccountAmount) {
				return false;
			}
		}

		if($decrementAccountAmount > 0) {
			if( !$accountModel->getTransactionTypeId($keyword)) {
				$keyword = 'tournamentdollars';
			}

			if(!$accountModel->decrement($decrementAccountAmount, $keyword, $desc)) {
				return false;
			}

			if(!$this->increment($decrementAccountAmount, 'purchase', 'Transferred from account balance')) {
				if(!$accountModel->increment($decrementAccountAmount, 'tournamentdollars', 'Failed to increase user\'s tournament dollars! Add the decrement back!')) {
					// :TODO: send email to tech!
				}

				return false;
			}
		}

		if(!$transactionId = $this->increment(-$amount, $keyword, $desc)) {
			if($decrementAccountAmount > 0) {
				if(!$this->increment(-$decrementAccountAmount, $keyword, 'Failed to decrease user\'s tournament dollars! Decrease the amount transferred from account balance')) {
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

	/**
	 * Get transaction type id
	 *
	 * @param  string transaction type
	 * @return integer
	 */
	function getTransactionTypeId($keyword) {
		$transactionTypeId = NULL;

		$db =& Jfactory::getDBO();
		$query =
			'SELECT
				id
			FROM
				' . $db->nameQuote('#__tournament_transaction_type') . '
       		WHERE
       			keyword = ' . $db->quote($keyword) . '
       		LIMIT 1';

		$db->setQuery($query);
		$rs = $db->loadObject();

		if($rs) {
			$transactionTypeId = $rs->id;
		}

		return $transactionTypeId;
	}

	/**
	 * Get transaction type record
	 *
	 * @param  string transaction type
	 * @return integer
	 */
	function getTransactionType($transactionType) {
		$db =& JFactory::getDBO();
		$query =
			'SELECT
				*
			FROM
				' . $db->nameQuote('#__tournament_transaction_type') . '
       		WHERE
       			keyword = ' . $db->quote($transactionType) . '
       		LIMIT 1';

		$db->setQuery($query);
		$rs = $db->loadObject();

		return $rs;
	}

	/**
	 * Validate if a deposit type is valid
	 *
	 * @param 	string		the keyword of transaction type
	 * @return	boolean		true on valid transaction type
	 */
	function validateTransactionType($transactionType) {
		return (bool)$this->getTransactionType( $transactionType );
	}

	/**
	 * Return the current total for a user’s Tournament Dollars
	 *
	 * @param int user id
	 * @param string the keyword of transaction type
	 * @return int user's tournament dollars
	 */
	function getTotal($userId = null, $transactionType = null) {
		$db =& JFactory::getDBO();

		if(null == $userId) {
			$userId = $this->user_id;
		}

		if(null == $userId) {
			$loginUser =& JFactory::getUser();
			$userId = $loginUser->id;
		}

		if(!$userId) {
			return false;
		}

		$query =
			'SELECT
				SUM(amount) as total_amount
			FROM
				' . $db->nameQuote('#__tournament_transaction') . '
			WHERE
				recipient_id = ' . $db->quote($userId);

		if($transactionType != null) {
			$transactionTypeId = $this->getTransactionTypeId($transactionType);

			if($transactionTypeId) {
				$query .= ' AND tournament_transaction_type_id = ' . $db->quote($transactionTypeId);
			}
		}

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Method to set a user Id
	 *
	 * @param string username
	 * @return intaccount
	 */
	function setUserId($userId) {
		$this->user_id = $userId;
	}

	/**
	 * Builds a query to get data from #__tournament_transaction
	 *
	 * @param int user id
	 * @param  string the keyword of transaction type
	 * @return string SQL query
	 */
	private function _buildQuery($userId = null, $transactionType = null) {
		if(null == $userId) {
			$userId = $this->user_id;
		}

		if(null == $userId) {
			$loginUser =& JFactory::getUser();
			$userId = $loginUser->id;
		}

		if(!$userId) {
			return false;
		}

		$db =& $this->getDBO();

		$tansactionTable 		= $db->nameQuote('#__tournament_transaction');
		$recipientTable 		= $db->nameQuote('#__users');
		$giverTable 			= $db->nameQuote('#__users');
		$friendTable 			= $db->nameQuote('#__users');

		$typeTable 				= $db->nameQuote("#__tournament_transaction_type");
		$tournamentTicketTable 	= $db->nameQuote("#__tournament_ticket");
		$tournamentTable 		= $db->nameQuote("#__tournament");
		$tournamentSportTable	= $db->nameQuote("#__tournament_sport");
		$referralTable 			= $db->nameQuote("#__user_referral");
		
		$betEntryTable			= $db->nameQuote('#__bet');
		$betWinTable			= $db->nameQuote('#__bet');
		$betRefundTable			= $db->nameQuote('#__bet');

		$query = "SELECT t.*, r.name as recipient, g.name as giver, tt.name as type, tt.description as description, "
		. " tk.id as ticket, tk.tournament_id, tk.buy_in_transaction_id, tk2.id as ticket2, tk2.tournament_id AS tournament_id2, tk2.entry_fee_transaction_id,"
		. " tk3.id as ticket3, tk3.tournament_id AS tournament_id3, tk3.result_transaction_id,"
		. " tourn.name as tournament, s.name as sport_name, tourn2.name as tournament2, s2.name as sport_name2,"
		. " tourn3.name as tournament3, s3.name as sport_name3, f.username as friend_username,  be.id as bet_entry_id, bw.id as bet_win_id, br.id as bet_refund_id"
		. " FROM $tansactionTable t"
		. " LEFT JOIN $recipientTable r ON r.id = t.recipient_id"
		. " LEFT JOIN $giverTable g ON g.id = t.giver_id"
		. " LEFT JOIN $typeTable tt ON tt.id = t.tournament_transaction_type_id"
		. " LEFT JOIN $tournamentTicketTable tk ON tk.buy_in_transaction_id = t.id"
		. " LEFT JOIN $tournamentTable tourn ON tourn.id = tk.tournament_id"
		. " LEFT JOIN $tournamentSportTable s ON s.id = tourn.tournament_sport_id"
		. " LEFT JOIN $tournamentTicketTable tk2 ON tk2.entry_fee_transaction_id = t.id"
		. " LEFT JOIN $tournamentTable tourn2 ON tourn2.id = tk2.tournament_id"
		. " LEFT JOIN $tournamentSportTable s2 ON s2.id = tourn2.tournament_sport_id"
		. " LEFT JOIN $tournamentTicketTable tk3 ON tk3.result_transaction_id = t.id"
		. " LEFT JOIN $tournamentTable tourn3 ON tourn3.id = tk3.tournament_id"
		. " LEFT JOIN $tournamentSportTable s3 ON s3.id = tourn3.tournament_sport_id"
		. " LEFT JOIN $referralTable referral ON referral.tournament_transaction_id = t.id"
		. " LEFT JOIN $friendTable f ON f.id = referral.friend_id"
		//for free bet
		. " LEFT JOIN $betEntryTable be ON be.bet_freebet_transaction_id = t.id"
		. " LEFT JOIN $betWinTable bw ON bw.result_transaction_id = t.id"
		. " LEFT JOIN $betRefundTable br ON br.refund_freebet_transaction_id = t.id"
		. " WHERE t.recipient_id = " . $db->quote($userId)
		. " AND t.amount != 0";

		if($transactionType != null) {
			$query .= " AND tt.keyword = " . $db->quote($transactionType);
		}

		$query .= " ORDER BY t.created_date DESC, t.id DESC";
		return $query;
	}

	/**
	 * List a user's transactions
	 *
	 * @param int user id
	 * @param  string the keyword of transaction type
	 * @return object a list of transactions
	 */
	function listTransactions($userId = null, $transactionType = null) {
		$db =& $this->_db;

		if(empty($this->_transactions)) {
			$query 		= $this->_buildQuery( $userId, $transactionType );
			$limitstart = $this->getState('limitstart');
			$limit 		= $this->getState('limit');

			$this->_transactions = $this->_getList($query, $limitstart, $limit);
		}

		return $this->_transactions;
	}
	
	/**
	 * Method to get a list of total transaction amount list
	 *
	 * @param array $params
	 * @return int
	 */
	function getTotalAmountListGroupByRecipientID($params)
	{
		$from_date			= isset($params['from_date']) ? $params['from_date'] : null;
		$end_date			= isset($params['end_date']) ? $params['end_date'] : null;
		$transaction_type	= isset($params['transaction_type']) ? $params['transaction_type'] : null;
		$has_btag			= isset($params['has_btag']) ? $params['has_btag'] : null;
		
		$db =& JFactory::getDBO();
				
		$query =
			'SELECT
				SUM(t.amount) AS total_amount,
				count(t.id) AS count,
				t.recipient_id
			FROM
				' . $db->nameQuote('#__tournament_transaction') . ' AS t
			LEFT JOIN
				' . $db->nameQuote('#__topbetta_user') . ' AS r
				ON
					r.user_id = t.recipient_id	
			';
		
		$where = array();
		if (!is_null($from_date)) {
			$where[] = 't.created_date >= ' . $db->quote($from_date . ' 00:00:00');	
		}
		if (!is_null($end_date)) {
			$where[] = 't.created_date < ' . $db->quote($end_date . ' 23:59:59'); 
		}

		if (!is_null($transaction_type)) {
			$type_id = $this->getTransactionTypeId($transaction_type);
			$where[] = 't.tournament_transaction_type_id = ' . $db->quote($type_id);				
		}
		
		if ($has_btag) {
			$where[] = 'r.btag IS NOT NULL AND r.btag !=""';
		}
		
		if (!empty($where)) {
			$query .= '
				WHERE
			' . implode(' AND ', $where);
		}
		
		$query .='
			GROUP BY
				t.recipient_id
		';

		$db->setQuery($query);
		
		return $db->loadObjectList();
	}

}
?>
