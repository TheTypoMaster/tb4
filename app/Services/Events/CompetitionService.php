<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/06/2015
 * Time: 3:50 PM
 */

namespace TopBetta\Services\Events;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\MeetingVenueRepositoryInterface;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;

class CompetitionService {

    /**
     * @var MeetingVenueRepositoryInterface
     */
    private $meetingVenueRepository;
    /**
     * @var CompetitionRepositoryInterface
     */
    private $competitionRepository;
    /**
     * @var SportRepositoryInterface
     */
    private $sportRepository;


    public function __construct(MeetingVenueRepositoryInterface $meetingVenueRepository,
                                CompetitionRepositoryInterface $competitionRepository,
                                SportRepositoryInterface $sportRepository)
    {
        $this->meetingVenueRepository = $meetingVenueRepository;
        $this->competitionRepository = $competitionRepository;
        $this->sportRepository = $sportRepository;
    }

    /**
     * @param $sportId
     * @param $tournamentCompetitionId
     * @param $venueId
     * @param $startDate Carbon
     * @return mixed
     * @throws \Exception
     */
    public function createCompetitionFromMeetingVenue($sportId, $tournamentCompetitionId, $venueId, $startDate)
    {
        $sport = $this->sportRepository->find($sportId);

        $venue = $this->meetingVenueRepository->find($venueId);

        $typeCode = SportService::getRacingCode($sport);

        return $this->competitionRepository->create(array(
            "name" => strtoupper($venue->name),
            "tournament_competition_id" => $tournamentCompetitionId,
            "start_date" => $startDate,
            "type_code" => $typeCode,
            "meeting_code" => strtoupper(str_replace(" ", "", $venue->name)) . '-' . $typeCode . '-' . $startDate->toDateString()
        ));
    }


    /**
     * update competition from meeting venue
     * @param $competition_id
     * @param $sportId
     * @param $tournamentCompetitionId
     * @param $venueId
     * @param $startDate
     * @return mixed
     * @throws \Exception
     */
    public function updateCompetitionFromMeetingVenue($competition_id, $type_code, $sportId, $tournamentCompetitionId, $venueId, $startDate) {

        $sport = $this->sportRepository->find($sportId);

        $venue = $this->meetingVenueRepository->find($venueId);

        $typeCode = SportService::getRacingCode($sport);

        $competition = $this->getCompetitionById($competition_id);

        $competition->name = strtoupper($venue->name);
        $competition->tournament_competition_id = $tournamentCompetitionId;
        $competition->start_date = $startDate;
        $competition->type_code = $type_code;
        $competition->meeting_code = strtoupper(str_replace(" ", "", $venue->name)) . '-' . $typeCode . '-' . $startDate->toDateString();

        $competition->update();

        return $competition;


    }

    public function isAbandoned($competition)
    {
        $events = $competition->events()->get()->load('eventstatus');

        $abandonedEvents = $events->filter(function ($v) {
            return $v->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_ABANDONED;
        });

        if ($events->count() / 2 < $abandonedEvents->count()) {
            return true;
        }

        return false;
    }

    /**
     * get competition by id
     * @param $id
     * @return mixed
     */
    public function getCompetitionById($id) {
        return $this->competitionRepository->getEventGroupByGroupId($id);
    }
}