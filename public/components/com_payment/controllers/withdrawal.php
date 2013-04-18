<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once JPATH_COMPONENT . DS . 'controller.php';
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

/**
 * Withdrawal Controller
 *
 * @package Joomla
 * @subpackage payment
 */
class PaymentControllerWithdrawal extends PaymentController {
	
    /**
     * Constructor
     * 
     * @return void
     */
    function __construct()
    {
        parent::__construct();
    }

    /**
	* Method to diplay the form
	*
	* @return Boolean true on success
	*/
    function display()
    {
    	$view = JRequest::getVar( 'view', 'withdrawalrequest');
    	$layout = JRequest::getVar( 'layout', 'default' );
    	
    	$view =& $this->getView( $view, 'html');
    	
    	$model =& $this->getModel( 'withdrawalrequest');
    	
    	$view->setModel( $model, true );
    	$view->setLayout( $layout );
    	
    	$session =& JFactory::getSession();
    	if( $sessFormData = $session->get('sessFormData', null, 'withdrawal') )
    	{
		    $formData = array();
    		if( $sessFormErrors = $session->get('sessFormErrors', null, 'withdrawal') )
    		{
    			$view->assign( 'formErrors', $sessFormErrors);
    			$session->clear('sessFormErrors', 'withdrawal');
    		}
    		
    		foreach($sessFormData as $k => $data) {
    			$formData[$k] = stripslashes($data);
    		}
    		
    		$view->assign('formData', $formData);
    		$session->clear('sessFormData', 'withdrawal');
    	}
		
		$view->assign('show_bankdeposit', $this->_showBankDeposit());

    	$view->assign( 'itemid', JRequest::getVar( 'Itemid' ) );
    	
    	$view->display();
    }
    
    /**
	* Method to withdraw money
	*
	* @return Boolean true on success
	*/
    function withdraw()
    {
    	$session =& JFactory::getSession();
    	$loginUser =& JFactory::getUser();
    	$config =& JComponentHelper::getParams( 'com_payment' );
    	
    	if( $loginUser->guest )
    	{
    		$this->setRedirect( '/');
    		return false;
    	}
    	
    	$model =& $this->getModel( 'withdrawalrequest');
    	
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
    	
    	$itemId = JRequest::getVar( 'itemid' );
    	$redirectTo = '/user/account/withdrawal-request/type/' . $withdrawalType;
    	
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
    	else if( $amount > ($loginUser->account_balance->getTotal()/100))
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
    		$session->set( 'sessFormErrors', $err, 'withdrawal' );
    		$session->set( 'sessFormData', $_POST, 'withdrawal');
    		$this->setRedirect( $redirectTo, 'There were some errors processing this form. See messages below.', 'error' );
    		
    		return false;
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
    		//TO DO: send web alert email to tech
    		$this->setRedirect( $redirectTo, 'Withdrawal Failed. Please contact webmaster.', 'error' );
    		return false;
    	}
    	
    	$this->setRedirect( $redirectTo, 'Your withdrawal request has been sent successfully.' );
    	
    	$user_model	=& $this->getModel('TopbettaUser', 'TopbettaUserModel');
    	$user		= $user_model->getUser();
    	
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
		$emailBody	.= 'Account balance: ' . '$'.number_format($loginUser->account_balance->getTotal()/100, 2, '.', ',') . "\n";
		$emailBody	.= "Bank account details:\nBank: $user->bank_name\nAccount name: $user->account_name\nBSB: $user->bsb_number\nAccount no.: $user->bank_account_number" . "\n\n";
    	$emailBody	.= 'ID Verified: ' . ($user->identity_verified_flag ? 'Yes' : 'No') . "\n\n";
    	$emailBody	.= "Contact Details:\n";
    	$emailBody	.= "Email: {$user->email}\nMobile Number: {$user->msisdn}\nHome Number: {$user->phone_number}";
    	
    	$mailer->setBody($emailBody);
    	$mailer->IsHTML(false);
    	$mailer->Send();
    	
    	return true;
    }
}
?>
