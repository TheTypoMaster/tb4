<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Tournament Helper
 *
 * @package Joomla
 * @subpackage tournament
 * @since 1.5
 */
class TournamentHelper
{
	/**
	* Export entrants list to csv
	* 
	* @param object the data to export
	* @param array csv headers
	* @param string export file name
	* @return void
	*/
	function exportEntrantsCsv( $data, $includeHeader = true )
	{
	    header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        header("Content-Type: application/octet-stream");
    	header ("Content-disposition: attachment; filename=tournament-entrants.csv");
        header("Content-Transfer-Encoding: binary");
        
		if( $includeHeader )
		{
			print "Position,Username,Final Bucks,Email Address\n";
		}
		
		foreach($data as $row)
		{
			$rowContent = array();
			$rowContent[] = self::csvQuote($row->rank);
			$rowContent[] = self::csvQuote($row->username);
			$rowContent[] = self::csvQuote(bcdiv($row->currency, 100));
			$rowContent[] = self::csvQuote($row->email);
			
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
	
	/**
	* Check if a remote file or url exists
	*
	* @param string $url
	* @return bool
	*/
	function remoteFileExists($url)
	{
		$curl = curl_init($url);

		//don't fetch the actual page, you only want to check the connection is ok
		curl_setopt($curl, CURLOPT_NOBODY, true);

		//do request
		$result = curl_exec($curl);

		$ret = false;

		//if request did not fail
		if ($result !== false) {
			//if request was ok, check response code
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  

			if ($statusCode == 200) {
				$ret = true;   
			}
		}
		
		curl_close($curl);

		return $ret;
	}
}
?>