<?php namespace TopBetta\Jobs;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use TopBetta\Processes\ScheduledDepositProcess;

class ProcessScheduledPayments extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'topbetta:process-scheduled-payments';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Processes any due scheduled payments.';
    /**
     * @var ScheduledDepositProcess
     */
    private $process;

    /**
	 * Create a new command instance.
	 */
	public function __construct(ScheduledDepositProcess $process)
	{
		parent::__construct();
        $this->process = $process;
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		try {
            $this->process->run();
        } catch (\Exception $e) {
            Log::error("Scheduled Payments Unknown Error: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
		);
	}

}
