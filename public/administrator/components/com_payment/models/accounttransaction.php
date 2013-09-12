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
	/** @var array of transaction objects */
	private $_transactions = null;
	/** @var int total number of requests */
	private $_total = null;
	/** @var Jpagination object */
	private $_pagination = null;
	/** @var array option array */
	public  $options = array(
		'transaction_type' => array(), //auto loaded
	);
	
	/**
	* Constructor
	* 
	* @return void
	*/
	function __construct()
	{
		global $mainframe;
		parent::__construct();
		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest(
		'global.list.limit',
		'limit', $mainframe->getCfg('list_limit'));
		$limitstart = $mainframe->getUserStateFromRequest(
		$option.'limitstart', 'limitstart', 0);
		// Set the state pagination variables
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	/**
	* Builds a query to get data from #__account_transaction
	* 
	* @return string SQL query
	*/
	private function _buildQuery()
	{
		$db =& $this->getDBO();
		$tansactionTable = $db->nameQuote('#__account_transaction');
		$recipientTable = $db->nameQuote('#__users');
		$giverTable = $db->nameQuote('#__users');
		$typeTable = $db->nameQuote("#__account_transaction_type");
		$query = "SELECT t.*, r.name as recipient, r.id as recipient_id, r.username as recipient_username,"
		    . " g.name as giver, g.id as giver_id, g.username as giver_username, tt.name as type FROM $tansactionTable t"
			. " LEFT JOIN $recipientTable r ON r.id = t.recipient_id"
			. " LEFT JOIN $giverTable g ON g.id = t.giver_id"
			. " LEFT JOIN $typeTable tt ON tt.id = t.account_transaction_type_id"
			. $this->_buildQueryWhere()
			. $this->_buildQueryOrderBy()
		;
//		print ($query);exit;
		return $query;
	}
	
	/**
	* Build the ORDER part of a query
	*
	* @return string part of an SQL query
	*/
	private function _buildQueryOrderBy()
	{
		global $mainframe, $option;
		$db =& $this->_db;
		// Array of allowable order fields
		$orders = array('id', 'recipient', 'giver', 'type', 'amount', 'created_date', 'giver_id', 'giver_username', 'recipient_id', 'recipient_username');
		// Get the order field and direction, default order field
		// is 'ordering', default direction is ascending
		$filter_order = $mainframe->getUserStateFromRequest(
		$option.'filter_order', 'filter_order', 'id');
		$filter_order_Dir = strtoupper(
		$mainframe->getUserStateFromRequest(
		$option.'filter_order_Dir', 'filter_order_Dir', 'ASC'));
		// Validate the order direction, mus
		
		if($filter_order_Dir != 'ASC' && $filter_order_Dir != 'DESC')
		{
			$filter_order_Dir = 'ASC';
		}
		// If order column is unknown use the default
		
		if (!in_array($filter_order, $orders))
		{
			$filter_order = 'id';
		}
		
		if( 'amount' == $filter_order )
		{
			$orderby = ' ORDER BY abs('. $db->nameQuote($filter_order) .') '.$filter_order_Dir;
		}
		else if( 'giver_id' == $filter_order )
		{
			$orderby = ' ORDER BY '. $db->nameQuote('g.id') .' '.$filter_order_Dir;
		}
		else if( 'recipient_id' == $filter_order )
		{
			$orderby = ' ORDER BY '. $db->nameQuote('r.id') .' '.$filter_order_Dir;
		}
		else
		{
			$orderby = ' ORDER BY '. $db->nameQuote($filter_order) .' '.$filter_order_Dir;
		}
		
		// Return the ORDER BY clause
		
		return $orderby;
	}
	
	
	/**
	* Builds the WHERE part of a query
	*
	* @return string Part of an SQL query
	*/
	private function _buildQueryWhere()
	{
		global $mainframe, $option;
		$db =& $this->_db;
		// Get the filter values
		$filter_search = $mainframe->getUserStateFromRequest($option.'filter_account_search', 'filter_account_search');
		$filter_transaction_type = $mainframe->getUserStateFromRequest($option.'filter_account_transaction_type', 'filter_account_transaction_type');
		$filter_from_date = $mainframe->getUserStateFromRequest($option.'filter_account_from_date', 'filter_account_from_date');
		$filter_to_date = $mainframe->getUserStateFromRequest($option.'filter_account_to_date', 'filter_account_to_date');
		$filter_from_amount = $mainframe->getUserStateFromRequest($option.'filter_account_from_amount', 'filter_account_from_amount');
		$filter_to_amount = $mainframe->getUserStateFromRequest($option.'filter_account_to_amount', 'filter_account_to_amount');
		
		// set some defaults
		$filter_from_date = $filter_from_date ? $filter_from_date : date("Y-m-d", (time() - 24 * 60 * 60));
		$filter_to_date = $filter_to_date ? $filter_to_date : date("Y-m-d", time());
				
		// Prepare the WHERE clause
		$where = array();
		// Determine search terms
		if ($filter_search = trim($filter_search))
		{
			$filter_search = JString::strtolower($filter_search);
			$filter_search = $db->getEscaped($filter_search);
			$orcond = array();
			$orcond[] = 'LOWER(r.name) LIKE "%'.$filter_search.'%"';
			$orcond[] = 'LOWER(r.username) LIKE "%'.$filter_search.'%"';
			$orcond[] = 'LOWER(g.name) LIKE "%'.$filter_search.'%"';
			$orcond[] = 'LOWER(r.username) LIKE "%'.$filter_search.'%"';
			$orcond[] = 'LOWER(t.notes) LIKE "%'.$filter_search.'%"';
			
			if( ctype_digit($filter_search) )
			{
				$orcond[] = 'r.id = ' . $filter_search;
				$orcond[] = 'g.id = ' . $filter_search;
			}
			
			$where[] = '(' . implode( ' OR ', $orcond ) . ')';
		}
		
		if( $filter_from_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_from_date, $m) )
		{
			$where[] = ' t.created_date >= ' . $db->quote( $filter_from_date . ' 00:00:00' );
		}
		
		if( $filter_to_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_to_date, $m) )
		{
			$where[] = ' t.created_date <= ' . $db->quote( $filter_to_date . ' 23:59:59' );
		}
		
		if( $filter_from_amount && ctype_digit($filter_from_amount))
		{
			$where[] = ' abs(t.amount/100) >= '. $db->quote($filter_from_amount);
		}
		
		if( $filter_to_amount && ctype_digit($filter_to_amount))
		{
			$where[] = ' abs(t.amount/100) <= '. $db->quote($filter_to_amount);
		}
		
		if( $filter_transaction_type )
		{
			$where[] = 't.account_transaction_type_id = ' . $db->quote($filter_transaction_type);
		}
		
		// return the WHERE clause
		return (count($where)) ? ' WHERE '.implode(' AND ', $where) : '';
}
		
	/**
	* Get a list of transactions
	*
	* @param int transaction id 
	* @return object
	*/
	function getTransaction( $transactionId )
	{
		// Get the database connection
		$db =& $this->getDBO();
		$tansactionTable = $db->nameQuote('#__account_transaction');
		$recipientTable = $db->nameQuote('#__users');
		$giverTable = $db->nameQuote('#__users');
		$typeTable = $db->nameQuote("#__account_transaction_type");
		
		if( empty($transactionId) || !ctype_digit($transactionId))
		{
			return null;
		}
		
		$query = "SELECT *, r.name as recipient, g.name as giver, tt.name as type FROM $tansactionTable t"
			. " LEFT JOIN $recipientTable r ON r.id = t.recipient_id"
			. " LEFT JOIN $giverTable g ON g.id = t.giver_id"
			. " LEFT JOIN $typeTable tt ON tt.id = t.account_transaction_type_id"
			. " WHERE t.id = " . $db->quote($transactionId)
		;
		
		$db->setQuery($query);
		// Return the transaction
		return $db->loadObject();
	}

	/**
	* Get a list of transactions
	* @param boolean the flag for csv list, which will ignore 'limit' and get all the transactions
	* @return array of objects
	*/
	function getTransactions( $isCsv = false )
	{
		// Get the database connection
		$db =& $this->_db;
		if( empty($this->_transactions) )
		{
			// Build query and get the limits from current state
			$query = $this->_buildQuery();
			if( $isCsv )
			{
				$limitstart = 0;
				$limit = 0;
			}
			else
			{
				$limitstart = $this->getState('limitstart');
				$limit = $this->getState('limit');
			}
			$this->_transactions = $this->_getList($query, $limitstart, $limit);
		}
		// Return the list of requests
		return $this->_transactions;
	}
	
	/**
	* Get a pagination object
	*
	* @return pagination object
	*/
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			// Import the pagination library
			jimport('joomla.html.pagination');
			// Prepare the pagination values
			$total = $this->getTotalPage();
			$limitstart = $this->getState('limitstart');
			$limit = $this->getState('limit');
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
    function getTotalPage()
    {
    	if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
    }


    /**
	* store a new transaction record
	*
	* @param  array increment params
	* @return boolean true on success
	*/
	function store( $params )
	{
		$db =& Jfactory::getDBO();
		
		$recipientId = $db->quote( $params['recipient_id'] );
		$giverId = $db->quote( $params['giver_id'] );
		$sessionTrackingId = $db->quote( $params['session_tracking_id'] );
		$amount = $db->quote( (int)$params['amount'] );
		$notes = $db->quote( $params['notes'] );

		$transactionTypeId = $db->quote($params['account_transaction_type']);

		$table = $db->nameQuote('#__account_transaction');
    	$insertQuery = "INSERT INTO $table
			( recipient_id, giver_id, session_tracking_id, account_transaction_type_id, amount, notes, created_date )
			VALUES ($recipientId, $giverId, $sessionTrackingId, $transactionTypeId, $amount, $notes, now() )
		";
		$db->setQuery($insertQuery);
		
		if(!$db->query())
		{
			return false;
		}
		
		return true;
	}
	
    /**
	* Method to load options from other table
	*
	* @return array
	*/
	function loadDynamicOptions()
	{
		$db =& $this->getDBO();
		$table = $db->nameQuote('#__account_transaction_type');
		
		$query = 'SELECT * FROM ' . $table;
		
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		foreach( $rows as $row )
		{
			$this->options['transaction_type'][$row['id']] = $row['name'];
		}
		return $db->query();
	}
	
	/**
	 * Method to get a user id from a given username
	 * 
	 * @param string recipient user name
	 * @param string recipient name
	 * @return int
	 */
	function getRecipientId( $recipientUsername, $recipientName )
	{
		$db =& $this->getDBO();
		$table = $db->nameQuote('#__users');
		
		$query = 'SELECT id FROM ' . $table 
			. ' WHERE username = ' . $db->quote($recipientUsername)
			. ' AND name = ' . $db->quote($recipientName)
			. ' AND usertype = ' . $db->quote('Registered')
			. ' LIMIT 1';
		$db->setQuery($query);
		$rec = $db->loadObject();
		return $rec->id;
	}
	
	
	function getUserList( $keyword )
	{
		
		$db =& $this->getDBO();
		$table = $db->nameQuote('#__users');
		$wild_keyword = $db->quote('%' . strtolower($keyword) . '%');
		
		//$query = 'SELECT id,username,name FROM ' . $table . 'WHERE usertype = "Registered" AND (CONCAT(lower(username), " " , lower(name)) LIKE ' . $wild_keyword;
		$query = 'SELECT id,username,name FROM ' . $table . ' WHERE usertype = "Registered" AND (lower(username) LIKE ' . $wild_keyword. ' OR lower(name) LIKE ' . $wild_keyword. ') ';
		if( ctype_digit($keyword) )
		{
			$query .= ' OR id = ' . $keyword;
		} 
		
		$query .= ' LIMIT 10';
		$db->setQuery($query);
		$rec = $db->loadObjectList();
		return $rec;
	}
}
?>
