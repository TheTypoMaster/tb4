<?php
/**
 * Top Betta XML-RPC Service Class
 * @version 1.0
 * @author Geoff Wellman
 *
 */

require('xmlrpc.php');

class topBettaXMLRPC {

	/**
	 * Name of service
	 * @const SERVICE_NAME
	 */
	const SERVICE_NAME = 'topbetta';

	/**
	 * XML-RPC Host URI
	 * @access private
	 * @var string
	 */
	private $host = '';

	/**
	 * API Key to connect to Service
	 * @access private
	 * @var string
	 */
	private $apiKey = '';

	/**
	 * XML-RPC Object
	 * @access private
	 * @var object
	 */
	private $client = null;

	/**
	 * Class constructor - load config and connect to service
	 * @access public
	 * @return void
	 */
	public function __construct(){

		$this->loadConfig();
		$this->client = & new xmlrpc_client('/xmlrpc/', $this->host, 80);
		$this->client->return_type = 'xmlrpcvals';
	}

	/**
	 * load configuration from server.xml
	 * @access private
	 * @return void
	 */
	private function loadConfig(){

		$xml = getConfigSection('services');

		$this->host = $xml->service[0]->host;
		$this->apiKey = $xml->service[0]->api_key;
	}

	/**
	 * Prepare XML RPC Call
	 * @access private
	 * @param string $service name
	 * @param integer $recipient_id
	 * @param integer $amount
	 * @param string $keyword
	 * @return object
	 */
	private function prepareXMLRPC($service, $recipient_id, $amount, $keyword){

	    $api_key 		= new xmlrpcval($this->api_key, 'string');
		$recipient_id 	= new xmlrpcval($recipient_id, 'int');
		$amount 		= new xmlrpcval($amount, 'int');
		$keyword 		= new xmlrpcval($keyword, 'string');

		$params = array($api_key, $recipient_id, $amount, $keyword);

		$msg = new xmlrpcmsg($service, $params);

		return $msg;
	}

	/**
	 * Send the XML to the Service
	 * @access private
	 * @param object $msg
	 * @return object
	 */
	private function sendXMLRPC($msg){

		$res = $this->client->send($msg, 0, '');

		if ($res->faultcode()){
			return $res;
		}
		else{
			return php_xmlrpc_decode($res->value());
		}
	}

	/**
	 * TURN on DEBUG for XML-RPC
	 * @param integer $state
	 * @return void
	 */
	public function setDebug($state=0){

		$this->client->setDebug($state);
	}


	/**
	 * Add Tournament Dollars to a user's balance.
	 * @access public
	 * @param integer $recipient_id
	 * @param integer $amount
	 * @param string  $keyword
	 * @return array (or an xmlrpcresp obj instance if call fails)
	 */
	public function tournamentDollarIncrement ($recipient_id, $amount, $keyword) {

		$msg = $this->prepareXMLRPC('tournament_dollars.increment', $recipient_id, $amount, $keyword);

		return $this->sendXMLRPC($msg);
	}

	/**
	 * Remove Tournament Dollars from a user's balance.
	 * @access public
	 * @param integer $recipient_id
	 * @param integer $amount
	 * @param string  $keyword
	 * @return array (or an xmlrpcresp obj instance if call fails)
	 */
	public function tournamentDollarDecrement ($recipient_id, $amount, $keyword) {

		$msg = $this->prepareXMLRPC('tournament_dollars.decrement', $recipient_id, $amount, $keyword);

		return $this->sendXMLRPC($msg);
	}

	/**
	 * Add Dollars to a user's balance.
	 * @access public
	 * @param integer $recipient_id
	 * @param integer $amount
	 * @param string  $keyword
	 * @return array (or an xmlrpcresp obj instance if call fails)
	 */
	public function accountBalanceIncrement ($recipient_id, $amount, $keyword) {

		$msg = $this->prepareXMLRPC('account_balance.increment', $recipient_id, $amount, $keyword);

		return $this->sendXMLRPC($msg);
	}

	/**
	 * Remove Dollars from a user's balance.
	 * @access public
	 * @param integer $recipient_id
	 * @param integer $amount
	 * @param string  $keyword
	 * @return array (or an xmlrpcresp obj instance if call fails)
	 */
	public function accountBalanceDecrement ($recipient_id, $amount, $keyword) {

		$msg = $this->prepareXMLRPC('account_balance.decrement', $recipient_id, $amount, $keyword);

		return $this->sendXMLRPC($msg);
	}

}


?>

