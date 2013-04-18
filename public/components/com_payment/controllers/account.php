<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once JPATH_COMPONENT . DS . 'controller.php';
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );


/**
 * Account Controller
 *
 */
class PaymentControllerAccount extends PaymentController {
	
    /**
     * Constructor
     * 
     * @return void
     */
	public function __construct()
    {
		$authenticate = array(
			'display'
		);
    	
		$user	=& JFactory::getUser();
    	$task	= JRequest::getVar('task', 'display');
    	
        parent::__construct();
        
    	if ($user->guest && in_array($task, $authenticate)) {
      		$msg = JText::_("You need to login to access this part of the site.");
			$this->setRedirect('/user/register', $msg, 'error');
			$this->redirect();
		}
    }

    /**
	* Method to diplay the form
	*
	* @return Boolean true on success
	*/
	public function display()
    {
    	$layout	= JRequest::getVar('layout', 'default');
    	
    	switch ($layout) {
    		case 'transactions':
    			$this->transactions();
    			break;
    		case 'instantdeposit':
    			$this->instantdeposit();
		    	break;
    	}
    }
    
    public function transactions()
    {
		global $mainframe, $option;
		
    	$transaction_model		=& $this->getModel('accounttransaction');
    	$tournament_sport_model	=& $this->getModel('TournamentSport', 'TournamentModel');
    	
    	$racing_sport_list		= $tournament_sport_model->excludeSports;
    	$transaction_list		= $transaction_model->listTransactions();
    	
    	$page					=& $transaction_model->getPagination();
    	
    	$lists = array();
    	
    	$transaction_type	= JRequest::getVar('transaction_type', null);
    	$filter_from_date	= $mainframe->getUserStateFromRequest($option.'filter_transaction_from_date', 'filter_transaction_from_date');
		$filter_to_date		= $mainframe->getUserStateFromRequest($option.'filter_transaction_to_date', 'filter_transaction_to_date');
    	
    	$lists['from_date']	= $filter_from_date;
    	$lists['to_date']	= $filter_to_date;
    	
		$view =& $this->getView('accounttransaction', 'html');
    	
    	$view->setModel($transaction_model, true);
    	
    	$view->assignRef('transaction_list', $transaction_list);
    	$view->assignRef('racing_sport_list', $racing_sport_list);
		$view->assignRef('page', $page);
		$view->assignRef('lists', $lists);
    	$view->assign('itemid', JRequest::getVar('Itemid'));
		$view->assign('transaction_type', $transaction_type);
		
		$bet_model =& $this->getModel('Bet', 'BettingModel');
		$view->setModel($bet_model);
		
		$meeting_model =& $this->getModel('Meeting', 'TournamentModel');
		$view->setModel($meeting_model);
		
		$race_model =& $this->getModel('Race', 'TournamentModel');
		$view->setModel($race_model);
		
		$runner_model =& $this->getModel('Runner', 'TournamentModel');
		$view->setModel($runner_model);
    	
    	$view->display();
    }
    
    public function instantdeposit()
    {
    	$model	=& $this->getModel('accounttransaction');
    	
    	$paypalPostUrl	= JRoute::_('/user/account/instant-deposit/type/paypal/process');
    	$ewayPostUrl	= JRoute::_('/user/account/instant-deposit/type/eway/process');
    	$moneybookersPostUrl	    = JRoute::_('/user/account/instant-deposit/type/moneybookers/process');
    	
    	$view =& $this->getView('accounttransaction', 'html');
    	
    	$view->assign('paypalPostUrl', $paypalPostUrl);
    	$view->assign('ewayPostUrl', $ewayPostUrl);
    	$view->assign('moneybookersPostUrl', $moneybookersPostUrl);
    	$view->assign('options', $model->options);
    	$view->assign('itemid', JRequest::getVar('Itemid'));
    	$view->assign('isAfterPayment', false);
    	
    	$loginUser	=& JFactory::getUser();
    	$view->assign('userMoneybookersEmail', $loginUser->email);
    
    	$session =& JFactory::getSession();
    	if ($sessFormData = $session->get('sessFormData', null, 'account')) {
    		$formData = array();
    		if ($sessFormErrors = $session->get('sessFormErrors', null, 'account')) {
    			$view->assign( 'formErrors', $sessFormErrors);
    			$session->clear('sessFormErrors', 'account');
    		}
    		foreach ($sessFormData as $k => $data) {
    			$formData[$k] = stripslashes($data);
    		}
    		$view->assign('formData', $formData);
    		$session->clear('sessFormData', 'account');
    	}
		
		$view->assign('show_bankdeposit', $this->_showBankDeposit());

    	$view->display();
    }
    
    /**
	* Method to process paypal online deposits
	*
	* @return void
	*/
    function paypal()
    {
    	$act = JRequest::getVar( 'act' );
    	//the page can only be accessed by 'POST'
    	if($_SERVER['REQUEST_METHOD'] != 'POST' && 'success' != $act) {
    		$this->setRedirect('/');
			$this->redirect();
    		return;
    	}
    	
		require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'class.paypal.php');
    	$model	=& $this->getModel( 'accounttransaction');
    	$config	=& JComponentHelper::getParams( 'com_payment' );
    	
    	$paypal = new Paypal();
    	
    	switch ($act) {
    		case 'process':
    			$session			=& JFactory::getSession();
    			
				$amount				= JRequest::getVar('paypal_amount', '', 'post');
				$email				= JRequest::getVar('paypal_email', '', 'post');
				
				$session			=& JFactory::getSession();
				$sessionTrackingId	= $session->get('sessionTrackingId');
				
				$minDeposit			= $config->get('paypal_min_deposit');
				
				if (!$sessionTrackingId) {
					//re-direct to login page?
					$err['paypal_system']	= 'Please re-login to make an instant deposit';
				}
				
				if (!$config->get('paypal_enabled')) {
					$err['paypal_system']	= 'Sorry, at the moment we don\'t accept paypal deposit';
				}
				
    			$itemId		= JRequest::getVar('itemid');
				$redirectTo	= '/user/account/instant-deposit/type/paypal';
				
    	    	if(!preg_match('/^[0-9\.]+$/', $amount) || $amount <= 0) {
		    		$err['paypal_amount']	= 'Please enter a number';
		    	} else if ( $amount < $minDeposit ) {
		    		$err['paypal_amount']	= 'The minimum deposit amount is ' . $minDeposit . ' dollars';
		    	}
    			
    	    	if ('' == $email) {
	    			$err['paypal_email']	= 'Please enter an email';
	    		} else if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
	    			$err['paypal_email']	= 'Invalid email';
	    		}
	    		
    	    	if (count($err) > 0) {
		    		$session->set( 'sessFormErrors', $err, 'account' );
		    		$session->set( 'sessFormData', $_POST, 'account');
		    		$this->setRedirect( $redirectTo, 'There were some errors processing this form. See messages below.', 'error' );
		    		
		    		return false;
		    	}
		    	
		    	//display the auto commited form
		        $view	= JRequest::getVar('view', 'accounttransaction');
		    	$view	=& $this->getView( $view, 'html');
    			$view->setLayout('paypal');
    			
    			$view->assign('response', $paypal->generateForm());
    			$view->display();
    		break;
    		case 'ipn':
    			$paypalLogModel	=& $this->getModel('paypalipnlog');
    			//do ipn callback
    			$ipnResponse	= $paypal->ipnCallback();
    			
    			switch ($ipnResponse) {
    				case 'VERIFIED':
	    				$paymentStatus	= JRequest::getVar('payment_status', '', 'post');
	    				$receiver_email	= JRequest::getVar('receiver_email', '', 'post');
	    				$config			=& JComponentHelper::getParams('com_payment' );
	    				
	    				if ('completed' == strtolower($paymentStatus) && $receiver_email == $config->get('paypal_account')) {
		    				$txnId = JRequest::getVar('txn_id', '', 'post');
		    				
		    				if(!$paypalLogModel->IsExistingPaypalTransaction($txnId)) {
		    					$amount				= JRequest::getVar('mc_gross', '', 'post');
		    					$custom				= JRequest::getVar('custom', '', 'post');
		    					$payerEmail			= JRequest::getVar('payer_email', '' , 'post');
		    					$payerId			= JRequest::getVar('payer_id', '', 'post');
			    				$custom				= unserialize($custom);
			    				$userId				= $custom['user_id'];
			    				$sessionTrackingId	= $custom['session_tracking_id'];
			    				
			    				$notes = "Paypal transaction id: $txnId\n";
			    				$notes .= "Payer Email: $payerEmail\n";
			    				$notes .= "Payer Id: $payerId";
			    						
			    				$params= array(
			    					'amount'					=> $amount * 100,
			    					'recipient_id'				=> $userId,
			    					'giver_id'					=> $userId,
			    					'session_tracking_id'		=> $sessionTrackingId,
			    					'notes'						=> $notes,
			    					'account_transaction_type'	=> 'paypaldeposit',
			    				);
			    				
		    					if (!$model->newTransaction($params)) {
		    						//TO DO : send email?
		    					}
		    				}
	    				}
	    				break;
    				case 'INVALID':
    					//TO DO : send email?
    					break;
    				case 'UNKNOWN':
    					//TO DO : send email?
    					break;
    			}
    			
				$paypalLogModel->addLog($ipnResponse);
				exit;
    		break;
    		case 'success':
    			$model	=& $this->getModel('paypalipnlog');
    			$view	= JRequest::getVar('view', 'accounttransaction');
    			$view	=& $this->getView($view, 'html');
    			$view->setModel($model);
    			$view->setLayout('instantdeposit');
    
    			$txnId	= JRequest::getVar('txn_id', '', 'post');
    			//new paypal return form doesn't support post return
    			//get the most recent payment txnId
    			if (!$txnId) {
    				$user	= JFactory::getUser();
    				$txnId	= $model->getMostRecentTxn($user->get('id'));
    			}
    			
    			if (!$txnId) {
    				//return to home page
		    		$this->setRedirect('/');
		    		$this->redirect();
		    		return;
    			}
    			
				//get the record from ipn log
    			$paymentInfo = $model->getLog($txnId);
    			
    			//give some time for finishing ipn
    			sleep(5);
    	    	$i = 1;
    			while ((!$paymentInfo || $paymentInfo->notification_flag == 1) && $i < 6) {
    				//wait up to 30 second to make sure ipn set up the record
    				sleep(5);
    				$paymentInfo = $model->getLog($txnId);
    				$i++;
    			}
    			
    			$loginUser =& JFactory::getUser();
    			
    			//we don't want user see other people's transactions
    			if ($paymentInfo && $loginUser->id != $paymentInfo->user_id) {
    				$this->setRedirect('/');
		    		$this->redirect();
    				return;
    			}
    			
    			$model->updateNotification($txnId);
    			
    			$paymentInfo->type = 'paypal';
    			
				$view->assignRef('paymentInfo', $paymentInfo);
				$view->assign('isAfterPayment', true);
    			$view->display();
    			
    		break;
    	}
    }
    
	/**
	* Method to process eway online deposits
	*
	* @return void
	*/
    function eway()
    {
		$session	=& JFactory::getSession();
		$act		= JRequest::getVar( 'act' );
    	$config		=& JComponentHelper::getParams( 'com_payment' );
		
		switch ($act) {
			case'process':
			    //the page can only be accessed by 'POST'
		    	if ( $_SERVER['REQUEST_METHOD'] != 'POST') {
		    		$this->setRedirect( '/' );
		    		return;
		    	}
				require_once (JPATH_COMPONENT_SITE.DS.'classes'.DS.'class.eway.php');
				require_once (JPATH_COMPONENT_SITE.DS.'libraries'.DS.'validatecreditcard.php');
				
    			$itemId			= JRequest::getVar( 'itemid' );
				$redirectTo		= '/user/account/instant-deposit/type/card';
				
		    	$model			=& $this->getModel( 'accounttransaction');
		    	$invoiceModel	=& $this->getModel( 'ewayinvoicenumber');
		    	
		    	$eway			= new eway();
		    	
		    	$minDeposit		= $config->get('eway_min_deposit');
		    	
				$err			= array();
		    	
		    	$cardName	= JRequest::getVar('eway_name', '', 'post');
		    	$cardNumber	= JRequest::getVar('eway_card_number', '', 'post');
		    	$cardType	= JRequest::getVar('eway_card_type', '', 'post');
		    	$expMonth	= JRequest::getVar('eway_expiry_month', '', 'post');
		    	$expYear	= JRequest::getVar('eway_expiry_year', '', 'post');
		    	$cvc		= JRequest::getVar('eway_cvc', '', 'post');
		    	$amount		= JRequest::getVar('eway_amount', '', 'post');
		    	
		    	$loginUser	=& JFactory::getUser();
		    	$userNames	= explode(' ', trim($loginUser->name));
		    	$firstName	= ucfirst(strtolower(array_shift($userNames)));
		    	$lastName	= ucwords(strtolower(implode( ' ', $userNames)));
		    	
				if (!$config->get('eway_enabled')) {
					$err['eway_system'] = 'Sorry, at the moment we don\'t accept credit card deposit';
				}
		    	
		    	if ('' == $cardName) {
		    		$err['eway_name'] = 'Please enter the card holder\'s name';
		    	}
		    	
		    	if ('' == $cardNumber) {
		    		$err['eway_card_number'] = 'Please enter card number';
		    	} else {
		    		$cardType = validate_cc_number($cardNumber);
		    		if (false == $cardType) {
		    			$err['eway_card_number'] = 'Invalid card number';
		    		}
		    	}
		    	
				if (!isset($model->options['year'][$expYear]) || !isset($model->options['month'][$expMonth])) {
					$err['eway_expiry'] = 'Invalid Option';
				}
				
				if (!isset($err['eway_expiry']) && $expYear == date('y') && $expMonth < date('m')) {
					$err['eway_expiry'] = 'Card Expired';
				}
				
				if ('' == $cvc) {
					$err['eway_cvc'] = 'Please enter a cvc number';
				} else if( strlen($cvc) != 3 ) {
					$err['eway_cvc'] = 'Invalid CVC';
				}
				
		    	if (!preg_match('/^[0-9\.]+$/', $amount) || $amount <= 0) {
					$err['eway_amount'] = 'Please enter a number';
				} else if ($amount < $minDeposit)
		    	{
		    		$err['eway_amount'] = 'The minimum deposit amount is ' . $minDeposit . ' dollars';
		    	}
				
				if (count($err) > 0) {
		    		$session->set('sessFormErrors', $err, 'account');
		    		$session->set('sessFormData', $_POST, 'account');
		    		$this->setRedirect($redirectTo, 'There were some errors processing this form. See messages below.', 'error');
		    		
		    		return false;
				}
				
				$invoiceNumber = $invoiceModel->generateInvoiceNumber();
				
				if (empty($invoiceNumber)) {
		    		$this->setRedirect($redirectTo, 'Interal Error. Please try again later.', 'error');
		    		return false;
				}
				
				$amountCents	= $amount*100;
				$userId			= $loginUser->id;
				
				$eway->setTransactionData("TotalAmount", $amountCents );
				$eway->setTransactionData("CustomerFirstName", $firstName);
				$eway->setTransactionData("CustomerLastName", $lastName);
				$eway->setTransactionData("CustomerEmail", $loginUser->email);
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
				
				//make an eway log
				$logModel		= & $this->getModel('ewayrequestlog');
				
				$logModel->addLog($userId, $amountCents, $cardName, $ewayResponse);
				
		    	$view			= JRequest::getVar( 'view', 'accounttransaction');
		    	$view			=& $this->getView($view, 'html');
		    	
		    	$view->setModel($model);
		    	$view->setLayout('instantdeposit');
				
				$paymentInfo		= new stdClass;
				$paymentInfo->type	= 'eway';
				
				if ('True' == $ewayResponse['EWAYTRXNSTATUS']) {
					$sessionTrackingId = $session->get('sessionTrackingId');
		
					$notes	= 'EWAY transaction id: ' . $ewayResponse['EWAYTRXNNUMBER'] . "\n";
					$notes	.= 'Bank authorisation number:' . $ewayResponse['EWAYAUTHCODE'] . "\n";
					$notes	.= 'Invoice number:' . $ewayResponse['EWAYTRXNOPTION1'] . "\n";
					
					$amountCents = $ewayResponse['EWAYRETURNAMOUNT'];
					
					$paymentInfo->txn_id			= $ewayResponse['EWAYTRXNNUMBER'];
					$paymentInfo->bank_auth_code	= $ewayResponse['EWAYAUTHCODE'];
					$paymentInfo->invoice_number	= $ewayResponse['EWAYTRXNOPTION1'];
					$paymentInfo->amount			= $amountCents / 100;
					
				    $params= array(
		    			'amount'					=> $amountCents,
		    			'recipient_id'				=> $userId,
		    			'giver_id'					=> $userId,
		    			'session_tracking_id'		=> $sessionTrackingId,
		    			'notes'						=> $notes,
		    			'account_transaction_type'	=> 'ewaydeposit',
		    		);
		    		
		    		if (!$model->newTransaction($params)) {
		    			$paymentInfo->err =  'Internal Error! Please contact webmaster!';
		    		}
				} else {
					$paymentInfo->err = $ewayResponse['EWAYTRXNERROR'];
				}
				
				$session->set('paymentInfo', $paymentInfo, 'account');
		    	$this->setRedirect('/user/account/instant-deposit/type/eway/success');
				break;
			case 'success':
				$paymentInfo	= $session->get('paymentInfo', null, 'account');
				
				$view			= JRequest::getVar('view', 'accounttransaction');
		    	$view			=& $this->getView($view, 'html');
		    	$view->setLayout('instantdeposit');
				
		    	$view->assignRef('paymentInfo', $paymentInfo);
		    	$view->assign('isAfterPayment', true);
		    	$view->display();
				break;
		}
    }
    
    /**
	* Method to process moneybookers online deposits
	*
	* @return void
	*/
    function moneybookers()
    {

    	jimport('mobileactive.client.moneybookers');

    	try{
    		$act = JRequest::getVar( 'act' );
    		//the page can only be accessed by 'POST'
    		if($_SERVER['REQUEST_METHOD'] != 'POST' && 'success' != $act && 'mbstatus' != $act) {
    			$this->setRedirect('/');
    			$this->redirect();
    			return;
    		}
    		switch ($act) {
    			case 'process':
    				$client_moneybookers = new MoneyBookers();
    					
    				$model	=& $this->getModel( 'accounttransaction');
    				$config	=& JComponentHelper::getParams( 'com_payment' );
    					
    				$session			=& JFactory::getSession();
    					
    				$amount				= JRequest::getVar('moneybookers_amount', '', 'post');
    				$email				= JRequest::getVar('moneybookers_email', '', 'post');

    				$session			=& JFactory::getSession();
    				$sessionTrackingId	= $session->get('sessionTrackingId');

    				$minDeposit			= $config->get('moneybookers_min_deposit');
    					
    				$err			= array();
    					
    				if (!$sessionTrackingId) {
    					//re-direct to login page?
    					$err['moneybookers_system']	= 'Please re-login to make an instant deposit';
    				}

    				if (!$config->get('moneybookers_enabled')) {
    					$err['moneybookers_system']	= 'Sorry, at the moment we don\'t accept MoneyBookers deposit';
    				}

    				$redirectTo	= '/user/account/instant-deposit/type/moneybookers';

    				if(!preg_match('/^[0-9\.]+$/', $amount) || $amount <= 0) {
    					$err['moneybookers_amount']	= 'Please enter a number';
    				} else if ( $amount < $minDeposit ) {
    					$err['moneybookers_amount']	= 'The minimum deposit amount is ' . $minDeposit . ' dollars';
    				}
    					
    				if ('' == $email) {
    					$err['moneybookers_email']	= 'Please enter an email';
    				} else if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
    					$err['moneybookers_email']	= 'Invalid email';
    				}
    					
    				if (count($err) > 0) {
    					$session->set( 'sessFormErrors', $err, 'account' );
    					$session->set( 'sessFormData', $_POST, 'account');
    					$this->setRedirect( $redirectTo, 'There were some errors processing this form. See messages below.', 'error' );

    					return false;
    				}
    					
    				// Get User information to be passed as hidden fields in form
    				$user_model =& $this->getModel('TopbettaUser', 'TopbettaUserModel');
    				$user_data = $user_model->getUser();

    				$mb_model =& $this->getModel('Moneybookers');

    				$transaction_id = $mb_model->addTransaction($user_data->user_id, $amount, $email,$sessionTrackingId);

    				//$md5sig= md5($transaction_id);
    				//$mb_model->updateTransaction($md5sig,$transaction_id);

    				$params= array(
    					'user_id' 		=> $user_data->user_id,
    					'title'			=> $user_data->title,
		    			'first_name'	=> $user_data->first_name,
		    			'last_name'		=> $user_data->last_name,
		    			'street'		=> $user_data->street,
		    			'city'		    => $user_data->city,
		    			'state'			=> $user_data->state,
    					'country'		=> $user_data->country,
		    			'postcode'	    => $user_data->postcode,
						'msisdn'        => $user_data->msisdn,
						'dob_day' 		=> $user_data->dob_day,
						'dob_month' 	=> $user_data->dob_month,
						'dob_year' 		=> $user_data->dob_year,
    				    'transaction_id' => $transaction_id,
    				    //'md5sig'		=> $md5sig,
    				);

    				//display the auto commited form

    				$view	= JRequest::getVar('view', 'accounttransaction');
    				$view	=& $this->getView( $view, 'html');
    				$view->setLayout('moneybookers');
    					
    				$view->assign('response', $client_moneybookers->generateForm($params));
    				$view->display();
    				break;
    			case 'mbstatus':

    				// Dump Everything from MB -- only fro dev
    				/*
    				$fp = fopen('/var/www/topbetta.com/subdomains/sandeep/wfiles/mbstatus.log', 'a+') or exit("Unable to open file!");
    				$today = date("F j, Y, g:i a");
    				$results = print_r($_REQUEST, true);
    				fwrite($fp, $today."\n------STATUS-----\n".$results);
					*/
    				
    				// Get parameters from MB
    				$pay_to_email				= JRequest::getVar('pay_to_email', '', 'post');
    				$pay_from_email				= JRequest::getVar('pay_from_email', '', 'post');
    				$transaction_id				= JRequest::getVar('transaction_id', '', 'post');
    				$mb_transaction_id			= JRequest::getVar('mb_transaction_id', '', 'post');
    				$mb_amount 					= JRequest::getVar('mb_amount', '', 'post');
    				$mb_currency 				= JRequest::getVar('mb_currency', '', 'post');
    				$status						= JRequest::getVar('status', '', 'post');
    				$amount						= JRequest::getVar('amount', '', 'post');
    				$currency 					= JRequest::getVar('currency', '', 'post');
    				$user_id 					= JRequest::getVar('user_id', '', 'post');
    				$merchant_id 				= JRequest::getVar('merchant_id', '', 'post');
    				$md5sig 				    = JRequest::getVar('md5sig', '', 'post');
    				$session_tracking_id 		= JRequest::getVar('session_tracking_id', '', 'post');

    				$params= array(
    					'pay_to_email' 		=> $pay_to_email,
    					'pay_from_email'	=> $pay_from_email,
		    			'transaction_id'	=> $transaction_id,
		    			'mb_transaction_id'	=> $mb_transaction_id,
		    			'mb_amount'			=> $mb_amount,
		    			'mb_currency'		=> $mb_currency,
		    			'status'			=> $status,
    					'amount'			=> $amount,
		    			'currency'	    	=> $currency,
						'user_id'        	=> $user_id,
						'merchant_id' 		=> $merchant_id,
    					'session_tracking_id' => $session_tracking_id,
    					'md5sig'			=> $md5sig,
    				);
    				
    				$config	=& JComponentHelper::getParams( 'com_payment' );
					$merchant_id		= $config->get('moneybookers_merchant_id');
    				$secret_word		= $config->get('moneybookers_secret_word');
    				
    				/* Create the md5 string based on moneybookers document in order to compare
    				 * with the md5 string provided by moneybookers during success params
    				 */ 
    				$secretword= strtoupper(md5($secret_word));	
    				$concat_string = $merchant_id.$transaction_id.$secretword.$mb_amount.$mb_currency.$status;
    				$generated_md5 = strtoupper(md5($concat_string));
    				 
    				// add status for transactions
    				$model	=& $this->getModel('Moneybookers');
    				$trans_model	=& $this->getModel( 'accounttransaction');
    				// Add status to DB
    				$model->addTransactionStatus($params);

    				// Get the transaction from our records based
    				$transaction_log = $model->getTransactionLog($transaction_id);
    				if ($transaction_log) {

    					//fwrite($fp, $today."\n------Got the transaction Log-----\n");

    					// Update transaction status
    					if(isset($status) && $generated_md5 == $md5sig){
    						//fwrite($fp, $today."\n------Update transaction Log-----status:$status,transaction_id: $transaction_id\n$md5sig");
    						$model->updateTransactionStatus($transaction_id,$status);
    							
    						// Update the User Transaction Data record on successful deposit
    						if($status == 2){
    							$notes = "MoneyBookers Transaction Id: ".$mb_transaction_id;

    							$tparams= array(
			    					'amount'					=> $amount * 100,
			    					'recipient_id'				=> $user_id,
			    					'giver_id'					=> $user_id,
			    					'session_tracking_id'		=> $session_tracking_id,
			    					'notes'						=> $notes,
			    					'account_transaction_type'	=> 'moneybookersdeposit',
    							);

    							if (!$trans_model->newTransaction($tparams)) {
    								//TO DO : send email?
    							}
    						}
    							
    					}
    					//
    				}
    				 
    				fclose($fp);
    				echo "OK";
    				exit;
    				break;
 					// Case will be called after Customer return from Gateway
    				case 'success':
						
    				//display the auto commited form
					$paymentInfo->type = 'moneybookers';
					$view			= JRequest::getVar('view', 'accounttransaction');
		    		$view			=& $this->getView($view, 'html');
		    		$view->setLayout('instantdeposit');
				
		    		$view->assignRef('paymentInfo', $paymentInfo);
		    		$view->assign('isAfterPayment', true);
		    		$view->display();
    				
    				break;
    			case 'INVALID':
    				//TO DO : send email?
    				break;
    			case 'UNKNOWN':
    				//TO DO : send email?
    				break;
    		}
    	}
    	catch(Exception $e){
    		trigger_error("Problem with MoneyBookers client ['{$e->getMessage()}']");
    	}

    }

}
?>