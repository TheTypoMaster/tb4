<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 20/11/14
 * File creation time: 13:19
 * Project: tb4
 */

use TopBetta\Models\Events;

class DbEventsRepository extends BaseEloquentRepository{

    protected $events;

    function __construct(Events $events) {
        $this->model = $events;
    }

    /**
     * @param $search
     * @return mixed
     */
    public function search($search)
    {
        return $this->model
            ->orderBy('start_date', 'DESC')
            ->where('name', 'LIKE', "%$search%")
            ->paginate();
    }

    /**
     * @return mixed
     */
    public function allEvents()
    {
        return $this->model
            ->orderBy('start_date', 'DESC')
            ->paginate();
    }
} 