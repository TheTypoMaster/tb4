<?php

jimport('mobileactive.wagering.api.tastab.authentication');
jimport('mobileactive.wagering.api.tastab.account');
jimport('mobileactive.wagering.api.tastab.push');
jimport('mobileactive.wagering.api.tastab.information');

class WageringApiTastab extends WageringApi 
{
	
	private $error_list = array();	
	
	public function placeBet(WageringBet $bet, $event, $custom_id)
	{
		return $this->placeBetList(array($bet), $event, $custom_id);
	}
	
	public function placeBetList($bet_list, $event, $custom_id)
	{
		$account = $this->_getAccountService();
		$account->setTypeCode($event->type_code);
		$account->setMeetingCode($event->meeting_code);
		$account->setCustomId($custom_id);
		
		return $this->_callOperation('placeBetList', $account, array('bet_list' => $bet_list, 'event' => $event));
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
		return WageringApiTastabService::getInstance('WageringApiTastabAccount');
	}
	
	public function getPushService(){
		return WageringApiTastabService::getInstance('WageringApiTastabPush');
	}
	
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