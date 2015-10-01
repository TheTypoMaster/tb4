<?php

namespace TopBetta\Console\Commands\DevTools;

use Carbon\Carbon;
use Illuminate\Console\Command;
use TopBetta\Repositories\Cache\Sports\BaseCompetitionRepository;
use TopBetta\Repositories\Cache\Sports\CompetitionRepository;
use TopBetta\Repositories\Cache\Sports\EventRepository;
use TopBetta\Repositories\Cache\Sports\MarketRepository;
use TopBetta\Repositories\Cache\Sports\SelectionRepository;
use TopBetta\Repositories\Cache\Sports\SportRepository;

class PopulateSportsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'topbetta:populate-sports-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';
    /**
     * @var SportRepository
     */
    private $sportRepository;
    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var MarketRepository
     */
    private $marketRepository;
    /**
     * @var BaseCompetitionRepository
     */
    private $baseCompetitionRepository;
    /**
     * @var SelectionRepository
     */
    private $selectionRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SportRepository $sportRepository, CompetitionRepository $competitionRepository, EventRepository $eventRepository, MarketRepository $marketRepository, BaseCompetitionRepository $baseCompetitionRepository, SelectionRepository $selectionRepository)
    {
        parent::__construct();
        $this->sportRepository = $sportRepository;
        $this->competitionRepository = $competitionRepository;
        $this->eventRepository = $eventRepository;
        $this->marketRepository = $marketRepository;
        $this->baseCompetitionRepository = $baseCompetitionRepository;
        $this->selectionRepository = $selectionRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Cache::tags(array("sports"))->flush();
        $sports = \TopBetta\Models\SportModel::where('id', '>', '3')->get()->load('baseCompetitions');

        foreach ($sports as $sport) {
            $this->sportRepository->makeCacheResource($sport);
            foreach ($sport->getModel()->baseCompetitions as $baseCompetition) {
                $this->baseCompetitionRepository->makeCacheResource($baseCompetition);

                $competitions = $baseCompetition->competitions()->where('close_time', '>=', Carbon::now()->subDays(2)->toDateTimeString())->get();

                foreach ($competitions as $competition) {
                    $this->info($competition->id);
                    $this->competitionRepository->makeCacheResource($competition);

                    $events = $competition->competitionEvents()->where('start_date', '>=', Carbon::now()->subDays(2)->toDateTimeString())->get();

                    foreach ($events as $event) {

                        $this->eventRepository->makeCacheResource($event);

                        $markets = $event->markets->load(array('selections', 'selections.price', 'selections.result', 'selections.team', 'selections.player', 'markettype.markettypegroup'));
                        $this->marketRepository->storeMarketsForEvent($markets, $event);
                     }
                }
            }
        }
    }
}
