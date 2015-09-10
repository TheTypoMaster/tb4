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
        if( ! $teamId = array_get($team, 'team_id', null) ) {
            Log::error($this->logprefix."No team_id specified");
            return 0;
        }

        //team data
        $data = array(
            "external_team_id" => $teamId,
            "name" => array_get($team, 'team_name', null),
        );

        Log::info($this->logprefix."Processing team " . $teamId. ", Name: ".$data['name']);

        //create the team
        $teamModel = $this->teamRepository->updateOrCreate($data, 'external_team_id');

        //update the player
        $this->processTeamPlayers(array_get($team, 'team_players', array()), array_get($teamModel, 'id', 0));

        if( $teamModel ) {
            return $teamModel['id'];
        }

        Log::error("BackAPI sports error processing team " . $teamId);
        return 0;
    }

    public function processTeamPlayers($playerData, $teamId)
    {

        $playerIds = $this->playerProcessor->processArray($playerData);

        //add the players to the team
        if($teamId) {
            //filter out 0's
            $ids = array_filter($playerIds, function ($value) { return $value > 0; });

            $this->teamRepository->addPlayers($teamId, $ids);

            $this->eventRepositoryInterface->addTeamPlayers($this->event, $teamId, $ids);
        }
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