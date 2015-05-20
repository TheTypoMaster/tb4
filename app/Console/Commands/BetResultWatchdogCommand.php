<?php namespace TopBetta\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use TopBetta\Facades\BetResultRepo;

class BetResultWatchdogCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'topbetta:clear-paying-bets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Results any bets that should be paid out.';

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
     * @return void
     */
    public function fire()
    {
        $this->info("Resulting any paying bets"); 

        BetResultRepo::resultAllBetsForPayingEvents();
        
        $this->info("Complete!");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
//            array('example', InputArgument::REQUIRED, 'An example argument.'),
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
//            array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}
