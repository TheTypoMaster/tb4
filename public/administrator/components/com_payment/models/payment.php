<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: payment.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage payment
 * @license GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class PaymentModelPayment extends JModel {
	
	public $paramFields = array(
			'help_email',
			'sender_email',
			'sender_name',
	    	'withdrawal_notify_email_subject',
	    	'withdrawal_notify_email_body',
	    	'withdrawal_approval_email_subject',
	    	'withdrawal_approval_email_body',
	    	'withdrawal_denial_email_subject',
	    	'withdrawal_denial_email_body',
	    	'paypal_enabled',
	    	'paypal_account',
	    	'paypal_url',
	    	'paypal_item_name',
	    	'paypal_min_deposit',
	    	'paypal_min_withdrawal',
	    	'eway_enabled',
	    	'eway_account',
	    	'eway_url',
	    	'eway_min_deposit',
	    	'eway_min_withdrawal',
			'moneybookers_enabled',
			'moneybookers_account',
			'moneybookers_url',
			'moneybookers_min_deposit',
			'moneybookers_min_withdrawal',
			'moneybookers_merchant_id',
			'moneybookers_secret_word'
		);
	public $rules = array(
			'integerFields' => array(
				'paypal_min_deposit',
				'paypal_min_withdrawal',
				'eway_min_deposit',
				'eway_min_withdrawal',
				'moneybookers_min_deposit',
				'moneybookers_min_withdrawal'
			),
			'varReplacements' => array(
				'first name' => 'customer\'s first name, e.g. "John"',
				'name' => 'customer\'s full name, e.g. "John Mayer"',
				'requested date' => 'the date of the request, e.g. "August 30, 2010, 2:41 pm"',
				'amount' => 'the amount of withdrawal, e.g. "$100.00"',
				'amount raw' => 'the raw number of withdrawal amount, e.g. "100"',
				'withdrawal method' => 'how customer wants to get the withdrawal, e.g. "PayPal Account" or "Bank Account"',
				'withdrawal account' => 'the withdrawal account (only for PayPal), e.g. "paypal-email@example.com"',
				'help email' => 'the help email which set up in this page, e.g. "help@topbetta.com"',
				'notes' => 'the notes which entered when approve/deny a request',
			),
		);
	
	 /**
     * Constructor
     * 
     * @return void;
     */
    function __construct() {
		parent::__construct();
    }
    
    /**
     * Update component params
     *
     * @param string params
     * @return boolean true on success
     */
    function updateParams( $params )
    {
		$db =& Jfactory::getDBO();
    	$table = $db->nameQuote('#__components');
    	$updateQuery = "UPDATE $table SET params = " . $db->quote($params) ." WHERE link = " . $db->quote('option=com_payment');
		$db->setQuery($updateQuery);
		return $db->query();
    }
}
?>