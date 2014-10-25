<?php namespace TopBetta\Services\Caching;
/**
 * Coded by Oliver Shanahan
 * File creation date: 25/10/14
 * File creation time: 7:55 PM
 * Project: tb4
 */

use Cache;
use Log;
use TopBetta\Repositories\DbNextToJumpRepository;

class NextToJumpCacheService {

    protected $nexttojump;

    /**
     * @param DbNextToJumpRepository $nexttojump
     */
    public function __construct(DbNextToJumpRepository $nexttojump){
        $this->nexttojump = $nexttojump;
    }

    /**
     * @param $raceExisting
     * @param $raceUpdate
     */
    public function manageCache($raceExisting, $raceUpdate){
        ($raceExisting) ? $this->_oldRace($raceUpdate, $raceExisting) : $this->_newRace($raceUpdate, $raceExisting);
    }

    /**
     * @param $raceUpdate
     * @param $raceExisting
     * @return bool
     */
    private function _newRace($raceUpdate, $raceExisting){

        Log::debug('NextToJump: Procesing New Race');
        // check if the status of this race is selling
        if(!$raceUpdate->event_status_id == 1) return false;

        // get the current next to jump object
        $nextToJumpCacheObject = $this->_geteNextToJumpCacheObject();

        // if no next to jump cache object
        if(!$nextToJumpCacheObject){
            Log::debug('NextToJump: Procesing New Race: No cache oject found');
            // run query to get the latest next to jump
            $nextToJumpArray = $this->nexttojump->getNextToJump(10);

            // no results no update
            if(!$nextToJumpArray) return false;

            // add the cache object
            return $this->_updateNextToJumpCacheObject($nextToJumpArray);
        }

        // get the first and last array elements from the cache object
        $firstRaceArray = reset($nextToJumpCacheObject);
        $lastRaceArray = end($nextToJumpCacheObject);

        // get the start date and convert to a unix time stamp for simple
        $firstRaceUnix = strtotime($firstRaceArray['start_date']);
        $lastRaceUnix = strtotime($lastRaceArray['start_date']);

        // current update timestamp
        $currentRaceUnix = strtotime($raceUpdate->start_date);

        // if it's between the current races then we need to update the cache object
        if(!$currentRaceUnix >=  $firstRaceUnix && !$currentRaceUnix <= $lastRaceUnix) return false;

        $this->_addDatabaseNextToJumpToCache();

    }

    /**
     * @param $raceUpdate
     * @param $raceExisting
     * @return bool
     */
    private function _oldRace($raceUpdate, $raceExisting){

        Log::debug('NextToJump: Procesing Old Race');
        Log::debug('NextToJump: Procesing Old Race: Update status - '.$raceUpdate->event_status_id.', start time - '.$raceUpdate->start_date.', Existing Status - '.$raceExisting['EventStatusId']);


        // if race is selling and start time hase not been updated then we don't update the cache
        if($raceUpdate->event_status_id == 1 && $raceUpdate->start_date == $raceExisting['StartDate']) return false;

        // if race status is not changing from selling to closed we don't update the cache
        if(!$raceUpdate->event_status_id == 5 && !$raceExisting['EventStatusId'] == 1) return false;

        // add the current DB to cache object
        return $this->_addDatabaseNextToJumpToCache();

    }

    /**
     * @param $nextToJumpArray
     * @return mixed
     */
    private function _updateNextToJumpCacheObject($nextToJumpArray){
        Log::debug('NextToJump: Adding cache object - '.print_r($nextToJumpArray,true));
        return Cache::tags('topbetta-nexttojump')->forever('topbetta-nexttojump', $nextToJumpArray);
    }

    /**
     * @return mixed
     */
    private function _geteNextToJumpCacheObject(){
        Log::debug('NextToJump: Getting cache object');
        return Cache::tags('topbetta-nexttojump')->get('topbetta-nexttojump');
    }

    /**
     * @return bool|mixed
     */
    private function _addDatabaseNextToJumpToCache(){
        Log::debug('NextToJump: Add database data to cache');
        // run query to get the latest next to jump
        $nextToJumpArray = $this->nexttojump->getNextToJump(10);

        // no results no update
        if(!$nextToJumpArray) return false;

        // add the cache object
        return $this->_updateNextToJumpCacheObject($nextToJumpArray);

    }

} 