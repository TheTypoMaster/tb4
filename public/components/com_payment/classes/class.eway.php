<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * EWAY Class
 * 
 */
class Eway
{
	private $_gateway;
	private $_customerId;
	private $_transactionData;
	
	public function __construct()
	{
		//populate config values
		$this->_getConfig();
	}
	
	private function _getConfig()
	{
    	$config =& JComponentHelper::getParams( 'com_payment' );
		$this->_gateway = $config->get('eway_url');
		$this->_customerId = $config->get('eway_account');
	}
	
	public function makePayment()
	{
		$responseFields = array();
		
		$xmlRequest = "<ewaygateway><ewayCustomerID>" . $this->_customerId . "</ewayCustomerID>";
		foreach( $this->_transactionData as $key=>$value )
		{
			$xmlRequest .= "<$key>$value</$key>";
		}
		$xmlRequest .= "</ewaygateway>";

		$xmlResponse = $this->_sendTransactionToEway( $xmlRequest );

		if($xmlResponse!="")
		{
			$responseFields = $this->_parseResponse($xmlResponse);
		}
		else
		{
			$responseFields = array(
				'EWAYTRXNSTATUS' => 'False',
				'EWAYTRXNERROR' => 'Error in XML response from eWAY',	
			);
		}
		
		return $responseFields;
	}
	
	public function setTransactionData( $field, $value )
	{
		$this->_transactionData["eway" . $field] = htmlentities(trim($value));
	}
	
	private function _sendTransactionToEway( $xmlRequest )
	{
		$ch = curl_init($this->_gateway);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $xmlResponse = curl_exec($ch);

        if( curl_errno( $ch ) == CURLE_OK )
        {
        	return $xmlResponse;
        }
	}
	
	private function _parseResponse( $xmlResponse )
	{
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser,  $xmlResponse, $xmlData, $index );
        $responseFields = array();
        foreach( $xmlData as $data )
        {
	    	if( $data["level"] == 2 )
	    	{
        		$responseFields[$data["tag"]] = $data["value"];
	    	}
        }
        return $responseFields;
	}
}

?>