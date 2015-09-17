<?php

namespace TopBetta\Console\Commands\Cache;

use Carbon\Carbon;
use Illuminate\Console\Command;
use TopBetta\Repositories\Cache\Tournaments\TournamentGroupRepository;
use TopBetta\Repositories\DbTournamentRepository;

class ManageTournamentGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'topbetta:manage-tournament-groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes old tournaments from visible tournament groups.';
    /**
     * @var TournamentGroupRepository
     */
    private $groupRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TournamentGroupRepository $groupRepository)
    {
        parent::__construct();
        $this->groupRepository = $groupRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $groups = $this->groupRepository->getTournamentGroups();

        foreach ($groups as $group) {
            foreach ($group->tournaments as $tournament) {
                if ($tournament->end_date <= Carbon::now()->startOfDay()) {
                    $this->groupRepository->removeTournamentFromGroups($tournament->getModel());
                }
            }
        }

    }
}
