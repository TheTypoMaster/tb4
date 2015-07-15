<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 2:30 PM
 */

namespace TopBetta\Services\Sports;


use Illuminate\Database\Eloquent\Collection;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Services\Resources\Sports\CompetitionResourceService;
use TopBetta\Services\Resources\Sports\EventResourceService;

class EventService {

    /**
     * @var EventResourceService
     */
    private $eventResourceService;
    /**
     * @var MarketService
     */
    private $marketService;
    /**
     * @var CompetitionResourceService
     */
    private $competitionResourceService;

    public function __construct(EventResourceService $eventResourceService, MarketService $marketService, CompetitionResourceService $competitionResourceService)
    {
        $this->eventResourceService = $eventResourceService;
        $this->marketService = $marketService;
        $this->competitionResourceService = $competitionResourceService;
    }

    public function getEventsForCompetitionOrBaseCompetition(array $criteria, $types = null)
    {
        if( $competition = array_get($criteria, 'competition_id') ) {
            return array(
                "data" => $this->getEventsForCompetitionWithFilteredMarkets($competition, $types),
                "selected_competition" => $competition
            );
        }

        if( $baseCompetition = array_get($criteria, 'base_competition_id') ) {
            $competitions = $this->getCompetitionsAndEventsForBaseCompetition($baseCompetition, $types);

            return array(
                "data" => $competitions,
                "selected_competition" => $competitions->first()->id
            );

        }

        throw new \Exception("Parameter Missing");
    }

    public function getCompetitionsAndEventsForBaseCompetition($baseCompetition, $types = null)
    {
        $competitions = $this->competitionResourceService->getVisibleCompetitionsByBaseCompetition($baseCompetition);

        $competitions->first()->setRelation(
            'events',
            $this->getEventsForCompetitionWithFilteredMarkets($competitions->first()->id, $types)
        );

        return $competitions;
    }

    public function getEventsForCompetitionWithFilteredMarkets($competitionId, $types = null)
    {
        //get events
        $events = $this->eventResourceService->getEventsForCompetition($competitionId);

        //get markets
        $markets = $this->marketService->getFilteredMarketsForCompetition($competitionId);

        //create empty collections for each event
        $events->each(function($event) {
            $event->setRelation('markets', new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\MarketResource'));
        });

        //get dictionary
        $dictionary = $events->getDictionary();

        foreach($markets as $market) {
            //push each relation onto correct model relation
            $dictionary[$market->event_id]->markets->push($market);
        }

        return $events;
    }
}