<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: helper.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage payment
 * @license GNU/GPL
 *
 * This is payment component
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * payment Helper
 *
 * @package Joomla
 * @subpackage payment
 * @since 1.5
 */
class PaymentHelper
{
	/**
	* Method to replace variables
	* 
	* @param array an associate array of replacement values
	* @param string the text which contains replacement variables
	* @return string the text after replacement
	*/
	function variableReplace( array $replacements, $text )
	{
		return str_replace(array_keys($replacements), array_values($replacements), $text);   
	}
	
	/**
	* Export transactions to csv
	* 
	* @param object the data to export
	* @param array csv headers
	* @param string export file name
	* @return void
	*/
	function exportTransactionCsv( $data, $includeHeader = true )
	{
	    header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/octet-stream");
    	header ("Content-disposition: attachment; filename=account-transactions-" . date("Ymd") . ".csv");
        header("Content-Transfer-Encoding: binary");
        
		if( $includeHeader )
		{
			print "ID,Recipient,Giver,Transaction Type,Transaction Date,Amount,Notes\n";
		}
		
		foreach($data as $row)
		{
			$rowContent = array();
			$rowContent[] = self::csvQuote($row->id);
			$rowContent[] = self::csvQuote($row->recipient);
			$rowContent[] = self::csvQuote($row->giver);
			$rowContent[] = self::csvQuote($row->type);
			$rowContent[] = self::csvQuote($row->created_date);

			if( $row->amount < 0)
			{
				$rowContent[] = self::csvQuote(sprintf('-$%.2f' , abs($row->amount/100)));
			}
			else
			{
				$rowContent[] = self::csvQuote(sprintf('$%.2f' , $row->amount/100));
			}
			$rowContent[] = self::csvQuote($row->notes);
			
			print (join(',', $rowContent) . "\n");
		}
	}
	
	/**
	* Export withdrawal requests to csv
	* 
	* @param object the data to export
	* @param array csv headers
	* @param string export file name
	* @return void
	*/
	function exportWithdrawalCsv( $data, $includeHeader = true )
	{
	    header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/octet-stream");
    	header ("Content-disposition: attachment; filename=withdrawals-" . date("Ymd") . ".csv");
        header("Content-Transfer-Encoding: binary");

		if( $includeHeader )
		{
			print "ID,Requester,Amount,Withdrawal Type,Requested Date,Fulfilled Date,Approved,Notes\n";
		}
		
		foreach($data as $row)
		{
			$rowContent = array();
			$rowContent[] = self::csvQuote($row->id);
			$rowContent[] = self::csvQuote($row->requester);
			$rowContent[] = self::csvQuote(sprintf('$%.2f' , $row->amount/100));
			$rowContent[] = self::csvQuote($row->withdrawal_type . ('paypal' == $row->withdrawal_type && $row->paypal_id ? ' - ' . htmlspecialchars($row->paypal_id) : ''));
			$rowContent[] = self::csvQuote($row->requested_date);
			$rowContent[] = self::csvQuote($row->fulfilled_date);
			$rowContent[] = self::csvQuote($row->approved_flag === null ? 'Pending' : ($row->approved_flag ? 'Yes' : 'No'));
			$rowContent[] = self::csvQuote($row->notes);
			
			print (join(',', $rowContent) . "\n");
		}
	}
	
	/**
	* Export to csv
	* 
	* @param string the value to quote
	* @return string the value after quote
	*/
	function csvQuote($value)
	{
		return '"' . str_replace('"', '""', $value) . '"';
	}
	
}
?>