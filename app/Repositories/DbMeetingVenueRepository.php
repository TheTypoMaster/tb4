<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/06/2015
 * Time: 2:48 PM
 */

namespace TopBetta\Repositories;

use TopBetta\Models\MeetingVenueModel;
use TopBetta\Repositories\Contracts\MeetingVenueRepositoryInterface;

class DbMeetingVenueRepository extends BaseEloquentRepository implements MeetingVenueRepositoryInterface
{

    public function __construct(MeetingVenueModel $model)
    {
        $this->model = $model;
    }

    public function findAll()
    {
        return $this->model->orderBy('name')->get();
    }

    public function getByName($name)
    {
        return $this->model->where('name', $name)->first();
    }
}