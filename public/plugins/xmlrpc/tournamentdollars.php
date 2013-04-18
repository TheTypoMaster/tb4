<?php
/**
* Tournament dollars XML-RPC plugin
*
*/

//no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgXMLRPCTournamentdollars extends JPlugin
{	
	function plgXMLRPCTournamentdollars(&$subject, $config)
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
	      // add tournament dollars
	      'tournament_dollars.increment' => array
	      (
	         'function' => 'plgXMLRPCTournamentdollarsServices::increment',
	         'docstring' => 'Add Tournament Dollars to a user\'s balance.',
	         'signature' => array(array($xmlrpcStruct, $xmlrpcString,
	         $xmlrpcInt, $xmlrpcInt, $xmlrpcString))
	      ),
	      // deduct tournament dollars
	      'tournament_dollars.decrement' => array
	      (
	         'function' => 'plgXMLRPCTournamentdollarsServices::decrement',
	         'docstring' => 'Deduct Tournament Dollars from a user\'s balance',
	         'signature' => array(array($xmlrpcStruct, $xmlrpcString,
	         $xmlrpcInt, $xmlrpcInt, $xmlrpcString))
	      )
	   );
	}
}
/**
* Tournament dollars XML-RPC service handler
*
* @static
*/
class plgXMLRPCTournamentdollarsServices
{
   /**
   * Add Tournament Dollars to a user's balance.
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
			exit;
		}
		
		global $xmlrpcString, $xmlrpcInt, $xmlrpcStruct;
   		include_once( '../components' . DS . "com_tournamentdollars" . DS . "models" . DS . "tournamenttransaction.php" );
		$model = new TournamentdollarsModelTournamenttransaction();
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
			
			if($transactionId = $model->increment($amount, $keyword ))
			{
				$status = 'success';
			}
		}

		// build the struct response
		$result = new xmlrpcval(array(
			'status' => new xmlrpcval($status, $xmlrpcString),
			'error_code' => new xmlrpcval($errorCode, $xmlrpcInt),
			'balance' => new xmlrpcval($model->getTotal(), $xmlrpcInt),
			'transaction_id' => new xmlrpcval($transactionId, $xmlrpcInt)
		), $xmlrpcStruct);
      
      // encapsulate the response value and return it
      return new xmlrpcresp($result);
   }
   
   
   /**
   * Deduct Tournament Dollars from a user's balance.
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
   		include_once( '../components' . DS . "com_tournamentdollars" . DS . "models" . DS . "tournamenttransaction.php" );
		include_once( '../components' . DS . "com_payment" . DS . "models" . DS . "accounttransaction.php" );
		$model = new TournamentdollarsModelTournamenttransaction();
		$accountModel = new PaymentModelAccounttransaction();
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
			$accountModel->setUserId( $recipientId );
			
			if( $amount > ($model->getTotal() + $accountModel->getTotal()) )
			{
				$errorCode = 3;
			}
			else
			{
				if($transactionId = $model->decrement($amount, $keyword ))
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
			'transaction_id' => new xmlrpcval($transactionId, $xmlrpcInt)
		), $xmlrpcStruct);

      // encapsulate the response value and return it
      return new xmlrpcresp($result);
   }
}
?>