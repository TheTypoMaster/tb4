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

class PaymentModelMoneybookers extends JModel
{

	/**
	 * Method to add an moneybookers Transaction log
	 * @return int id on success
	 */
	function addTransaction( $user_id, $amount, $pay_from_email,$session_tracking_id)
	{
		// Get the table
		$db =& Jfactory::getDBO();

		$query =
			'INSERT INTO ' . $db->nameQuote('#__moneybookers_transaction') . ' (
				user_id,
				amount,
				pay_from_email,
				session_tracking_id
			) VALUES (
				' . $db->quote($user_id) . ',
				' . $db->quote($amount) . ',
				' . $db->quote($pay_from_email) . ',
				' . $db->quote($session_tracking_id) . '
			)';
		 
		 
		$db->setQuery($query);
		$db->query();

		return $db->insertId();
	}

	/**
	 * Method to add an moneybookers Transaction log
	 * @return int id on success
	 */
	function updateTransaction($md5sig,$transaction_id)
	{
		// Get the table
		$db =& Jfactory::getDBO();

		$transaction_id = $db->quote($transaction_id);
		$md5sig = $db->quote($md5sig);
		 
		$query = 'update '. $db->nameQuote('#__moneybookers_transaction') . '
			 set md5sig='.$md5sig.' where id='. $transaction_id;
		 
		$db->setQuery($query);

		return $db->query();
	}

	/**
	 * Method to add an moneybookers Transaction status
	 * @return int id on success
	 */
	function addTransactionStatus($params)
	{
		// Get the table
		$db =& Jfactory::getDBO();

		$query =
			'INSERT INTO ' . $db->nameQuote('#__moneybookers_transaction_status_log') . ' (
				pay_to_email,
				pay_from_email,
				transaction_id,
				mb_transaction_id,
				mb_amount,
				mb_currency,
				status,
				amount,
				currency,
				user_id,
				session_tracking_id,
				md5sig
			) VALUES (
				' . $db->quote($params['pay_to_email']) . ',
				' . $db->quote($params['pay_from_email']) . ',
				' . $db->quote($params['transaction_id']) . ',
				' . $db->quote($params['mb_transaction_id']) . ',
				' . $db->quote($params['mb_amount']) . ',
				' . $db->quote($params['mb_currency']) . ',
				' . $db->quote($params['status']) . ',
				' . $db->quote($params['amount']) . ',
				' . $db->quote($params['currency']) . ',
				' . $db->quote($params['user_id']) . ',
				' . $db->quote($params['session_tracking_id']) . ',
				' . $db->quote($params['md5sig']) . '
			)';
		 
		$db->setQuery($query);
		$db->query();

		return $db->insertId();
	}

	/**
	 * Method to get a transaction log with pending status
	 *
	 * @param  string Paypal tansaction ID
	 * @return object
	 */
	function getTransactionLog( $transaction_id, $user_id)
	{
		$db =& Jfactory::getDBO();
		$table = $db->nameQuote('#__moneybookers_transaction');
		$transaction_id = $db->quote( $transaction_id);
		//$user_id = $db->quote( $user_id);
		//$session_tracking_id = $db->quote($session_tracking_id);
		 
		$query = 'SELECT id FROM ' . $table .' WHERE (status!=2 or status is null) and id =' . $transaction_id;
		$db->setQuery( $query );
		 
		return $db->loadObject();
	}

	/**
	 * update moneybookers Transaction log status
	 * @return int id on success
	 */
	function updateTransactionStatus($transaction_id,$status)
	{
		// Get the table
		$db =& Jfactory::getDBO();

		$transaction_id = $db->quote($transaction_id);
		$status = $db->quote($status);
		 
		$query = 'update '. $db->nameQuote('#__moneybookers_transaction') . '
			 set updated_date=now(),status='.$status.' where id='. $transaction_id;
		 
		$db->setQuery($query);

		return $db->query();
	}

}

?>