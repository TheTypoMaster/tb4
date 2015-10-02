<?php

namespace TopBetta\Repositories\Contracts;

interface TournamentEventGroupSportRepositoryInterface {
    public function createTourEventGroupSport($data);

    public function getSportsByTourEventGroupId($tour_event_group_id);
}
