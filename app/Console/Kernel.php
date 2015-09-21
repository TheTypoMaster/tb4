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
        'TopBetta\Jobs\ProcessScheduledPayments',
        'TopBetta\Console\Commands\DevTools\PopulateSportsCache',
        'TopBetta\Console\Commands\DevTools\PopulateRacingCache',
        'TopBetta\Console\Commands\NextToJump\ManageSportsNextToJump',
        'TopBetta\Console\Commands\DevTools\PopulateTournamentCache',
        'TopBetta\Console\Commands\Cache\ManageTournamentGroups',
        'TopBetta\Console\Commands\Pusher\PusherHeartbeat',
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

        $schedule->command('topbetta:manage-tournament-groups')
            ->daily();

        $schedule->command('topbetta:pusher-heartbeat')
            ->everyMinute();
	}

}
