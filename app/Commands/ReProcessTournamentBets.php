<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use TopBetta\Tournaments\TournamentReprocess;

class ReProcessTournamentBets extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'topbetta:tournament-re-process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Re-process bets in a tournament - only use if your OLIVER!';

    protected $tournaments;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(TournamentReprocess $tournaments)
	{
        $this->tournaments = $tournaments;

		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
        // get the required arguments
        $tournamentId = $this->argument('tournamentId');

        $this->tournaments->reprocessTournamentbets($tournamentId);

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('tournamentId', InputArgument::REQUIRED, 'The database ID of the tournament you want re-processed.'),
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
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}