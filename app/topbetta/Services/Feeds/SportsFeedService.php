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
use TopBetta\Services\Feeds\Processors\SelectionListProcessor;

class SportsFeedService {

    /**
     * @var GameListProcessor
     */
    private $gameListProcessor;
    /**
     * @var MarketListProcessor
     */
    private $marketListProcessor;
    /**
     * @var SelectionListProcessor
     */
    private $selectionListProcessor;

    public function __construct(GameListProcessor $gameListProcessor, MarketListProcessor $marketListProcessor, SelectionListProcessor $selectionListProcessor)
    {
        $this->gameListProcessor = $gameListProcessor;
        $this->marketListProcessor = $marketListProcessor;
        $this->selectionListProcessor = $selectionListProcessor;
    }

    /**
     * Process the sport feed
     * @param $data
     */
    public function processSportsFeed($data)
    {
        $this->gameListProcessor->processArray(array_get($data, 'GameList', array()));

        $this->marketListProcessor->processArray(array_get($data, 'MarketList', array()));

        $this->selectionListProcessor->processArray(array_get($data, 'SelectionList', array()));
    }
}