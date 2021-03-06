<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/02/2015
 * Time: 4:29 PM
 */

namespace TopBetta\Services\Risk;


use TopBetta\Repositories\Cache\MeetingRepository;
use TopBetta\Repositories\Cache\RaceRepository;
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
    /**
     * @var MeetingRepository
     */
    private $meetingRepository;
    /**
     * @var RaceRepository
     */
    private $raceRepository;

    public function __construct(EventModelRepositoryInterface $eventRepository, CompetitionRepositoryInterface $competitionRepository, MeetingRepository $meetingRepository, RaceRepository $raceRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->competitionRepository = $competitionRepository;
        $this->meetingRepository = $meetingRepository;
        $this->raceRepository = $raceRepository;
    }

    /**
     * Set the display flag to show a single compeition
     *
     * @param $competitionId
     * @return mixed
     */
    public function showCompetition($competitionId)
    {
        return $this->setDisplayFlagForCompetition($competitionId, 1);
    }

    /**
     * Set the display flag to hid a single competition
     *
     * @param $competitionId
     * @return mixed
     */
    public function hideCompetition($competitionId)
    {
        return $this->setDisplayFlagForCompetition($competitionId, 0);
    }


    /**
     * Show or hide a single competition
     *
     * @param $competitionId
     * @param $displayFlag
     * @return mixed
     */
    private function setDisplayFlagForCompetition($competitionId, $displayFlag)
    {
        $competition = $this->meetingRepository->updateWithId($competitionId, array("display_flag" => $displayFlag), 'external_event_group_id');

        //get events explicitly using get() as events seems to be already be an attribute on Eloquent Models
        //$events = $competition->events()->get();
        $events = $this->competitionRepository->getDisplayedEventsForCompetition($competitionId);

        foreach ($events as $event) {
            $this->raceRepository->updateWithId($event->id, array("display_flag" => $displayFlag));
        }

        return $competition;
    }

    /**
     * Disable fixed odds on all competitions and child events
     */
    public function disableAllFixedOdds()
    {
        return $this->disableFixedOddsForAllCompetitions();
    }

    /**
     * Disable fixed odds on all competitions and child events
     */
    private function disableFixedOddsForAllCompetitions()
    {
        $competitions = $this->competitionRepository->getCompetitionsWithFixedOddsEnabled();

        foreach($competitions as $competition){
            $this->disableFixedOdds($competition->id);
        }
    }

    /**
     * Enable fixed odds on a single competition
     *
     * @param $competitionId
     * @return mixed
     */
    public function enableFixedOdds($competitionId)
    {
        return $this->setFixedOddsFlagForCompetition($competitionId, 1);
    }

    /**
     * Disable fixed odds on a single compeititon
     *
     * @param $competitionId
     * @return mixed
     */
    public function disableFixedOdds($competitionId)
    {
        return $this->setFixedOddsFlagForCompetition($competitionId, 0);
    }


    /**
     * If a competition's fixed odds are turned on or off then all the events are also set the same
     *
     * @param $competitionId
     * @param $fixedOddsFlag
     * @return mixed
     */
    private function setFixedOddsFlagForCompetition($competitionId, $fixedOddsFlag)
    {
        $competition = $this->meetingRepository->updateWithId($competitionId, array("fixed_odds_enabled" => $fixedOddsFlag), 'external_event_group_id');

        //get events explicitly using get() as events seems to be already be an attribute on Eloquent Models
        //$events = $competition->events()->get();
        $events = $this->competitionRepository->getFixedOddsEventsForCompetition($competitionId);

        foreach($events as $event)
        {
            $this->raceRepository->updateWithId($event->external_event_id, array("fixed_odds_enabled" => $fixedOddsFlag), "external_event_id");
        }

        return $competition;
    }

}