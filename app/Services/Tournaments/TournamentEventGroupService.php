<?php

namespace TopBetta\Services\Tournaments;

use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentEventGroupRepositoryInterface;
use TopBetta\Repositories\DbSportsRepository;
use TopBetta\Repositories\TournamentEventGroupRepository;

class TournamentEventGroupService
{

    public function __construct(TournamentEventGroupRepositoryInterface $tournamentEventGroupRepo,
                                CompetitionRepositoryInterface $eventGroupRepository,
                                EventRepositoryInterface $eventRepository,
                                DbSportsRepository $sportsRepository)
    {
        $this->tournamentEventGroupRepo = $tournamentEventGroupRepo;
        $this->eventGroupRepository = $eventGroupRepository;
        $this->eventRepository = $eventRepository;
        $this->sportsRepository = $sportsRepository;
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

        //check if the sport is race or not, if it is race, get the sport name
        $race = $this->sportsRepository->isRace($sportId);

        if($race) {

            $type_code = '';
            if($race == 'galloping') {
                $type_code = 'R';
            } else if($race == 'harness') {
                $type_code = 'H';
            } else if($race == 'greyhounds') {
                $type_code = 'G';
            }

            $event_groups = $this->eventGroupRepository->getEventGroupByRaceType($type_code);
        } else {
            $event_groups = $this->eventGroupRepository->getEventGruopsBySportId($sportId);
        }

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

    /**
     * get events that belongs to tournament event group
     * @param $group_id
     */
    public function getEventsByTournamentEventGruop($group_id) {

        $events = $this->tournamentEventGroupRepo->getEvents($group_id);

        return $events;
    }

    /**
     * get events with event group id
     * @param $group_id
     * @return array
     */
    public function getEventsByTournamentEventGroupToArray ($group_id) {

        $event_list = array();

        $events = $this->tournamentEventGroupRepo->getEvents($group_id);

        foreach($events as $key => $event) {

            $event_group = $this->eventRepository->getEventGroup($event->id);
            $event_with_group = array('event' => $event, 'event_group_name' => $event_group->name);
            $event_list[$event->id] = $event_with_group;
        }

        return $event_list;
    }

}