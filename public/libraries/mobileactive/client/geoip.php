<?php
defined('_JEXEC') or die();

jimport('mobileactive.config.reader');
jimport('joomla.cache.cache');

class ClientGeoIP extends ConfigReader{
	private $ip = null;
	private $response = null;
	private $service_uri = null;
	
	public function __construct($ip)
	{
		$service = $this->getService('geoip');
		$this->_setIP($ip);
		$this->_setServiceURI($service->uri);

		$response = $this->_sendRequest();
		$this->_response = $this->_processResponse($response);
	}
	
	private function _setIP($ip)
	{
		$this->ip = $ip;
	}
	
	private function _setServiceURI($uri)
	{
		$this->service_uri = $uri;
	}
	
	private function _sendRequest()
	{
		$cache = &JFactory::getCache('geoip');
		$cache->setCaching( 1 );
		$cache->setLifeTime( 3600 );
				
		$function = 'file_get_contents';
		$url = $this->service_uri . $this->ip;
		$response = $cache->call($function, $url);
		
		if (!$response){
            $id = $cache->_makeId( $function, $url );
            $cache->remove( $id, 'geoip' );
            
			throw new GeoIPException(GeoIPException::ERROR_CONNECTION);
		}
		
		return $response;
	}
	
	private function _processResponse($response)
	{
		
		$response_object = json_decode($response);
		
		if ($response_object->status == 'success'){
			return $response_object->result;
		}
		else if ($response_object->status == 'error'){
			throw new GeoIPException(GeoIPException::ERROR_CUSTOM, $response_object->message);
		}
		
		throw new GeoIPException(GeoIPException::ERROR_CONNECTION);
	}
	
	public function __call($name, $arguments)
	{
		
		if (preg_match('/^getCountry(\w*)$/', $name, $matches)){
			$field = strtolower($matches[1]);
			
			if (!is_null($this->_response)){
				return $this->_response->{$field};
			}
			else{
				return null;
			}
		}	
	}
}


class GeoIPException extends Exception
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