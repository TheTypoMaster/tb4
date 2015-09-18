<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/09/2015
 * Time: 3:29 PM
 */

namespace TopBetta\Repositories\Cache\Tournaments;


use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Repositories\DbTournamentTicketRepository;

class TournamentTicketRepository extends CachedResourceRepository implements TournamentTicketRepositoryInterface {


    /**
     * @var TournamentRepository
     */
    private $tournamentRepository;

    public function __construct(DbTournamentTicketRepository $repository, TournamentRepository $tournamentRepository)
    {
        $this->repository = $repository;
        $this->tournamentRepository = $tournamentRepository;
    }

    public function create($data)
    {
        $model = parent::create($data);

        $this->tournamentRepository->makeCacheResource($model->tournament);

        return $model;
    }

    public function makeCacheResource($model)
    {
        return $model;
    }

    public function getTicketsInTournament($tournamentId)
    {
        return $this->repository->getTicketsInTournament($tournamentId);
    }

    public function getWithUserAndTournament($ticketId)
    {
        return $this->repository->getWithUserAndTournament($ticketId);
    }

    public function getTicketByUserAndTournament($userId, $tournamentId)
    {
        return $this->repository->getTicketByUserAndTournament($userId, $tournamentId);
    }

    public function getRecentAndActiveTicketsForUserWithTournament($user)
    {
        return $this->repository->getRecentAndActiveTicketsForUserWithTournament($user);
    }

    public function nextToJumpTicketsForUser($user, $limit = 10)
    {
        return $this->repository->nextToJumpTicketsForUser($user, $limit);
    }

    public function getActiveTicketsForUser($user)
    {
        return $this->repository->getActiveTicketsForUser($user);
    }

    public function getTicketsForUserOnDate($user, \Carbon\Carbon $date)
    {
        return $this->repository->getTicketsForUserOnDate($user, $date);
    }

    public function getAllForUserPaginated($user)
    {
        return $this->repository->getAllForUserPaginated($user);
    }

    public function getByResultTransaction($transaction)
    {
        return $this->repository->getByResultTransaction($transaction);
    }
}