<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 4:35 PM
 */

namespace TopBetta\Services\Feeds\Processors;


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

    public function __construct(TeamRepositoryInterface $teamRepository, PlayerProcessor $playerProcessor)
    {
        $this->teamRepository = $teamRepository;
        $this->playerProcessor = $playerProcessor;
    }

    public function process($team)
    {
        //check team id exists
        if( ! $teamId = array_get($team, 'team_id', null) ) {
            return 0;
        }

        //team data
        $data = array(
            "external_team_id" => $teamId,
            "name" => array_get($team, 'team_name', null),
        );

        //create the team
        $teamModel = $this->teamRepository->updateOrCreate($data, 'external_team_id');

        //update the player
        $this->processTeamPlayers(array_get($team, 'team_players', array()), array_get($teamModel, 'id', 0));

        if( $teamModel ) {
            return $teamModel['id'];
        }

        return 0;
    }

    public function processTeamPlayers($playerData, $teamId)
    {
        foreach($playerData as $players)
        {
            $playerIds = $this->playerProcessor->processArray(array_get($players, 'Player', array()));

            //add the players to the team
            if($teamId) {
                //filter out 0's
                $ids = array_filter($playerIds, function ($value) { return $value > 0; });

                $this->teamRepository->addPlayers($teamId, $ids);
            }
        }
    }
}