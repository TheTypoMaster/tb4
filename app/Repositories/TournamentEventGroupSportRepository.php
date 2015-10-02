<?php

namespace TopBetta\Repositories;

use TopBetta\Models\TournamentEventGroupSportModel;
use TopBetta\Repositories\Contracts\TournamentEventGroupSportRepositoryInterface;

/**
 * Tournament Repo for admin interface
 *
 * @author mic
 */
class TournamentEventGroupSportRepository extends BaseEloquentRepository implements TournamentEventGroupSportRepositoryInterface
{

    public function __construct(TournamentEventGroupSportModel $tournamentEventGroupSportModel) {
        $this->tournamentEventGroupSportModel = $tournamentEventGroupSportModel;
    }

    /**
     * check if the record exists, if not, create a new one
     * @param $data
     * @return static
     */
    public function createTourEventGroupSport($data) {
//        dd($data);
        $tour_event_group_sport = $this->tournamentEventGroupSportModel->where('tournament_event_group_id', $data['tournament_event_group_id'])
                                                                       ->where('sport_id', $data['sport_id'])
                                                                       ->first();
        if($tour_event_group_sport == null) {
            return $this->tournamentEventGroupSportModel->create($data);
        } else {
            return null;
        }
    }

    /**
     * get tournament event group sport relationship
     * @param $data
     * @return mixed
     */
    public function getTourEventGroupSport($data) {
        return $this->tournamentEventGroupSportModel->where('tournament_event_group_id', $data['tournament_event_group_id'])
            ->where('sport_id', $data['sport_id'])
            ->first();
    }

    /**
     * get sports id by tournament event group id
     * @param $tour_event_group_id
     * @return mixed
     */
    public function getSportsByTourEventGroupId($tour_event_group_id) {
        $tour_event_group_sports = $this->tournamentEventGroupSportModel->where('tournament_event_group_id', $tour_event_group_id)
                                                                        ->where('sport_id', '>', 0)
                                                                        ->orderBy('sport_id', 'ASC')
                                                                        ->get();
        return $tour_event_group_sports;
    }

}
