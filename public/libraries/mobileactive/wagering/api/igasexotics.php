<?php

jimport('mobileactive.wagering.api.igas.exotics_service');

class WageringApiIgasexotics extends WageringApi 
{
	
	private $error_list = array();	
	
	public function checkConnection()
	{
		$account = $this->_getAccountService();
		return $this->_callOperation('checkConnection', $account);
	}
	
	public function placeBet(WageringBet $bet, $event, $custom_id)
	{
		
		return $this->placeBetList(array($bet), $event, $custom_id);
	}
	
	
	public function placeRacingBet(WageringBet $bet, $event, $custom_id, $userID, $raceNO, $priceType, $meetingID)
	{
	
		return $this->placeRacingBetList(array($bet), $event, $custom_id, $userID, $raceNO, $priceType, $meetingID);
	}
	
	
	public function placeRacingBetList($bet_list, $event, $custom_id, $userID, $raceNO, $priceType, $meetingID)
	{
		
		//$bl = print_r($bet_list,true);
		//file_put_contents('/tmp/saveExoticsBet', "* igasexotcs: PlaceRacingbetList. Bet List". $bl . "\n", FILE_APPEND | LOCK_EX);
		$account = $this->_getAccountService();
		$account->setTypeCode($event->type_code);
		// $account->setMeetingCode($event->meeting_code);
		$account->setCustomId($custom_id);
		
		return $this->_callOperation('placeRaceBetList', $account, array('bet_list' => $bet_list, 'event' => $event), $userID, $raceNO, $priceType, $meetingID);
	}
	
	public function placeBetList($bet_list, $event, $custom_id)
	{
		//$bl = print_r($bet_list,true);
		//file_put_contents('/tmp/saveExoticsBet', "* igasexotcs: PlacebetList. Bet List". $bl . "\n", FILE_APPEND | LOCK_EX);
		$account = $this->_getAccountService();
		$account->setTypeCode($event->type_code);
		// $account->setMeetingCode($event->meeting_code);
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
		return new WageringApiIgasexoticsService();
	}
/*	
	public function getPushService(){
		return WageringApiTastabService::getInstance('WageringApiTastabPush');
	}
	*/
	private function _callOperation($operation, $service)
	{	
		$extra_args = array_slice(func_get_args(), 2);
		try{
			return call_user_func_array(array($service, $operation), $extra_args);
		}
		catch(Exception $e){
			$this->error_list[$operation][] = unserialize($e->getMessage());
		}
	}
}