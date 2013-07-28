<?php

class WageringApiIgassportsService extends ConfigReader{
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

		$api = 'igassports';
		$this->date = new DateTime();
		$this->api = $this->getApi($api);
		$this->service_url = 'http://' . $this->api->host . $this->api->url;
		
		if (method_exists($this, 'initialise')){
			$this->initialise();
		}
	}

	public function initialise()
	{
		$components = array("com_betting", "com_tournament", "com_sportsbetting");

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
		
		//$this->token_model	=& JModel::getInstance('Token', 'BettingModel');
		//$this->runner_model	=& JModel::getInstance('Runner', 'TournamentModel');

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
	public function placeSportsBet($clientID, $betID, $amount, $gameID, $marketID, $line, $odds, $selectionID)
	{
		
		// TODO: get from config file or server.xml
		// Topbetta related params
		$userName = "topbetta";
		$userPassword = "T0pB3tter@AP!";
		$companyID = "TopBetta";
		$this->setLogger("placeSportsBet: Params - $clientID, $betID, $amount, $gameID, $marketID, $line, $odds, $selectionID");
		
	    // Bet related Paramaters
		$paramslist = array('betID' => "$betID", 'clientID' => "$clientID",'amount' => "$amount",
				'gameId' => "$gameID", 'marketId' => "$marketID",
				'selection' => "$selectionID", 'line' => "$line", 'odds' => "$odds");
		$p = print_r($paramslist,true);
		
		$this->setLogger("sports_service: placeSportsBet. Params List: $p");
		
		// Generate Data Key from all bet params
		$betDataKey = $this->getDataKey($userName, $userPassword, $companyID, $paramslist, '(*&j2zoez');
		$this->setLogger("sports_service: placeSportsBet. JSON dataKey: $betDataKey");
		
		// format JSON object for POSTing to iGAS
		$this->send_bet = $this->formatIgasPOST ($userName,$userPassword,$companyID, $paramslist, $betDataKey );
		$this->setLogger("sports_service: placeSportsBet. iGAS JSON POST: $this->send_bet");
		
		// POST JSON to iGAS
		$response = $this->action($this->send_bet, $this->service_quickbet_path);
		
		
		return $response;
	}
	
	public function placeBetList(Array $bet_data)
	{
		$this->setLogger();
		$bet_list = $bet_data['bet_list'];
		$event = $bet_data['event'];
	
		$params = $this->_buildBetList($bet_list);
		$response = $this->action($this->send_bet, $this->service_quickbet_path);
		/*ob_start(); // Test output
			print_r($params);
		$output = ob_get_contents();
		ob_end_flush();
		throw new ApiException("Outputting: ".$output);*/
	
		return $response;
	}
	
	
	private function getDataKey($userName, $userPassword, $companyID, $paramslist, $secretKey){
		// Get input object params
	
		$paramListBetData = '';
		$paramList = $userName . $userPassword . $companyID;
		foreach($paramslist as $param){
			$paramListBetData .= $param;
		}
		// join params together
		// concatinate with secret key
		$paramsPlusSecret = $paramList . $paramListBetData . $secretKey;
		// generate HASH
		$hashedParams = md5($paramsPlusSecret);
	
		$this->setLogger("sports_service: getDataKey: params plus secret:$paramsPlusSecret");
	
		return $hashedParams;
	
		// append generated sequence to function call request
	
	
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
		/*ob_start(); // Test output
		print_r($this->action($params[0], $this->service_bets_path));
		$output = ob_get_contents();
		ob_end_flush();
		//throw new ApiException("Outputting: ".$output);
		return $output;*/
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

	/**
	 * Build bet list. Set BM data in class property $send_bet
	 *
	 * @param array $bet_list
	 * @param bool $return_multiple
	 * @return object
	 */
	private function _buildBetList($bet_list, $return_multiple = true){

		if(is_null($this->meeting_code) || is_null($this->type_code)){
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
			'firstfour' => 'FF',
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

			if($bet_type == "win" || $bet_type == "place"){
				$bet_product_id = 0;
				$runner	= $this->runner_model->getRunnerDetailsByRaceIDAndNumber($race_id, $selections);

				if($bet_type == "win")
				{
					$bet_product_id = $runner[0]->w_product_id;
				} 
				elseif($bet_type == "place")
				{
					$bet_product_id = $runner[0]->p_product_id;
				}			
					
				$bm_bet_product = null;
				switch ($bet_product_id){
					case 5: // TopTote
						$bm_bet_product = ($bet_type == "win") ? "BT3" : "BP3";
						break;
					case 6: // MidTote
						$bm_bet_product = ($bet_type == "win") ? "BT1" : "BP1";
						break;
					case 7: // BestOrSP
						$bm_bet_product = "BT5";
						break;
					case 4: // VicTote. Don't need this at all, but leave here just in case and for vic exotics.
						$bm_bet_product = null;
						break;
					default: // If no tote selected
						$bm_bet_product = null;
						throw new ApiException("No bet product found or invalid product id.");
						break; 
				}

				// Conventional bet
				$wager = JRequest::getVar('wager_id', 'post');
				$optionId = $wager['first'][$selections];

				/*** Currently falling back directly on MidTote **/
				// If not midtote or toptote
				/*if($bm_bet_product == null) {
					if($bet_type == "win"){
						$bm_bet_product = "VICWin";
					} else {
						$bm_bet_product = "VICPlace";
					}
				}*/
				// Internal
				$formatted_bet['bet'] = array(
						'amount' => $bet->amount,
						'eventId' => $event_id,
						'betAmount' => ($bet->amount/100),
						'optionId' => $optionId[0],
						'handicap' => 1,
						'betType' => $bet_type,
						'special' => $bm_bet_product,
						'clientReferenceId' => $this->custom_id,
						'flexi' => $bet->isFlexiBet(),
						'poolType' => $bet_type_external,
						'racePoolId' => $bet->race_number[$bet_type_external]
				);

				// To send to BM
				$formatted_bet['request'] = array(
					'eventId' => $event_id,
					'betAmount' => ($bet->amount/100),
					'optionId' => $optionId[0],
					'handicap' => "1",
					'betType' => $bet_type,
					'special' => $bm_bet_product
				);

			} else {
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

				// To send to BM
				$formatted_bet['request'] = array(
						'eventId' => $event_id,
						'betAmount' => ($bet->amount/100),
						'optionId' => "0",
						'handicap' => "1",
						'betType' => "exotic",
						'exoticType' => $bet_type_string
				);
			}

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
					$formatted_bet['request']['exoticSels'] = $bet->formatBetSelections();
				}
			}		

			$formatted_bet_list[] = $formatted_bet['bet'];
			
		}
		$this->send_bet = $formatted_bet['request'];

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
		$this->setLogger("sports_service: Entering curlRequest. Command:$command");
		$this->setLogger("sports_service: curlRequest. post_string:$params");
		
		$ch = curl_init($this->service_url."/".$command);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($params))
		);
		
		
		$c = print_r($ch,true);
		$this->setLogger("sports_service: curlRequest. Curl Instance:$c");
		
		
		 
		
		//$ch = curl_init();
		//curl_setopt($ch,CURLOPT_URL,$this->service_url."/".$command);
		//curl_setopt($ch, CURLOPT_HEADER, 0);
		//curl_setopt($ch,CURLOPT_POSTFIELDS,$post_string);
		//$size = sizeof(split("&",$post_string));
		//if($size > 0){
		//	curl_setopt($ch, CURLOPT_POST, $size);
		//}
		//curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5-'.$_SERVER['SERVER_ADDR']);
		//curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		
		
		$error = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		
		$res = curl_exec($ch);
		
		$this->setLogger("Command: " . $command . "\nUser Agent: :" . $this->useragent . "\nRaw Response: " . $res . "\n-------------");		
		
		$response = json_decode($res);
		$r = print_r($response,true);
		$this->setLogger("sports_service: curlRequest. Response :$r");
		
		curl_close($ch);
		if ($response == "") {
			//throw new ApiException("Server has returned nothing.<br>Token Response: ".$error." ".$this->token."<br/>".$this->service_url."/".$command."<br/>".$post_string);
			throw new ApiException("Betting is not available on this race at this stage - Try again shortly.");
		} else {
			return $response;
		}
		throw new ApiException("Curl request error");
		
	}

	public function action($params=array(), $command=null)
	{
		$this->setLogger("sports_service: Entering action. Command:$command");
		$p = print_r($paramslist,true);
		$this->setLogger("sports_service: action. Params:$p");
		
	if($command == "Betinput.aspx")
		{
				
				$response = $this->curlRequest($command, $paramslist);
				$r = print_r($response, true);
				$this->setLogger("sports_service: action: Response from curlRequest:$r");
				
				if ($response->ErrorNo == "0") 
				{
					$this->setLogger("sports_service: action. No Errors!");
					$bet = new stdClass;
					$bet->isSuccess = "true";
					$bet->wagerId = $response->TransactionId;
					$bet->status = "S";
					
					$this->setLogger("sports_service: action. Bet Placed!");
					
					return $bet;
				} 
				else 
				{
					$this->setLogger("sports_service: curlRequest Failed.");
					
					
					throw new ApiException("Bet could not be posted. ".$response->ErrorText);

				}

		} 
		elseif($command == "bets")
		{
				//$params['token'] = $this->token;
				$response = $this->curlRequest($command."?token=".$this->token, $params);

				if ($response->result == "true") {
					$bet = new stdClass;

					$bet_status = null;
					//if(strstr($response->rows[0]->BetStatus, 'Return of') != false || strstr($response->rows[0]->BetStatus, 'Return @') != false)
					if ($response->rows[0]->BetOutcome == "Win")
					{
						$bet_status = "Win";
					} 
					else if ($response->rows[0]->BetStatus == "Scratched")
					{
    					$bet_status = "Refunded";
					} 
					else
					{
						$bet_status = $response->rows[0]->BetOutcome;
					}

		            $bet_status_array = array(
		            	'Win' => 'W',
		            	'Loss' => 'L',
		            	'Cancelled' => 'CN',
		            	'Refunded'	=>	'CN',
		            	'failed' => 'F',
		            	'error' => 'E',
		            	'Accepted' => 'S'
		            	);										
		            /*
		            $bet_status_array = array(
		            	'win' => 'W',
		            	'No Return' => 'L',
		            	'Scratched' => 'CN',
		            	'failed' => 'F',
		            	'error' => 'E',
		            	'Accepted' => 'S'
		            	);
		            */
					$bet->isSuccess = "true";
					$bet->status = $bet_status_array[$bet_status];
					$bet->amount = $response->rows[0]->BetAmount*100;

					if($bet->status == "W")
					{ // winning
						$bet->amountWon = $response->rows[0]->BetPayout*100;
					} 
					elseif($bet->status == "E" || $bet->status == "F") 
					{
						$bet->betErrorMessage = $response->rows[0]->BetStatus;
					} 

					return $bet;
				} 
				else 
				{
					//ob_start();
					//print_r($response);
					//print_r($post_bet_string);
					//$status = ob_get_contents();
					//ob_end_flush();
					$status = $response->hash;
					throw new ApiException("API Error ".$command.": ".$status);
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
	
	public function setLogger($msg="")
	{
		//STAGING: $myFile = "/var/www/staging.topbetta.com/document-root/logs/bm_curl.log";
		$myFile = "/tmp/igassports_curl.log";
		
		
		if ($fh = fopen($myFile, 'a')) {
			fwrite($fh, date('Y-m-d H:i:s') . "\n" . $msg);
			fwrite($fh, "\n");
			fclose($fh);					
		}
		
		
		/*$file_path = "/var/www/vhosts/topbetta.com/logs/bm_".date("Ymd").".log";
		//print_r($this->getRESTService());

		if (!$handle = fopen($file_path, 'a')) {
	        //throw new ApiException();
	        throw new ApiException("Service Not Available");
			return false;
    	}
    	else{
			$this->fhandle = $handle;
    	}*/
	}
	
	private function formatIgasPOST($UserName, $UserPassword, $CompanyID, $paramslist, $DataKey){
	
	
		return '{ "Username": "'.$UserName.'", "Password": "'.$UserPassword.'", "CompanyID": "'.$CompanyID.'", "ReferenceId": "'.$paramslist['betID'].'",
				"ClientId": "'.$paramslist['clientID'].'",  "Amount": '.$paramslist['amount'].', "Flexi": '.$paramslist['flexi'].', "DataKey": "'.$DataKey.'",
	
 				 "BetList": [
  					{ "GameId": '.$paramslist['gameId'].', "MarketId": '.$paramslist['marketId'].', "Selection": "'.$paramslist['selection'].'", "Line": "'.$paramslist['line'].'",
      				"Odds": "'.$paramslist['odds'].'" }
		 			]
				}';
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
