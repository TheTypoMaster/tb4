<?php
jimport('mobileactive.wagering.api.tastab.service');

class WageringApiTastabAuthentication extends WageringApiTastabService{
	const SERVICE_PATH = '/Session.asmx?WSDL';
	private $auth = null;
	
	public function initialise(){
		$soap = $this->getSoapService(self::SERVICE_PATH);

		$params = array(
			'account' => $this->api->account['number'], 
			'name' => $this->api->account['name'], 
			'password' => $this->api->account['pin']
		);
		
		$this->auth = $soap->login($params)->LoginResult;
		
		if ($this->auth->Error) {
			throw new Exception($this->auth->ErrorMessage);
		}
	}
	
	public function getSessionId(){
		return $this->auth->SessionId;
	}
}