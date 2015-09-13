<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 11:39 AM
 */

namespace TopBetta\Services\Feeds;

use TopBetta\Services\Caching\SportsDataCacheManager;
use TopBetta\Services\Feeds\Processors\GameListProcessor;
use TopBetta\Services\Feeds\Processors\MarketListProcessor;
use TopBetta\Services\Feeds\Processors\ResultListProcessor;
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
    /**
     * @var ResultListProcessor
     */
    private $resultListProcessor;
    /**
     * @var SportsDataCacheManager
     */
    private $cacheManager;

    public function __construct(GameListProcessor $gameListProcessor,
                                MarketListProcessor $marketListProcessor,
                                SelectionListProcessor $selectionListProcessor,
                                ResultListProcessor $resultListProcessor,
                                SportsDataCacheManager $cacheManager)
    {
        $container = new SportsCollectionContainer;
        $this->gameListProcessor = $gameListProcessor;
        $this->gameListProcessor->setModelContainer($container);
        $this->marketListProcessor = $marketListProcessor;
        $this->marketListProcessor->setModelContainer($container);
        $this->selectionListProcessor = $selectionListProcessor;
        $this->selectionListProcessor->setModelContainer($container);
        $this->resultListProcessor = $resultListProcessor;
        $this->resultListProcessor->setModelContainer($container);
        $this->cacheManager = $cacheManager;
    }

    /**
     * Process the sport feed
     * @param $data
     */
    public function processSportsFeed($data)
    {
        $this->gameListProcessor->processArray(array_get($data, 'GameList', array()));

        file_put_contents('/tmp/game-queries.log', print_r(\DB::getQueryLog(), true));

        $this->marketListProcessor->processArray(array_get($data, 'MarketList', array()));

        $this->selectionListProcessor->processArray(array_get($data, 'SelectionList', array()));

        $this->resultListProcessor->processArray(array_get($data, 'ResultList', array()));

        $this->cacheManager->updateCache();
    }
}