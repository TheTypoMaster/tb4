<?php

namespace TopBetta\Console\Commands\NextToJump;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use TopBetta\Services\Caching\SportsDataCacheManager;

class ManageSportsNextToJump extends Command
{
    const TOTAL_NEXT_TO_JUMP_EVENTS = 12;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'topbetta:sport-next-to-jump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates next to jump sport events every {time} seconds';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SportsDataCacheManager $cacheManager)
    {
        $time = time();
        for(;;) {
            $lastUpdated = $time;
            $time = time();

            $cacheManager->updateCache(date('Y-m-d H:i:s', $lastUpdated), self::TOTAL_NEXT_TO_JUMP_EVENTS);

            sleep($this->argument('time') - (time()-$time));
        }
    }

    protected function getArguments()
    {
        return array(
            array('time', InputArgument::REQUIRED, 'No of seconds between each update'),
        );
    }
}
