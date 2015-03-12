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
        $events = $competition->events()->get();
        foreach($events as $event)
        {
            $this->eventRepository->setDisplayFlagForEvent($event->id, $displayFlag);
        }

        return $competition;
    }

}