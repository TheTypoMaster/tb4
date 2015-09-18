<?php

namespace TopBetta\Services\Tournaments;

use TopBetta\Repositories\Contracts\TournamentEventGroupRepositoryInterface;
use TopBetta\Repositories\TournamentEventGroupRepository;

class TournamentEventGroupService
{

    public function __construct(TournamentEventGroupRepositoryInterface $tournamentEventGroupRepo)
    {
        $this->tournamentEventGroupRepo = $tournamentEventGroupRepo;
    }

    /**
     * get all tournament event groups
     * @return array
     */
    public function getAllEventGroups()
    {
        $event_group_list = $this->tournamentEventGroupRepo->getAllEventGroup();

        return $event_group_list;
    }

    /**
     * get all event gruops and change them to array
     * @return array
     */
    public function getAllEventGroupsToArray()
    {
        $event_groups = $this->tournamentEventGroupRepo->getAllEventGroup();

        $event_group_list = array();
        foreach ($event_groups as $event_group) {
            $event_group_list[$event_group->id] = $event_group->name;
        }

        return $event_group_list;
    }


}