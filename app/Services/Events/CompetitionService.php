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
}