<?php

jimport('mobileactive.wagering.api.igas.racing_service');

class WageringApiIgasracing extends WageringApi 
{
	
	
	
	private $error_list = array();	
	
	public function checkConnection()
	{
		//WageringApiIgasracingService::setLogger("igasracing: checkConnection. Entering");
		$account = $this->_getAccountService();
		return $this->_callOperation('checkConnection', $account);
	}
	
	public function placeRacingBet($clientID, $betID, $amount, $flexi, $meetingID, $raceNo, $betType, $priceType, $selection)
	{
		$account = $this->_getAccountService();
		return $this->_callOperation('placeRacingBet', $account, $clientID, $betID, $amount, $flexi, $meetingID, $raceNo, $betType, $priceType, $selection);
	}
	
	
	public function placeBet(WageringBet $bet, $event, $custom_id)
	{
		//WageringApiIgasracingService::setLogger("igasracing: placeBet. Entering");
		return $this->placeBetList(array($bet), $event, $custom_id);
	}
	
	public function placeBetList($bet_list, $event, $custom_id)
	{
		//WageringApiIgasracingService::setLogger("igasracing: placeBetList. Entering...");
		
		//$o = print_r($event);
		//WageringApiIgasracingService::setLogger("igasracing: placeBetList. Event: $o");
		
		$account = $this->_getAccountService();
		$account->setTypeCode($event->type_code);
		$account->setMeetingCode($event->meeting_code);
		$account->setCustomId($custom_id);
		return $this->_callOperation('placeBetList', $account, array('bet_list' => $bet_list, 'event' => $event));
	}
	
	public function getBetInfo($wager_id)
	{
		$account = $this->_getAccountService();
	
		return $this->_callOperation('getBetInfo', $account, array('wager_id' => $wager_id));
	}
	
	public function validateBet(WageringBet $bet, $event)
	{
		//WageringApiIgasracingService::setLogger("igasracing: validateBet. Entering");
		$account = $this->_getAccountService();
		$account->setTypeCode($event->type_code);
		$account->setMeetingCode($event->meeting_code);
		$account->setCustomId(0);
		
		return $this->_callOperation('validateBet', $account,
                        array('bet_list' => array($bet), 'event' => $event));
	}
	
	public function getAccountHistory($date = null)
	{
		$account = $this->_getAccountService();
		
		return $this->_callOperation('getAccountHistory', $account, $date);
	}
	
	public function getBetResult(){
		
	}
	
	public function getErrorList($return_string = false)
	{
		if ($return_string) {
			$error_display_list	= array();
			foreach ($this->error_list as $error) {
				if (is_array($error)) {
					foreach ($error as $e)
					$error_display_list[]= $e;
				} else {
					$error_display_list[] = $error;
				}
			}
			$error_list = implode('; ', $error_display_list);
			
			if (count($this->error_list) > 0) {
				$error_list = 'Error - ' . $error_list;
			}
		} else {
			$error_list = $this->error_list;
		}
		
		return $error_list;	
	}
	
	private function _getAccountService(){
		//WageringApiIgasracingService::setLogger("igasracing: _getAccountService. Entering");
		return new WageringApiIgasracingService();
	}
/*	
	public function getPushService(){
		return WageringApiTastabService::getInstance('WageringApiTastabPush');
	}
	*/
	private function _callOperation($operation, $service)
	{	
		//WageringApiIgasracingService::setLogger("igasracing: _callOperation. Entering. Operation:$operation");
		
		$extra_args = array_slice(func_get_args(), 2);
		//$o =print_r($extra_args, true);
		//WageringApiIgasracingService::setLogger("igasracing: _callOperation. Entering. Extra Args:$o");
		try{
			return call_user_func_array(array($service, $operation), $extra_args);
		}
		catch(Exception $e){
			$this->error_list[$operation][] = unserialize($e->getMessage());
		}
	}
}