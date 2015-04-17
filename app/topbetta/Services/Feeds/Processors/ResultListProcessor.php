<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/04/2015
 * Time: 4:07 PM
 */

namespace TopBetta\Services\Feeds\Processors;


class ResultListProcessor extends AbstractFeedProcessor {

    public function __construct()
    {}

    public function process($data)
    {
        //make sure game and market ids exists
        if( ! ($eventId = array_get($data, 'GameId', false)) || ! ($marketId = array_get($data, 'MarketId', false)) ) {
            return 0;
        }
    }
}