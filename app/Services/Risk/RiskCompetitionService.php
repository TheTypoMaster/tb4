<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/02/2015
 * Time: 4:29 PM
 */

namespace TopBetta\Services\Risk;


use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventModelRepositoryInterface;

class RiskCompetitionService {
    /**
     * @var EventModelRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;

    public function __construct(EventModelRepositoryInterface $eventRepository, CompetitionRepositoryInterface $competitionRepository)
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


    private function setDisplayFlagForCompetition($competitionId, $displayFlag)
    {
        $competition = $this->competitionRepository->setDisplayFlagForCompetition($competitionId, $displayFlag);

        //get events explicitly using get() as events seems to be already be an attribute on Eloquent Models
        //$events = $competition->events()->get();
        $events = $this->competitionRepository->getDisplayedEventsForCompetition($competitionId);

        foreach($events as $event)
        {
            $this->eventRepository->setDisplayFlagForEvent($event->external_event_id, $displayFlag);
        }

        return $competition;
    }


    public function enableFixedOdds($competitionId)
    {
        return $this->setFixedOddsFlagForCompetition($competitionId, 1);
    }

    public function disableFixedOdds($competitionId)
    {
        return $this->setFixedOddsFlagForCompetition($competitionId, 0);
    }


    private function setFixedOddsFlagForCompetition($competitionId, $fixedOddsFlag)
    {
        $competition = $this->competitionRepository->setFixedOddsFlagForCompetition($competitionId, $fixedOddsFlag);

        //get events explicitly using get() as events seems to be already be an attribute on Eloquent Models
        //$events = $competition->events()->get();
        $events = $this->competitionRepository->getFixedOddsEventsForCompetition($competitionId);

        foreach($events as $event)
        {
            $this->eventRepository->setFixedOddsFlagForEvent($event->external_event_id, $fixedOddsFlag);
        }

        return $competition;
    }

}