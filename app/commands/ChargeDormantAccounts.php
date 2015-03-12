<?php


use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use TopBetta\Services\Accounting\AccountTransactionService;

class ChargeDormantAccounts extends Command {

	/**
	 * Number of days without a transaction that classifies an account as formant
	 */
	const DORMANT_DAYS = 365;

	/**
	 * Monthly fee amount for dormant accounts
	 */
	const DORMANT_CHARGE = 1000;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'topbetta:charge-dormant-accounts';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Charges dormant user accounts a monthly fee';

	/**
	 * @var AccountTransactionService
	 */
	private $accountTransactionService;

	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct(AccountTransactionService $accountTransactionService)
	{
		parent::__construct();
		$this->accountTransactionService = $accountTransactionService;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->info("Charging accounts");
		$date = \Carbon\Carbon::now()->subMonth()->endOfMonth();
		try {
			\Log::info("Charging dormant accounts for date " . $date->toDateTimeString());
			$this->accountTransactionService->chargeDormantAccounts(self::DORMANT_DAYS, self::DORMANT_CHARGE, $date->toDateTimeString());
		} catch (\Exception $e) {
			\Log::error("Charging accounts failed with message " . $e->getMessage());
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
