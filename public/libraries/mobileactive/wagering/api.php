<?php
defined('_JEXEC') or die();

jimport('mobileactive.config.reader');

abstract class WageringApi
{
	
	const API_TASTAB = 'tastab',
		API_UNITAB = 'unitab',
		API_DEFAULT = 'tob',
		API_BM = 'bm',
		API_TOB = 'tob',
		API_IGASRACING = 'igasracing',
		API_IGASSPORTS = 'igassports';
	
		
	static public $service = self::API_DEFAULT;
	
	public static function getInstance($wagering_api=self::API_DEFAULT){
			static $instance = null;
			
			if(is_null($instance)){
				jimport('mobileactive.wagering.api.'.$wagering_api);
				$service_class = 'WageringApi'.$wagering_api;
				$instance = new $service_class();
			}
			
			return $instance;
	}
	
	abstract public function placeBetList($bet_list, $event, $custom_id);
	abstract public function placeBet(WageringBet $bet, $event, $custom_id);
	abstract public function getBetResult();
	abstract public function checkConnection();
}