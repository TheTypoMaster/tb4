<?php

jimport('mobileactive.wagering.api.igas.sports_service');

class WageringApiIgassports extends WageringApi 
{
	
	
	
	private $error_list = array();	
	
	public function checkConnection()
	{
		/* $file = "/tmp/api.txt";
		$debug = "- BMSPORTS.php: In check connection function\n";
		file_put_contents($file, $debug, FILE_APPEND | LOCK_EX); */
		$account = $this->_getAccountService();
		return $this->_callOperation('checkConnection', $account);
	}
	
	public function placeSportsBet($event_id, $special, $handicap, $bet_type, $bet_amount, $bet_option, $bet_dividend)
	{
		/* $file = "/tmp/api.txt";
		$betarray = print_r($bet, true);
		$debug = "- BMSPORTS.php: In place bet function. EvenvtID:$event_id, Special:$special, handicap:$handicap, BetType:$bet_type, BetAmount:$bet_amount, BetOption:$bet_option, BetDividend:$bet_dividend\n";
		file_put_contents($file, $debug, FILE_APPEND | LOCK_EX); */
		$account = $this->_getAccountService();
		// pass the bet params to the service function (bet , event and bet_id)
		$params = "eventId=$event_id&special=&handicap=$bet_handicap&betType=$bet_type&betAmount=$bet_amount&optionId=$bet_option&dividend=$bet_dividend";
		return $this->_callOperation('placeSportsBet', $account, $event_id, $special, $handicap, $bet_type, $bet_amount, $bet_option, $bet_dividend);
	} 
	
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
		return new WageringApiBMSportsService();
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