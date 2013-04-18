<?php
/**
* Account balance XML-RPC plugin
*
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgXMLRPCAccountbalance extends JPlugin
{	
	function plgXMLRPCAccountbalance(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage( '', JPATH_ADMINISTRATOR );
	}

	/**
	* @return array An array of associative arrays defining the available methods
	*/
	function onGetWebServices()
	{
	  // get the XMl-RPC types
	   global $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble,
	   $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64,
	   $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;
	   // return the definitions
	   return array
	   (
	      // add funds
	      'account_balance.increment' => array
	      (
	         'function' => 'plgXMLRPCAccountbalanceServices::increment',
	         'docstring' => 'Add a user\'s account balance.',
	         'signature' => array(array($xmlrpcStruct, $xmlrpcString,
	         $xmlrpcInt, $xmlrpcInt, $xmlrpcString))
	      ),
	      // deduct funds
	      'account_balance.decrement' => array
	      (
	         'function' => 'plgXMLRPCAccountbalanceServices::decrement',
	         'docstring' => 'Deduct a user\'s account balance.',
	         'signature' => array(array($xmlrpcStruct, $xmlrpcString,
	         $xmlrpcInt, $xmlrpcInt, $xmlrpcString))
	      )
	   );
	}
}
/**
* Account balance XML-RPC service handler
*
* @static
*/
class plgXMLRPCAccountbalanceServices
{
   /**
   * Add funds to a user's Account Balance.
   *
   * @static
   * @param string api key
   * @param int recipient id
   * @param int the tournament dollar amount
   * @param int transaction type keyword
   * @return xmlrpcresp 
   */
   function increment($apiKey, $recipientId, $amount, $keyword)
   {
		//check api key
		$registry =& JFactory::getConfig();
		$apiKeyLocal = $registry->getValue('api_key');
		if( $apiKeyLocal != $apiKey )
		{
			header('HTTP/1.1 403 Forbidden');
			return;
		}
		
		global $xmlrpcString, $xmlrpcInt, $xmlrpcStruct;
		include_once( '../components' . DS . "com_payment" . DS . "models" . DS . "accounttransaction.php" );
		$model = new PaymentModelAccounttransaction();
		$db =& Jfactory::getDBO();
		$transactionId = null;

		//init $errorCode to 0
		$errorCode = 0;
		//init status to fail
		$status = 'fail';
		
		//get recipient record
		$table = $db->nameQuote('#__users');
		$query = "SELECT * FROM $table
			WHERE id = " . $db->quote($recipientId) . " LIMIT 1";
		$db->setQuery($query);
		$recipient = $db->loadRow();
			
		if( !$recipient )
		{
			$errorCode = 1;
		}
		else if( 'Super Administrator' == $recipent['usertype'] || 'Administrator' == $recipent['usertype'] )
		{
			$errorCode = 2;
		}
		else
		{
			$model->setUserId( $recipientId );
			
			if( $transactionId = $model->increment($amount, $keyword ) )
			{
				$status = 'success';
			}
		}

		// build the struct response
		$result = new xmlrpcval(array(
			'status' => new xmlrpcval($status, $xmlrpcString),
			'error_code' => new xmlrpcval($errorCode, $xmlrpcInt),
			'balance' => new xmlrpcval($model->getTotal(), $xmlrpcInt),
			'transaction_id' => new xmlrpcval( $transactionId, $xmlrpcInt )
		), $xmlrpcStruct);
      
      // encapsulate the response value and return it
      return new xmlrpcresp($result);
   }
   
   
   /**
   * Remove funds from a user's Account Balance
   *
   * @static
   * @param string api key
   * @param int recipient id
   * @param int the tournament dollar amount
   * @param int transaction type keyword
   * @return xmlrpcresp 
   */
   function decrement($apiKey, $recipientId, $amount, $keyword )
   {
		//check api key
		$registry =& JFactory::getConfig();
		$apiKeyLocal = $registry->getValue('api_key');
		if( $apiKeyLocal != $apiKey )
		{
			header('HTTP/1.1 403 Forbidden');
			exit;
		}
		
		global $xmlrpcString, $xmlrpcInt, $xmlrpcStruct;
		include_once( '../components' . DS . "com_payment" . DS . "models" . DS . "accounttransaction.php" );
		$model = new PaymentModelAccounttransaction();
		$db =& Jfactory::getDBO();
		$transactionId = null;
		
		//init $errorCode to 0
		$errorCode = 0;
		//init status to fail
		$status = 'fail';
		
		//get recipient record
		$table = $db->nameQuote('#__users');
		$query = "SELECT * FROM $table
			WHERE id = " . $db->quote($recipientId) . " LIMIT 1";
		$db->setQuery($query);
		$recipient = $db->loadRow();
		
		
		if( !$recipient )
		{
			$errorCode = 1;
		}
		else if( 'Super Administrator' == $recipent['usertype'] || 'Administrator' == $recipent['usertype'] )
		{
			$errorCode = 2;
		}
		else
		{
			$model->setUserId( $recipientId );
			
			if( $amount > $model->getTotal() )
			{
				$errorCode = 3;
			}
			else
			{
				if( $transactionId = $model->decrement($amount, $keyword ))
				{
					$status = 'success';
				}
			}
		}

		// build the struct response
		$result = new xmlrpcval(array(
			'status' => new xmlrpcval($status, $xmlrpcString),
			'error_code' => new xmlrpcval($errorCode, $xmlrpcInt),
			'balance' => new xmlrpcval($model->getTotal(), $xmlrpcInt),
			'transaction_id' => new xmlrpcval( $transactionId, $xmlrpcInt )
		), $xmlrpcStruct);
      
      // encapsulate the response value and return it
      return new xmlrpcresp($result);
   }
}
?>