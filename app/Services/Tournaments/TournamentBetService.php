<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/05/2015
 * Time: 1:59 PM
 */

namespace TopBetta\Services\Tournaments;


use TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface;
use TopBetta\Services\Betting\EventService;

class TournamentBetService {

    /**
     * @var TournamentBetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var EventService
     */
    private $eventService;

    public function __construct(TournamentBetRepositoryInterface $betRepository, EventService $eventService)
    {
        $this->betRepository = $betRepository;
        $this->eventService = $eventService;
    }

    public function getBetsForUserInTournamentWhereEventClosed($user, $tournament)
    {
        $statuses = $this->eventService->getClosedEventStatusIds();

        return $this->betRepository->getBetsForUserInTournamentWhereEventStatusIn($user, $tournament, $statuses);
    }
}