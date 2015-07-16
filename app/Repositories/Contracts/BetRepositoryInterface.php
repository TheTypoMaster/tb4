<?php namespace TopBetta\Repositories\Contracts;

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 10:40
 * Project: tb4
 */
 
interface BetRepositoryInterface {

    public function getBetsForUserByEvent($userId, $eventId, $type = null);

    public function getBetsForUserBySelection($userId, $selection, $type = null);
}