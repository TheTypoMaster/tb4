<?php namespace TopBetta\backend;

use TopBetta;

class BetResultsController extends \BaseController {

	/**
	 * Default log message type
	 *
	 * @var integer
	 */
	const LOG_TYPE_NORMAL = 0;
	
	/**
	 * Debug log message type
	 *
	 * @var integer
	 */
	const LOG_TYPE_DEBUG = 1;
	
	/**
	 * Log message type for errors
	 *
	 * @var integer
	 */
	const LOG_TYPE_ERROR = 2;
	
	/**
	 * Default time formatting string for log messages
	 *
	 * @var string
	 */
	const LOG_TIME_FORMAT_DEFAULT = 'r';
	
	/**
	 * Show time string in log messages
	 *
	 * @var string
	 */
	const LOG_TIME_SHOWN = false;
		
	/**
	 * Debugging mode flag
	 *
	 * @var boolean
	 */
	private $debug = true;

	// iGAS Transacation status' - //TODO: Code table?
	const TRANSACTION_STATUS_WON = 'W',
	TRANSACTION_STATUS_LOST = 'L',
	TRANSACTION_STATUS_CANCELLED = 'C',
	TRANSACTION_STATUS_REFUNDED = 'R',
	TRANSACTION_STATUS_INVALID = 'V',
	TRANSACTION_STATUS_UNDECIDED = 'U',
	
	// BM ones
	TRANSACTION_STATUS_FAILED = 'F',
	TRANSACTION_STATUS_NEW = 'N',
	TRANSACTION_STATUS_SUBMITTED = 'S',
	TRANSACTION_STATUS_ERROR = 'E';
	
	private $status_process_unresulted_list = array(
			self::TRANSACTION_STATUS_WON,
			self::TRANSACTION_STATUS_LOST,
			self::TRANSACTION_STATUS_CANCELLED,
			self::TRANSACTION_STATUS_REFUNDED
	);
	
	private $status_process_pending_list = array(
			self::TRANSACTION_STATUS_SUBMITTED,
			self::TRANSACTION_STATUS_CANCELLED,
			self::TRANSACTION_STATUS_FAILED,
			self::TRANSACTION_STATUS_WON,
			self::TRANSACTION_STATUS_LOST
	);
	
	
	
	public function __construct()
	{
	 	//$this->beforeFilter('apiauth');
	}
	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		// return RaceMeetings::all();
		return "Bet Result's API Index";
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response$resultsJSON
	 */
	public function store()
	{
		
		// Log this
		$this->l("BackAPI: BetResults - Reciving POST");
		
		// get the JSON POST
		$resultsJSON = \Input::json();
		$jsonSerialized = serialize($resultsJSON);
		
		if($this->debug){
			$timeStamp = date("YmdHis");
			\File::append('/tmp/backAPIresults-'.$timeStamp, $jsonSerialized);
		}
		
		// make sure JSON was received
		$keyCount = count($resultsJSON);
		if(!$keyCount){
			Topbetta\LogHelper::l("BackAPI: BetResults - No Data In POST",2);
			return \Response::json(array(
					'error' => true,
					'message' => 'Error: No JSON data received'),
					400
			);
		}

		// base structure for reponse payload
		$responsePayload = array('error' => 'false', 'result' => array());
		
		
		//$resultsJSON = print_r($resultsJSON, true);
		//echo"$resultsJSON\n\n\n\n\n";
		//exit;
		
		//TODO: // validate the json. Create some rules and check the json validates
		/* $validation = Validator::make(array('json'=> $resultsJSON),array('json' => 'mime:json'));
		if($validation->fails())
		{
			return Response::json($validation->errors);
		}
		else
		{
			// all OK!
		}
		exit; */
		// JSON objects OutcomeList
		
		$this->l("BackAPI: BetResults - Processing '$keyCount' Objects");
		$objectCount=1;
		// loop on objects in data
		foreach($resultsJSON as $key => $resultsArray){
			
			// Make sure we have some data to process in the array
			if(is_array($resultsArray)){
				
				// process the meeting/race/runner data
				switch($key){
					
					// Meeting Data - the meeting/venue
					case "OutcomeList":
						Topbetta\LogHelper::l("BackAPI: BetResults - Processing OutcomeList, Object:$objectCount");
						
						//TODO: CHECK DataKey to validate bet result
						//$dataKey = $resultsArray['DataKey'];
						$dataKey = true;
						
						if($dataKey){
							
							foreach ($resultsArray as $dataArray){
								
								$transaction = array();
								// store data from array
								if(isset($dataArray['TransactionId'])){

									// bet result details from IGAS
									$transaction['transactionID'] = $dataArray['TransactionId'];
									$transaction['betOutcome'] = $dataArray['BetOutcome'];
									$transaction['returnAmount'] = $dataArray['ReturnAmount'];
										
									Topbetta\LogHelper::l("BackAPI: BetResults - iGas TransactionID: ".$transaction['transactionID'].". BetOutcome: ".$transaction['betOutcome'].". Return Amount: ".$transaction['returnAmount']);
									
									// check if transaction ID exists in DB if not throw error
									$transactionExists = TopBetta\Bet::getBetExists($transaction['transactionID']);
									
									// If there is a matching bet
									if($transactionExists){
										Topbetta\LogHelper::l("BackAPI: BetResults - iGas bet found in DB");
										// get the bet record based on the transactionID
										$betObject = TopBetta\Bet::getBetDetails($transaction['transactionID'])->toArray();
										
										$b = print_r($betObject[0],true);
										Topbetta\LogHelper::l("BackAPI: BetResults - Bet data from DB: $b");
																			
										// check it can be processed
										if($this->_canTransactionBeProcessed($transaction,$this->status_process_unresulted_list) && $betObject[0]['bet_result_status_id'] == "1"){
											Topbetta\LogHelper::l("BackAPI: BetResults - Bet status '".$transaction['betOutcome']."' processing ");
											// process unresulted bets
											$this->processTransaction($transaction, $betObject[0]);
										}
										$responsePayload['result'][] = array('trasnactionID' => $transaction['transactionID'], 'betOutcome' => $transaction['betOutcome'], 'error' => 'false');
									} else{
										
										// Email on failer to result bet
										$emailSubject = "iGAS Bet Results: Bet Result not processed: ".$transaction['transactionID'].".";
										$emailDetails = array( 'email' => 'oliver@topbetta.com', 'first_name' => 'Oliver', 'from' => 'betresults@topbetta.com', 'from_name' => 'TopBetta iGAS BetResults', 'subject' => "$emailSubject" );
										
										$newEmail = \Mail::send('hello', $emailDetails, function($m) use ($emailDetails)
										{
											$m->from($emailDetails['from'], $emailDetails['from_name']);
											$m->to($emailDetails['email'], 'Oliver Shanahan')->subject($emailDetails['$emailSubject']);
										});
										/*
										return \Response::json(array(
												'error' => true,
												'message' => 'Error: Transaction Id not found in DB: '. $transaction['transactionID']),
												400
										);*/
										
										$responsePayload['error'] = "true";
										$responsePayload['result'][] = array('transactionID' => $transaction['transactionID'], 'betOutcome' => $transaction['betOutcome'], 'error' => 'true');
										
									}
								
								}else{
									return \Response::json(array(
											'error' => true,
											'message' => 'Error: No Transaction ID JSON: '),
											400
									);
									
								}
							}
							
						}else{
							return \Response::json(array(
									'error' => true,
									'message' => 'Error: DataKey rejected. Results NOT processed'),
									400
							);
						}
						break;

					default :
						$this->l("BackAPI: BetResults - Processing $objectCount: $key", 2);
						return \Response::json(array(
							'error' => true,
							'message' => 'Error: Data format not recognised: '. $key),
							400
						);
				}
			}else{
				$this->l("BackAPI: BetResults - Processing $objectCount: $key. No Data. Can't Process", 2);
				/* return Response::json(array(
						'error' => true,
						'message' => 'Error: No Data found'),
						400
				); */
			}
			$objectCount++;
		}
		
		$j = json_encode($responsePayload);
		
		$b = print_r($j,true);
		Topbetta\LogHelper::l("BackAPI: BetResults - RETURNED JSON: $b");
		return \Response::json($responsePayload);
		
		/* return \Response::json(array(
				'error' => false,
				'message' => 'OK: Bet Results Processed Successfully'),
				200
		); */
		//return RaceMeetings::all();
		
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	/**
	 * Log a message to laravel logs
	 *
	 * @param string 	$message
	 * @param integer 	$type
	 * @param string 	$time_format
	 * @param boolean 	$add_new_line
	 */
	public function l($message, $type = null, $show_time = true, $time_format = null, $add_new_line = true) {
		if(is_null($type)) {
			$type = self::LOG_TYPE_NORMAL;
		}
	
		if($type == self::LOG_TYPE_DEBUG && $this->debug == FALSE){
			return 0;
		}
		
		if(self::LOG_TIME_SHOWN){
			$time = $this->_formatLogTime($time_format);
		}else{
			$time = '';
		}
		
		
		//$processPID = getmypid();
	
		$prefix = array(
				self::LOG_TYPE_NORMAL => 'Info: ',
				self::LOG_TYPE_DEBUG =>  'Debug: ',
				self::LOG_TYPE_ERROR =>  'Error: '
		);
	
		$suffix = ($add_new_line) ? "\n" : '';
	
		\Log::info(sprintf('%s %s%s %s', $time, $prefix[$type], $message, $suffix));
		//echo sprintf('%s %s%s %s', $time, $prefix[$type], $message, $suffix);
	}
	
	/**
	 * Format the timestamp for a log message
	 *
	 * @param string $format
	 */
	private function _formatLogTime($format = null) {
		if(is_null($format)) {
			$format = self::LOG_TIME_FORMAT_DEFAULT;
		}
	
		return '[' . date($format) . ']';
	}
	
	
	
	
	private function canTransactionBeProcessed($transaction,$status_process_list)
	{
		return in_array($transaction['betOutcome'], $status_process_list);
	}
	
	private function processTransaction($transaction, $betArray)
	{
	
		// get a model instance of the bet table
		$betRecord = TopBetta\Bet::find($betArray['id']);
		
		// Set results status to PAID
		$result_status = TopBetta\BetResultStatus::STATUS_PAID;
		
		// Set resulted flag to 0 in model
		$betRecord->resulted_flag = 1;
		
		// Log some stuff
		Topbetta\LogHelper::l('BackAPI: BetResults - Processing Bet ID: ' . $betArray['id'] .'. Bet free flag: '. $betArray['bet_freebet_flag']. '. Bet free amount: '. $betArray['bet_freebet_amount']);
			
		// Bet should be refunded
		if ($transaction['betOutcome'] == self::TRANSACTION_STATUS_CANCELLED || $transaction['betOutcome'] == self::TRANSACTION_STATUS_INVALID || $transaction['betOutcome'] == self::TRANSACTION_STATUS_REFUNDED){
			// Full bet amount was on free credit
			if ($betArray['bet_freebet_flag'] == 1 && $betArray['bet_freebet_amount'] == $transaction['returnAmount']) {
				Topbetta\LogHelper::l("BackAPI: BetResults - Full bet amount was on free credit");
				$betRecord->refund_freebet_transaction_id = $this->awardFreeBetRefund($betArray['user_id'], $betArray['bet_freebet_amount']);
				Topbetta\LogHelper::l('Free Bet full refund: ' . $betArray['bet_freebet_amount'] . ' cents');
			} else if ($betArray['bet_freebet_flag'] == 1 && $betArray['bet_freebet_amount'] < $transaction['returnAmount']) {
				// Free bet amount was less then refund
				Topbetta\LogHelper::l("BackAPI: BetResults - Free bet amount was less than refund");
				$refund_amount = $transaction['returnAmount'] - $betArray['bet_freebet_amount'];
				// Refund free bet amount
				$betRecord->refund_freebet_transaction_id = $this->awardFreeBetRefund($betArray['user_id'], $betArray['bet_freebet_amount']);
				Topbetta\LogHelper::l('Free Bet partial refund: ' . $betArray['bet_freebet_amount'] . ' cents');
				// Refund balance to account
				$betRecord->refund_transaction_id =$this->awardBetRefund($betArray['user_id'], $refund_amount);
				Topbetta\LogHelper::l('Paid partial refund: ' . $refund_amount . ' cents');
			} else {
				// No free credit was used - refund full amount to account
				$betRecord->refund_transaction_id = $this->awardBetRefund($betArray['user_id'], $transaction['returnAmount']);
				Topbetta\LogHelper::l('Paid refund: ' . $transaction['returnAmount'] . ' cents');
			}
			$betRecord->refunded_flag = 1;
			$result_status = TopBetta\BetResultStatus::STATUS_FULL_REFUND;
				
		}
	
		// Winning bets
		if ($transaction['returnAmount'] > 0 && $transaction['betOutcome'] == self::TRANSACTION_STATUS_WON){
			$actual_win_amount = $transaction['returnAmount'];
			//for free bets places, deduct the stake amount from the winnings first
			if ($betArray['bet_freebet_flag'] == 1) {
				$actual_win_amount -= $betArray['bet_freebet_amount'];
			}
			$betRecord->result_transaction_id = $this->awardBetWin($betArray['user_id'], $actual_win_amount);
			
			$br = print_r($betRecord->result_transaction_id,true);
			Topbetta\LogHelper::l("BackAPI: BetResults -  Result Trans ID:$br");
			
			if ($betArray['bet_freebet_flag'] == 1) {
				Topbetta\LogHelper::l('BackAPI: BetResults - Paid win: ' . $transaction['returnAmount'] . ' cents - ' . $betArray['bet_freebet_amount'] . ' cents free credit = ' . $actual_win_amount . ' cents');
			} else {
				Topbetta\LogHelper::l('BackAPI: BetResults - Paid win: ' . $transaction['returnAmount'] . ' cents');
				Topbetta\LogHelper::l("BackAPI: BetResults - Transaction ID for Bet Win record: $betRecord->result_transaction_id");
			}
			
			
			// Is this used in racing or for tournaments? 
			//$betRecord->resulted_flag = 1;
		}
	
		if ($transaction['betOutcome'] == self::TRANSACTION_STATUS_SUBMITTED){
			$result_status = TopBetta\BetResultStatus::STATUS_UNRESULTED;
			Topbetta\LogHelper::l('BackAPI: BetResults - Submitted: ' . $transaction['returnAmount'] . ' cents');
			$betRecord->resulted_flag = 0;
		}
		
		Topbetta\LogHelper::l('BackAPI: BetResults - Resulted Bet ID: ' . $betArray['id']);
		$betRecord->bet_result_status_id = TopBetta\BetResultStatus::getBetResultStatusByName($result_status);
		
		$br = print_r($betRecord,true);
		Topbetta\LogHelper::l("BackAPI: BetResults - Result Status ID: $br");
				
		// change
		$betRecord->save();
	}
	
		/**
		 * Increment a user's account balance
		 *
		 * @param object 	$user
		 * @param integer 	$amount
		 * @param string 	$keyword
		 */
		private function awardCash($user_id, $amount, $keyword) {
			//$this->account_balance->setUserId($user_id);
			return TopBetta\AccountBalance::_increment($user_id, $amount, $keyword);
		}
	
		private function awardBetWin($user_id, $amount)
		{
			return $this->awardCash($user_id, $amount, TopBetta\AccountBalance::TYPE_BETWIN);
		}
	
		private function awardBetRefund($user_id, $amount)
		{
			return $this->awardCash($user_id, $amount, TopBetta\AccountBalance::TYPE_BETREFUND);
		}
	
		private function awardFreeBetRefund($user_id, $amount)
		{
			//$this->tournament_balance->setUserId($user_id);
			return TopBetta\FreeCreditBalance::_increment($user_id, $amount, TopBetta\FreeCreditBalance::TYPE_FREEBETREFUND);
		}
		
		private function _canTransactionBeProcessed($transaction,$status_process_list)
		{
			return in_array($transaction['betOutcome'], $status_process_list);
		}
	

}