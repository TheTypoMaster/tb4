<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Paypal Class
 * 
 */
class PayPal
{
	private $_fields = array();
	private $_paypalUrl;
	private $_businessAccount;
	private $_itemName;
	
	/**
	* Constructor
	*
	* @param array Paypal params
	* @return void
	*/
	public function __construct()
	{
		$params = $this->_getPaypalParams();
		
		$this->_businessAccount = $params['account'];
		$this->_paypalUrl = $params['url'];
		$this->_itemName = $params['item_name'];
	}

	/**
	* Add a Paypal form field
	*
	* @param string  
	* @return void
	*/
	private function _addField($field, $value)
	{
		$this->_fields[$field] = $value;
	}

	/**
	* Generate a paypal post form
	*
	* @return string
	*/
	private function _paypalPostForm()
	{
		$form ="<p>Please wait, your order is being processed and you will be redirected to the paypal website.</p>\n";
      	$form .= "<form method=\"post\" name=\"paypal_form\" action=\"" . $this->_paypalUrl . "\">\n";

		foreach ($this->_fields as $name => $value)
		{
      		$name = htmlspecialchars($name);
      		$value = htmlspecialchars($value);
			$form .= "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
		}
		$form .= "<p>If you are not automatically redirected to ";
		$form .= "paypal within 5 seconds...</p>\n";
		$form .= "<input type=\"submit\" value=\"Click Here\">\n";
		$form .= "</form>\n";
      
		$form .=<<<EOD
			<script language="JavaScript" type="text/javascript">
				window.addEvent('domready', function(){
					document.paypal_form.submit();
				});
			</script> 
EOD;
		return $form;
	}
   
   
	/**
	* Talk to IPN and get the response
	*
	* @return string
	*/
	public function ipnCallback()
	{
		// parse the paypal URL
		$urlParsed=parse_url($this->_paypalUrl);
		$ipnResponse = null;    

		// generate the post string from the _POST vars aswell as load the
		// _POST vars into an arry so we can play with them from the calling
		// script.
		$postString = '';    
		foreach ($_POST as $field=>$value)
		{
			$this->ipnData[$field] = $value;
			$postString .= $field.'='.urlencode(stripslashes($value)).'&'; 
		}
		$postString.="cmd=_notify-validate"; // append ipn command

		// open the connection to paypal
		$fp = fsockopen($urlParsed['host'], '80',$errNum, $errStr,30); 
		if(!$fp)
		{  
			return false; 
		}
		else
		{
			// Post the data back to paypal
			fputs($fp, "POST ". $urlParsed['path'] . " HTTP/1.1\r\n"); 
			fputs($fp, "Host: " . $urlParsed['host'] . "\r\n"); 
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
			fputs($fp, "Content-length: ".strlen($postString)."\r\n"); 
			fputs($fp, "Connection: close\r\n\r\n"); 
			fputs($fp, $postString . "\r\n\r\n"); 

			// loop through the response from the server and append to variable
			while(!feof($fp))
			{ 
				$ipnResponse .= fgets($fp, 1024); 
			} 

         	fclose($fp); // close connection

		}
      

		if (eregi("VERIFIED",$ipnResponse))
		{
			$response = 'VERIFIED';
		}
		else if (eregi("INVALID",$ipnResponse))
		{
			$response = 'INVALID';
		}
		else
		{
			$response = 'UNKNOWN';
		}
		
		return $response;

	}

	
	/**
	* Method to generate paypal form
	*
	* @return string 
	*/
	public function generateForm()
	{
		$response = null;
		
		$amount = JRequest::getVar( 'paypal_amount', '', 'post');
		$email = JRequest::getVar( 'paypal_email', '', 'post');
				
		$loginUser =& JFactory::getUser();
		$session =& JFactory::getSession();
		$sessionTrackingId = $session->get('sessionTrackingId');
		
		$custom = array(
			'user_id' => $loginUser->id,
			'session_tracking_id' => $sessionTrackingId,
		);
		
		$baseURI = JURI::base();
		
		$this->_addField('business', $this->_businessAccount);
	 	$this->_addField('return', $baseURI . 'user/account/instant-deposit/type/paypal/success' );
		$this->_addField('cancel_return', $baseURI . 'user/account/instant-deposit');
		$this->_addField('notify_url', $baseURI . 'index.php?option=com_payment&c=account&task=paypal&act=ipn');
		$this->_addField('currency_code', 'AUD' );
		$this->_addField('item_name', $this->_itemName);
		$this->_addField('no_note', '1');
		$this->_addField('custom', serialize($custom));
		$this->_addField('rm', 2 );// Return method = POST
		$this->_addField('cmd','_xclick'); 
		
		$this->_addField('email', $email );
		$this->_addField('amount', $amount );
		
		$response = $this->_paypalPostForm();
		
		return $response;
	}
	
	/**
	 * Get paypal parameters from payment config
	 *
	 * @return	array
	 */
	private function _getPaypalParams()
	{
    	$config =& JComponentHelper::getParams( 'com_payment' );
		$params= array(
			'url' => $config->get('paypal_url'),
			'account' => $config->get('paypal_account'),
			'item_name' => $config->get('paypal_item_name'),
		);
		//replace [name] and [pin]
		$loginUser =& JFactory::getUser();
		$params['item_name'] = str_replace('[name]', $loginUser->name, $params['item_name']);
		$params['item_name'] = str_replace('[pin]', $loginUser->username, $params['item_name']);
		
		return $params;
	}
}
?>