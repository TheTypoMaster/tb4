<?php namespace TopBetta\Services\Caching;
/**
 * Coded by Oliver Shanahan
 * File creation date: 25/10/14
 * File creation time: 7:55 PM
 * Project: tb4
 */

use Cache;
use Log;
use TopBetta\Jobs\Pusher\Racing\NextToJumpSocketUpdate;
use TopBetta\Repositories\DbNextToJumpRepository;

class NextToJumpCacheService {

    protected $nexttojump;

    /**
     * @param DbNextToJumpRepository $nexttojump
     */
    public function __construct(DbNextToJumpRepository $nexttojump){
        $this->nexttojump = $nexttojump;
		$this->logprefix = 'NextToJumpCacheService: ';

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

        Log::debug($this->logprefix.'Procesing New Race');
        // check if the status of this race is selling
        if(!$raceUpdate['event_status_id'] == 1) return false;

        // get the current next to jump object
        $nextToJumpCacheObject = $this->getNextToJumpCacheObject();

        // if no next to jump cache object
        if(!$nextToJumpCacheObject || count($nextToJumpCacheObject) < 10){
            Log::debug($this->logprefix.' Procesing New Race: No cache oject found');
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
        $currentRaceUnix = strtotime($raceUpdate['start_date']);

        // if it's between the current races then we need to update the cache object
        if(!$currentRaceUnix >=  $firstRaceUnix && !$currentRaceUnix <= $lastRaceUnix){
            Log::debug($this->logprefix.' Procesing New Race: Race start time not in nextToJump');
            return false;
        }

        $this->_addDatabaseNextToJumpToCache();

    }

    /**
     * @param $raceUpdate
     * @param $raceExisting
     * @return bool
     */
    private function _oldRace($raceUpdate, $raceExisting){

        Log::debug($this->logprefix.' Procesing Existing Race');

        // if race is selling and start time hase not been updated then we don't update the cache
        if($raceUpdate['event_status_id'] == 1 && $raceUpdate['start_date'] == $raceExisting['start_date']) return false;

        // if race status is not changing from selling to closed we don't update the cache
        if($raceUpdate['event_status_id'] == 5 && $raceExisting['event_status_id'] == 1) {
            // add the current DB to cache object
            return $this->_addDatabaseNextToJumpToCache();
        }
        return false;



    }

    /**
     * @param $nextToJumpArray
     * @return mixed
     */
    private function _updateNextToJumpCacheObject($nextToJumpArray){
        Log::debug($this->logprefix.'Adding cache object - ', $nextToJumpArray);
        return Cache::tags('topbetta-nexttojump')->forever('topbetta-nexttojump', $nextToJumpArray);
    }

    /**
     * @return mixed
     */
    public function getNextToJumpCacheObject(){
        Log::debug($this->logprefix.' Getting cache object');
        $nextToJump = Cache::tags('topbetta-nexttojump')->get('topbetta-nexttojump');
    }

    public function getNextToJumpDBObject(){
        return $nextToJumpArray = $this->nexttojump->getNextToJump(10);
    }

    /**
     * @return bool|mixed
     */
    private function _addDatabaseNextToJumpToCache(){
        Log::debug($this->logprefix.' Add database data to cache');
        // run query to get the latest next to jump
        $nextToJumpArray = $this->nexttojump->getNextToJump(10);

        // no results no update
        if(!$nextToJumpArray) return false;

        //updates so trigger socket up
        event(new NextToJumpSocketUpdate($nextToJumpArray));

        // add the cache object
        return $this->_updateNextToJumpCacheObject($nextToJumpArray);

    }

} 