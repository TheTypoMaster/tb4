<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/07/2015
 * Time: 4:20 PM
 */

namespace TopBetta\Services\Resources\Tournaments;


use TopBetta\Repositories\Contracts\TournamentGroupRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class TournamentGroupResourceService {

    /**
     * @var TournamentGroupRepositoryInterface
     */
    private $tournamentGroupRepository;

    public function __construct(TournamentGroupRepositoryInterface $tournamentGroupRepository)
    {
        $this->tournamentGroupRepository = $tournamentGroupRepository;
    }

    public function getVisibleSportTournamentGroupsWithTournaments($date = null)
    {
        $groups = $this->tournamentGroupRepository->getVisibleSportTournamentGroupsWithTournaments($date);

        if ($groups instanceof EloquentResourceCollection) {
            return $groups;
        }

        return new EloquentResourceCollection($groups, 'TopBetta\Resources\Tournaments\TournamentGroupResource');
    }

    public function getVisibleRacingTournamentGroupsWithTournaments($date = null)
    {
        $groups = $this->tournamentGroupRepository->getVisibleRacingTournamentGroupsWithTournaments($date);

        if ($groups instanceof EloquentResourceCollection) {
            return $groups;
        }

        return new EloquentResourceCollection($groups, 'TopBetta\Resources\Tournaments\TournamentGroupResource');
    }
}