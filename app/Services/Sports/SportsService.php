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
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Services\Resources\Cache\Sports\CachedSportResourceService;
use TopBetta\Services\Resources\Sports\SportResourceService;

class SportsService {

    /**
     * @var CachedSportResourceService
     */
    private $sportResourceService;
    /**
     * @var CompetitionService
     */
    private $competitionService;

    public function __construct(CachedSportResourceService $sportResourceService, CompetitionService $competitionService)
    {
        $this->sportResourceService = $sportResourceService;
        $this->competitionService = $competitionService;
    }

    public function getVisibleSportsWithCompetitions($date = null)
    {
        if( $date ) {
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
                    $baseCompetition->setRelation('compeititions', $collection);
                    $sport->addBaseCompetition($baseCompetition);
                    break;
                }
            }
        }

        return array("data" => $sports, "selected_competition" => array_get($competitionData, "selected_competition"));
    }
}