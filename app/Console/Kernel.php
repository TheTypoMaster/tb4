<?php namespace TopBetta\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'TopBetta\Console\Commands\Inspire',
		'TopBetta\Jobs\BetResultWatchdogCommand',
		'TopBetta\Jobs\ChargeDormantAccounts',
		'TopBetta\Jobs\DashboardPusher',
        'TopBetta\Jobs\ProcessScheduledPayments'

	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('inspire')
				 ->hourly();
	}

}