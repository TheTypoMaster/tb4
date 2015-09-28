<?php

namespace TopBetta\Repositories;


use Carbon\Carbon;
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
        return $this->model->paginate();
    }

    /**
     * get event groups without paginate
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getEventGroupsWithoutPaginate()
    {
        return $this->model->where('start_date', '>=', Carbon::today())->get();
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
    public function getEventGroup($event_group_id)
    {
        return $this->model->find($event_group_id);
    }


    /**
     * get event groups by type
     * @param $type_id
     * @return mixed
     */
    public function getEventGroupsByType($type_id)
    {
        if ($type_id == 0) {
            $type = 'Race';
        } else {
            $type = 'Sport';
        }
        return $this->model->where('type', $type)
            ->where('start_date', '>=', Carbon::today())
            ->get();
    }

    /**
     * get all race event groups
     * @return mixed
     */
    public function getRaceEventGroups()
    {
        return $this->model->where('type', 'Race')
            ->where('start_date', '>=', Carbon::today())
            ->get();
    }

    /**
     * get all sport event groups
     * @return mixed
     */
    public function getSportEventGroups()
    {
        return $this->model->where('type', 'Sport')
            ->where('start_date', '>=', Carbon::today())
            ->get();
    }

    /**
     * get event group type
     * @param $group_id
     * @return mixed
     */
    public function getEventGroupType($group_id)
    {
        $tournament_event_group = $this->model->find($group_id);

        return $tournament_event_group->type;
    }

}