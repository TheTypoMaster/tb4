<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 4:35 PM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\TeamRepositoryInterface;

class TeamProcessor extends AbstractFeedProcessor {

    /**
     * @var TeamRepositoryInterface
     */
    private $teamRepository;
    /**
     * @var PlayerProcessor
     */
    private $playerProcessor;

    private $event = null;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepositoryInterface;

    public function __construct(TeamRepositoryInterface $teamRepository, PlayerProcessor $playerProcessor, EventRepositoryInterface $eventRepositoryInterface)
    {
        $this->teamRepository = $teamRepository;
        $this->playerProcessor = $playerProcessor;
        $this->logprefix = 'SportsFeedService - TeamProcessor: ';
        $this->eventRepositoryInterface = $eventRepositoryInterface;
    }

    public function process($team)
    {
        //check team id exists
        if( (! $teamId = array_get($team, 'team_id', null)) || (! $eventId = array_get($team, 'event_id'))) {
            Log::error($this->logprefix."No team_id specified");
            return 0;
        }

        if (!$event = $this->modelContainer->getEvent($eventId)) {
            $event = $this->eventRepositoryInterface->getBySerenaId($eventId);
            if (!$event) {
                $event = $this->eventRepositoryInterface->getEventModelFromExternalId(array_get($team, 'event_id_bg'));
            }

            $this->setEvent($event);
            $this->modelContainer->addEvent($this->event, $eventId);
        } else {
            $this->setEvent($event);
        }

        //team data
        $data = array(
            "serena_team_id" => $teamId,
            "name" => array_get($team, 'team_name', null),
        );

        Log::info($this->logprefix."Processing team " . $teamId. ", Name: ".$data['name']);

        //create the team
        if ($teamModel = $this->modelContainer->getTeam($teamId)) {
            $teamModel = $this->teamRepository->update($teamModel, $data);
        } else {
            if ($teamModel = $this->teamRepository->getBySerenaId($teamId)) {
                $teamModel = $this->teamRepository->update($teamModel, $data);
            } else if ($teamModel = $this->teamRepository->findByExternalId(array_get($team, 'team_id_bg'))) {
                $teamModel = $this->teamRepository->update($teamModel, $data);
            } else {
                $teamModel = $this->teamRepository->create($data);
            }
        }

        $this->modelContainer->addTeam($teamModel, $teamId);

        if ($this->event) {
            $teamPosition = array("team_position" => array_get($team, 'team_position'));

            $this->eventRepositoryInterface->addTeamsToModel($this->event, array($teamModel->id => $teamPosition));
        }

        if( $teamModel ) {
            return $teamModel['id'];
        }

        Log::error("BackAPI sports error processing team " . $teamId);
        return 0;
    }


    /**
     * @param null $event
     * @return $this
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

}