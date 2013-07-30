<?php

abstract class WageringApi
{
	
	const 	API_DEFAULT = 'IGAS',
		  	API_BM = 'bm',
		  	API_IGAS = 'IGAS';
		
	static public $service = self::API_DEFAULT;
	
	public static function getInstance($wagering_api=self::API_DEFAULT){
			static $instance = null;
			
			if(is_null($instance)){
				$service_class = 'WageringApi'.$wagering_api;
				$instance = new $service_class();
			}
			return $instance;
	}
	
	abstract public function placeBetList($bet_list, $event, $custom_id);
	abstract public function placeBet(WageringBet $bet, $event, $custom_id);
	abstract public function placeSportsBet($event_id, $special, $handicap, $bet_type, $bet_amount, $bet_option, $bet_dividend);
	abstract public function getBetResult();
	abstract public function checkConnection();
}