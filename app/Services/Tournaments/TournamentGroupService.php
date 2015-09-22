<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/07/2015
 * Time: 10:29 AM
 */

namespace TopBetta\Services\Tournaments;


use Carbon\Carbon;
use TopBetta\Models\TournamentModel;
use TopBetta\Repositories\Contracts\TournamentGroupRepositoryInterface;
use TopBetta\Services\Resources\Tournaments\TournamentGroupResourceService;

class TournamentGroupService {

    /**
     * @var TournamentGroupRepositoryInterface
     */
    private $tournamentGroupRepository;
    /**
     * @var TournamentGroupResourceService
     */
    private $tournamentGroupResourceService;

    public function __construct(TournamentGroupRepositoryInterface $tournamentGroupRepository, TournamentGroupResourceService $tournamentGroupResourceService)
    {
        $this->tournamentGroupRepository = $tournamentGroupRepository;
        $this->tournamentGroupResourceService = $tournamentGroupResourceService;
    }

    /**
     * Creates a competition tournament group if one does not exist and attaches the tournament
     * @param TournamentModel $tournament
     * @return TournamentModel
     */
    public function addTournamentToCompetitionGroup(TournamentModel $tournament)
    {
        $groupName = $tournament->eventGroup->name;

        $date = Carbon::createFromFormat('Y-m-d H:i:s', $tournament->eventGroup->start_date);
        if( $tournament->eventGroup->sport_id ) {
            $groupName .= ' ' . $date->year;
        } else {
            $groupName .= ' '. $date->toDateString();
        }

        $group = $this->tournamentGroupRepository->getByName($groupName);

        if( ! $group ) {
            $group = $this->tournamentGroupRepository->create(array(
                'group_name' => $groupName
            ));
        }

        if( ! $tournament->groups->where('id', $group['id'])->first() ) {
            $this->addTournamentToGroups($tournament, array($group['id']));
        }

        return $tournament;
    }

    public function addTournamentToGroups($tournament, $groups)
    {
//        $this->tournamentGroupRepository->addTournamentToGroups($tournament, $groups);
    }

    /**
     * @param string $type
     * @param string $date
     * @return \TopBetta\Resources\EloquentResourceCollection
     */
    public function getGroupsWithTournaments($type = 'racing', $date = null)
    {
        if( ! is_null($date) ) {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }

        switch($type)
        {
            case 'racing':
                return $this->tournamentGroupResourceService->getVisibleRacingTournamentGroupsWithTournaments($date);
            case 'sport':
                return $this->tournamentGroupResourceService->getVisibleSportTournamentGroupsWithTournaments($date);
        }

        throw new \InvalidArgumentException("Type " . $type . " is not available");
    }
}