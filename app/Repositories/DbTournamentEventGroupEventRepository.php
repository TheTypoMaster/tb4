<?php

namespace TopBetta\Repositories;

use TopBetta\Models\TournamentEventGroupEventModel;
use TopBetta\Repositories\Contracts\TournamentEventGroupEventRepositoryInterface;

class DbTournamentEventGroupEventRepository extends BaseEloquentRepository implements TournamentEventGroupEventRepositoryInterface {

    public function __construct(TournamentEventGroupEventModel $tournamentEventGroupEventModel) {
        $this->model = $tournamentEventGroupEventModel;
    }

    /**
     * remove event from group
     * @param $group_id
     * @param $event_id
     */
    public function removeEvent($group_id, $event_id) {
        $this->model->where('tournament_event_group_id', $group_id)
                    ->where('event_id', $event_id)
                   ->delete();
    }

    /**
     * remove all events that belong to the group
     * @param $group_id
     */
    public function removeAllEventsFromGroup($group_id) {
        $this->model->where('tournament_event_group_id', $group_id)
                    ->delete();
    }
}