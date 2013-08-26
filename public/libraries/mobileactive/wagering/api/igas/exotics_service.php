<?php

class WageringApiIgasexoticsService extends ConfigReader{
	// Command paths
	private $service_login_path = 'login'; // login path to get token
	private $service_quickbet_path = 'Betinput.aspx'; // quickbet path
	private $service_bets_path = 'bets'; // bet lookup path
	private $service_token_validator_path = 'checkauth'; // Token validator path

	private $meeting_code = null;
	private $type_code = null;
	private $send_bet = null;
	private $service_url = null;
	private $token = null;
	private $token_model = null;
	private $runner_model = null;
	
	//used for unique user agent for each node
	private $hostid = null;
	private $useragent = null;


	const DATE_FORMAT_ACCOUNT = 'Y-m-d',
	DATE_FORMAT_INFORMATION = 'd-M-Y';
	
	static function getInstance($class_name)
	{
		static $instance = null;
		
		if(is_null($instance)){
			$instance = new $class_name();
		}
		
		return $instance;
	}
	
	final public function __construct()
	{
		$config =& JFactory::getConfig();
		
		$api = 'igasracing';
		$this->date = new DateTime();
		$this->api = $this->getApi($api);
		$this->service_url = 'http://' . $this->api->host . $this->api->url;
		
	
		
		if (method_exists($this, 'initialise')){
			$this->initialise();
		}

		
	}

	public function initialise()
	{
		$components = array("com_betting", "com_tournament");

		foreach($components as $component) {
			$path = JPATH_BASE . DS;
			//MC if($admin) {
				//MC $path .= 'administrator' . DS;
			//MC }

			$path .= 'components' . DS . $component;

			if(file_exists($path) && is_dir($path)) {
				JModel::addIncludePath($path . DS . 'models');
			} else {
				$this->l("ERROR - Could not find component {$name}");
			}
		}

		$this->token_model	=& JModel::getInstance('Token', 'BettingModel');
		$this->runner_model	=& JModel::getInstance('Runner', 'TournamentModel');

	}
	
	public function checkConnection()
	{
		// Maybe call the validator here to verify server connectivity
		return true;
	}
	
	/**
	 * Place bet
	 *
	 * @param array $bet_data
	 * @return object
	 */
	public function placeBetList(Array $bet_data)
	{
		//$b = print_r($bet_data,true);
		//$this->setLogger("* exotics_service: placebetList: bet_data:$b");
		
		$bet_list = $bet_data['bet_list'];
		$event = $bet_data['event'];
		
		// Build up the bet parameters
		$params = $this->_buildBetList($bet_list);
		//$this->setLogger("* exotics_service: placebetList: Bet Params: bet_data:".print_r($params,true));
		
		// Generate Data Key from all bet params
		$betDataKey = $this->getDataKey($userName, $userPassword, $companyID, $params, "$secretKey");
		//$this->setLogger("* exotics_service: placebetList: DataKey: $betDataKey");
		
		// format JSON object for POSTing to iGAS
		$this->send_bet = $this->formatIgasPOST ($userName,$userPassword,$companyID, $params, $betDataKey );
		$this->setLogger("racing_service: placeRacingBet. iGAS JSON POST: $this->send_bet");
		
		return $this->action($this->send_bet, $this->service_quickbet_path);
		
	}
	
	public function placeRaceBetList(Array $bet_data, $userID, $raceNO, $priceType, $meetingID)
	{
		// Topbetta related params 
		$userName = "topbetta";
		$userPassword = "T0pB3tter@AP!";
		$companyID = "TopBetta";
		$secretKey = "(*&j2zoez";
	
		//TODO: Change to use server_igas.xml
		//$userName = $this->api->username;
		//$userPassword = $this->api->password;
		//$companyID = $this->api->companyid;
		
		//$b = print_r($bet_data,true);
		//$this->setLogger("* exotics_service: placeRacebetList: bet_data:$b");
	
		// Split up bet and event data
		$bet_list = $bet_data['bet_list'];
		$event = $bet_data['event'];
	
		// Build up the bet parameters
		$params = $this->_buildBetList($bet_list, true, $userID, $raceNO, $priceType, $meetingID);
		
		//TODO: Cater for multibets

		$betParams = $this->send_bet;
		$sb = print_r($this->send_bet,true);
		//$this->setLogger("* exotics_service: placeRacebetList: Send bet params:$sb");
		
		// Build up bet array for JSON
		$paramsList = array('ReferenceID' => $betParams['eventId'], 'ClientID' => $userID,
				'Amount' => $betParams['betAmount'], 'Flexi' => $betParams['flexi'], 'MeetingId' => $meetingID, 
				'RaceNo' => $raceNO, 'BetType' => $betParams['betType'], 
				'PriceType' => $priceType, 'Selection' => $betParams['Selection']);
	
		// Generate Data Key from all bet params
		$betDataKey = $this->getDataKey($userName, $userPassword, $companyID, $paramsList, "$secretKey");
		//$this->setLogger("* exotics_service: placeRacebetList: DataKey: $betDataKey");
	
		// format JSON object for POSTing to iGAS
		$this->send_bet = $this->formatIgasPOST ($userName, $userPassword, $companyID, $paramsList, $betDataKey );
		$this->setLogger("* exotics_service: placeRacebetList. iGAS JSON POST: $this->send_bet");
	
		return $this->action($this->send_bet, $this->service_quickbet_path);
	}
	

	/**
	 * Lookup bets
	 *
	 * @param array $bet
	 * @return object
	 */
	public function getBetInfo($bet)
	{
		$bet_params = array();
		$bet_params['invoice_id'] = $bet['wager_id'];
		$response_bet = $this->action($bet_params, $this->service_bets_path);

		return $response_bet;
	}
	
	public function getAccountBalance(){

	}

	public function setTypeCode($value){
		$this->type_code = $value;
	}

	public function setMeetingCode($value){
		$this->meeting_code = $value;
	}

	public function setCustomId($value){
		$this->custom_id = $value;
	}
	
	private function formatIgasPOST($UserName, $UserPassword, $CompanyID, $paramslist, $DataKey){
	
		$betObjectArray = array('Username' => $UserName, 'Password' => $UserPassword, 'CompanyID' => $CompanyID, 
						'ReferenceID' => $paramslist['ReferenceID'], 'ClientID' => $paramslist['ClientID'], 
						'Amount' => $paramslist['Amount'], 'Flexi' => $paramslist['Flexi'], 'DataKey' => $DataKey, 
						'BetList' =>array(array('MeetingId' => $paramslist['MeetingId'], 'RaceNo' => $paramslist['RaceNo'], 
						'BetType' => $paramslist['BetType'], 'PriceType' => $paramslist['PriceType'], 'Selection' => $paramslist['Selection'])));
		
		return json_encode($betObjectArray);

	}

	/**
	 * Build bet list. Set iGAS data in class property $send_bet
	 *
	 * @param array $bet_list
	 * @param bool $return_multiple
	 * @return object
	 */
	private function _buildBetList($bet_list, $return_multiple = true, $userID, $raceNO, $priceType, $meetingID){
		
		//$b = print_r($bet_list,true);
		//$this->setLogger("exotics_service: Build Bet List:$b");
				
		if(is_null($this->type_code)){ // is_null($this->meeting_code) || 
			throw new Exception('Meeting code and type code must be set to place bet');
		}

		foreach($bet_list as $bet){

			if(!($bet instanceof WageringBet)){
				throw new Exception('Must be array of WageringBet objects');
			}
						
			// Form array to get the bettype required
			$bet_type_array = array('win' => 'W',
			'place' => 'P',
			'quinella' => 'Q',
			'exacta' => 'E',
			'trifecta' => 'T',
			'firstfour' => 'F',
			'quadrella' => 'QD'
			);
			
			$bet_type = strtolower($bet->getBetType());
			$bet_type_external = $bet_type_array[$bet_type];
			
			if($bet_type == "firstfour" && $bet->isCombinationBetType() == 1){
				$bet_string = "FirstFour";
			} elseif($bet->isCombinationBetType() == 1) {
				$bet_string = ucwords($bet_type);
			} else {
				$bet_string = $bet_type;
			}

			// Boxed Exotic?
			$bet_type_string = $bet->isBoxed() ? "Box".$bet_string : $bet_string;

			// Add rugNumbers for bets
			$selections = (string) $bet->getBetSelectionObject();
			$rug_list = explode("/", $selections);
			$rug_count = $bet->isBoxed() ? $bet->getPositionSelectionCount() : count($rug_list);

			$event_id = JRequest::getVar('event_id', 'post');
			$race_id = JRequest::getVar('race_id', 'post');

			// Exotic Bet, internal
			$formatted_bet['bet'] = array(
					'amount' => $bet->amount,
					'eventId' => $event_id,
					'betAmount' => ($bet->amount/100),
					'optionId' => 0,
					'handicap' => 1,
					'betType' => "exotic",
					'exoticType' => $bet_type_string,
					'clientReferenceId' => $this->custom_id,
					'flexi' => $bet->isFlexiBet(),
					'poolType' => $bet_type_external,
					'racePoolId' => $bet->race_number[$bet_type_external]
			);

			
			$this->setLogger("* exotic_service. Build bet list. Before: Formatted bets for iGAS, cli:$userID, bt:$bet_type, mid:$bm_bet_product, rn:$raceNO, pt:$priceType.");
			// To send to iGAS
			$formatted_bet['request'] = array(
					
					
					'eventId' => $this->custom_id,
					'betAmount' => $bet->amount,
					//'optionId' => "0",
					//'handicap' => "1",
					'betType' => "$bet_type_external",
					'flexi' => 1
					//'exoticType' => $bet_type_string
					
					/*
					'referenceID' => $bet->id,
					'clientID' => $userID,
					'amount' => $bet->amount,
					'flexi' => $bet->isFlexiBet(),
					'betType' => "$bet_type",
					'meetingID' => $bm_bet_product,
					'raceNO' => $raceNO,
					'PriceType' => 'TOP' // This should be done here maybe and not later!
					//'Selection' => $optionId[0]
					 
					 */
			);
	
			$this->setLogger("* exotic_service. Build bet list. After: Formatted bets for iGAS ");
			
			if($bet_type == "quinella")
			{
			 	$rug_count = count($rug_list);
			}
			
			for($i=1; $i <= $rug_count; $i++)
			{
				$rugNumber = 'rugNumber'.$i;
				
				if($bet_type == "quinella")
				{
				 	$formatted_bet['bet'][$rugNumber] = $rug_list[$i-1];
				}else{
					$formatted_bet['bet'][$rugNumber] = $bet->isBoxed() ? $selections : $rug_list[$i-1];

				}
				if($bet->isCombinationBetType() == 1){
					/*if($i>=2){ 
						$formatted_bet['request']['exoticSels'] .= "/";
					}
					$formatted_bet['request']['exoticSels'] .= str_replace("+", ",", $rug_list[$i-1]);*/
					$formatted_bet['request']['Selection'] = $bet->formatBetSelections();
				}
			}		

			$formatted_bet_list[] = $formatted_bet['bet'];
			
		}
		$this->send_bet = $formatted_bet['request'];
		
		$fb = print_r($formatted_bet, true);
		$this->setLogger("* exotic_service. Build bet list. Formatted bet: $fb");

		return ($return_multiple) ? $formatted_bet_list : $formatted_bet;
	}

	private function _getTransactionDate()
	{
		return $this->date->format(self::DATE_FORMAT_INFORMATION);
	}

	private function _getDate()
	{
		return $this->date->format(self::DATE_FORMAT_ACCOUNT);
	}

	private function getToken()
	{
		// Get last token from DB
		$token_array = $this->token_model->getStoredToken($this->hostid);
		$this->token = $token_array->token;
		$this->useragent = $token_array->useragent;
	}
	private function setToken($token=null)
	{
		// Set new working token to DB row
		$params = array('hostid' => $this->hostid, 'token' => $token);
		if ($this->token_model->setStoredToken($params) == false) {
			throw new ApiException("API Error: Could not store token in database.");
		}
	}
	
	private function _setHostId() {
		if (!$this->hostid) {
			$this->hostid = str_replace("\n","",shell_exec("hostname -f")); 
		}	
	}

	private function _validateToken()
	{
		$path = $this->service_token_validator_path."?token=".$this->token;
		$response = $this->curlRequest($path);
		if($response->result == 1)
		{
			return true;
		} else {
			return false;
		}
	}

	private function _getNewLoginToken()
	{
		$params = array(
			'username' 	=> $this->api->account['number'],
			'password'	=> $this->api->account['pin']
		);
		// Set token property to new token
		$response = $this->curlRequest($this->service_login_path, $params);

		if($response->status == "Success")
		{
			$this->token = $response->token;
			$this->setToken($this->token);
			return true;
		} else {
			return false;
		}
		
	}

	private function _checkLoginToken()
	{
		if($this->_validateToken() == true){ // validated and therefore continue using token
			return true;
		} else { // get new token
			if($this->_getNewLoginToken() == false)
			{
				throw new ApiException("API Offline: Could not get token from issuer.");
			}
			// Do validation for new token else fatal error
			if($this->_validateToken() == true){ // Valid!
				return true;
			} else { // Invalid, something is wrong.
				throw new ApiException("API Error. Token invalid.");
			}
		}
		// If unknown error
		return false;
	}

	private function curlRequest($command=null, $params=null)
	{
		//$this->setLogger("exotics_service: Entering curlRequest. Command:$command");
		$this->setLogger("exotics_service: curlRequest. post_string:$params");
		
		/*
		 * Send the bet object to iGAS for processing
		 */
		$ch = curl_init($this->service_url."/".$command);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($params))
		);
		
		
		
		//$c = print_r($ch,true);
		//$this->setLogger("exotics_service: curlRequest. Curl Instance:$c");
				
		$error = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$res = curl_exec($ch);
		
		//$this->setLogger("Command: " . $command . "\nUser Agent: :" . $this->useragent . "\nRaw Response: " . $res . ". Post String:".$params."\n-------------");		
		
		$response = json_decode($res);
		$r = print_r($response,true);
		$this->setLogger("exotics_service: curlRequest. Response :$r");
		curl_close($ch);
		if ($response == "") {
			//throw new ApiException("Server has returned nothing.<br>Token Response: ".$error." ".$this->token."<br/>".$this->bm_service_url."/".$command."<br/>".$post_string);
			throw new ApiException("Betting is not available on this race at this stage - Try again shortly.");
		} else {
			return $response;
		}
		throw new ApiException("Curl request error");
	}

	private function getDataKey($userName, $userPassword, $companyID, $paramslist, $secretKey){
		// Get input object params
	
		$paramListBetData = '';
		$headerParams = $userName . $userPassword . $companyID;
		foreach($paramslist as $param){
			$paramListBetData .= $param;
		}
		// join params together
		// concatinate with secret key
		$paramsPlusSecret = $headerParams . $paramListBetData . $secretKey;
		// generate HASH
		$hashedParams = md5($paramsPlusSecret);
	
		$this->setLogger("exotics_service: getDataKey: params plus secret:$paramsPlusSecret");
	
		return $hashedParams;
	
		// append generated sequence to function call request
	
	
	}
	
	
	public function action($params=array(), $command=null)
	{
		if($command == "Betinput.aspx")
		{
			$response = $this->curlRequest($command, $params);
			if ($response->ErrorNo == "0")
			{
				$bet = new stdClass;
				$bet->isSuccess = "true";
				$bet->wagerId = $response->TransactionId;
				$bet->status = "S";
					
				$this->setLogger("exotics_service: action. Bet Placed!");
				return $bet;
			}
			else {
				$this->setLogger("exotics_service: action Failed.");
				throw new ApiException("Bet could not be posted. ".$response->detail);
			}
		}
		throw new ApiException("API error: No api path selected. ");
	}

	private function formatUrlString($params=array()){
		$fields_string = "";

		foreach($params as $key=>$value) { 
			if ($fields_string != ""){
				$fields_string .= "&";
			}
			$fields_string .= $key.'='.$value; 
		}
		return $fields_string;
	}
	
	private function setLogger($msg="")
	{
		//STAGING: $myFile = "/var/www/staging.topbetta.com/document-root/logs/bm_curl.log";
		$myFile = "/tmp/igas_exotics_betting.log";

		if ($fh = fopen($myFile, 'a')) {
			fwrite($fh, date('Y-m-d H:i:s') . "\n" . $msg);
			fwrite($fh, "\n");
			fclose($fh);					
		}
	}
		
}

class ApiException extends Exception
{
	public function __construct($response){
		if(is_array($response)){
			$error_list = array();
			foreach($response as $response_single){
				if($response_single->errorCode){
					$error_list[] = '(' . $response_single->errorCode . ') ' . $response_single->errorMessage;
				} else {
					$error_list[] = '';
				}
			}
			throw new Exception(serialize($error_list));
		}
		elseif(is_string($response)){
			throw new Exception(serialize('(' . 0 . ') ' . $response));
		}
		else{
			throw new Exception(serialize('(' . $response->errorCode . ') ' . $response->errorMessage));
		}
	}
}
