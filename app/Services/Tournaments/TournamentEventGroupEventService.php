<?php

namespace TopBetta\Services\Tournaments;

use TopBetta\Repositories\DbTournamentEventGroupEventRepository;

class TournamentEventGroupEventService
{

    public function __construct(DbTournamentEventGroupEventRepository $tournamentEventGroupEventRepository)
    {
        $this->tournamentEventGroupEventRepository = $tournamentEventGroupEventRepository;
    }

    public function createEventGroupEvent($items)
    {
        $new_models = array();
        $tournament_event_group_id = 0;

        foreach ($items as $key => $item) {
            if($key === 'id') {
                $tournament_event_group_id = $item;
            } else {
                $tournament_event_group_event_params = array('tournament_event_group_id' => $tournament_event_group_id, 'event_id' => $item);
                $new_model = $this->tournamentEventGroupEventRepository->create($tournament_event_group_event_params);
                array_push($new_models, $new_model);
            }
        }

        return $new_models;
    }

    /**
     * remove event from event group
     * @param $group_id
     * @param $event_id
     */
    public function removeEventFromGroup($group_id, $event_id) {
        $this->tournamentEventGroupEventRepository->removeEvent($group_id, $event_id);
    }

    /**
     * remove all events that belong to the group
     * @param $group_id
     */
    public function removeAllEventsFromGroup($group_id) {
        $this->tournamentEventGroupEventRepository->removeAllEventsFromGroup($group_id);
    }
}