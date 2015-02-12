<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/02/2015
 * Time: 2:36 PM
 */

namespace TopBetta\Services\Risk;


use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;

class RiskEventService {

    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function __construct(EventRepositoryInterface $eventRepository, CompetitionRepositoryInterface $competitionRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->competitionRepository = $competitionRepository;
    }

    public function showCompetition($competitionId)
    {
        return $this->setDisplayFlagForCompetition($competitionId, 1);
    }

    public function hideCompetition($competitionId)
    {
        return $this->setDisplayFlagForCompetition($competitionId, 0);
    }

    public function showEvent($eventId)
    {
        $event = $this->eventRepository->updateWithId($eventId, array("display_flag" => 1));

        $this->competitionRepository->updateWithId($event->competition->first()->id, array("display_flag" => 1));

        return $this;
    }

    public function hideEvent($eventId)
    {
        $event = $this->eventRepository->updateWithId($eventId, array("display_flag" => 0));

        $competitionId = $event->competition->first()->id;
        if( ! count( $this->competitionRepository->getDisplayedEventsForCompetitions($competitionId) ) ) {
            $this->competitionRepository->updateWithId($competitionId, array("display_flag" => 0));
        }

        return $this;
    }

    public function setDisplayFlagForCompetition($competitionId, $displayFlag)
    {
        $competition = $this->competitionRepository->updateWithId($competitionId, array("display_flag" => $displayFlag));

        foreach($competition->events as $event)
        {
            $this->eventRepository->updateWithId($event->id, array("display_flag" => $displayFlag));
        }

        return $this;
    }

}