<?php

require_once('../common/shell-bootstrap.php');
jimport('mobileactive.wagering.api');
jimport('mobileactive.application.utilities.format');

/**
 * IADataExport Class
 * CronJob that should run daily to export registration data and sales data to IA
 * @author geoff
 *
 */
class IADataExport extends TopBettaCLI
{
	private $_registration_file_path;
	private $_registration_ssh_path;
	private $_sales_file_path;
	private $_sales_ssh_path;
	private $_ssh_host;
	private $_ssh_user;
	private $_ssh_private_key_path;
	private $_export_date;
	
	/**
	 * Constructor
	 * @return void
	 */
	public function initialise()
	{
		jimport('mobileactive.config.reader');

		$reader = ConfigReader::getInstance();
		$config	= $reader->getAffiliate('incomeaccess');
		
		$overlap_time = date('G') > 4 ? 'today' : '-1 day';
		$timestamp = is_null($this->arg('export-date')) ? strtotime($overlap_time) : strtotime($this->arg('export-date'));
		
		$this->_export_date	= date('Y-m-d', $timestamp);
		
		$date = date('Ymd', $timestamp);
		$hour = date('G');
		$this->_registration_file_path	= $config->registration['source_file_path'] . 'TopBetta_REG_' . $date . '.csv';
		$this->_registration_ssh_path	= $config->registration['destination_file_path'];
		
		$this->_sales_file_path			= $config->sales['source_file_path'] . 'TopBetta_SALES_' . $date . '.csv';
		$this->_sales_ssh_path			= $config->sales['destination_file_path']; 
		
		$this->_ssh_user				= $config->ssh['user'];
		$this->_ssh_host				= $config->ssh['host'];
		$this->_ssh_private_key_path	= $config->ssh['private_key_path'];
		
		$this->addComponentModels('topbetta_user');
		$this->user	=& JModel::getInstance('TopbettaUser', 'TopbettaUserModel');
		
		$this->addComponentModels('payment');
		$this->account_transaction	=& JModel::getInstance('AccountTransaction', 'PaymentModel');
		
		$this->addComponentModels('tournamentdollars');
		$this->tournament_transaction	=& JModel::getInstance('TournamentTransaction', 'TournamentDollarsModel');

	}
	
	/**
	 * (non-PHPdoc)
	 * @see TopBettaCLI::execute()
	 */
	final public function execute()
	{
		$this->l('Starting IA Data Exporter');
		$this->l('Starting to Export Registration CSV for Date ' . $this->_export_date);
		$user_list = $this->user->getBtagUserList($this->_export_date, $this->_export_date);

		$this->l('Generating Registration CSV File');
		$registration_csv_list = $this->_generateRegistrationCsvList($user_list);
		
		$this->l('Storing Registration CSV File to ' . $this->_registration_file_path);
		$this->_writeToCsvFile($registration_csv_list, $this->_registration_file_path);
		
		$this->l('Copying Registration CSV File to ' . $this->_ssh_host . ':' . $this->_registration_ssh_path);
		$this->_copyFileToHostBySSH($this->_registration_file_path, $this->_registration_ssh_path);
				
		$this->l('Starting to Export Sales CSV for Date ' . $this->_export_date);
		$sales_list = array();
		$params = array(
			'from_date' 		=> $this->_export_date,
			'end_date'			=> $this->_export_date,
			'has_btag'			=> true
		);
		
		$transaction_types	= array('deposit', 'chargeback', 'promo', 'betentry', 'betwin', 'betrefund', 'entry', 'betshands');
		foreach ($transaction_types as $transaction_type) {
			$params['transaction_type']		= $transaction_type;
			// MC: we want the tournament entry fee to come from account transactions only
			if($transaction_type == '_entry'){
				$total_amount_list	= $this->tournament_transaction->getTotalAmountListGroupByRecipientID($params);
			}
			else{
				$total_amount_list	= $this->account_transaction->getTotalAmountListGroupByRecipientID($params);
			}
			$this->_merge_sales_list($sales_list, $total_amount_list, $transaction_type);
		}
		
		$this->l('Genertating Sales CSV File');
		$sales_csv_list = $this->_generateSalesCsvList($sales_list);
			
		$this->l('Storing Sales CSV File to ' . $this->_sales_file_path);

		$this->_writeToCsvFile($sales_csv_list, $this->_sales_file_path);
		
		$this->l('Copying Sales CSV File to ' . $this->_ssh_host . ':' . $this->_sales_ssh_path);
		$this->_copyFileToHostBySSH($this->_sales_file_path, $this->_sales_ssh_path);
	}
	
	/**
	 * Merge payment list to sales list
	 * 
	 * @param array $sales_list
	 * @param array $total_payment_list
	 * @param string type
	 * @return void
	 */
	private function _merge_sales_list(&$sales_list, $total_payment_list, $type)
	{
		foreach ($total_payment_list as $total_payment) {
			if (!isset($sales_list[$total_payment->recipient_id])) {
				$sales_list[$total_payment->recipient_id] = array();
			}
			
			$sales_list[$total_payment->recipient_id][$type] = ($type == 'betshands' ? $total_payment->count : $total_payment->total_amount);
		}
	}
	
	/**
	 * Write data to a csv file
	 * 
	 * @param array $data_list
	 * @param string filename
	 * @return void
	 */
	private function _writeToCsvFile($data_list, $filename)
	{
		$handle = fopen($filename, 'w');
		
		foreach ($data_list as $data) {
			fwrite($handle,'"'.implode($data,'","')."\"\r\n");
		}
		
		fclose($handle);
	}
	
	private function _generateSalesCsvList($sales_list){
		$sales_csv_list = array();
		foreach ($sales_list as $recipient_id => $sales) {
			$sales_csv_list[$recipient_id]['transaction_date']	= $this->_export_date;
			$sales_csv_list[$recipient_id]['player_id']			= $recipient_id;
			
			$recipient = $this->user->getUser($recipient_id);
			$sales_csv_list[$recipient_id]['btag'] = $recipient->btag;
			
			$sales_csv_list[$recipient_id]['deposit']		= (isset($sales['deposit']) && $sales['deposit'] > 0) ? Format::currency($sales['deposit']) : 0;
			$sales_csv_list[$recipient_id]['chargeback']	= (isset($sales['chargeback']) && $sales['chargeback'] < 0) ? Format::currency(abs($sales['chargeback'])) : 0;
			
			$sales_csv_list[$recipient_id]['betshands']	= isset($sales['betshands']) ? $sales['betshands'] : 0;
			
			$total_bet_entry	= isset($sales['betentry']) ? abs($sales['betentry']) : 0;
			$total_bet_refund	= isset($sales['betrefund']) ? $sales['betrefund'] : 0;
			
			$bet_win	= isset($sales['betwin']) ? $sales['betwin'] : 0;

			$turn_over		= $total_bet_entry - $total_bet_refund;
			$revenue	= $turn_over - $bet_win;

			$sales_csv_list[$recipient_id]['revenue']	= ($revenue != 0) ? Format::currency($revenue) : 0;
			$sales_csv_list[$recipient_id]['stake']		= ($turn_over > 0) ? Format::currency($turn_over) : 0;
			
			$sales_csv_list[$recipient_id]['bonus']		= (isset($sales['promo'])) ? Format::currency($sales['promo']) : 0;
			
			$total_entry		= isset($sales['entry']) ? abs($sales['entry']) : 0;
			$tournament_fees	= $total_entry;
			
			$sales_csv_list[$recipient_id]['tournament_fees'] = ($tournament_fees != 0) ? Format::currency($tournament_fees) : 0;
			
			$this->d(implode(',', $sales_csv_list[$recipient_id]));
		}
		
		return $sales_csv_list;
	}
	
	private function _generateRegistrationCsvList($user_list){
		$registration_csv_list = array();
		foreach ($user_list as $user) {
			$registration_csv_list[$user->id] = array(
				$this->_export_date,
				$user->btag,
				$user->id,
				$user->username,
				$user->country
			);
			$this->d(implode(',', $registration_csv_list[$user->id]));
		}
		
		return $registration_csv_list;
	}
	
	private function _copyFileToHostBySSH($local_file, $remote_file){
		//exec('scp -i '. $this->_ssh_private_key_path . ' ' . $local_file . ' ' . $this->_ssh_user . '@' . $this->_ssh_host . ':' . $remote_file);
		exec('scp -i '. $this->_ssh_private_key_path . ' -P 2222 ' . $local_file . ' ' . $this->_ssh_user . '@' . $this->_ssh_host . ':' . $remote_file);
	}
}

$cronjob = new IADataExport();
$cronjob->debug(false);
$cronjob->execute();
