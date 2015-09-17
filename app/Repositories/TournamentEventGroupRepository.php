<?php

namespace TopBetta\Repositories;


use TopBetta\Models\TournamentEventGroupModel;
use TopBetta\Repositories\Contracts\TournamentEventGroupRepositoryInterface;

class TournamentEventGroupRepository extends BaseEloquentRepository implements TournamentEventGroupRepositoryInterface {

    public function __construct(TournamentEventGroupModel $tournamentEventGroup) {
        $this->tournamentEventGroup = $tournamentEventGroup;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAllEventGroup() {

        return TournamentEventGroupModel::all();

    }
}