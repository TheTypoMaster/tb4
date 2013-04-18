<?php
jimport('mobileactive.wagering.api.tastab.service');

class WageringApiTastabAccount extends WageringApiTastabService{
	const SERVICE_PATH = '/Account.asmx?WSDL';
	private $meeting_code = null;
	private $type_code = null;
	private $soap = null;
	
	public function initialise()
	{
		$this->soap = $this->getSoapService(self::SERVICE_PATH);
	}
	
	public function placeBetList(Array $bet_data)
	{
		$bet_list = $bet_data['bet_list'];
		$event = $bet_data['event'];
		
		$meeting_date = new DateTime($event->start_date);
		$meeting_date = $meeting_date->format(parent::DATE_FORMAT_ACCOUNT);
		
		$params = array(
			'toteCode' 		=> '',
			'sessionId' 	=> $this->session_id,
			'accountNumber' => $this->api->account['number'],
			'pin'			=> $this->api->account['pin'],
			'meetingDate'	=> $meeting_date,
			'bets'			=> $this->_buildBetList($bet_list)
		);
		
		$response = $this->soap->PlaceBets($params)->PlaceBetsResult->BetResponses->BetResponse;

		if(is_array($response)){
			foreach($response as $bet){
				if($bet->Error){
					throw new ApiException($response);
				}
			}
			return false;
		}
		else{
			if($response->Error){
				throw new ApiException($response);
				return false;
			}
		}
		
		return $response;
	}
	
	public function validateBet(Array $bet_data)
	{
		$bet_list = $bet_data['bet_list'];
		$event = $bet_data['event'];
                
		$meeting_date = new DateTime($event->start_date);
		$meeting_date = $meeting_date->format(parent::DATE_FORMAT_ACCOUNT);

		$params = array(
			'sessionId' => $this->session_id,
			'meetingDate' => $meeting_date,
			'bet' => $this->_buildBetList($bet_list, false)
		);
                
		$response = $this->soap->ValidateBet($params)->ValidateBetResult;

		if($response->Error){
			throw new ApiException($response);
			return false;
		}
		
		return $response;
	}
	
	public function getAccountHistory($date = null)
	{
		if (is_null($date)) {
			$date = $this->_getDate();
		}
		
		$params = array(
			'sessionId' 			=> $this->session_id,
			'accountNumber' 		=> $this->api->account['number'],
			'pin'					=> $this->api->account['pin'],
			'transactionDate'		=> $date,
			'includeParimutuelBets'	=> true,
			'includeFunds'			=> false,
			'includeSportsbets'		=> false,
		);
		
		$response = $this->soap->GetAccountHistory($params)->GetAccountHistoryResult;

		if($response->Error){
			throw new ApiException($response);
			return false;
		}
		
		if(!empty($response->BetTransactions)){
			return $this->_sortTransactionListByTransactionID($response->BetTransactions->BetTransaction);
		}
		else{
			return;
		}
	}
	
	private function _sortTransactionListByTransactionID($transaction_list)
	{
		if (!is_array($transaction_list)) {
			return array($transaction_list->TransactionId => $transaction_list);
		}
		$sorted_list = array();
		foreach($transaction_list as $transaction){
			$sorted_list[$transaction->TransactionId] = $transaction;
		}
		return $sorted_list;
	}
	
	public function getAccountBalance(){
		
	}
	
	public function setTypeCode($value){
		$this->type_code = $value;
	}
	
	public function setMeetingCode($value){
		$this->meeting_code = $value;
	}
	
	public function setCustomId($value){
		$this->custom_id = $value;
	}
	
	private function _buildBetList($bet_list, $return_multiple = true){
		
		if(is_null($this->meeting_code) || is_null($this->type_code)){
			throw new Exception('Meeting code and type code must be set to place bet');
		}
		
		foreach($bet_list as $bet){
			
			if(!($bet instanceof WageringBet)){
				throw new Exception('Must be array of WageringBet objects');
			}
				
			$formatted_bet = array(
				'Venue' => $this->meeting_code,
				'MeetingType' => $this->type_code,
				'BetType' => $bet->getBetType(),
				'RaceNumber' => $bet->race_number,
				'Selections' => (string) $bet->getBetSelectionObject(),
				'Value' => $bet->amount,
				'ValueSecondary' => 0,
				'Id' => $this->custom_id,
				'Flexi' => $bet->isFlexiBet()
			);
				
			$formatted_bet_list[] = $formatted_bet;
		}
		
		return ($return_multiple) ? $formatted_bet_list : $formatted_bet;
	}
	
	private function _getTransactionDate()
	{
		return $this->date->format(parent::DATE_FORMAT_INFORMATION);
	}
	
	private function _getDate()
	{
		return $this->date->format(parent::DATE_FORMAT_ACCOUNT);
	}
}

class ApiException extends Exception
{
	public function __construct($response){
		if(is_array($response)){
			$error_list = array();
			foreach($response as $response_single){
				if($response_single->Error){
					$error_list[] = '(' . $response_single->ErrorCode . ') ' . $response_single->ErrorMessage;
				} else {
					$error_list[] = '';
				}
			}	
			throw new Exception(serialize($error_list));
		}
		else{
			throw new Exception(serialize('(' . $response->ErrorCode . ') ' . $response->ErrorMessage));
		}
	}
}