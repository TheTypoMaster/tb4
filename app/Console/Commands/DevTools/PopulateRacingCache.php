<?php

namespace TopBetta\Console\Commands\DevTools;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use TopBetta\Repositories\Cache\MeetingRepository;
use TopBetta\Repositories\Cache\RaceRepository;
use TopBetta\Repositories\Cache\RacingSelectionRepository;
use TopBetta\Resources\RaceResource;
use TopBetta\Services\Racing\RaceResultService;

class PopulateRacingCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'topbetta:populate-racing-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';
    /**
     * @var MeetingRepository
     */
    private $meetingRepository;
    /**
     * @var RaceRepository
     */
    private $raceRepository;
    /**
     * @var RacingSelectionRepository
     */
    private $selectionRepository;
    /**
     * @var RaceResultService
     */
    private $resultService;

    /**
     * Create a new command instance.
     *
     * @param MeetingRepository $meetingRepository
     * @param RaceRepository $raceRepository
     * @param RacingSelectionRepository $selectionRepository
     */
    public function __construct(MeetingRepository $meetingRepository, RaceRepository $raceRepository, RacingSelectionRepository $selectionRepository, RaceResultService $resultService)
    {
        parent::__construct();
        $this->meetingRepository = $meetingRepository;
        $this->raceRepository = $raceRepository;
        $this->selectionRepository = $selectionRepository;
        $this->resultService = $resultService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $meetings = \TopBetta\Models\CompetitionModel::where('start_date', '>=', Carbon::now()->subDays(2))->where('sport_id', '<=', 3)->where('type_code', $this->argument('type_code'))->get();

        foreach ($meetings as $meeting) {
            $this->meetingRepository->makeCacheResource($meeting);

            foreach ($meeting->competitionEvents as $event) {
                $race = new RaceResource($event);
                $this->resultService->loadresultForRace($race);

                $this->raceRepository->save($race);

                foreach ($event->markets->first()->selections as $selection) {
                    $this->selectionRepository->makeCacheResource($selection);
                }
            }
        }

    }

    protected function getArguments()
    {
        return [
            ['type_code', InputArgument::REQUIRED, 'Type code R|G|H'],
        ];
    }
}
