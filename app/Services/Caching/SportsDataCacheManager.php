<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/09/2015
 * Time: 4:53 PM
 */

namespace TopBetta\Services\Caching;


use Carbon\Carbon;
use TopBetta\Repositories\Cache\Sports\CompetitionRepository;
use TopBetta\Repositories\Cache\Sports\EventRepository;


class SportsDataCacheManager {

    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;

    public function __construct(EventRepository $eventRepository, CompetitionRepository $competitionRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->competitionRepository = $competitionRepository;
    }

    public function updateCache($lastUpdate, $n2jLimit = 10)
    {
        $events = $this->eventRepository->nextToJump();

        if (!$events || $events->count() < $n2jLimit || $events->first()->start_time <= Carbon::now()) {

            if ($events->first() && $events->first()->start_time <= Carbon::now()) {
                $this->updateEvents($lastUpdate);
            }

            $this->eventRepository->updateNextToJump($n2jLimit);
        }
    }


    public function updateEvents($lastUpdate)
    {
        $expiredEvents = $this->eventRepository->getVisibleEventsBetween($lastUpdate, Carbon::now()->toDateTimeString());

        foreach ($expiredEvents as $event) {

            $competitionEvents = $this->eventRepository->getEventsForCompetition($event->competition->first()->id);

            //filter for future events
            $competitionEvents = $competitionEvents->filter(function ($v) {
                return $v->start_date >= Carbon::now();
            });

            //if no events for competition then remove it frrom visible resource
            if (!$competitionEvents->count()) {
                $this->competitionRepository->removeVisibleResource($event->competition->first());
            }
        }
    }

}