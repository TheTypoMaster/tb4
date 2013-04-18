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

class PaymentModelEwayrequestlog extends JModel
{
    /**
	* Method to add an eway log
	*
	* @param  string user id
	* @param  int total payment amount
	* @param  string card holder's name
	* @param  array eway response
	* @return boolean true on success
	*/
    function addLog( $userId, $totalAmount, $cardHoldersName, $ewayResponse)
    {
    	// Get the table
    	$db =& Jfactory::getDBO();

    	$userId = $db->quote($userId);
    	$totalAmount = $db->quote($totalAmount);
    	$cardHoldersName = $db->quote($cardHoldersName);
    	$trxnStatus = $db->quote($ewayResponse['EWAYTRXNSTATUS']);
    	$trxnReference = $db->quote($ewayResponse['EWAYTRXNNUMBER']);
    	$authCode = $db->quote($ewayResponse['EWAYAUTHCODE']);
    	$returnAmount = $db->quote($ewayResponse['EWAYRETURNAMOUNT']);
    	$trxnError = $db->quote($ewayResponse['EWAYTRXNERROR']);
    	$invoiceNumber = $db->quote($ewayResponse['EWAYTRXNOPTION1']);
		
    	$table = $db->nameQuote('#__eway_request_log');
    	$insertQuery = "INSERT INTO $table
			( user_id, total_amount, card_holders_name, trxn_status, trxn_reference, invoice_number, auth_code, return_amount, trxn_error, request_date )
			VALUES ( $userId, $totalAmount, $cardHoldersName, $trxnStatus, $trxnReference, $invoiceNumber, $authCode, $returnAmount, $trxnError, now())
		";
    	
		$db->setQuery($insertQuery);
		
		return $db->query();
    }
}
?>