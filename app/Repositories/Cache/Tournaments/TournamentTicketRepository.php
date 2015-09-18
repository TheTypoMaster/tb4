<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/09/2015
 * Time: 3:29 PM
 */

namespace TopBetta\Repositories\Cache\Tournaments;


use Carbon\Carbon;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Repositories\DbTournamentTicketRepository;
use TopBetta\Resources\EloquentResourceCollection;

class TournamentTicketRepository extends CachedResourceRepository implements TournamentTicketRepositoryInterface {

    const CACHE_KEY_PREFIX = 'tickets_';

    protected $resourceClass = 'TopBetta\Resources\Tournaments\TicketResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $storeIndividualResource = false;

    protected $tags = array("users", "tickets");

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

    public function addAvailableCurrency($userId, $tournament, $currency)
    {
        $ticket = $this->getTicket($userId, $tournament->id);

        if ($ticket) {
            $ticket->addAvailableCurrency($currency);
            $this->put($this->cachePrefix . $userId . '_' . $tournament->id, $ticket->toArray(), Carbon::now()->addWeek()->diffInMinutes());

            $this->updateActiveTickets($ticket);
            $this->updateDateTickets($tournament->end_date, $ticket);
        }
    }

    public function setPosition($user, $tournament, $position)
    {
        $ticket = $this->getTicket($user, $tournament);

        if ($ticket && $ticket->getPosition() != $position) {
            $ticket->setPosition($position);
            $this->put($this->cachePrefix . $ticket->user_id . '_' . $ticket->tournament_id, $ticket->toArray(), Carbon::now()->addWeek()->diffInMinutes());

            $this->updateActiveTickets($ticket);
            $this->updateDateTickets($ticket->tournament->end_date, $ticket);
        }
    }

    public function getTicket($user, $tournament)
    {
        return $this->get($this->cachePrefix . $user . '_' . $tournament);
    }

    public function getActiveTicketsArray($user)
    {
        return \Cache::tags($this->tags)->get($this->cachePrefix . $user .'_active');
    }

    public function getDateTicketsArray($user, $date)
    {
        return \Cache::tags($this->tags)->get($this->cacheForever . $user . '_' . $date);
    }

    public function getActiveTickets($user)
    {
        return $this->getCollection($this->cachePrefix . $user .'_active');
    }

    public function getDateTickets($user, $date)
    {
        return $this->getCollection($this->cacheForever . $user . '_' . $date);
    }

    public function updateActiveTickets($resource)
    {
        $tickets = $this->getActiveTicketsArray($resource->user_id);
        if ($resource->resulted_flag && array_get($tickets, $resource->id)) {
            unset($tickets[$resource->id]);
        } else if (!$resource->resulted_flag) {
            $tickets[$resource->id] = $resource->toArray();
        }

        $this->put($this->cachePrefix . $resource->user_id . '_active', $tickets, Carbon::now()->addMonth()->diffInMinutes());
    }

    public function updateDateTickets($date, $resource)
    {
        if (($carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $date)) > Carbon::now()->startOfDay()->subDays(2)) {
            $tickets = $this->getDateTicketsArray($resource->user_id, $date);

            $tickets[$resource->id] = $resource->toArray();

            $this->put($this->cachePrefix . $resource->user_id . '_active', $tickets, $carbonDate->addDay(2)->diffInMinutes());
        }
    }

    public function saveTicket($model)
    {
        $resource = $this->createResource($model);
        $this->put($this->cachePrefix . $model->user_id . '_'. $model->tournament_id, $resource->toArray(), $carbonDate->addDays(2)->diffInMinutes());
        return $resource;

    }

    public function storeActiveTickets($user, $tickets)
    {
        $ticketResources = new EloquentResourceCollection($tickets, $this->resourceClass);

        $this->put($this->cachePrefix . $user . '_active', $ticketResources->toArray(), Carbon::now()->addMonth()->diffInMinutes());
    }

    public function storeDateTickets($user, $date, $tickets)
    {
        $ticketResources = new EloquentResourceCollection($tickets, $this->resourceClass);

        $this->put($this->cachePrefix . $user . '_' . $date, $ticketResources->toArray(), Carbon::createFromFormat('Y-m-d', $date)->addDay(2)->diffInMinutes());
    }

    public function makeCacheResource($model)
    {
        $resource = $this->saveTicket($model);

        if ($resource) {
            $this->updateActiveTickets($resource);

            $this->updateDateTickets($model->tournament->end_date, $resource);
        }

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
        $ticket = $this->getTicket($userId, $tournamentId);

        if ($ticket) {
            return $ticket;
        }

        return $this->saveTicket($this->repository->getTicketByUserAndTournament($userId, $tournamentId));
    }

    public function getRecentAndActiveTicketsForUserWithTournament($user)
    {
        $active = $this->getActiveTicketsForUser($user);

        $recent = $this->getTicketForUserByEndDate($user, Carbon::now());

        $tickets = $active->merge($recent);

        return $tickets;
    }

    public function nextToJumpTicketsForUser($user, $limit = 10)
    {
        return $this->repository->nextToJumpTicketsForUser($user, $limit);
    }

    public function getActiveTicketsForUser($user)
    {
        $tickets = $this->getActiveTickets($user);

        if ($tickets) {
            return $tickets;
        }

        $tickets = $this->repository->getActiveTicketsForUser($user);

        $this->storeActiveTickets($user, $tickets);

        $tickets = new EloquentResourceCollection($tickets, $this->resourceClass);

        return $tickets;
    }

    public function getTicketsForUserOnDate($user, \Carbon\Carbon $date)
    {
        return $this->repository->getTicketsForUserOnDate($user, $date);
    }

    public function getTicketForUserByEndDate($user, Carbon $date)
    {
        $tickets = $this->getDateTickets($user, $date->toDateString());

        if ($tickets) {
            return $tickets;
        }

        $tickets = $this->repository->getTicketsForUserByEndDate($user, $date);

        $this->storeDateTickets($user, $date->toDateString(), $tickets);

        $tickets = new EloquentResourceCollection($tickets, $this->resourceClass);

        return $tickets;
    }

    public function getAllForUserPaginated($user)
    {
        return $this->repository->getAllForUserPaginated($user);
    }

    public function getByResultTransaction($transaction)
    {
        return $this->repository->getByResultTransaction($transaction);
    }

    /**
     * @param $key
     * @return EloquentResourceCollection
     */
    public function getCollection($key, $resource = null)
    {
        $collection = \Cache::tags($this->tags)->get($key);

        if ($collection) {
            return $this->createCollectionFromArray($collection, $resource);
        }

        return null;
    }
}