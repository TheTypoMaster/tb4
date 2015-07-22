<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/07/2015
 * Time: 9:55 AM
 */

namespace TopBetta\Services\Resources\Tournaments;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\Tournaments\TournamentResource;

class TournamentResourceService {

    /**
     * @var TournamentRepositoryInterface
     */
    private $tournamentRepositoryInterface;

    public function __construct(TournamentRepositoryInterface $tournamentRepositoryInterface)
    {
        $this->tournamentRepositoryInterface = $tournamentRepositoryInterface;
    }

    public function getVisibleRacingTournaments(Carbon $date = null)
    {
        $tournaments = $this->tournamentRepositoryInterface->getVisibleRacingTournaments($date);

        return new EloquentResourceCollection($tournaments, 'TopBetta\Resources\Tournaments\TournamentResource');
    }

    public function getVisibleSportTournaments(Carbon $date = null)
    {
        $tournaments = $this->tournamentRepositoryInterface->getVisibleSportTournaments($date);

        return new EloquentResourceCollection($tournaments,' TopBetta\Resources\Tournaments\TournamentResource');
    }

    public function getTournament($id)
    {
        $tournament = $this->tournamentRepositoryInterface->find($id);

        $tournament->load('tickets');

        return new TournamentResource($tournament);
    }
}