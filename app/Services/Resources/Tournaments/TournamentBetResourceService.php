<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 9:39 AM
 */

namespace TopBetta\Services\Resources\Tournaments;


use TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;

class TournamentBetResourceService {

    /**
     * @var TournamentBetRepositoryInterface
     */
    private $betRepository;

    public function __construct(TournamentBetRepositoryInterface $betRepository)
    {
        $this->betRepository = $betRepository;
    }

    public function getBetsForUserInTournament($user, $tournament)
    {
        $bets = $this->betRepository->getBetsForUserTournament($user, $tournament);

        return new EloquentResourceCollection($bets, 'TopBetta\Resources\Tournaments\TournamentBetResource');
    }

    public function getBetsForUserInTournamentWhereEventStatusIn($user, $tournament, $statuses)
    {
        $bets = $this->betRepository->getBetResourcesForUserInTournamentWhereEventStatusIn($user, $tournament, $statuses);

        return new EloquentResourceCollection($bets, 'TopBetta\Resources\Tournaments\TournamentBetResource');
    }

    public function findBets($bets)
    {
        $bets = $this->betRepository->findBets($bets);

        return new EloquentResourceCollection($bets, 'TopBetta\Resources\Tournaments\TournamentBetResource');
    }

}