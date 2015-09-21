<?php

namespace TopBetta\Repositories;


use TopBetta\Models\TournamentEventGroupModel;
use TopBetta\Repositories\Contracts\TournamentEventGroupRepositoryInterface;

class TournamentEventGroupRepository extends BaseEloquentRepository implements TournamentEventGroupRepositoryInterface
{

    public function __construct(TournamentEventGroupModel $tournamentEventGroup)
    {
        $this->model = $tournamentEventGroup;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllEventGroup()
    {

//        return TournamentEventGroupModel::all()->paginate();
        return $this->model->paginate();

    }

    /**
     * get tournament event group by id
     * @param $id
     * @return mixed
     */
    public function getEventGroupByID($id)
    {

        $event_group = $this->model->where('id', $id)->first();

        return $event_group;
    }

    /**
     * get events that belong to tournament event gruop
     * @param $group_id
     * @return mixed
     */
    public function getEvents($group_id)
    {
        return $this->model->find($group_id)
            ->events()
            ->get();
    }

    /**
     * get event group by event group id
     * @param $event_group_id
     * @return mixed
     */
    public function getEventGroup($event_group_id) {
        return $this->model->find($event_group_id)->first();
    }

}