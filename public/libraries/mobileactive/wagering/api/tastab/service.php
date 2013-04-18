<?php

abstract class WageringApiTastabService extends ConfigReader
{		
	const DATE_FORMAT_ACCOUNT = 'Y-m-d',
		DATE_FORMAT_INFORMATION = 'd-M-Y';
		
	protected $date = null;
	protected $session_id = null;
	protected $test_api = true;
	
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
		$test_status = $config->getValue('api_test_mode');
		
		$this->setTestMode($test_status);
		$api = $this->test_api ? 'tastab-test' : 'tastab';

		$this->date = new DateTime();
		$this->api = $this->getApi($api);
		if (!($this instanceof WageringApiTastabAuthentication)){
			$auth = self::getInstance('WageringApiTastabAuthentication');
			$this->session_id = $auth->getSessionId();
		}
		
		if (method_exists($this, 'initialise')){
			$this->initialise();
		}
	}
	
	final public function incrementDateByDays($no_of_days)
	{
		$this->date->modify('+' . $no_of_days . ' day');
	}

	final protected function getSoapService($service_path)
	{
		return new SoapClient('https://' . $this->api->host . $this->api->url . $service_path, array('trace' => true));
	}
	
	final protected function setTestMode($status){
		if(is_bool($status)){
			$this->test_api = $status;
		}
	}
}

