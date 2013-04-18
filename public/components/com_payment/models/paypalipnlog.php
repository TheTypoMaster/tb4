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

class PaymentModelPaypalipnlog extends JModel
{
    /**
	* Method to add a paypal IPN log
	*
	* @param  string IPN callback value
	* @return boolean true on success
	*/
    function addLog( $callback )
    {
    	// Get the table
    	$db =& Jfactory::getDBO();

    	$custom = unserialize( JRequest::getVar('custom', '', 'post'));
    	$userId = $db->quote($custom['user_id']);
    	 	
		$txnId = $db->quote(JRequest::getVar( 'txn_id', '', 'post'));
		$mcGross = $db->quote(JRequest::getVar( 'mc_gross', '', 'post'));
		$payerEmail = $db->quote(JRequest::getVar( 'payer_email', '', 'post'));
		$payerId = $db->quote(JRequest::getVar('payer_id', '', 'post'));
		$paymentType = $db->quote(JRequest::getVar('payment_type', '', 'post'));
		$paymentStatus = $db->quote(JRequest::getVar('payment_status', '', 'post'));
		$callback = $db->quote($callback);

    	$paymentDateParts = explode( ' ', JRequest::getVar('payment_date', '', 'post') );
    	//remove time zone
    	unset($paymentDateParts[4]);
    	$paymentDate = $db->quote(date('Y-m-d H:i:s', strtotime(implode(' ', $paymentDateParts))));
		
    	$table = $db->nameQuote('#__paypal_ipn_log');
    	$insertQuery = "INSERT INTO $table
			( txn_id, user_id, mc_gross, payer_email, payer_id, payment_type, payment_status, payment_date, ipn_date, ipn_response, notification_flag)
			VALUES ($txnId, $userId, $mcGross, $payerEmail, $payerId, $paymentType, $paymentStatus, $paymentDate, now(), $callback, 0 )
		";
    	
		$db->setQuery($insertQuery);
		
		return $db->query();
    }
    
    
    /**
	* Method to get a paypal IPN log
	*
	* @param  string Paypal tansaction ID
	* @return object 
	*/
    function getLog( $txnId )
    {
    	$db =& Jfactory::getDBO(); 	
    	$table = $db->nameQuote('#__paypal_ipn_log');
    	$txnId = $db->quote( $txnId );
    	$query = 'SELECT * FROM ' . $table .' WHERE txn_id =' . $txnId;
    	$db->setQuery( $query );
    	
    	return $db->loadObject();
    }
    
    
    /**
	* Method to update notification field in paypal IPN log 
	*
	* @param  string Paypal tansaction ID
	* @param  int notification flag value	
	* @return boolean true on success
	*/
    function updateNotification( $txnId, $flag = 1 )
    {
    	$db =& Jfactory::getDBO();
		$table = $db->nameQuote('#__paypal_ipn_log');
		
		$txnId = $db->quote( $txnId );
		$flag = $db->quote( $flag );
		
    	$updateQuery = "UPDATE $table SET notification_flag = $flag WHERE txn_id = $txnId";
    	$db->setQuery($updateQuery);
		
		return $db->query();
    }
    

	/**
	 * Check if a transaction already exists
	 *
	 * @return true if exists
	 */
	function IsExistingPaypalTransaction( $txnId )
	{
		$db =& Jfactory::getDBO();
		$table = $db->nameQuote('#__paypal_ipn_log');
		
		$txnId = $db->quote( $txnId );
		
    	$query = "SELECT * FROM $table WHERE txn_id = $txnId";
    	
    	
    	$db->setQuery($query);
		
		return $db->loadObject();
	}
	
	/**
	 * Get User's most recent transaction
	 *
	 * @return string $txn_id
	 */
	function getMostRecentTxn( $userId )
	{
		$db =& Jfactory::getDBO(); 	
    	$table = $db->nameQuote('#__paypal_ipn_log');
    	$userId = $db->quote( $userId );
    	$query = 'SELECT txn_id FROM ' . $table .' WHERE user_id =' . $userId . ' ORDER BY payment_date DESC LIMIT 1';
    	$db->setQuery( $query );
    	
    	return $db->loadResult();
	}
}
?>