<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/07/2015
 * Time: 11:20 AM
 */

namespace TopBetta\Services\Sports;


use Illuminate\Database\Eloquent\Collection;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Services\Resources\Sports\CompetitionResourceService;

class CompetitionService {

    /**
     * @var CompetitionResourceService
     */
    private $competitionResourceService;
    /**
     * @var EventService
     */
    private $eventService;

    public function __construct(CompetitionResourceService $competitionResourceService, EventService $eventService)
    {
        $this->competitionResourceService = $competitionResourceService;
        $this->eventService = $eventService;
    }

    public function getCompetitionsWithEvents(array $criteria, $types = null)
    {
        if( $competition = array_get($criteria, 'competition_id') ) {

            $competitions = new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\Sports\CompetitionResource');
            $competitions->push($this->competitionResourceService->getCompetitionResource($competition));

            $competitions->first()->setRelation('events', $this->eventService->getEventsForCompetitionWithFilteredMarkets($competitions->first(), $types));

            return array(
                "data" => $competitions,
                "selected_competition" => $competition
            );
        }

        if( $baseCompetition = array_get($criteria, 'base_competition_id') ) {
            $competitions = $this->getCompetitionsAndEventsForBaseCompetition($baseCompetition, $types);

            return array(
                "data" => $competitions,
                "selected_competition" => $competitions->first() ? $competitions->first()->id : 0
            );

        }

        throw new \Exception("Parameter Missing");
    }

    public function getCompetitionsAndEventsForBaseCompetition($baseCompetition, $types = null)
    {
        $competitions = $this->competitionResourceService->getVisibleCompetitionsByBaseCompetition($baseCompetition);

        if( $competitions->count() ) {
            $competitions->first()->setRelation(
                'events',
                $this->eventService->getEventsForCompetitionWithFilteredMarkets($competitions->first(), $types)
            );
        }

        return $competitions;
    }
}
