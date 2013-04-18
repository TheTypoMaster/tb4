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
class TournamentdollarsHelper
{
	/**
	* Export to csv
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
    	header ("Content-disposition: attachment; filename=tournament-dollars-transactions-" . date("Ymd") . ".csv");
        header("Content-Transfer-Encoding: binary");
		
		if( $includeHeader )
		{
			$csv .= "ID,Recipient,Giver,Transaction Type,Transaction Date,Amount,Notes\n";
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