<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * payment Helper
 *
 * @package Joomla
 * @subpackage payment
 * @since 1.5
 */
class TopbettaUserHelper
{
	/**
	* Export to csv
	* 
	* @param object the data to export
	* @param array csv headers
	* @param string export file name
	* @return void
	*/
	function exportUserCsv( $data, $includeHeader = true )
	{
	    header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/octet-stream");
    	header ("Content-disposition: attachment; filename=topbetta-user-" . date("Ymd") . ".csv");
        header("Content-Transfer-Encoding: binary"); 
		
		if( $includeHeader )
		{
			print "ID,User ID,Username,First Name,Last Name,";
			print "Account Balance,Tournament Balance,Email,";
			print "Street,City,State,Postcode,Country,";
			print "Registration Date,Last Login,";
			print "DOB,Mobile,Home Phone,Heard About,Heard About Info,Marketing Opt-in,";
			print "Identity Verified,BSB Number,Bank Account Number,Status\n";
		}
		
		foreach($data as $row)
		{
			$rowContent = array();
			$rowContent[] = self::csvQuote($row->id);
			$rowContent[] = self::csvQuote($row->user_id);
			$rowContent[] = self::csvQuote($row->username);
			$rowContent[] = self::csvQuote($row->first_name);
			$rowContent[] = self::csvQuote($row->last_name);
			$rowContent[] = self::csvQuote('$' . number_format($row->account_balance/100, '2', '.', ','));
			$rowContent[] = self::csvQuote('$' . number_format($row->tournament_balance/100, '2', '.', ','));
			$rowContent[] = self::csvQuote($row->email);
			$rowContent[] = self::csvQuote($row->street);
			$rowContent[] = self::csvQuote($row->city);
			$rowContent[] = self::csvQuote($row->state);
			$rowContent[] = self::csvQuote($row->postcode);
			$rowContent[] = self::csvQuote($row->country);
			$rowContent[] = self::csvQuote($row->registerDate);
			$rowContent[] = self::csvQuote($row->lastvisitDate);
			$rowContent[] = self::csvQuote($row->dob_year . '-' . $row->dob_month . '-' . $row->dob_day);
			$rowContent[] = self::csvQuote($row->msisdn);
			$rowContent[] = self::csvQuote($row->phone_number);
			$rowContent[] = self::csvQuote($row->heard_about);
			$rowContent[] = self::csvQuote($row->heard_about_info);
			$rowContent[] = self::csvQuote($row->marketing_opt_in_flag ? 'Yes' : 'No');
			$rowContent[] = self::csvQuote($row->identity_verified_flag ? 'Yes' : 'No');
			$rowContent[] = self::csvQuote($row->bsb_number);
			$rowContent[] = self::csvQuote($row->bank_account_number);
			$rowContent[] = self::csvQuote($row->block ? 'Inactive' : 'Active');

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