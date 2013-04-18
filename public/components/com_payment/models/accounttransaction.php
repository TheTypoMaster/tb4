<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: payment.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage payment
 * @license GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class PaymentModelAccounttransaction extends JModel
{
	/**
	 * Payment form credit-card and date options
	 *
	 * @var array
	 */
	public $options = array(
		'cardType' => array(
	    	'VISA' => 'VISA',
	    	'MASTERCARD' => 'Master Card',
		),
	    'year' => array(),
	    'month' => array()
	);

	/**
	 * Current user ID for transactions
	 *
	 * @var int
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
	 * Pagination object
	 *
	 * :TODO: This should not be here, please move it to the controller and view.
	 *
	 * @var JPagination
	 */
	private $_pagination = null;
	
	const TYPE_BETWIN = 'betwin',
		TYPE_BETREFUND = 'betrefund',
		TYPE_BETENTRY = 'betentry';

	/**
	 * Constructor
	 *
	 * @return void
	 */
	function __construct()
	{
		global $mainframe, $option;
		parent::__construct();

		$config = JFactory::getConfig();
		if($mainframe) {
			$this->setState('limit', $mainframe->getUserStateFromRequest('com_payment.limit', 'limit', 20, 'int'));
			$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
		}

		for($i = 1; $i <= 12; $i++) {
			$this->options['month'][$i] = sprintf('%02s', $i);
		}

		for($i = 0; $i < 10; $i++) {
			$year = sprintf('%02s'  ,date('y')+$i );
			$this->options['year'][$year] = $year;
		}
		
	}

	/**
	 * Get a single account transaction record.
	 *
	 * @param integer $id
	 * @return object
	 */
	function getAccountTransaction($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				recipient_id,
				giver_id,
				session_tracking_id,
				account_transaction_type_id,
				amount,
				notes,
				created_date
			FROM
				' . $db->nameQuote('#__account_transaction') . '
			WHERE
				id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Builds a query to get data from #__account_transaction
	 *
	 * @return string SQL query
	 */
	private function _buildQuery($userId = null)
	{
		$db =& $this->getDBO();
		$tansactionTable		= $db->nameQuote('#__account_transaction');
		$recipientTable 		= $db->nameQuote('#__users');
		$giverTable 			= $db->nameQuote('#__users');

		$typeTable 				= $db->nameQuote("#__account_transaction_type");
		$tournamentTicketTable 	= $db->nameQuote("#__tournament_ticket");
		$tournamentTable 		= $db->nameQuote("#__tournament");
		$tournamentSportTable	= $db->nameQuote("#__tournament_sport");
		
		$betEntryTable			= $db->nameQuote('#__bet');
		$betWinTable			= $db->nameQuote('#__bet');
		$betRefundTable			= $db->nameQuote('#__bet');

		$query = "SELECT t.*, r.name as recipient, r.id as recipient_id, g.name as giver, g.id as giver_id, tt.name as type,"
		. " tt.description as description, tourn.id as tournament_id, tourn.name as tournament, s.name as sport_name,"
		. " tk.refunded_flag as ticket_refunded_flag, be.id as bet_entry_id, bw.id as bet_win_id, br.id as bet_refund_id"
		. " FROM $tansactionTable t"
		. " LEFT JOIN $recipientTable r ON r.id = t.recipient_id"
		. " LEFT JOIN $giverTable g ON g.id = t.giver_id"
		. " LEFT JOIN $typeTable tt ON tt.id = t.account_transaction_type_id"
		. " LEFT JOIN $tournamentTicketTable tk ON tk.result_transaction_id = t.id"
		. " LEFT JOIN $tournamentTable tourn ON tk.tournament_id = tourn.id"
		. " LEFT JOIN $tournamentSportTable s ON s.id = tourn.tournament_sport_id"
		. " LEFT JOIN $betEntryTable be ON be.bet_transaction_id = t.id"
		. " LEFT JOIN $betWinTable bw ON bw.result_transaction_id = t.id"
		. " LEFT JOIN $betRefundTable br ON br.refund_transaction_id = t.id"
		. $this->_buildQueryWhere($userId = null)
		. " ORDER BY t.created_date DESC, t.id DESC";
		;

		return $query;
	}
	
	/**
	* Builds the WHERE part of a query
	*
	* @return string Part of an SQL query
	*/
	private function _buildQueryWhere($userId = null)
	{
		global $mainframe, $option;
		
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
		
		$db =& $this->_db;
		// Get the filter values
		$transaction_type	= JRequest::getVar('transaction_type', null);
		$filter_from_date	= $mainframe->getUserStateFromRequest($option.'filter_transaction_from_date', 'filter_transaction_from_date');
		$filter_to_date		= $mainframe->getUserStateFromRequest($option.'filter_transaction_to_date', 'filter_transaction_to_date');
		
		// Prepare the WHERE clause
		$where = array();

		if( $filter_from_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_from_date, $m) )
		{
			$where[] = ' t.created_date >= FROM_UNIXTIME(' . $db->quote(strtotime($filter_from_date)) . ')';
		}
		
		if( $filter_to_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_to_date, $m) )
		{
			$where[] = ' t.created_date <= FROM_UNIXTIME(' . $db->quote(strtotime($filter_to_date)) . ')';
		}
		
		switch ($transaction_type) {
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
		
		$where[] = 't.recipient_id = ' . $db->quote($userId);
		$where[] = '(tourn.parent_tournament_id IS NULL OR tourn.parent_tournament_id = -1)';
		//$where[] = '(tk.refunded_flag IS NULL OR tk.refunded_flag = 0)';

		// return the WHERE clause
		return (count($where)) ? ' WHERE '.implode(' AND ', $where) : '';
	}
	/**
	 * List a user's transactions
	 *
	 * @param int user id
	 * @return object a list of transactions
	 */
	function listTransactions($userId = null)
	{
		$db =& $this->_db;
		if(empty($this->_transactions)) {
			$query 		= $this->_buildQuery($userId);
			$limitstart = $this->getState('limitstart');
			$limit 		= $this->getState('limit');

			$this->_transactions = $this->_getList($query, $limitstart, $limit);
		}

		return $this->_transactions;
	}

	/**
	 * Get a pagination object
	 *
	 * @return pagination object
	 */
	function getPagination()
	{
		if(empty($this->_pagination)) {
			jimport('joomla.html.pagination');

			$total 		= $this->getTotalPage();
			$limitstart = $this->getState('limitstart');
			$limit 		= $this->getState('limit');

			$this->_pagination = new JPagination($total,$limitstart,$limit);
		}

		return $this->_pagination;
	}

	/**
	 * Get number of requests
	 *
	 * @return integer
	 */
	function getTotalPage()
	{
		$db =& Jfactory::getDBO();
		if(empty($this->_total)) {
			//$query = $this->_buildQuery();
			//$this->_total = $this->_getListCount($query);
			
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
			
			$query = "SELECT COUNT(*) AS tot FROM " . $db->nameQuote('#__account_transaction') . $this->_buildQueryWhere($userId = null);
				
			$db->setQuery($query);
			$rs = $db->loadObject();
	
				if($rs) {
					$this->_total = $rs->tot;
				}
		}

		return $this->_total;
	}

	/**
	 * change a user's balance
	 *
	 * @param  array increment params
	 * @return int transaction id
	 */
	function newTransaction($params)
	{
		$db =& Jfactory::getDBO();
		$insertQuery =
			'INSERT INTO ' . $db->nameQuote('#__account_transaction') . ' (
				recipient_id,
				giver_id,
				session_tracking_id,
				account_transaction_type_id,
				amount,
				notes,
				created_date
			) VALUES (
				' . $db->quote($params['recipient_id']) . ',
				' . $db->quote($params['giver_id']) . ',
				' . $db->quote($params['session_tracking_id']) . ',
				' . $db->quote($this->getTransactionTypeId( $params['account_transaction_type'] )) . ',
				' . $db->quote((int)$params['amount']) . ',
				' . $db->quote($params['notes']) . ',
				now()
			)';

		$db->setQuery($insertQuery);
		if(!$db->query()) {
			return false;
		}

		return $db->insertid();
	}

	/**
	 * Add to a user's balance
	 *
	 * @param int transaction amount
	 * @param keyword transaction type keyword
	 * @param string transaction description
	 * @return int transaction id
	 */
	function increment($amount, $keyword, $desc = null)
	{
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
			$loginUser =& JFactory::getUser();
			$giver_id = $loginUser->id;
		}

		$recipient_id = $this->user_id;
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

		return $this->newTransaction($params);
	}

	/**
	 * Deduct from a user's balance
	 *
	 * @param int transaction amount
	 * @param string transaction type keyword
	 * @param string transaction description
	 * @return int transaction id
	 */
	function decrement($amount, $keyword, $desc = null)
	{
		return $this->increment(-$amount, $keyword, $desc);
	}

	/**
	 * Get transaction type id
	 *
	 * @param  string transaction type
	 * @return integer
	 */
	function getTransactionTypeId($keyword)
	{
		$transactionTypeId = NULL;
		$db =& Jfactory::getDBO();

		$query =
			'SELECT
				id
			FROM
				' . $db->nameQuote('#__account_transaction_type') . '
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
	function getTransactionType($transactionType)
	{
		$db =& JFactory::getDBO();
		$query =
			'SELECT
				*
			FROM
				' . $db->nameQuote('#__account_transaction_type') . '
			WHERE
				keyword = ' . $db->quote($transactionType) . '
			LIMIT 1';

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Validate if a deposit type is valid
	 *
	 * @param 	string		the keyword of withdrawal type
	 * @return	boolean		true on valid withdrawal type
	 */
	function validateTransactionType($transactionType)
	{
		return (bool)$this->getTransactionType($transactionType);
	}

	/**
	 * Get the total of a user's balance
	 *
	 * @param int user id
	 * @return int user's balance
	 */
	function getTotal($userId = null)
	{
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

		$table = $db->nameQuote('#__account_transaction');

		$query =
			'SELECT
				SUM(amount) as total_amount
			FROM
				' . $db->nameQuote('#__account_transaction') . '
			WHERE
				recipient_id = ' . $db->quote($userId);

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Method to set a user Id
	 *
	 * @param string username
	 * @return int
	 */
	function setUserId($userId)
	{
		$this->user_id = $userId;
	}
	
	/**
	 * Method to get user total amount
	 *
	 * @param string $transactionType,
	 * @param int $userId,
	 * @param int $fromTime
	 * @param int $fromTime
	 * @return int
	 */
	function getTotalAmountByTransactionType($transactionType, $userId = null, $fromTime = null, $toTime = null)
	{
		$db =& JFactory::getDBO();
		if (null == $userId) {
			$loginUser =& JFactory::getUser();
			$userId = $loginUser->id;
		}
		
		if (!$userId) {
			return false;
		}
		
		$betTypeID = $this->getTransactionTypeId($transactionType);
		
		if (is_null($betTypeID)) {
			return false;
		}
				
		$query =
			'SELECT
				SUM(amount) as winning_amount
			FROM
				' . $db->nameQuote('#__account_transaction') . '
			WHERE
				recipient_id = ' . $db->quote($userId) . '
			AND
				account_transaction_type_id = ' . $db->quote($betTypeID);
		
		if (!is_null($fromTime)) {
			$query .= '
				AND created_date >= FROM_UNIXTIME(' . $db->quote($fromTime) . ')'; 
		}
		
		if (!is_null($toTime)) {
			$query .= '
				AND created_date < FROM_UNIXTIME(' . $db->quote($toTime) . ')'; 
		}

		$db->setQuery($query);
		return $db->loadResult();
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
				' . $db->nameQuote('#__account_transaction') . ' AS t
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
			switch ($transaction_type) {
				case 'deposit':
					$paypal_deposit_type_id = $this->getTransactionTypeId('paypaldeposit');
					$eway_deposit_type_id	= $this->getTransactionTypeId('ewaydeposit');
					$bank_deposit_type_id	= $this->getTransactionTypeId('bankdeposit');
					$bpaydeposit_type_id	= $this->getTransactionTypeId('bpaydeposit');
					
					$where[] = 't.account_transaction_type_id IN (
						' . $db->quote($paypal_deposit_type_id) . ',
						' . $db->quote($eway_deposit_type_id) . ',
						' . $db->quote($bank_deposit_type_id) . ',
						' . $db->quote($bpaydeposit_type_id) . '
					)';
					break;
				case 'betshands':
					$enrty_type_id		= $this->getTransactionTypeId('entry');
					$bet_entry_type_id	= $this->getTransactionTypeId('betentry');
					
					$where[] = 't.account_transaction_type_id IN (
						' . $db->quote($enrty_type_id) . ',
						' . $db->quote($bet_entry_type_id) . '
					)';
					
					$where[] = 't.amount != 0';
					
					break;
				default:
					$type_id = $this->getTransactionTypeId($transaction_type);
					$where[] = 't.account_transaction_type_id = ' . $db->quote($type_id);
					break;				
			}
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
