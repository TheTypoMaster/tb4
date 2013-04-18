<?php


class WageringApiBmService extends ConfigReader{
	private $service_login_path = '/login'; //login path to get token
	private $service_quickbet_path = '/quickbet'; //quickbet path
	private $service_bets_path = '/bets'; //bet lookup path

	private $meeting_code = null;
	private $type_code = null;
	//private $soap = null;
	private $request = null;
	private $response = null;

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
		
		$api = 'bm';
		$this->date = new DateTime();
		$this->api = $this->getApi($api);

		if (method_exists($this, 'initialise')){
			$this->initialise();
		}
	}

	public function initialise()
	{
		//no need for this
		//$this->soap = $this->getSoapService(self::SERVICE_PATH);

		$this->getRESTService();
	}
	
	public function sendJSONRequest(){
		//get token
		//send json to service
		//
	}

	public function checkConnection()
	{
		//get token and return true or false
		//return $this->soap;
		return true;
		//return $this->getRESTService();
	}
	
	public function placeBetList(Array $bet_data)
	{
		$this->setLogger();

		$bet_list = $bet_data['bet_list'];
		$event = $bet_data['event'];

		//use token instead
		$params = array(
			'username' 			=> $this->api->account['number'],
			'password'			=> $this->api->account['pin'],
			'requests'			=> $this->_buildBetList($bet_list)
		);
		
		$final_soap_params= array('arg0' => $params);
			
		fwrite($this->fhandle,"\n".date('l jS \of F Y h:i:s A'). "\n");
		
		if(is_object($this->soap)){
					
			$response = $this->soap->placeWagersSync($final_soap_params)->return->responses;
			
			fwrite($this->fhandle,"REQUEST:\n" . $this->soap->__getLastRequest() . "\n");
			fwrite($this->fhandle,"RESPONSE:\n" . $this->soap->__getLastResponse() . "\n");
					
			if(!$response->wagerId){
					throw new ApiException($response);
					return false;
				}
	
			return $response;
		}else{
			throw new ApiException("Service Not Available");
			return false;
		}
	}

	public function getBetInfo(Array $bet_data)
	{
		$wager_id = $bet_data['wager_id'];
		$params = array(
			'username' 			=> $this->api->account['number'],
			'password'			=> $this->api->account['pin'],
			'betIds'			=> $wager_id
		);
		
		$final_soap_params= array('arg0' => $params);
		
		if(is_object($this->soap)){
			
			$response = $this->soap->getWagerInfo($final_soap_params)->return->responses;
					
			if(!$response->status){
					throw new ApiException($response);
					return false;
				}
	
			return $response;
		}else{
			throw new ApiException("Service Not Available");
			return false;
		}
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
			
			$formatted_bet = array(
					'amount' => $bet->amount,
					'clientReferenceId' => $this->custom_id,		
					'flexi' => $bet->isFlexiBet(),
					'poolType' => $bet_type_external,
					'racePoolId' => $bet->race_number[$bet_type_external],
			);
			
			// Add rugNumbers for bets
			$selections = (string) $bet->getBetSelectionObject();
			$rug_list = explode("/", $selections);
			$rug_count = $bet->isBoxed() ? $bet->getPositionSelectionCount() : count($rug_list);
			
			
			if($bet_type == "quinella")
			{
			 	$rug_count = count($rug_list);
			}
			
			for($i=1; $i <= $rug_count; $i++)
			{
				$rugNumber = 'rugNumber'.$i;
				
				if($bet_type == "quinella")
				{
				 	$formatted_bet[$rugNumber] = $rug_list[$i-1];
				}else{
					$formatted_bet[$rugNumber] = $bet->isBoxed() ? $selections : $rug_list[$i-1];
				}
			}
			
			$formatted_bet_list[] = $formatted_bet;
		}
		
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


	private function _getToken()
	{
		//
		return $this->getRESTService();
	}

	private function getRESTService($service_path=null){
		if($service_path==null){
			$service_path = $this->service_login_path;
		}
		$bm_service_url = 'https://' . $this->api->host . $this->api->url . $service_path;
		$params = array(
			'username' 			=> $this->api->account['number'],
			'password'			=> $this->api->account['pin']
		);
		//$postfields = "login=login&username=$username&password=$password";

		foreach($params as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }

		//$cookie="/tmp/cookie";
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $bm_service_url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
		//curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie);
		//curl_setopt ($ch, CURLOPT_REFERER, $bm_service_url);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt ($ch, CURLOPT_POST, 1);
		$result = curl_exec ($ch);

		curl_close($ch);
		echo $result;
	}

	/*private function getSoapService($service_path)
	{
		$tob_service_file_path = 'https://' . $this->api->host . $this->api->url . $service_path;
		if (!$handle = @fopen($tob_service_file_path, 'r')) {
			return null;
		}else{
			$soap_object = new SoapClient($tob_service_file_path, array('trace' => true));
			if(is_object($soap_object))
			{
				return $soap_object;
			}
		}
		
		return null;
	}*/
	
	private function setLogger()
	{
		/*$file_path = "/usr/local/mobileactive/logs/topbetta-crons/theoddsbroker_".date("Ymd").".log";
		if (!$handle = fopen($file_path, 'a')) {
	        throw new ApiException("Service Not Available");
			return false;
    	}
    	else{
			$this->fhandle = $handle;
    	}*/
    	return false;
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
