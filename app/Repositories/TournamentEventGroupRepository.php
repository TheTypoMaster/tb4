<?php

namespace TopBetta\Repositories;


use TopBetta\Models\TournamentEventGroupModel;
use TopBetta\Repositories\Contracts\TournamentEventGroupRepositoryInterface;

class TournamentEventGroupRepository extends BaseEloquentRepository implements TournamentEventGroupRepositoryInterface {

    public function __construct(TournamentEventGroupModel $tournamentEventGroup) {
        $this->model = $tournamentEventGroup;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllEventGroup() {

        return TournamentEventGroupModel::all();

    }

    /**
     * get tournament event group by id
     * @param $id
     * @return mixed
     */
    public function getEventGroupByID($id) {

        $event_group = $this->model->where('id', $id)->first();

        return $event_group;
    }
}