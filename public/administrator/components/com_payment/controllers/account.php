<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );

/**
 * Account Controller
 *
 * @package Joomla
 */
class PaymentControllerAccount extends JController {

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
		JToolBarHelper::addNewX();
		JToolBarHelper::title( JText::_( 'Account Transactions' ), 'generic.png' );
		$view = JRequest::getVar( 'view', 'accounttransaction');
		$layout = JRequest::getVar( 'layout', 'default' );

		if( 'recipient' == $layout && $_SERVER['REQUEST_METHOD'] != 'POST' )
		{
			return;
		}

		$view =& $this->getView( $view, 'html');

		$model =& $this->getModel( 'accounttransaction' );
		$model->loadDynamicOptions();

		$view->setModel( $model, true );
		$view->setLayout( $layout );

		global $mainframe;
		// Prepare list array
		$lists = array();
		// Get the user state
		$filter_order = $mainframe->getUserStateFromRequest($option.'filter_order','filter_order');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.'filter_order_Dir','filter_order_Dir', 'ASC');
		$filter_search = $mainframe->getUserStateFromRequest($option.'filter_account_search', 'filter_account_search');
		$filter_transaction_type = $mainframe->getUserStateFromRequest($option.'filter_account_transaction_type', 'filter_account_transaction_type');
		$filter_from_date = $mainframe->getUserStateFromRequest($option.'filter_account_from_date', 'filter_account_from_date');
		$filter_to_date = $mainframe->getUserStateFromRequest($option.'filter_account_to_date', 'filter_account_to_date');
		$filter_from_amount = $mainframe->getUserStateFromRequest($option.'filter_account_from_amount', 'filter_account_from_amount');
		$filter_to_amount = $mainframe->getUserStateFromRequest($option.'filter_account_to_amount', 'filter_account_to_amount');

		// Build the list array for use in the layout
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['search'] = $filter_search;
		$lists['transaction_type'] = $filter_transaction_type;
		$lists['from_date'] = $filter_from_date;
		$lists['to_date'] = $filter_to_date;
		$lists['from_amount'] = $filter_from_amount;
		$lists['to_amount'] = $filter_to_amount;

		$transactions =& $model->getTransactions();

		$page =& $model->getPagination();
		// Assign references for the layout to use
		$view->assignRef('lists', $lists);
		$view->assignRef('transactions', $transactions);
		$view->assignRef('page', $page);

		$view->assign('transactionTypes', (array('' => 'All transactions') + $model->options['transaction_type']));

		if($recipient = JRequest::getVar( 'recipient' ))
		{
			$view->assignRef('recipients', $model->getUserList($recipient));
		}

		$view->display();
	}

	/**
	 * Method to add a transaction record
	 *
	 * @return boolean true on success
	 */
	function add()
	{
		JRequest::setVar('hidemainmenu', 1);
		// Build the toolbar for the add function
		JToolBarHelper::title( JText::_('Account Transaction')
		. ': [<small>Add</small>]' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		// Get a new revue from the model
		$model =& $this->getModel( 'accounttransaction' );
		$model->loadDynamicOptions();

		$layout = JRequest::getVar( 'layout', 'edit' );
		$view = JRequest::getVar( 'view', 'accounttransaction');
		$view =& $this->getView( $view, 'html');

		$view->setModel( $model, true );
		$view->setLayout( $layout );

		$view->assignRef( 'options', $model->options);

		//get the validation msg and keep the value entered after validation
		$session =& JFactory::getSession();

		$formData = array();
		if( $sessFormData = $session->get('sessFormData', null, 'account') )
		{
			//print_r($session->get('sessFormErrors', null, 'withdrawal'));exit;
			if( $sessFormErrors = $session->get('sessFormErrors', null, 'account') )
			{
				$view->assign( 'formErrors', $sessFormErrors);
				$session->clear('sessFormErrors', 'account');
			}

			$formData = array(
		        'recipient' => stripslashes($sessFormData['recipient']),
		        'notes' => stripslashes($sessFormData['notes']),
		        'amount' => stripslashes($sessFormData['amount']),
		        'transaction_type' => stripslashes($sessFormData['transaction_type']),
			);
			$session->clear('sessFormData', 'account');
		}

		$view->assignRef('formData', $formData);


		$view->display();
	}

	/**
	 * Method to save a transaction record
	 *
	 * @return boolean true on success
	 */
	function save()
	{
		$model =& $this->getModel( 'accounttransaction' );
		$model->loadDynamicOptions();
		$session =& JFactory::getSession();
		$loginUser =& JFactory::getUser();

		$recipient = trim(JRequest::getVar( 'recipient', '', 'post' ));
		$amount = JRequest::getVar( 'amount', '', 'post');
		$transactionType = JRequest::getVar( 'transaction_type', '', 'post');
		$notes = JRequest::getVar( 'notes', '', 'post');

		$failedRedirectTo = 'index.php?option='
		.JRequest::getVar('option')
		.'&c=' . JRequest::getVar('c')
		.'&task=add';

		$successRedirectTo = 'index.php?option='
		.JRequest::getVar('option')
		.'&c=' . JRequest::getVar('c')
		.'&task=display';


		//validate post params
		$err = array();

		if( !$recipient )
		{
			$err['recipient'] = 'Please enter a recipient';
		}
		else if( !preg_match('/^\[#([0-9]+)\s([a-zA-Z0-9]+)\]\s([a-zA-Z0-9\s\-\']*)$/', $recipient, $m) )
		{
			$err['recipient'] = 'Invalid recipient';
		}
		else
		{
			$recipientUsername = trim($m[2]);
			$recipientName = trim($m[3]);

			$recipientId = $model->getRecipientId($recipientUsername, $recipientName);
			if( null == $recipientId || trim($m[1]) != $recipientId )
			{
				$err['recipient'] = 'Invalid recipient';
			}
		}

		if( !preg_match('/^(\d|-)?[0-9\.]+$/', $amount) )
		{
			$err['amount'] = 'Please enter a number';
		}
		else if( abs($amount) == 0 )
		{
			$err['amount'] = 'Must be a non-zero number';
		}
		else if( 0 )
		{
			//TO DO : check the withdrawal amount with rules
			$err[$withdrawalType . '_amount'] = 'Invalid Withdrawal';
		}

		if( '' == $transactionType )
		{
			$err['transaction_type'] = 'You must select a transaction type';
		}
		else if (!isset($model->options['transaction_type'][$transactionType]))
		{
			$err['transaction_type'] = 'Invalid Option';
		}

		if( '' == $notes )
		{
			$err['notes'] = 'You must enter a notes';
		}

		if( count($err) > 0 )
		{
			$session->set( 'sessFormErrors', $err, 'account' );
			$session->set( 'sessFormData', $_POST, 'account');

			$this->setRedirect( $failedRedirectTo, 'There were some errors processing this form. See messages below.', 'error' );
			return false;
		}

		$params = array(
        'recipient_id' => $recipientId,
        'giver_id' => $loginUser->id,
        'session_tracking_id' => $session->get( 'sessionTrackingId' ),
        'account_transaction_type' => $transactionType,
        'amount' => $amount * 100,
        'notes' => $notes,
		);

		if (!$model->store( $params ))
		{
			//TO DO: send web alert email to tech
			$this->setRedirect( $failedRedirectTo, 'Update Failed. Please contact webmaster.', 'error' );

			return false;
		}

		$this->setRedirect( $successRedirectTo, 'Request Saved' );

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
		.'&c=account';
		$this->setRedirect( $redirectTo );
	}

	/**
	 * Method to export transactions in csv
	 *
	 * @return void
	 */
	function csv_export()
	{
		$model =& $this->getModel( 'accounttransaction' );
		PaymentHelper::exportTransactionCsv($model->getTransactions(true));
		exit;
	}

}
?>