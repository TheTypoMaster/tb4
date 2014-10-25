<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 25/10/14
 * File creation time: 8:24 PM
 * Project: tb4
 */

use TopBetta\RaceEvent;

class DbNextToJumpRepository {

    protected $model;

    public function __construct(RaceEvent $model){
        $this->model = $model;
    }

    public function getNextToJump($limit = 10){
        $nexttojump = $this->model->join('tbdb_event_group_event as ege', 'ege.event_id', '=', 'tbdb_event.id')
                            ->join('tbdb_event_group as eg', 'eg.event_group_id', '=', 'ege.event_group_id')
                            ->where('eg.sport_id', 0)
                            ->where('tbdb_event.event_status.id', 1)
                            ->where('tbdb_event.display_flag', 1)
                            ->orderBy('tbdb_event.start_date', 'ASC')
                            ->take($limit)
                            ->select('tbdb_event.id', 'tbdb_event.start_date', 'tbdb_event.number', 'eg.type_code',
                                     'eg.id as meeting_id', 'eg.name', 'eg.state')
                            ->get();
        if($nexttojump){
            return $nexttojump->toArray();
        }
        return false;
    }

} 