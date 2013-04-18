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
    /**
	* Method to withdraw money
	*
	* @param  Array withdraw params
	* @return Boolean true on success
	*/
    
    function withdraw( $params )
    {
    	// Get the table
    	$db =& Jfactory::getDBO();

    	$requesterId = $db->quote( $params['requesterId'] );
    	$sessionTrackingId = $db->quote( $params['sessionTrackingId'] );
    	$withdrawalType = $db->quote( $params['withdrawalType'] );
    	$amount = $db->quote( $params['amount'] );
    	$requestedDate = $db->quote( $params['requestedDate'] );
    	    	
    	//get withdraw type id
    	$withdrawalTypeId = $db->quote($this->getWithdrawalTypeId( $params['withdrawalType'] ));
    	
    	$table = $db->nameQuote('#__withdrawal_request');
    	$insertQuery = "INSERT INTO $table
			( requester_id, session_tracking_id, withdrawal_type_id, amount, requested_date )
			VALUES ($requesterId, $sessionTrackingId, $withdrawalTypeId, $amount, $requestedDate )
		";
    	
		$db->setQuery($insertQuery);
		
		if(!$db->query())
		{
			return false;
		}
		
		//setup withdrawal_paypal record for paypal withdrawal requests
		if( 'paypal' == $params['withdrawalType'] )
		{
			$withdrawalRequestId = $db->quote(mysql_insert_id());
			$paypalEmail = $db->quote($params['paypalEmail']);
		
			$table = $db->nameQuote('#__withdrawal_paypal');
	    	$insertQuery = "INSERT INTO $table
				( withdrawal_request_id, paypal_id )
				VALUES ( $withdrawalRequestId, $paypalEmail )
			";
	    	$db->setQuery($insertQuery);
	    	
	    	if(!$db->query())
	    	{
	    		//remove withdrawal request record
	    		$table = $db->nameQuote('#__withdrawal_request');
	    		$deleteQuery = "Delete FROM $table WHERE id = $withdrawalRequestId";
	    		$db->setQuery($deleteQuery);
	    		$db->query();
	    		return false;
	    	}
		}
		
    		//setup withdrawal_moneybookers record for Moneybookers withdrawal requests
		if( 'moneybookers' == $params['withdrawalType'] )
		{
			$withdrawalRequestId = $db->quote(mysql_insert_id());
			$moneybookersEmail = $db->quote($params['moneybookersEmail']);
		
			$table = $db->nameQuote('#__moneybookers_withdrawal');
	    	$insertQuery = "INSERT INTO $table
				( withdrawal_request_id, moneybookers_id )
				VALUES ( $withdrawalRequestId, $moneybookersEmail )
			";
	    	$db->setQuery($insertQuery);
	    	
	    	if(!$db->query())
	    	{
	    		//remove withdrawal request record
	    		$table = $db->nameQuote('#__withdrawal_request');
	    		$deleteQuery = "Delete FROM $table WHERE id = $withdrawalRequestId";
	    		$db->setQuery($deleteQuery);
	    		$db->query();
	    		return false;
	    	}
		}
		
		return true;
    }
    
	/**
	 * Get stored withdrawal type id
	 *
	 * @param 	String		the keyword of withdrawal type
	 * @return	Integer
	 */
	private function getWithdrawalTypeId( $keyword )
	{
		$withdrawalTypeId = NULL;
		
		$db =& Jfactory::getDBO();
		
		$table = $db->nameQuote('#__withdrawal_type');
		
		$query = "SELECT id FROM $table
			 WHERE keyword = " . $db->quote($keyword) . " LIMIT 1";
		;
		
		$db->setQuery($query);
		$rs = $db->loadObject();
		if($rs)
		{
			$withdrawalTypeId = $rs->id;
		}
		
		return $withdrawalTypeId;
	}

	/**
	 * Validate if a withdrawal type is valid
	 *
	 * @param 	String		the keyword of withdrawal type
	 * @return	Boolean		true on valid withdrawal type
	 */
	function validateWithdrawalType( $withdrawalType )
	{	
		$db =& JFactory::getDBO();
		
		$table = $db->nameQuote('#__withdrawal_type');
		
		$query = "SELECT id FROM $table
			 WHERE keyword = " . $db->quote($withdrawalType) . " LIMIT 1";
		
		$db->setQuery($query);
		$rs = $db->loadObject();
		
		return (bool)$rs;
	}
	
}
?>