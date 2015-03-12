<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/02/2015
 * Time: 2:36 PM
 */

namespace TopBetta\Services\Risk;


use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventModelRepositoryInterface;

class RiskEventService {

    /**
     * @var EventRepositoryInterface
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

    public function showEvent($eventId)
    {
        $event = $this->eventRepository->setDisplayFlagForEvent($eventId, 1);

        $this->competitionRepository->setDisplayFlagForCompetition($event->competition->first()->id, 1);

        //reload the competition to save the changes
        $event->load('competition');

        return $event;
    }

    public function hideEvent($eventId)
    {
        $event = $this->eventRepository->setDisplayFlagForEvent($eventId, 0);

        $competitionId = $event->competition->first()->id;
        if( ! count( $this->competitionRepository->getDisplayedEventsForCompetition($competitionId) ) ) {
            $this->competitionRepository->setDisplayFlagForCompetition($competitionId, 0);
        }

        return $event;
    }


}