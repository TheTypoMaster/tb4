<?php namespace TopBetta\Repositories\Contracts;
use Carbon\Carbon;

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 10:40
 * Project: tb4
 */
 
interface BetRepositoryInterface {

    public function getBetsForSelectionsByBetType($user, $selections, $betType);

    public function getBetsForUserByMarket($user, $market, $type=null);

    public function getBetsForUserByEvent($userId, $eventId, $type = null);

    public function getBetsForUserBySelection($userId, $selection, $type = null);

    public function getAllBetsForUser($user);

    public function getWinningBetsForUser($user);

    public function getLosingBetsForUser($user);

    public function getRefundedBetsForUser($user);

    public function getUnresultedBetsForUser($user, $page = true);

    public function getBetsOnDateForUser($user, Carbon $date, $resulted = null);

    public function getBetsForEventGroup($user, $eventGroup);

    public function getByResultTransaction($transaction);

    public function getByRefundTransaction($transaction);

    public function getByEntryTransaction($transaction);

    public function findBets($bets);

    public function getBetsForEventByStatus($event, $status, $type = null);

    public function getBetsForEventByStatusAndProduct($event, $status, $product, $type = null);
}