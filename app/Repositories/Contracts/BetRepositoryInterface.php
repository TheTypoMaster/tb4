<?php namespace TopBetta\Repositories\Contracts;
use Carbon\Carbon;

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 10:40
 * Project: tb4
 */
 
interface BetRepositoryInterface {

    public function getBetsForUserByEvent($userId, $eventId, $type = null);

    public function getBetsForUserBySelection($userId, $selection, $type = null);

    public function getAllBetsForUser($user, $page = null);

    public function getWinningBetsForUser($user, $page = null);

    public function getLosingBetsForUser($user, $page = null);

    public function getRefundedBetsForUser($user, $page = null);

    public function getUnresultedBetsForUser($user, $page = null);

    public function getBetsOnDateForUser($user, Carbon $date, $resulted = null);

    public function getBetsForEventGroup($user, $eventGroup);
}