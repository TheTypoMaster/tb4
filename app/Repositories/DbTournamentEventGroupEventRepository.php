<?php

namespace TopBetta\Repositories;

use TopBetta\Models\TournamentEventGroupEventModel;
use TopBetta\Repositories\Contracts\TournamentEventGroupEventRepositoryInterface;

class DbTournamentEventGroupEventRepository extends BaseEloquentRepository implements TournamentEventGroupEventRepositoryInterface {

    public function __construct(TournamentEventGroupEventModel $tournamentEventGroupEventModel) {
        $this->model = $tournamentEventGroupEventModel;
    }
}