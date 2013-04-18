<?php
defined('_JEXEC') or die();

jimport('joomla.base.object');
jimport('joomla.registry.registry');

/**
 * Config Reader for MobileActive server.xml
 *
 * @author declan
 */
class ConfigReader extends JObject
{
	/**
	 * Instance of the config reader
	 *
	 * @var ConfigReader
	 */
	private static $instance = null;

	/**
	 * Return an instance of the config reader
	 *
	 * @return ConfigReader
	 */
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance =  new ConfigReader;
		}

		return self::$instance;
	}
	
	/**
	 * Get MemCache Server
	 *
	 * @return object
	 */
	public function getMemCacheServerList() {
		$section = $this->_getConfigSection('memcache');
		
		$server_list = null;
		foreach($section->server as $node) {
			$server = array(
				'host' 		=> (string)$node->attributes()->host,
				'port' 		=> (string)$node->attributes()->port
			);
			
			$server_list[] = $server;
		}

		return $server_list;
	}
	/**
	 * Get Service
	 *
	 * @return object
	 */
	public function getService($name){
		
		$service = $this->_getXpath('/setting/section[@name="services"]/service[@name="'.$name.'"]');
		return $service[0];
	}
	
	/**
	 * Get timezone setting
	 *
	 * @return string
	 */
	public function getTimezone() {
		static $timezone = null;

		if(is_null($timezone)) {
			$timezone = (string)current($this->_getXpath('/setting/section[@name="general"]/timezone'));
		}

		return $timezone;
	}

	/**
	 * Get the current environment setting
	 *
	 * @return string
	 */
	public function getEnvironment() {
		static $environment = null;

		if(is_null($environment)) {
			$environment = (string)current($this->_getXpath('/setting/section[@name="general"]/environment'));
		}

		return $environment;
	}

	/**
	 * Get database connection settings
	 *
	 * @param string $name
	 * @return array
	 */
	public function getDatabase($name) {
		static $database_list = array();

		if(!isset($database_list[$name])) {
			$section = $this->_getConfigSection('database');

			$db = null;
			foreach($section->database as $node) {
				if($node->attributes()->name != $name) {
					continue;
				}

				$db = array(
					'host' 		=> (string)$node->host,
					'user' 		=> (string)$node->user,
					'password' 	=> (string)$node->password,
					'database'	=> (string)$node->name
				);
			}

			$database_list[$name] = $db;
		}

		return $this->_getRegistry("database_{$name}", $database_list[$name]);
	}
	
	/** 
	 * Get API settings 
	 * 
	 * @param string $name
	 * @return array
	 */
	public function getApi($name){
		static $api_list = array();
	
		if (!array_key_exists($name,$api_list)){
			$section = $this->_getConfigSection('api');
			
			foreach($section->api as $node){
				if($node->attributes()->name != $name){
					continue;
				}
				
				$api = new stdClass;
				
				$api->host = (string) $node->host;
				$api->url = (string) $node->url;
				$api->account = array(
					'number' => (string) $node->account->number,
					'pin'	=>	(string) $node->account->pin,
					'name'	=>	(string) $node->account->name
				);
				
				$api_list[$name] = $api;
			}
		}
		
		return $api_list[$name];
	}
	
	/** 
	 * Get Income Access settings 
	 * 
	 * @param string $name
	 * @return array
	 */
	public function getAffiliate($name){
		static $affiliate_list = array();
		
		if (!array_key_exists($name, $affiliate_list)) {
			$section = $this->_getConfigSection('affiliate');
			
			foreach ($section->affiliate as $node) {
				if($node->attributes()->name != $name){
					continue;
				}
				
				$affiliate = new stdClass;
				
				$affiliate->ssh = array(
					'user'				=> (string) $node->ssh->user,
					'host'				=> (string) $node->ssh->host,
					'private_key_path'	=> (string) $node->ssh->private_key_path
				);
				
				$affiliate->registration = array(
					'source_file_path'		=> (string) $node->registration->source_file_path,
					'destination_file_path'	=> (string) $node->registration->destination_file_path
				
				);
				
				$affiliate->sales = array(
					'source_file_path'		=> (string) $node->sales->source_file_path,
					'destination_file_path'	=> (string) $node->sales->destination_file_path
				);
				
				$affiliate_list[$name] = $affiliate;
			}
		}
		
		return $affiliate_list[$name];
	}
	

	/**
	 * Get the URL for a datafeed
	 *
	 * @param string $name
	 * @return string
	 */
	public function getDatafeed($name) {
		static $feed_list = array();

		if(!isset($feed_list[$name])) {
			$section = $this->_getConfigSection('datafeed');

			$default = null;
			$setting = null;
			foreach($section->feed as $feed) {
				$current = $feed->attributes()->name;
				if($current == 'default') {
					$default = $feed;
				}

				if($current == $name) {
					$setting = $feed;
				}
			}

			if(is_null($setting)) {
				$setting = $default;
			}

			if(!is_null($setting)) {
				$feed_list[$name] = (string)$setting->host;
			}
		}

		return $feed_list[$name];
	}

	/**
	 * Returns a JRegistry object for an associative array of settings
	 *
	 * @param string $name
	 * @param array $data
	 * @return JRegistry
	 */
	private function _getRegistry($name, array $data) {
		static $registry_list = array();

		if(!isset($registry_list[$name])) {
			$registry_list[$name] =& JRegistry::getInstance($name);
			$registry_list[$name]->loadArray($data);
		}

		return $registry_list[$name];
	}

	/**
	 * Query the internal SimpleXML object for an expression
	 *
	 * @param string $expression
	 * @return SimpleXMLElement
	 */
	private function _getXpath($expression) {
		$xml = $this->_getServerXML();
		return $xml->xpath($expression);
	}

	/**
	 * Get a section from the server.xml config file
	 *
	 * @param string $section_name
	 * @return SimpleXMLElement
	 */
	private function _getConfigSection($section_name) {
		static $section_list = array();

		if(!isset($section_list[$section_name])) {
			$xml = $this->_getServerXML();
			$section = $xml->xpath("/setting/section[@name='{$section_name}']");

			if(empty($section)) {
				trigger_error("The section '{$section_name}' was not found in the server config.", E_USER_ERROR);
			}

			if(count($section) > 1) {
				trigger_error("Server config contains multiple nodes for section '{$section_name}'. Using first node.", E_USER_WARNING);
			}

			$section_list[$section_name] = $section[0];
		}

		return $section_list[$section_name];
	}

	/**
	 * Load the server XML file into a SimpleXMLElement
	 *
	 * @return SimpleXMLElement
	 */
	private function _getServerXML() {
		static $xml = null;

		if(is_null($xml)) {
			$path = '/';
			if('\\' == DS) {
				$path = 'C:';
			}

			//$path .= DS . 'mnt' . DS . 'web' . DS . 'server.xml';
			$path = '/Users/mic/server.xml';
			$xml = simplexml_load_file($path);
		}

		return $xml;
	}
}