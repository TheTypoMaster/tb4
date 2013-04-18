<?php
require_once '../common/shell-bootstrap.php';

class BetProcessor extends TopBettaCLI
{
	/**
	 * Max execution time
	 * 
	 * @var integer
	 */
	protected $max_execution_time = 59;
	
	const TRANSACTION_STATUS_WON = 'W',
		TRANSACTION_STATUS_LOST = 'L',
		TRANSACTION_STATUS_CANCELLED = 'CN',
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
	
	final public function initialise(){
		jimport('mobileactive.wagering.api');
	
		$this->addComponentModels('betting');
		$this->addComponentModels('payment');
		$this->addComponentModels('tournamentdollars');
		
		$this->bet_type 			=& JModel::getInstance('BetType', 'BettingModel');
		$this->bet_status 			=& JModel::getInstance('BetResultStatus', 'BettingModel');
		$this->bet					=& JModel::getInstance('Bet', 'BettingModel');
		$this->account_balance		=& JModel::getInstance('AccountTransaction', 'PaymentModel');
		$this->tournament_balance	=& JModel::getInstance('TournamentTransaction', 'TournamentdollarsModel');		
		$this->bet_product_model	=& JModel::getInstance('BetProduct', 'BettingModel');
		$this->bet_product_id = (int)0;
	}
	
	final public function execute()
	{
		
		$display_message = true;
		
		if(!$this->arg('debug')){
			while($this->_checkForRunningInstance(basename(__FILE__))){
				if($display_message){
					$this->l('Bet Processor Instance already running. WAIT');
					$display_message = false;
				}
				time_nanosleep(0, 500000000);
			}
		}
		
			while (1) {
			    $this->_executionExpiryCheck();
				$this->processBets();
				sleep(2);
			}
	}
	
	final private function _executionExpiryCheck()
	{
		if ($this->hasExecutionTimeExpired()) {
			$this->cleanExit();
		}
	}
	
	/**
	 * Make sure script writes to lock file when script exits
	 * @param int $sig
	 */
	final public function cleanExit($sig=null)
	{
		exit;
	}	
	
	private function processBets()
	{
		$bet_products = $this->bet_product_model->getBetProductKeywordList();

		// Iterate through all bet products and process lists.
		foreach($bet_products as $bet_product){
			
			$bet_product = $this->bet_product_model->getBetProductByKeyword($bet_product->keyword);
			$this->bet_product_id = (int)$bet_product->id;

			// Process the Unresulted Bet List
			$bet_list = $this->bet->getUnresultedBetList(true,$this->bet_product_id);
			
			$api = WageringApi::getInstance(WageringApi::API_TOB);
			
			if(!empty($bet_list)){
				$this->l("[API] Processing '".count($bet_list)."' unresulted bets");
				foreach($bet_list as $bet){
					$transaction = null;
					//$transaction = $api->getBetInfo($bet->external_bet_id);
					$this->l("[API] Processing bet ID: '".$bet->invoice_id."'");
					$transaction = $api->getBetInfo($bet->invoice_id);
					$this->l("[API] Complete");
					if($this->_canTransactionBeProcessed($transaction,$this->status_process_unresulted_list)){
						$this->_processTransaction($transaction, $bet);
					}
				}
				
			}
			
			// Process The Pending Bet List
			$bet_list_pending = $this->bet->getPendingBetList(true,$this->bet_product_id);
			if(!empty($bet_list_pending)){
				$this->l("[API] Processing '".count($bet_list_pending)."' pending bets");
				foreach($bet_list_pending as $bet){
					$this->l("[API] Processing bet ID:'".$bet->external_bet_id."'");
					$transaction = $api->getBetInfo($bet->external_bet_id);
					$this->l("[API] Complete");
					if($this->_canTransactionBeProcessed($transaction,$this->status_process_pending_list)){
						$this->_processTransaction($transaction, $bet);
					}
				}
				
			}


		}

		
	}
	
	private function _canTransactionBeProcessed($transaction,$status_process_list)
	{
		return in_array($transaction->status, $status_process_list);
	}
	
	private function _processTransaction($transaction, $bet)
	{	
		
		$this->l('Processing Bet ID: ' . $bet->id);
		$result_status = BettingModelBetResultStatus::STATUS_PAID;
		$bet->resulted_flag = 1;	
        $this->l('Bet free flag: ' . $bet->bet_freebet_flag);
        $this->l('Bet free amount: ' . $bet->bet_freebet_amount);  

		if ($transaction->status == self::TRANSACTION_STATUS_CANCELLED || $transaction->status == self::TRANSACTION_STATUS_FAILED){
			//full bet amount was on free credit
			if ($bet->bet_freebet_flag == 1 && $bet->bet_freebet_amount == $transaction->amount) {
			    $bet->refund_freebet_transaction_id = $this->_awardFreeBetRefund($bet->user_id, $bet->bet_freebet_amount);
			    $this->l('Free Bet full refund: ' . $bet->bet_freebet_amount . ' cents');			    
			} else if ($bet->bet_freebet_flag == 1 && $bet->bet_freebet_amount < $transaction->amount) {
			    //free bet amount was less then refund
			    $refund_amount = $transaction->amount - $bet->bet_freebet_amount;
			    //refund free bet amount
			    $bet->refund_freebet_transaction_id = $this->_awardFreeBetRefund($bet->user_id, $bet->bet_freebet_amount);
			    $this->l('Free Bet partial refund: ' . $bet->bet_freebet_amount . ' cents');				    
			    //refund balance to account
			    $bet->refund_transaction_id = $this->_awardBetRefund($bet->user_id, $refund_amount);
    			$this->l('Paid partial refund: ' . $refund_amount . ' cents');			    
			} else {
			    //no free credit was used - refund full amount to account
			    $bet->refund_transaction_id = $this->_awardBetRefund($bet->user_id, $transaction->amount);
    			$this->l('Paid refund: ' . $transaction->amount . ' cents');			    
			}			
			$bet->refunded_flag = 1;
			//N/A $result_status = BettingModelBetResultStatus::STATUS_PARTIAL_REFUND;
			//N/A if($transaction->RefundCents == $transaction->BetCostCents){
				$result_status = BettingModelBetResultStatus::STATUS_FULL_REFUND;
			//}
			if($transaction->betErrorMessage)
			{
			$bet->external_bet_error_message = (string)$transaction->betErrorMessage;
			}
			
		}
		
		if ($transaction->amountWon > 0 && $transaction->status == self::TRANSACTION_STATUS_WON){
			$actual_win_amount = $transaction->amountWon;
			//for free bets places, deduct the stake amount from the winnings first
			if ($bet->bet_freebet_flag == 1) {
			    $actual_win_amount -= $bet->bet_freebet_amount;
			}
			$bet->result_transaction_id = $this->_awardBetWin($bet->user_id, $actual_win_amount);

			if ($bet->bet_freebet_flag == 1) {
			    $this->l('Paid win: ' . $transaction->amountWon . ' cents - ' . $bet->bet_freebet_amount . ' cents free credit = ' . $actual_win_amount . ' cents');
			} else {
    			$this->l('Paid win: ' . $transaction->amountWon . ' cents');			
			}
		}
		
		if ($transaction->status == self::TRANSACTION_STATUS_SUBMITTED){
			$result_status = BettingModelBetResultStatus::STATUS_UNRESULTED;
			$this->l('Submitted: ' . $transaction->amount . ' cents');
			$bet->resulted_flag = 0;
		}
		
		$this->l('Resulted Bet ID: ' . $bet->id);
		$bet->bet_result_status_id = $this->bet_status->getBetResultStatusByName($result_status)->id;
		$this->_save($bet);
	}
	
	/**
	* Increment a user's account balance
	*
	* @param object 	$user
	* @param integer 	$amount
	* @param string 	$keyword
	*/
	private function _awardCash($user_id, $amount, $keyword) {
		$this->account_balance->setUserId($user_id);
		return $this->account_balance->increment($amount, $keyword);
	}
	
	private function _awardBetWin($user_id, $amount)
	{
		return $this->_awardCash($user_id, $amount, PaymentModelAccounttransaction::TYPE_BETWIN);	
	}
	
	private function _awardBetRefund($user_id, $amount)
	{
		return $this->_awardCash($user_id, $amount, PaymentModelAccounttransaction::TYPE_BETREFUND);	
	}
	
	private function _awardFreeBetRefund($user_id, $amount)
	{
		$this->tournament_balance->setUserId($user_id);
		return $this->tournament_balance->increment($amount, TournamentdollarsModelTournamenttransaction::TYPE_FREEBETREFUND);
	}	
}

$cron = new BetProcessor();
$cron->debug(false);
$cron->execute();
