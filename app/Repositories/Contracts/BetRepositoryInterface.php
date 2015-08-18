<?php namespace TopBetta\Repositories\Contracts;

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 10:40
 * Project: tb4
 */
 
interface BetRepositoryInterface {

    public function getBetsForSelectionsByBetType($user, $selections, $betType);

    public function getBetsForUserByEvents($user, $events, $type=null);

    public function getBetsForUserByMarket($user, $market, $type=null);
}