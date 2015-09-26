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
use TopBetta\Services\Markets\MarketOrderingService;
use TopBetta\Services\Resources\Cache\Sports\CachedEventResourceService;
use TopBetta\Services\Resources\Sports\CompetitionResourceService;
use TopBetta\Services\Resources\Sports\EventResourceService;
use TopBetta\Services\Resources\Sports\MarketResourceService;

class EventService {

    /**
     * @var CachedEventResourceService
     */
    private $eventResourceService;
    /**
     * @var CompetitionResourceService
     */
    private $competitionResourceService;
    /**
     * @var MarketOrderingService
     */
    private $marketOrderingService;
    /**
     * @var MarketResourceService
     */
    private $marketResourceService;

    public function __construct(CachedEventResourceService $eventResourceService, CompetitionResourceService $competitionResourceService, MarketOrderingService $marketOrderingService, MarketResourceService $marketResourceService)
    {
        $this->eventResourceService = $eventResourceService;
        $this->competitionResourceService = $competitionResourceService;
        $this->marketOrderingService = $marketOrderingService;
        $this->marketResourceService = $marketResourceService;
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
                "selected_competition" => $competitions->first() ? $competitions->first()->id : 0
            );

        }

        throw new \Exception("Parameter Missing");
    }


    public function getEventsForCompetitionWithFilteredMarkets($competition, $types = null)
    {
        if( ! $types ) {
            $types = $this->marketOrderingService->getMarketTypeIds($competition->base_competition_id);
        }

        //get events
        $events = $this->eventResourceService->getEventsForCompetitionWithFilteredMarkets($competition->id, $types);

        return $events;
    }

    public function getEventWithFilteredMarkets($event, $competition,  $types = null)
    {
        if( ! $types ) {
            $types = $this->marketOrderingService->getMarketTypeIds($competition->base_competition_id);
        }

        //get events
        $events = $this->eventResourceService->getEventWithFilteredMarkets($event, $types);

        return $events;
    }
}