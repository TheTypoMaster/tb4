<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 5:05 PM
 */

namespace TopBetta\Services\Feeds\Processors;

use Log;
use TopBetta\Repositories\Contracts\PlayersRepositoryInterface;

class PlayerProcessor extends AbstractFeedProcessor {

    /**
     * @var PlayersRepositoryInterface
     */
    private $playerRepository;

    public function __construct(PlayersRepositoryInterface $playerRepository)
    {
        $this->playerRepository = $playerRepository;
        $this->logprefix = 'SportsFeedService - PlayerProcessor: ';
    }

    public function process($data)
    {
        if( ! $externalId = array_get($data, 'player_id', false) ) {
            Log::error($this->logprefix."No player id specified");
            return 0;
        }

        Log::info($this->logprefix."Processing player " . $externalId);
        
        $data = array(
            "external_player_id" => $externalId,
            "name" => array_get($data, "FirstName", "") . " " . array_get($data, "LastName", ""),
            "short_name" => array_get($data, "Name", ""),            
        );
        
        $player = $this->playerRepository->updateOrCreate($data, "external_player_id");

        if( $player ) {
            return $player['id'];
        }

        Log::error($this->logprefix."Error creating/updating player " . $externalId);
        return 0;
    }


}