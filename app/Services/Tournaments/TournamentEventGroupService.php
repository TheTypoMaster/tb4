<?php

namespace TopBetta\Services\Tournaments;

use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentEventGroupRepositoryInterface;
use TopBetta\Repositories\TournamentEventGroupRepository;

class TournamentEventGroupService
{

    public function __construct(TournamentEventGroupRepositoryInterface $tournamentEventGroupRepo,
                                CompetitionRepositoryInterface $eventGroupRepository)
    {
        $this->tournamentEventGroupRepo = $tournamentEventGroupRepo;
        $this->eventGroupRepository = $eventGroupRepository;
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
     * get tournament event group by id
     * @param $id
     * @return mixed
     */
    public function getEventGroupByID($id) {
        $event_group = $this->tournamentEventGroupRepo->getEventGroupByID($id);

        return $event_group;
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

    /**
     * create new event group
     * @param $item
     * @return mixed
     */
    public function createEventGroup($item) {
        return $this->tournamentEventGroupRepo->create($item);
    }

    /**
     * get event groups by sport id
     * @param $sportId
     * @return mixed
     */
    public function getEventGroups($sportId) {
        $event_groups = $this->eventGroupRepository->getEventGruopsBySportId($sportId);
        return $event_groups;
    }

    /**
     * get events by event group id
     * @param $event_group_id
     * @return mixed
     */
    public function getEventsByEventGroup($event_group_id) {

        $events = $this->eventGroupRepository->getEvents($event_group_id);

        return $events;
    }

}