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

class PaymentModelWithdrawalrequest extends JModel
{
	/** @var array of payment requests objects */
	private $_requests = null;
	/** @var int total number of requests */
	private $_total = null;
	/** @var Jpagination object */
	private $_pagination = null;
	
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
	* Builds a query to get data from #__withdrawal_request
	* 
	* @return string SQL query
	*/
	private function _buildQuery()
	{
		$db =& $this->getDBO();
		$rtable = $db->nameQuote('#__withdrawal_request');
		$ttable = $db->nameQuote('#__withdrawal_type');
		$utable = $db->nameQuote('#__users');
		$ptable = $db->nameQuote('#__withdrawal_paypal');
		$mtable = $db->nameQuote('#__moneybookers_withdrawal');
		$query = 'SELECT r.*, t.name AS withdrawal_type,'
		. ' u.name AS requester, u.username, u.id as requester_id, u2.name AS fulfiller, u2.id AS fulfiller_id, p.paypal_id, m.moneybookers_id'
		. ' FROM ' . $rtable. ' AS r'
		. ' LEFT JOIN '.$ttable.' AS t ON r.withdrawal_type_id=t.id'
		. ' LEFT JOIN '.$utable.' AS u ON r.requester_id=u.id'
		. ' LEFT JOIN '.$utable.' AS u2 ON r.fulfiller_id=u2.id'
		. ' LEFT JOIN '.$ptable.' As p ON r.id=p.withdrawal_request_id'
		. ' LEFT JOIN '.$mtable.' As m ON r.id=m.withdrawal_request_id'
		. $this->_buildQueryWhere()
		. $this->_buildQueryOrderBy()
		;
		//print($query);exit;
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
		$orders = array('id', 'requester', 'username', 'requested_date', 'approved_flag', 'fulfilled_date', 'requester_id');
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
		
		if( 'requester_id' == $filter_order )
		{
			$filter_order = 'u.id';
		}
		
		$orderby = ' ORDER BY '. $db->nameQuote($filter_order).' '.$filter_order_Dir;
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
		$filter_search = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_search', 'filter_withdrawal_search');
		$filter_requested_from_date = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_requested_from_date', 'filter_withdrawal_requested_from_date');
		$filter_requested_to_date = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_requested_to_date', 'filter_withdrawal_requested_to_date');
		$filter_fulfilled_from_date = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_fulfilled_from_date', 'filter_withdrawal_fulfilled_from_date');
		$filter_fulfilled_to_date = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_fulfilled_to_date', 'filter_withdrawal_fulfilled_to_date');
		$filter_from_amount = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_from_amount', 'filter_withdrawal_from_amount');
		$filter_to_amount = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_to_amount', 'filter_withdrawal_to_amount');
		$filter_status = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_status', 'filter_withdrawal_status');
		// Prepare the WHERE clause
		$where = array();
		// Determine search terms
		if ($filter_search = trim($filter_search))
		{
			$filter_search = JString::strtolower($filter_search);
			$filter_search = $db->getEscaped($filter_search);
			$orcond = array();
			$orcond[] = 'LOWER(u.name) LIKE "%'.$filter_search.'%"';
			$orcond[] = 'LOWER(u.username) LIKE "%'.$filter_search.'%"';
			$orcond[] = 'LOWER(u2.name) LIKE "%'.$filter_search.'%"';
			$orcond[] = 'LOWER(p.paypal_id) LIKE "%'.$filter_search.'%"';
			$orcond[] = 'LOWER(r.notes) LIKE "%'.$filter_search.'%"';
			
			if( ctype_digit( $filter_search ) )
			{
				$orcond[] = 'u.id = "' . $filter_search . '"';
			}
			
			$where[] = '(' . implode( ' OR ', $orcond ) . ')';
		}
		
		if( $filter_requested_from_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_requested_from_date, $m) )
		{
			$where[] = 'r.requested_date >= ' . $db->quote($filter_requested_from_date . ' 00:00:00');
		}
		
		if( $filter_requested_to_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_requested_to_date, $m) )
		{
			$where[] = 'r.requested_date <= ' . $db->quote($filter_requested_to_date . ' 23:59:59');
		}
		
		if( $filter_fulfilled_from_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_fulfilled_from_date, $m) )
		{
			$where[] = 'r.fulfilled_date >= ' . $db->quote($filter_fulfilled_from_date . ' 00:00:00');
		}
		
		if( $filter_fulfilled_to_date && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $filter_fulfilled_to_date, $m) )
		{
			$where[] = 'r.fulfilled_date <= ' . $db->quote($filter_fulfilled_to_date . ' 23:59:59');
		}
		
		if( $filter_from_amount && ctype_digit($filter_from_amount))
		{
			$where[] = '(r.amount/100) >= '. $db->quote($filter_from_amount);
		}
		
		if( $filter_to_amount && ctype_digit($filter_to_amount))
		{
			$where[] = '(r.amount/100) <= '. $db->quote($filter_to_amount);
		}
		
		switch( $filter_status )
		{
			case 'pending':
				$where[] = 'r.approved_flag is null'; 
				break;
			case 'approved':
				$where[] = 'r.approved_flag = 1';
				break;
			case 'denied':
				$where[] = 'r.approved_flag = 0';
				break;
		}
		
		
		// return the WHERE clause
		return (count($where)) ? ' WHERE '.implode(' AND ', $where) : '';
}
		
	/**
	* Get a list of withdrawal requests
	*
	* @param int request id 
	* @return object
	*/
	function getRequest( $requestId )
	{
		// Get the database connection
		$db =& $this->getDBO();
		$rtable = $db->nameQuote('#__withdrawal_request');
		$ttable = $db->nameQuote('#__withdrawal_type');
		$utable = $db->nameQuote('#__users');
		$ptable = $db->nameQuote('#__withdrawal_paypal');
		$mtable = $db->nameQuote('#__moneybookers_withdrawal');
		
		if( empty($requestId) || !ctype_digit($requestId))
		{
			return null;
		}
		
		$query = 'SELECT r.*, t.name AS withdrawal_type, t.keyword AS withdrawal_type_keyword,'
		. ' u.name AS requester, u.email AS requester_email, u2.name AS fulfiller, p.paypal_id, m.moneybookers_id'
		. ' FROM ' . $rtable. ' AS r'
		. ' LEFT JOIN '.$ttable.' AS t ON r.withdrawal_type_id=t.id'
		. ' LEFT JOIN '.$utable.' AS u ON r.requester_id=u.id'
		. ' LEFT JOIN '.$utable.' AS u2 ON r.fulfiller_id=u2.id'
		. ' LEFT JOIN '.$ptable.' As p ON r.id=p.withdrawal_request_id'
		. ' LEFT JOIN '.$mtable.' As m ON r.id=m.withdrawal_request_id'
		. ' WHERE r.id =' . $db->quote($requestId)
		;
		
		$db->setQuery($query);
		// Return the list of requests
		return $db->loadObject();
	}

	/**
	* Get a list of withdrawal requests
	*
	* @return array of objects
	*/
	function getRequests( $isCsv = false )
	{
		// Get the database connection
		$db =& $this->_db;
		if( empty($this->_requests) )
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
			$this->_requests = $this->_getList($query, $limitstart, $limit);
		}
		// Return the list of requests
		return $this->_requests;
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
			$total = $this->getTotal();
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
    function getTotal()
    {
    	if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
    }

    /**
	* Method to store a request
	*
	* @param Array an array of values for updating request record
	* @return Boolean true on success
	*/
	function store( $params )
	{
		// Get the database connection
		$db =& $this->getDBO();
		$table = $db->nameQuote('#__withdrawal_request');
		
		$id = $db->quote($params['id']);
		$approvedFlag = $db->quote(($params['approvedFlag'] == 'yes' ? '1' : '0'));
		$notes = $db->quote($params['notes']);
		$fulfiller = $db->quote($params['fulfiller']);
		
		$updateQuery = 'UPDATE '. $table
			. ' SET approved_flag = ' . $approvedFlag
			. ' , notes = ' . $notes
			. ' , fulfilled_date = now()'
			. ' , fulfiller_id = ' . $fulfiller
			. ' WHERE id = ' . $id
		;
		
		$db->setQuery($updateQuery);
		
		return $db->query();
	}
}
?>