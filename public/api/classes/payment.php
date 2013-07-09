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
			return OutputHelper::json(500, array('error_msg' => 'Please login to make a deposit'  ));
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
	 /**
	 * Method to make withdraw requests
	 *
	 * @params POST data
	 * @return string
	 */
	public function doWithdrawRequest() {
		
   		$session =& JFactory::getSession();
    	$loginUser =& JFactory::getUser();
    	$config =& JComponentHelper::getParams( 'com_payment' );
    	
    	if( $loginUser->guest )
    	{
    		return OutputHelper::json(500, array('error_msg' => 'Please login first.' ));
    	}

		if (!class_exists('PaymentModelWithdrawalrequest')) {
			JLoader::import('withdrawalrequest', JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models');
		}
		
		if (!class_exists('PaymentModelAccounttransaction')) {
			JLoader::import('accounttransaction', JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models');
		}		

    	//$model =& $this->getModel( 'withdrawalrequest');
    	$model = new PaymentModelWithdrawalrequest();
		
		$paymentModel = new PaymentModelAccounttransaction();
    	
    	$withdrawalType = null;
    	$err = array();
    	
    	//don't trust the value posted. we make sure the value is what we want.
    	$withdrawalType = JRequest::getVar('withdrawalType') ? JRequest::getVar('withdrawalType') : 'moneybookers';
    	
    	$amount = JRequest::getVar($withdrawalType . '_amount');
    	$email = JRequest::getVar($withdrawalType . '_email');
    	if( 'bank' == $withdrawalType )
    	{
    		$minWithdrawal = $config->get('eway_min_withdrawal');
    	}
    	elseif('moneybookers' == $withdrawalType)
    	{
    		$minWithdrawal = $config->get('moneybookers_min_withdrawal');
    	}
    	else
    	{
    		$minWithdrawal = $config->get('paypal_min_withdrawal');
    	}
    	
    	if( !$withdrawalType )
    	{
    		$err['formError'] = 'Invalid Form';
    	}
    	else if( !$model->validateWithdrawalType( $withdrawalType ) )
    	{
    		//TO DO: send web alert email
    		$err['formError'] = 'Internal Error. Please contact webmaster.';
    	}
    	
    	if( !preg_match('/^[0-9\.]+$/', $amount) || $amount <= 0  )
    	{
    		$err[$withdrawalType . '_amount'] = 'Please enter a number';
    	}
    	else if( $amount < $minWithdrawal )
    	{
    		$err[$withdrawalType . '_amount'] = 'The minimum withdrawal amount is ' . $minWithdrawal . ' dollars';
    	}
    	else if( $amount > ($paymentModel->getTotal()/100))
    	{
    		$err[$withdrawalType . '_amount'] = 'Your account balance is not enough for this withdrawal';
    	}
    	
    	if( $withdrawalType == 'paypal' || $withdrawalType == 'moneybookers')
    	{
    		if( '' == $email )
    		{
    			$err[$withdrawalType . '_email'] = 'Please enter an email.';
    		}
    		else if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
    		{
    			$err[$withdrawalType . '_email'] = 'Invalid email';
    		}
    	}
    	
    	if( count($err) > 0 )
    	{

    		return OutputHelper::json(500, array('error_msg' => $err ));	
    		
    	}
    	
    	$amountCents = round($amount * 100);
    	$ts = time(); // we generate a timestamp here, which will be stored as requested date and also used in email.
    	$params = array(
    		'requesterId' => $loginUser->get('id'),
    		'withdrawalType' => $withdrawalType,
    		'amount' => $amountCents,
    		'sessionTrackingId' => $session->get( 'sessionTrackingId' ),
    		'requestedDate' => date('Y-m-d H:i:s', $ts),
    	);
    	
    	if( 'paypal' == $withdrawalType )
    	{
    		$params['paypalEmail'] = $email;
    	}
    	
    	if( 'moneybookers' == $withdrawalType )
    	{
    		$params['moneybookersEmail'] = $email;
    	}
    	
    	if (!$model->withdraw( $params ))
    	{

    		return OutputHelper::json(500, array('error_msg' => 'Withdrawal Failed. Please contact webmaster.' ));
    		
    	}
    	
		if (!class_exists('TopbettaUserModelTopbettaUser')) {
			JLoader::import('topbettauser', JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models');
		}	    	
		
		$userModel = new TopbettaUserModelTopbettaUser();
    	$user = $userModel->getUser();
		
    	
    	$senderEmail	= $config->get('sender_email');
		$senderName		= $config->get('sender_name');
		
    	$amountFormatted = sprintf('$%.2f', $amountCents / 100);
    	$requestedDate = date("F j, Y, g:i a", $ts);
    	
    	$withdrawalMethod = null;
    	$accountInfo = null;
    	switch( $withdrawalType )
    	{
    		case 'bank':
    			$withdrawalMethod = 'Bank Account';
    		break;
    		case 'paypal':
    			$withdrawalMethod = 'PayPal Account';
    			$accountInfo = $email;
    		case 'moneybookers':
    			$withdrawalMethod = 'MoneyBookers Account';
    			$accountInfo = $email;
    		break;
    	}
    	
    	$emailBody = $config->get('withdrawal_notify_email_body');
    	$emailSubject = $config->get('withdrawal_notify_email_subject');
    	
    	// send email
    	require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'helpers' . DS . 'helper.php');
    	require_once (JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'helpers' . DS . 'helper.php');		
    	if( $userEmail = $loginUser->email )
    	{	
			$mailer = new UserMAIL();

			$replacements = array(
	   			'[first name]' => $user->first_name,
				'[name]' => $loginUser->name,
	   			'[requested date]' => $requestedDate,
	   			'[amount]' => $amountFormatted,
	   			'[amount raw]' => $amount,
	   			'[withdrawal method]' => $withdrawalMethod,
	   			'[withdrawal account]' => $accountInfo,
	   			'[help email]' => $config->get('help_email'),
   			);
   			$emailBody = PaymentHelper::variableReplace($replacements, $emailBody);
    		
    		$mailer->setSender(array($senderEmail, $senderName));
    		$mailer->addReplyTo(array($senderEmail));
    		
    		$mailer->addRecipient($userEmail);
    		$mailer->setSubject($emailSubject);
    		$mailer->setBody($emailBody);
    		$mailer->IsHTML(false);
			$mailer->Send();
    	}
    	
    	//send email to admin
    	$mailer = new JMail();
    	$mailer->setSender(array($senderEmail, $senderName));
    	$mailer->addReplyTo(array($senderEmail));
    	$mailer->addRecipient($senderEmail);
    	
    	$mailer->setSubject('Withdrawal Request from ' . $user->username);
    	
    	$emailBody	 = "A request for withdrawal has been made on $requestedDate for user {$user->username}\n\n";
    	$emailBody 	.= "Withdrawal Details:\nUser: {$user->first_name} {$user->last_name}\nAmount: $amountFormatted\nMethod: $withdrawalMethod\nAccount: $accountInfo\n\n";
		$emailBody	.= 'Account balance: ' . '$'.number_format($paymentModel->getTotal()/100, 2, '.', ',') . "\n";
		$emailBody	.= "Bank account details:\nBank: $user->bank_name\nAccount name: $user->account_name\nBSB: $user->bsb_number\nAccount no.: $user->bank_account_number" . "\n\n";
    	$emailBody	.= 'ID Verified: ' . ($user->identity_verified_flag ? 'Yes' : 'No') . "\n\n";
    	$emailBody	.= "Contact Details:\n";
    	$emailBody	.= "Email: {$user->email}\nMobile Number: {$user->msisdn}\nHome Number: {$user->phone_number}";
    	
    	$mailer->setBody($emailBody);
    	$mailer->IsHTML(false);
    	$mailer->Send();
    	
    	return OutputHelper::json(200, array('msg' => 'Request received.'));	
		
	}

	public function setBetLimit() {
				
		return OutputHelper::json(200, array('msg' => 'Bet Limit set!.'));	
		//return OutputHelper::json(500, array('error_msg' => 'NO Bet Limit set!.'));	
		
	}
		

}
?>
