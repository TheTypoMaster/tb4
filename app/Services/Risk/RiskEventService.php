<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/02/2015
 * Time: 2:36 PM
 */

namespace TopBetta\Services\Risk;

use Log;

use TopBetta\Repositories\Cache\MeetingRepository;
use TopBetta\Repositories\Cache\RaceRepository;
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
    /**
     * @var RaceRepository
     */
    private $raceRepository;
    /**
     * @var MeetingRepository
     */
    private $meetingRepository;

    public function __construct(EventModelRepositoryInterface $eventRepository, CompetitionRepositoryInterface $competitionRepository, RaceRepository $raceRepository, MeetingRepository $meetingRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->competitionRepository = $competitionRepository;
        $this->raceRepository = $raceRepository;
        $this->meetingRepository = $meetingRepository;
    }

    public function showEvent($eventId)
    {
        $event = $this->raceRepository->updateWithId($eventId, array("display_flag" => true), "external_event_id");

        $this->meetingRepository->update($event->competition->first(), array("display_flag" => true));

        //reload the competition to save the changes
        $event->load('competition');

        return $event;
    }

    public function hideEvent($eventId)
    {
        $event = $this->raceRepository->updateWithId($eventId, array("display_flag" => false), "external_event_id");

        $competitionId = $event->competition->first()->external_event_id;
        if( ! count( $this->competitionRepository->getDisplayedEventsForCompetition($competitionId) ) ) {
            $this->meetingRepository->update($event->competition->first(), array("display_flag" => false));
        }

        return $event;
    }

    public function enableFixedOdds($eventId)
    {
        $event = $this->raceRepository->updateWithId($eventId, array("fixed_odds_enabled" => true), "external_event_id");

        $this->meetingRepository->update($event->competition->first(), array("fixed_odds_enabled" => true));

        //reload the competition to save the changes
        $event->load('competition');

        return $event;
    }

    public function disableFixedOdds($eventId)
    {
        $event = $this->raceRepository->updateWithId($eventId, array("fixed_odds_enabled" => false), "external_event_id");

 //       $competitionId = $event->competition->first()->external_event_id;
  //      if( ! count( $this->competitionRepository->getDisplayedEventsForCompetition($competitionId) ) ) {
   //         $this->competitionRepository->setDisplayFlagForCompetition($competitionId, 0);
  //      }

        return $event;
    }


}