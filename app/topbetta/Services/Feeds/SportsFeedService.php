<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 11:39 AM
 */

namespace TopBetta\Services\Feeds;

use TopBetta\Services\Feeds\Processors\GameListProcessor;
use TopBetta\Services\Feeds\Processors\MarketListProcessor;

class SportsFeedService {

    /**
     * @var GameListProcessor
     */
    private $gameListProcessor;
    /**
     * @var MarketListProcessor
     */
    private $marketListProcessor;

    public function __construct(GameListProcessor $gameListProcessor, MarketListProcessor $marketListProcessor)
    {
        $this->gameListProcessor = $gameListProcessor;
        $this->marketListProcessor = $marketListProcessor;
    }

    /**
     * Process the sport feed
     * @param $data
     */
    public function processSportsFeed($data)
    {
        $this->gameListProcessor->processArray(array_get($data, 'GameList', array()));

        $this->marketListProcessor->processArray(array_get($data, 'MarketList', array()));
    }
}