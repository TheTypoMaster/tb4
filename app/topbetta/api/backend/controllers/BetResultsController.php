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
			self::TRANSACTION_STATUS_FAILED
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
			$this->l("BackAPI: BetResults - No Data In POST",2);
			return \Response::json(array(
					'error' => true,
					'message' => 'Error: No JSON data received'),
					400
			);
		}

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
						$this->l("BackAPI: BetResults - Processing OutcomeList, Object:$objectCount");
						
						//TODO: CHECK DataKey to validate bet result
						//$dataKey = $resultsArray['DataKey'];
						$dataKey = true;
						
						if($dataKey){
							
							foreach ($resultsArray as $dataArray){
								// store data from array
								if(isset($dataArray['TransactionId'])){

									// bet result details from IGAS
									$transaction['transactionID'] = $dataArray['TransactionId'];
									$transaction['betOutcome'] = $dataArray['BetOutcome'];
									$transaction['returnAmount'] = $dataArray['ReturnAmount'];
										
									// check if transaction ID exists in DB if not throw error
									$transactionExists = TopBetta\Bet::getBetExists($transaction['transactionID']);
									
									if($transactionExists){
										
										// get the bet record based on the transactionID
										$betObject = TopBetta\Bet::getBetDetails($transaction['transactionID'])->toArray();
										
										// check it can be processed based on status?
										$b = print_r($betObject,true);

										Topbetta\LogHelper::l("racing_service: Entering placeBetList. bet_data: $b");
																				
										// process bet result
										$this->processTransaction($transaction, $betObject[0]);
									
									} else{
										return \Response::json(array(
												'error' => true,
												'message' => 'Error: Transaction Id not found in DB: '. $transaction['transactionID']),
												400
										);
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
		
		return \Response::json(array(
				'error' => false,
				'message' => 'OK: Bet Results Processed Successfully'),
				200
		);
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
			
		$this->l('Processing Bet ID: ' . $betArray['id']);
		$result_status = TopBetta\BetResultStatus::STATUS_PAID;
		$betRecord->resulted_flag = 1;
		$this->l('Bet free flag: ' . $betArray['bet_freebet_flag']);
		$this->l('Bet free amount: ' . $betArray['bet_freebet_amount']);
		// if bet should be refunded
		if ($transaction['betOutcome'] == self::TRANSACTION_STATUS_CANCELLED || $transaction['betOutcome'] == self::TRANSACTION_STATUS_INVALID || $transaction['betOutcome'] == self::TRANSACTION_STATUS_REFUNDED){
			//full bet amount was on free credit
			if ($betArray['bet_freebet_flag'] == 1 && $betArray['bet_freebet_amount'] == $transaction['ReturnAmount']) {
				$betRecord->refund_freebet_transaction_id = $this->awardFreeBetRefund($betArray['user_id'], $betArray['bet_freebet_amount']);
				$this->l('Free Bet full refund: ' . $betArray['bet_freebet_amount'] . ' cents');
			} else if ($betArray['bet_freebet_flag'] == 1 && $betArray['bet_freebet_amount'] < $transaction['ReturnAmount']) {
				//free bet amount was less then refund
				$refund_amount = $transaction['ReturnAmount'] - $betArray['bet_freebet_amount'];
				//refund free bet amount
				$betRecord->refund_freebet_transaction_id = $this->awardFreeBetRefund($betArray['user_id'], $betArray['bet_freebet_amount']);
				$this->l('Free Bet partial refund: ' . $betArray['bet_freebet_amount'] . ' cents');
				//refund balance to account
				$betRecord->refund_transaction_id = $this->awardBetRefund($betArray['user_id'], $refund_amount);
				$this->l('Paid partial refund: ' . $refund_amount . ' cents');
			} else {
				//no free credit was used - refund full amount to account
				$betRecord->refund_transaction_id = $this->awardBetRefund($betArray['user_id'], $transaction['ReturnAmount']);
				$this->l('Paid refund: ' . $transaction['ReturnAmount'] . ' cents');
			}
			$betRecord->refunded_flag = 1;
			$result_status = TopBetta\BetResultStatus::STATUS_FULL_REFUND;
				
			}
	
			if ($transaction['returnAmount'] > 0 && $transaction['betOutcome'] == self::TRANSACTION_STATUS_WON){
				$actual_win_amount = $transaction['returnAmount'];
				//for free bets places, deduct the stake amount from the winnings first
				if ($betArray['bet_freebet_flag'] == 1) {
					$actual_win_amount -= $bet['bet_freebet_amount'];
				}
				$betRecord->result_transaction_id = $this->awardBetWin($betArray['user_id'], $actual_win_amount);
	
				if ($betArray['bet_freebet_flag'] == 1) {
					$this->l('Paid win: ' . $transaction['ReturnAmount'] . ' cents - ' . $betArray['bet_freebet_amount'] . ' cents free credit = ' . $actual_win_amount . ' cents');
				} else {
					$this->l('Paid win: ' . $transaction['ReturnAmount'] . ' cents');
				}
			}
	
			if ($transaction['betOutcome'] == self::TRANSACTION_STATUS_SUBMITTED){
				$result_status = TopBetta\BetResultStatus::STATUS_UNRESULTED;
				$this->l('Submitted: ' . $transaction['ReturnAmount'] . ' cents');
				$betRecord->resulted_flag = 0;
			}
	
			$this->l('Resulted Bet ID: ' . $betArray['id']);
			$betRecord->bet_result_status_id = TopBetta\BetResultStatus::getBetResultStatusByName($result_status);
			
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
			return TopBetta\AccountBalance::increment($amount, $keyword);
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
			return TopBetta\FreeCreditBalance::increment($amount, TopBetta\FreeCreditBalance::TYPE_FREEBETREFUND);
		}
	

}