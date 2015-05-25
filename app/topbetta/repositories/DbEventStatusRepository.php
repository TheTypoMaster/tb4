<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 08:49
 * Project: tb4
 */

use TopBetta\Models\EventStatusModel;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;

class DbEventStatusRepository extends BaseEloquentRepository implements EventStatusRepositoryInterface{

    protected $eventstatus;

    function __construct(EventStatusModel $eventstatus) {
        $this->model = $eventstatus;
    }

    public function getEventStatusList(){
        return $this->model->lists('name', 'id');
    }

    public function getStatusIds($eventStatuses)
    {
        return $this->model->whereIn('keyword', $eventStatuses)->lists('id');
    }
}