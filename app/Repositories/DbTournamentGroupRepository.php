<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/07/2015
 * Time: 9:30 AM
 */

namespace TopBetta\Repositories;

use Carbon\Carbon;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentGroupRepositoryInterface;
use TopBetta\Models\TournamentGroupModel;

class DbTournamentGroupRepository extends BaseEloquentRepository implements TournamentGroupRepositoryInterface
{

    public function __construct(TournamentGroupModel $model)
    {
        $this->model = $model;
    }

    public function getVisibleSportTournamentGroupsWithTournaments(Carbon $date = null)
    {
        $model = $this->getVisibleTournamentGroupBuilder($date);

        //join competition and sport and look for non racing sports
        $model->join('tbdb_event_group as eg', 'eg.id', '=', 't.event_group_id')
            ->join('tb_sports as s', 's.id', '=', 'eg.sport_id')
            ->whereNotIn('s.name', array(SportRepositoryInterface::SPORT_GALLOPING, SportRepositoryInterface::SPORT_GREYHOUNDS, SportRepositoryInterface::SPORT_HARNESS));

        return $model->get(array('tg.*'));
    }

    public function getVisibleRacingTournamentGroupsWithTournaments(Carbon $date = null)
    {
        $model = $this->getVisibleTournamentGroupBuilder($date);

        //join competition and sport and look for racing
        $model->join('tbdb_event_group as eg', 'eg.id', '=', 't.event_group_id')
            ->leftJoin('tb_sports as s', 's.id', '=', 'eg.sport_id')
            ->where(function($q) {
                $q->whereIn('s.name', array(SportRepositoryInterface::SPORT_GALLOPING, SportRepositoryInterface::SPORT_GREYHOUNDS, SportRepositoryInterface::SPORT_HARNESS))
                    ->orWhere('eg.sport_id', 0);
            });

        return $model->get(array('tg.*'));
    }

    /**
     * @param Carbon $date
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getVisibleTournamentGroupBuilder(Carbon $date = null)
    {
        $model = $this->model
            ->from('tb_tournament_groups as tg')
            ->join('tb_tournament_group_tournament as tgt', 'tgt.tournament_group_id', '=', 'tg.id')
            ->join('tbdb_tournament as t', 't.id', '=', 'tgt.tournament_id')
            ->where('t.status_flag', true)
            ->groupBy('tg.id');

        if( $date ) {
            $model->where('t.start_date', '>=', $date->startOfDay()->toDateTimeString())->where('t.start_date', '<=', $date->endOfDay()->toDateTimeString())
                ->with(array('tournaments' => function($q) use ($date) {
                    $q->where('status_flag', true)
                        ->where('start_date', '>=', $date->startOfDay()->toDateTimeString())
                        ->where('start_date', '>=', $date->endOfDay()->toDateTimeString())
                        ->with('tickets');
                }));
        } else {
            $model->where('t.start_date', '>=', Carbon::now()->startOfDay())
                ->with(array('tournaments' => function($q) {
                    $q->where('status_flag', true)
                        ->where('start_date', '>=', Carbon::now()->startOfDay())
                        ->with('tickets');
                }));
        }

        return $model;
    }


    public function getByName($name)
    {
        return $this->model->where('group_name', $name)->first();
    }

    public function search($term)
    {
        return $this->model->where('group_name', 'LIKE', "%$term%")->paginate();
    }

    public function addTournamentToGroups($tournament, $groups)
    {
        $tournament->groups()->sync($groups);

        return $tournament;
    }
}