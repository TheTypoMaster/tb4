<?php
/**
 * @version		$Id: payment.php  Michael Costa $
 * @package		API
 * @subpackage
 * @copyright	Copyright (C) 2012 Michael Costa. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
jimport('joomla.application.component.controller');
jimport( 'joomla.environment.request' ); 
jimport( 'joomla.user.user' );
jimport('joomla.user.helper');

class Api_Payment extends JController {

	function Api_Payment() {

	}

	 /**
	 * Method to make instant credit card deposits
	 *
	 * @params POST data
	 * @return string
	 */
	public function doInstantDeposit() {

        $loggedUser =& JFactory::getUser();
        if ($loggedUser->guest) {
			return OutputHelper::json(500, array('error' => 'Please login to make a deposit'  ));
		}
      
	    $session	=& JFactory::getSession();
		$config		=& JComponentHelper::getParams( 'com_payment' );

		require_once (JPATH_BASE.DS.'components'.DS.'com_payment'.DS.'classes'.DS.'class.eway.php');
		require_once (JPATH_BASE.DS.'components'.DS.'com_payment'.DS.'libraries'.DS.'validatecreditcard.php');
		require_once (JPATH_BASE.DS.'components'.DS.'com_payment'.DS.'models'.DS.'accounttransaction.php');
		$model			= new PaymentModelAccounttransaction();
		require_once (JPATH_BASE.DS.'components'.DS.'com_payment'.DS.'models'.DS.'ewayinvoicenumber.php');
		$invoiceModel	= new PaymentModelEwayinvoicenumber();
		    	
		$eway			= new eway();
		    	
		$minDeposit		= $config->get('eway_min_deposit');
		    	
		$err			= array();
		    	
		$cardName	= JRequest::getVar('name', '', 'post');
		$cardNumber	= JRequest::getVar('card_number', '', 'post');
		$cardType	= JRequest::getVar('card_type', '', 'post');
		$expMonth	= JRequest::getVar('expiry_month', '', 'post');
		$expYear	= JRequest::getVar('expiry_year', '', 'post');
		$cvc		= JRequest::getVar('cvc', '', 'post');
		$amount		= JRequest::getVar('amount', '', 'post');
		    	
		    	
		$userNames	= explode(' ', trim($loggedUser->name));
				
		$firstName	= ucfirst(strtolower(array_shift($userNames)));
		$lastName	= ucwords(strtolower(implode( ' ', $userNames)));
		   	
		if (!$config->get('eway_enabled')) {
			$err['eway_system'] = 'Sorry, at the moment we don\'t accept credit card deposit';
		}
		
		if ('' == $cardName) {
			$err['name'] = 'Please enter the card holder\'s name';
		}
		 
		if ('' == $cardNumber) {
			$err['card_number'] = 'Please enter card number';
		} else {
			$cardType = validate_cc_number($cardNumber);
			if (false == $cardType) {
				$err['card_number'] = 'Invalid card number';
			}
		}
		    	
		if (!isset($expYear) || !isset($expMonth)) {
			$err['expiry'] = 'Invalid Expiry';
		}
				
		if (!isset($err['expiry']) && $expYear == date('y') && $expMonth < date('m')) {
			$err['eway_expiry'] = 'Card Expired';
		}
		
		if ('' == $cvc) {
			$err['cvc'] = 'Please enter a cvc number';
		} else if( strlen($cvc) != 3 ) {
			$err['cvc'] = 'Invalid CVC';
		}
		
		if (!preg_match('/^[0-9\.]+$/', $amount) || $amount <= 0) {
			$err['amount'] = 'Please enter a number';
		} else if ($amount < $minDeposit)
		{
			$err['amount'] = 'The minimum deposit amount is ' . $minDeposit . ' dollars';
		}
		
		if (count($err) > 0) {
			
			return OutputHelper::json(500, array('error_msg' => $err ,'form_data' => $_GET  ));  
		}
		
		$invoiceNumber = $invoiceModel->generateInvoiceNumber();
		 	
		if (empty($invoiceNumber)) {
			return OutputHelper::json(500, array('error_msg' => 'Interal Error. Please try again later.' )); 
		}
				
	    $amountCents	= $amount*100;
		$userId			= $loggedUser->id;
		 		
		$eway->setTransactionData("TotalAmount", $amountCents );
		$eway->setTransactionData("CustomerFirstName", $firstName);
		$eway->setTransactionData("CustomerLastName", $lastName);
		$eway->setTransactionData("CustomerEmail", $loggedUser->email);
		$eway->setTransactionData("CustomerAddress", '');
		$eway->setTransactionData("CustomerPostcode", '');
		$eway->setTransactionData("CustomerInvoiceDescription", 'TopBetta Pty Ltd - Credit Card Deposit');
		$eway->setTransactionData("CustomerInvoiceRef", $invoiceNumber);
		$eway->setTransactionData("CardHoldersName", $cardName);
		$eway->setTransactionData("CardNumber", $cardNumber);
		$eway->setTransactionData("CardExpiryMonth", $expMonth);
		$eway->setTransactionData("CardExpiryYear", $expYear);
		$eway->setTransactionData("TrxnNumber", '');
		$eway->setTransactionData("Option1", $invoiceNumber);
		$eway->setTransactionData("Option2", '');
		$eway->setTransactionData("Option3", '');
		$eway->setTransactionData("CVN", $cvc);
		
		$ewayResponse	= $eway->makePayment();
		
		require_once (JPATH_BASE.DS.'components'.DS.'com_payment'.DS.'models'.DS.'ewayrequestlog.php');
		//make an eway log
		$logModel		= new PaymentModelEwayrequestlog();
		
		$logModel->addLog($userId, $amountCents, $cardName, $ewayResponse);
				
		if ('True' == $ewayResponse['EWAYTRXNSTATUS']) {
			$sessionTrackingId = $session->get('sessionTrackingId');

			$amountCents = $ewayResponse['EWAYRETURNAMOUNT'];
			//$amountCents = $amountCents / 100;
			
			$params= array(
				'amount'					=> $amountCents,
				'recipient_id'				=> $userId,
				'giver_id'					=> $userId,
				'session_tracking_id'		=> $sessionTrackingId,
				'notes'						=> 'Instant Deposit From Mobile',
				'account_transaction_type'	=> 'ewaydeposit',
			);
			
			if (!$model->newTransaction($params)) {
				return OutputHelper::json(500, array('error_msg' => $ewayResponse['EWAYTRXNERROR'] , 'internal_error' => 'Internal Error! Please contact webmaster!' ));
			}

			return OutputHelper::json(200, array('msg' => $ewayResponse['EWAYTRXNNUMBER']));
		
		} else {
			
             return OutputHelper::json(500, array('error_msg' => $ewayResponse['EWAYTRXNERROR'] ));		
		}
		
		
		
			
	}
	

}
?>
