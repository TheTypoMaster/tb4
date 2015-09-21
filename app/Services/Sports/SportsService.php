<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/07/2015
 * Time: 4:40 PM
 */

namespace TopBetta\Services\Sports;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Services\Resources\Cache\Sports\CachedBaseCompetitionResourceService;
use TopBetta\Services\Resources\Cache\Sports\CachedSportResourceService;

class SportsService
{

    /**
     * @var CachedSportResourceService
     */
    private $sportResourceService;
    /**
     * @var CompetitionService
     */
    private $competitionService;
    /**
     * @var CachedBaseCompetitionResourceService
     */
    private $baseCompetitionResourceService;
    /**
     * @var EventService
     */
    private $eventService;


    public function __construct(CachedSportResourceService $sportResourceService, CompetitionService $competitionService, CachedBaseCompetitionResourceService $baseCompetitionResourceService, EventService $eventService,
                                SportRepositoryInterface $sportRepository)
    {
        $this->sportResourceService = $sportResourceService;
        $this->competitionService = $competitionService;
        $this->baseCompetitionResourceService = $baseCompetitionResourceService;

        $this->eventService = $eventService;
        $this->sportRepository = $sportRepository;
    }

    public function getVisibleSportsWithCompetitions($date = null)
    {
        if ($date) {
            $date = Carbon::createFromFormat("Y-m-d", $date);
        }

        $sports = $this->sportResourceService->getVisibleSportsWithCompetitions($date);

        return $sports;
    }

    public function getVisibleSportsWithCompetitionAndEvent($competition)
    {
        $sports = $this->sportResourceService->getVisibleSports($competition);

        $competitionData = $this->competitionService->getCompetitionsWithEvents(array("competition_id" => $competition));
        $competition = array_get($competitionData, 'data');

        if ($competition) {

            $competition = $competition->first();

            foreach ($sports as $sport) {
                if ($baseCompetition = $sport->baseCompetitions->keyBy('id')->get($competition->base_competition_id)) {
                    $collection = new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\Sports\CompetitionResource');
                    $collection->push($competition);
                    $baseCompetition->setRelation('competitions', $collection);
                    $sport->addBaseCompetition($baseCompetition);
                    break;
                }
            }
        }

        return array("data" => $sports, "selected_competition" => array_get($competitionData, "selected_competition"));
    }

    public function getSportsWithCompetitionsForSport($sport)
    {
        $sports = $this->sportResourceService->getVisibleSports($sport);

        if ($sportResource = array_get($sports->getDictionary(), $sport)) {
            $baseCompetitions = $this->baseCompetitionResourceService->getBaseCompetitionsForSportWithCompetitions($sport);
            $sportResource->setRelation('baseCompetitions', $baseCompetitions);
        }

        return $sports;
    }

    public function getSportsWithCompetitionsAndEventForCompetition($competition)
    {
        $baseComp = $this->baseCompetitionResourceService->getBaseCompetitionForCompetitionId($competition);

        $sports = $this->sportResourceService->getVisibleSports($baseComp->sport_id);

        if ($sportResource = array_get($sports->getDictionary(), $baseComp->sport_id)) {
            $baseCompetitions = $this->baseCompetitionResourceService->getBaseCompetitionsForSportWithCompetitions($baseComp->sport_id, $competition);

            if ($baseCompetition = array_get($baseCompetitions->getDictionary(), $baseComp->id)) {
                $competitions = $baseCompetition->competitions->keyBy('id');
                $comp = $competitions->get($competition);

                $comp->setRelation('events', $this->eventService->getEventsForCompetitionWithFilteredMarkets($comp));

                $baseCompetition->setRelation('competitions', $competitions->values());
            }

            $sportResource->setRelation('baseCompetitions', $baseCompetitions);
        }

        return $sports;
    }

    public function attachBaseCompetitionsForSport($sportsCollection, $sport, $competition = null)
    {

    }

    /**
     * get all sports
     * @return mixed
     */
    public function getAllSports()
    {
        $sports = $this->sportRepository->getAllSportsWithoutPaginate();
        $sport_list = array();
        foreach($sports as $sport) {
            $sport_list[$sport->id] = $sport->name;
        }

        return $sport_list;
    }


}