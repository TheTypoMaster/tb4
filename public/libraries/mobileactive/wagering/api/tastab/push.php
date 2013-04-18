<?php
jimport('mobileactive.wagering.api.tastab.service');
jimport('mobileactive.wagering.api.tastab.lib.iPushMessageType');
jimport('mobileactive.wagering.api.tastab.lib.pushMessage');

/**
 * Tastab Push Service
 * @author geoff
 *
 */
class WageringApiTastabPush extends WageringApiTastabService implements PushMessageType
{
	const SERVICE_PATH = '/GetDataFeedMsgs.aspx?';
	const X_PATH_ROOT = '/response/msgs';
	private $response = '';
	private $message = array();
	private $sequence_id = 0;
	const START_SEQ_ID = 0;
	/**
	 * _getLastSequenceId
	 * Get the last message's sequence id
	 * @return integer
	 */
	private function _getLastSequenceId()
	{
		return $this->sequence_id - 1;
	}
	/**
	 * getSequenceId
	 * get the current sequence id
	 * @return integer
	 */
	public function getSequenceId()
	{
		return $this->sequence_id;
	}
	/**
	 * setSequenceId
	 * force set the current sequnce Id
	 * @param integer $id
	 */
	public function setSequenceId($id)
	{
		$this->sequence_id = $id;
	}
	/**
	 * _processMessage
	 * process message
	 * @return boolean
	 */
	private function _processMessage()
	{	
		$seq = $this->getSequenceId();
		
		$this->message = $this->_getMessage($seq);
		
		if(is_null($this->message)){
			return false;
		}
		
		return true;
	}
	/**
	 * getNextMessage
	 * retrieve the next message
	 * @return string
	 */
	public function getNextMessage()
	{
		$start_time = time();
		$this->_serviceCall();
		while ($this->_processMessage() === false){
			while ($this->_serviceCall() === false){		
				echo "CONNECTION ERROR: Sleep for 2 seconds then retry\n";
				sleep(2);
			}

			if($start_time + 30 < time()){
				return PushMessage::customError('No new messages for 30 seconds');
			}
		}
		return $this->message;
	}
	/**
	 * _getMessage
	 * get different xml response's
	 * @param integer $id
	 * @return mixed
	 */
	private function _getMessage($id)
	{
		if ($this->response === false){
			return null;
		}
		
		$message = $this->response->xpath(self::X_PATH_ROOT.'/*[@seq=\''.$id.'\']');
		if (!empty($message)){
			$message_object = new PushMessage($message[0]);
		
			$this->setSequenceId(++$id);
			return $message_object;
		}
		
		$message = $this->response->xpath(self::X_PATH_ROOT.'/exc[@fromSeq=\''.$id.'\']');
		if (!empty($message)){
			$skipped_object = new PushMessage($message[0], PushMessage::SKIP_MESSAGE);
			$seq_id =  (int) $skipped_object->getAttribute('toSeq');
			$this->setSequenceId(++$seq_id);
			return $skipped_object;
		}
		
		$message = $this->response->xpath('/response/error');
		if (!empty($message)){
			$error_object = new PushMessage($message[0], PushMessage::ERROR_MESSAGE);
			return $error_object;
		}
				
		$message = $this->response->xpath('/response/newDay');
		if( !empty($message)){
			$message = $this->response->xpath('/response');
			$newday_object = new PushMessage($message[0], PushMessage::ERROR_MESSAGE);
			return $newday_object;
		}
		
		return null;
	}
	/**
	 * getMessage
	 * get a specific message based on a sequence id
	 * @param integer $id
	 * @return mixed
	 */
	public function getMessage($id){
		if ($this->_serviceCall($id-1) !== false){
			return $this->_getMessage($id);
		}
		return null;
	}
	/**
	 * getXML
	 * retrieve the current actual XML response
	 * @return string
	 */
	public function getXML()
	{
		return $this->response->asXML();
	}
	/**
	 * _getMessageList
	 * get full list of messages
	 */
	private function _getMessageList()
	{
		 return $this->response->xpath(self::X_PATH_ROOT);
	}
	/**
	 * _serviceCall
	 * Make initial call or get next set of messages
	 * @param int $id
	 * @return boolean
	 */
	private function _serviceCall($id=null)
	{	
		$this->response = false;
		$status = $this->getCurrentStatus();
		
		if(!is_null($status)){
			$seq_id = $this->_getLastSequenceId();
			
			// new reset logic - only allow refresh in early hours
			if ($status->date == $this->date->format(parent::DATE_FORMAT_ACCOUNT)) {
				if(date('G') > 5 && date('G') < 9){
					if($status->max_seq < $seq_id){
						$seq_id = self::START_SEQ_ID;
						$this->setSequenceId($seq_id);
					}
				}
			}
			
			$seq_id = is_null($id) ? $seq_id : $id;
			
			list($year, $month, $day) = explode('-', $status->date);
			$this->date->setDate($year, $month, $day);
			
			$query_list = array(
				'sid'		=> $this->session_id,
				'date'		=> $this->date->format(parent::DATE_FORMAT_INFORMATION),
				'lastSeq'	=> $seq_id
			);
			
			$this->response = $this->_loadXmlFile($query_list);
			//echo $this->api->host . $this->api->url . self::SERVICE_PATH . http_build_query($query_list); 
		}
		
		if($this->response === false){
			return false;
		}
		
		return true;
	}
	/**
	 * getCurrentStatus
	 * retrieve current status of the feeds
	 * @return mixed
	 */
	public function getCurrentStatus()
	{
		$query_list = array(
					'sid'		=> $this->session_id
		);
		
		$response = $this->_loadXmlFile($query_list);
		
		if($response === false){
			return;
		}

		$date = $response->xpath('/response/status/@date');
		$msgs = $response->xpath('/response/status/@msgs');
		$maxSeq = $response->xpath('/response/status/@maxSeq');

		$status = new stdClass;
		
		$status->date 		= (string) $date[0];
		$status->msg_count 	= (int) $msgs[0];
		$status->max_seq 	= isset($maxSeq[0]) ? (int) $maxSeq[0] : 0;
		
		return $status;
		
	}
	/**
	 * _loadXmlFile
	 * load in xml file into the simple xml
	 * @param unknown_type $query_list
	 */
	private function _loadXmlFile($query_list)
	{	
		$fp = @fsockopen('ssl://' . $this->api->host , 443, $errno, $errstr, 30);
		if ($fp) {
			stream_set_timeout ($fp, 30);
			$out = "GET " . $this->api->url . self::SERVICE_PATH . http_build_query($query_list)." HTTP/1.1\r\n";
			$out .= "Host: " . $this->api->host . "\r\n";
			$out .= "Connection: Close\r\n\r\n";
		    fwrite($fp, $out); 
		
			// get response
			$resp = "";
			while (!feof($fp)) {
				$resp .= fgets($fp, 128);
			}
			
			$info = stream_get_meta_data($fp);
			if ($info['timed_out']) {
				return false;
			}
						
			fclose($fp); 
	
			// check status is 200
			$status_regex = "/HTTP\/1\.\d\s(\d+)/";
		
			if (preg_match($status_regex, $resp, $matches) && $matches[1] == 200) {
				// load xml as object
				$parts = explode("\r\n\r\n", $resp);
				return simplexml_load_string($parts[1]);
			}
		}
		return false;
	}
}

