<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

/**
 * Withdrawal Controller
 *
 * @package Joomla
 */
class PaymentControllerWithdrawal extends JController {
	
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
    	
    	global $mainframe;
		// Prepare list array
		$lists = array();
		// Get the user state
		$filter_order = $mainframe->getUserStateFromRequest($option.'filter_order','filter_order');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.'filter_order_Dir','filter_order_Dir', 'ASC');
		$filter_search = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_search', 'filter_withdrawal_search');
		$filter_requested_from_date = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_requested_from_date', 'filter_withdrawal_requested_from_date');
		$filter_requested_to_date = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_requested_to_date', 'filter_withdrawal_requested_to_date');
		$filter_fulfilled_from_date = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_fulfilled_from_date', 'filter_withdrawal_fulfilled_from_date');
		$filter_fulfilled_to_date = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_fulfilled_to_date', 'filter_withdrawal_fulfilled_to_date');
		$filter_from_amount = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_from_amount', 'filter_withdrawal_from_amount');
		$filter_to_amount = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_to_amount', 'filter_withdrawal_to_amount');
		$filter_status = $mainframe->getUserStateFromRequest($option.'filter_withdrawal_status', 'filter_withdrawal_status');
	
		// Build the list array for use in the layout
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['search'] = $filter_search;
		$lists['requested_from_date'] = $filter_requested_from_date;
		$lists['requested_to_date'] = $filter_requested_to_date;
		$lists['fulfilled_from_date'] = $filter_fulfilled_from_date;
		$lists['fulfilled_to_date'] = $filter_fulfilled_to_date;
		$lists['from_amount'] = $filter_from_amount;
		$lists['to_amount'] = $filter_to_amount;
		$lists['status'] = $filter_status;
		
		$requests =& $model->getRequests();
		
		$page =& $model->getPagination();
		// Assign references for the layout to use
		$view->assignRef('lists', $lists);
		$view->assignRef('requests', $requests);
		$view->assignRef('page', $page);
		
		$statusOptions = array(
			'' => 'All',
			'pending' => 'Pending',
			'approved' => 'Approved',
			'denied' => 'Denied',
		);
		$view->assignRef('statusOptions', $statusOptions );
		
    	$view->display();
    }
    
    /**
	* Method to edit withdrawal requests
	* 
	* @return Boolean true on success
	*/
    function edit()
    {
		$view = JRequest::getVar( 'view', 'withdrawalrequest');
    	$layout = JRequest::getVar( 'layout', 'edit' );
    	$requestId = JRequest::getVar( 'request' );
    	JRequest::setVar('hidemainmenu', 1);
    	
    	$view =& $this->getView( $view, 'html');
    	$model =& $this->getModel( 'withdrawalrequest');
    	$config =& JComponentHelper::getParams( 'com_payment' );
    	
		$request = $model->getRequest($requestId);
		
		$formData = array(
			'approved_flag' => $request->approved_flag,
			'notes' => $request->notes,
		);
		
		if( $request->approved_flag === null )
		{
			//only display save for the records which have not been operated.
			JToolBarHelper::save();
		}
		
		JToolBarHelper::cancel('cancel', 'Close');
		
    	if( !$request )
    	{
    		//request does not exist, redirect to the list page
    		$redirectTo = 'index.php?option=com_payment&c=withdrawal';
    		$this->setRedirect( $redirectTo );
    		return false;
    	}
    	
    	$view->setModel( $model, true );
    	$view->setLayout( $layout );
    	
		// Assign references for the layout to use
		$view->assignRef('request', $request);
		
		//Set up email template info
		/** To Do: use the config information **/
		$senderEmail = $config->get('sender_email');
		$senderName = $config->get('sender_name');
		
    	$userNames = explode(' ', trim($request->requester));
    	$firstName = ucwords(strtolower($userNames[0]));
    	$amount = $request->amount / 100;
    	$amountFormatted = sprintf('$%.2f', $amount );
    	$requestedDate = date("F j, Y, g:i a", strtotime($request->requested_date));
    		
    	$withdrawalMethod = null;
    	$accountInfo = null;
    	
    	switch( $request->withdrawal_type_keyword )
    	{
    		case 'bank':
    			$withdrawalMethod = 'Bank Account';
    		break;
    		case 'paypal':
    			$withdrawalMethod = 'PayPal Account';
    			$accountInfo = $request->paypal_id;
    		case 'moneybookers':
    			$withdrawalMethod = 'MoneyBookers Account';
    			$accountInfo = $request->moneybookers_id;	
    		break;
    	}
    	
    	$denialEmailSubject = $config->get('withdrawal_denial_email_subject');
    	$denialEmailBody = $config->get('withdrawal_denial_email_body');
    	$approvalEmailSubject = $config->get('withdrawal_approval_email_subject');
   		$approvalEmailBody = $config->get('withdrawal_approval_email_body');
   		
   		$replacements = array(
   			'[first name]' => $firstName,
   			'[name]' => $request->requester,
   			'[requested date]' => $requestedDate,
   			'[amount]' => $amountFormatted,
   			'[amount raw]' => $amount,
   			'[withdrawal method]' => $withdrawalMethod,
   			'[withdrawal account]' => $accountInfo,
   			'[help email]' => $config->get('help_email'),
   		);
   		
   		$approvalEmailBody = PaymentHelper::variableReplace($replacements, $approvalEmailBody);
   		$denialEmailBody = PaymentHelper::variableReplace($replacements, $denialEmailBody);

		$view->assign('senderName', $senderName);
		$view->assign('senderEmail', $senderEmail);
		$view->assign('denialEmailSubject', $denialEmailSubject);
		$view->assign('denialEmailBody', $denialEmailBody);
		$view->assign('approvalEmailSubject', $approvalEmailSubject);
		$view->assign('approvalEmailBody', $approvalEmailBody);
		
		//get the validation msg and keep the value entered after validation
		$session =& JFactory::getSession();
		
    	if( $sessFormData = $session->get('sessFormData', null, 'withdrawal') )
    	{
    		//print_r($session->get('sessFormErrors', null, 'withdrawal'));exit;
    		if( $sessFormErrors = $session->get('sessFormErrors', null, 'withdrawal') )
    		{
    			$view->assign( 'formErrors', $sessFormErrors);
    			$session->clear('sessFormErrors', 'withdrawal');
    		}
    		
    		$formData = array(
				'approved_flag' => stripslashes($sessFormData['approved_flag']),
				'notes' => stripslashes($sessFormData['notes']),
    			'send_email' => stripslashes($sessFormData['send_email']),
    			'notifying_email_subject' => stripslashes($sessFormData['notifying_email_subject']),
    			'notifying_email_body' => stripslashes($sessFormData['notifying_email_body']),
    		);
    		$session->clear('sessFormData', 'withdrawal');
    	}

		$view->assignRef('formData', $formData);
    	$view->display();
    	
    	return true;
    }
    
    /**
	* Method to save a withdrawal request
	* 
	* @return Boolean true on success
	*/
    function save()
    {
    	$model =& $this->getModel( 'withdrawalrequest' );
    	$session =& JFactory::getSession();
    	$loginUser =& JFactory::getUser();
    	$config =& JComponentHelper::getParams( 'com_payment' );
    	
    	$id = JRequest::getVar( 'id', '', 'post' );
    	$approvedFlag = JRequest::getVar( 'approved_flag', '', 'post');
    	$notes = JRequest::getVar( 'notes', '', 'post');
    	$sendEmail = JRequest::getVar( 'send_email', '', 'post');
    	$notifyingEmailSubject = JRequest::getVar( 'notifying_email_subject', '', 'post');
    	$notifyingEmailBody = JRequest::getVar( 'notifying_email_body', '', 'post');
    	
    	$failedRedirectTo = 'index.php?option='
			.JRequest::getVar('option')
			.'&c=' . JRequest::getVar('c')
			.'&task=edit&request=' . JRequest::getVar('id');
			
		$successRedirectTo = 'index.php?option='
			.JRequest::getVar('option')
			.'&c=' . JRequest::getVar('c')
			.'&task=display';
    	
			
    	//validate post params
    	$err = array();
    	$request = $model->getRequest($id);
    	
    	if( !$request )
    	{
    		$err['formError'] = 'The Record does not exist.';
    	}
    	else if( $request->approved_flag !== null )
    	{
    		$err['formError'] = 'This request has already been operated.';
    	}
    	
    	if( !in_array($approvedFlag, array('yes', 'no')))
    	{
    		$err['approved_flag'] = 'Invalid Option.';
    	}
    	else if( 'no' == $approvedFlag && ''== trim($notes))
    	{
    		$err['notes'] = 'You must enter a denial reason.';
    	}
    	
        if( $sendEmail && '' == $notifyingEmailSubject)
    	{
    		$err['notifying_email_subject'] = 'You must provide email subject';
    	}
    	
    	if( $sendEmail && '' == $notifyingEmailBody)
    	{
    		$err['notifying_email_body'] = 'You must provide email content';
    	}
    	
    	if( count($err) > 0 )
    	{
    		$session->set( 'sessFormErrors', $err, 'withdrawal' );
    		$session->set( 'sessFormData', $_POST, 'withdrawal');
    		
			$this->setRedirect( $failedRedirectTo, 'There were some errors processing this form. See messages below.', 'error' );
    		return false;
    	}
    	
    	
    	$params = array(
    		'id' => $id,
    		'approvedFlag' => $approvedFlag,
    		'notes' => $notes,
    		'fulfiller' => $loginUser->id,
    	);
    	
		if (!$model->store( $params ))
		{
			//TO DO: send web alert email to tech
			$this->setRedirect( $failedRedirectTo, 'Update Failed. Please contact webmaster.', 'error' );
			
    		return false;
		}
		
		$this->setRedirect( $successRedirectTo, 'Request Saved' );
		
		//send notifying email
		if( $sendEmail && '' != $notifyingEmailBody && $request->requester_email )
		{
			$mailer = new UserMAIL();
    		$notifyingEmailBody = str_replace('[notes]', $notes, $notifyingEmailBody );
    		
			$senderEmail = $config->get('sender_email');
			$senderName = $config->get('sender_name');
		
		    $mailer->setSender(array($senderEmail, $senderName));
    		$mailer->addReplyTo(array($senderEmail));
    		
    		$mailer->addRecipient($request->requester_email);
    		$mailer->setSubject($notifyingEmailSubject);
    		$mailer->setBody($notifyingEmailBody);
    		$mailer->IsHTML(false);
    		
		    if( $mailer->Send() !== true)
    		{
				$this->setRedirect( $successRedirectTo, 'Request Saved. Failed to send notifying email to ' . $senderName . ' <' . $senderEmail . '>!' );
    		}
		}
		
		return true;
    }
    
	/**
	* Method to cancel
	*
	* @return void
	*/
	function cancel()
	{
		$redirectTo = 'index.php?option='
		.JRequest::getVar('option')
		.'&c=withdrawal';
		$this->setRedirect( $redirectTo );
	}    

	/**
	* Method to export withdrawal requests in csv
	*
 	* @return void
 	*/
	function csv_export()
	{
		$model =& $this->getModel( 'withdrawalrequest' );
    	PaymentHelper::exportWithdrawalCsv($model->getRequests(true));
		exit;
	}
}
?>