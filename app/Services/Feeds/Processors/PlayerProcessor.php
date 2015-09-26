<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 5:05 PM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\PlayersRepositoryInterface;
use TopBetta\Repositories\Contracts\TeamRepositoryInterface;

class PlayerProcessor extends AbstractFeedProcessor {

    /**
     * @var PlayersRepositoryInterface
     */
    private $playerRepository;
    /**
     * @var TeamRepositoryInterface
     */
    private $teamRepository;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    public function __construct(PlayersRepositoryInterface $playerRepository, TeamRepositoryInterface $teamRepository, EventRepositoryInterface $eventRepository)
    {
        $this->playerRepository = $playerRepository;
        $this->logprefix = 'SportsFeedService - PlayerProcessor: ';
        $this->teamRepository = $teamRepository;
        $this->eventRepository = $eventRepository;
    }

    public function process($data)
    {
        if( (! $externalId = array_get($data, 'player_id', false)) || (! $eventId = array_get($data, 'event_id')))   {
            Log::error($this->logprefix."No player id specified");
            return 0;
        }

        if (!$event = $this->modelContainer->getEvent($eventId)) {
            if (!$event = $this->eventRepository->getBySerenaId($eventId)) {
                $event = $this->eventRepository->getEventModelFromExternalId(array_get($data, 'event_id_bg'));
            }
            $this->modelContainer->addEvent($event, $eventId);
        }

        $teamId = array_get($data, 'team_id_bg');
        $team = null;
        if ($teamId && !$team = $this->modelContainer->getTeam($teamId)) {
            if (!$team = $this->teamRepository->getBySerenaId($teamId)) {
                $team = $this->teamRepository->findByExternalId(array_get($data, 'team_id_bg'));
            }
            $this->modelContainer->addTeam($team, $teamId);
        }

        Log::info($this->logprefix."Processing player " . $externalId);
        
        $playerData = array(
            "serena_player_id" => $externalId,
            "name" => array_get($data, "FirstName", "") . " " . array_get($data, "LastName", ""),
            "short_name" => array_get($data, "Name", ""),            
        );

        if ($player = $this->modelContainer->getPlayer($externalId)) {
            $player = $this->playerRepository->update($player, $playerData);
        } else if ($player = $this->playerRepository->getBySerenaId($externalId)) {
            $player = $this->playerRepository->update($player, $playerData);
        } else if ($player = $this->playerRepository->findByExternalId(array_get($data, 'player_id_bg'))) {
            $player = $this->playerRepository->updte($player, $playerData);
        } else {
            $player = $this->playerRepository->create($playerData);
        }

        $this->modelContainer->addPlayer($player, $externalId);

        if($team) {
            $this->teamRepository->addPlayers($team->id, array($player->id));

            $this->eventRepository->addTeamPlayers($event, $team->id, array($player->id));
        }

        if( $player ) {
            return $player['id'];
        }

        Log::error($this->logprefix."Error creating/updating player " . $externalId);
        return 0;
    }


}