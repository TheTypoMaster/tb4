<?php
jimport('mobileactive.wagering.api.tastab.service');

class WageringApiTastabInformation extends WageringApiTastabService{
	const SERVICE_PATH = '/RaceInfo.asmx?WSDL';
	const NO_OF_FUTURE_DAYS = 3;

	public function getMeetingList()
	{	
		
		for($i = 1; $i == self::NO_OF_FUTURE_DAYS; $i++){
			$param_list = array(
				'sessionId' 	=> $this->getSessionId(),
				'date'   		=> $this->_getDate(),
			);
			
			$this->soap->GetMeetings($param_list);
			
			$this->incrementDateByDays(1);
			
			if ($this->auth->Error) {
				throw new Exception($this->auth->ErrorMessage);
			}
		}
	}
	
	private function _getDate()
	{
		return $this->date->format(parent::DATE_FORMAT_ACCOUNT);
	}
}