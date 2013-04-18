<?php
defined('_JEXEC') or die('Restricted access');


class MoneyBookers
{
	private $_fields = array();
	private $_moneybookersUrl;
	private $_businessAccount;
	private $_itemName;

	/**
	 * Constructor
	 *
	 * @param array MoneyBookers params
	 * @return void
	 */
	public function __construct()
	{
		$params = $this->_getMoneybookersParams();
		
		$this->_businessAccount = $params['account'];
		$this->_moneybookersUrl = $params['url'];
		$this->_itemName = $params['item_name'];
	}

	/**
	 * Add a MoneyBookers form field
	 *
	 * @param string
	 * @return void
	 */
	private function _addField($field, $value)
	{
		$this->_fields[$field] = $value;
	}

	/**
	 * Generate a MoneyBookers post form
	 *
	 * @return string
	 */
	private function _moneybookersPostForm()
	{
		$form ="<p>Please wait, your order is being processed and you will be redirected to the moneybookers website.</p>\n";
		$form .= "<form method=\"post\" name=\"moneybookers_form\" action=\"" . $this->_moneybookersUrl . "\">\n";

		foreach ($this->_fields as $name => $value)
		{
			$name = htmlspecialchars($name);
			$value = htmlspecialchars($value);
			$form .= "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
		}
		$form .= "<p>If you are not automatically redirected to ";
		$form .= "moneybookers within 5 seconds...</p>\n";
		$form .= "<input type=\"submit\" value=\"Click Here\">\n";
		$form .= "</form>\n";

		$form .=<<<EOD
			<script language="JavaScript" type="text/javascript">
				window.addEvent('domready', function(){
					document.moneybookers_form.submit();
				});
			</script> 
EOD;
		return $form;
	}


	/**
	 * Method to generate MoneyBookers form
	 *
	 * @return string
	 */
	public function generateForm($params)
	{
		$response = null;

		$amount = JRequest::getVar( 'moneybookers_amount', '', 'post');
		$email = JRequest::getVar( 'moneybookers_email', '', 'post');

		$loginUser =& JFactory::getUser();
		$session =& JFactory::getSession();
		$sessionTrackingId = $session->get('sessionTrackingId');
		
		$custom = array(
			'user_id' => $loginUser->id,
			'session_tracking_id' => $sessionTrackingId,
		);

		$baseURI = JURI::base();

		$this->_addField('pay_to_email', $this->_businessAccount);
		$this->_addField('currency', 'AUD' );
		$this->_addField('language', 'EN' );
		$this->_addField('detail1_description', 'Your Deposit for TopBetta');
		$this->_addField('detail1_text', '');
		$this->_addField('recipient_description', 'TopBetta');
		$this->_addField('pay_from_email', $email);
		$this->_addField('amount', $amount );
		
		// User Personal Information
		//$this->_addField('title', $params['title'] );
		$this->_addField('firstname', $params['first_name'] );
		$this->_addField('lastname', $params['last_name'] );
		$this->_addField('address', $params['street'] );
		$this->_addField('city', $params['city'] );
		$this->_addField('state', $params['state'] );
		$this->_addField('country', $params['country'] );
		$this->_addField('postal_code', $params['postcode'] );
		$this->_addField('phone_number', $params['msisdn'] );
		
		$dob=str_pad($params['dob_day'],2,"0",STR_PAD_LEFT).str_pad($params['dob_month'],2,"0",STR_PAD_LEFT).$params['dob_year'];
		$this->_addField('date_of_birth', $dob );
		
		// This will help to update status on callback
		$this->_addField('transaction_id', $params['transaction_id']);
		//$this->_addField('md5sig', $params['md5sig'] );
		
		//$merchant_fields="uid=".$params['user_id'];
		$this->_addField('merchant_fields', 'user_id,session_tracking_id' );
		$this->_addField('user_id', $custom['user_id'] );
		$this->_addField('session_tracking_id', $custom['session_tracking_id'] );
		
		# Comment the base URI when going staging/prod
		//$baseURI = 'http://sandeepdev.staging.services.mobileactive.com/';
		$this->_addField('return_url', $baseURI . 'user/account/instant-deposit/type/moneybookers/success' );
		//$this->_addField('return_url', $baseURI . '');
		$this->_addField('cancel_url', $baseURI . 'user/account/deposit');
		$this->_addField('status_url', $baseURI . 'user/account/instant-deposit/type/moneybookers/mbstatus');
		
		/*
		 * $this->_addField('item_name', $this->_itemName);
		*/
		
		$response = $this->_moneybookersPostForm();

		return $response;
	}

	/**
	 * Get MoneyBookers parameters from payment config
	 *
	 * @return	array
	 */
	private function _getMoneybookersParams()
	{
		$config =& JComponentHelper::getParams( 'com_payment' );
		$params= array(
			'url' => $config->get('moneybookers_url'),
			'account' => $config->get('moneybookers_account'),
			'item_name' => $config->get('moneybookers_item_name'),
		);
		//replace [name] and [pin]
		$loginUser =& JFactory::getUser();
		$params['item_name'] = str_replace('[name]', $loginUser->name, $params['item_name']);
		$params['item_name'] = str_replace('[pin]', $loginUser->username, $params['item_name']);

		return $params;
	}

}

class MBException extends Exception
{
	const
	ERROR_CONNECTION = 'connection error',
	ERROR_CUSTOM = 'custom';

	static $error_list = array(
	self::ERROR_CONNECTION => 'Error connecting to service'
	);

	public function __construct($error_type, $message = ''){

		if($error_type == self::ERROR_CUSTOM){
			throw new Exception($message);
		}

		throw new Exception(self::$error_list[$error_type]);
	}
}