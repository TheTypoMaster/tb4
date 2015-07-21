<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/07/2015
 * Time: 9:30 AM
 */

namespace TopBetta\Repositories;

use Carbon\Carbon;
use TopBetta\Repositories\Contracts\TournamentGroupRepositoryInterface;
use TopBetta\Models\TournamentGroupModel;

class DbTournamentGroupRepository extends BaseEloquentRepository implements TournamentGroupRepositoryInterface
{

    public function __construct(TournamentGroupModel $model)
    {
        $this->model = $model;
    }

    public function getVisibleTournamentGroupsWithTournaments(Carbon $date = null)
    {
        $model = $this->model
            ->from('tb_tournament_group as tg')
            ->join('tb_tournament_group_tournament as tgt', 'tgt.tournament_group_id', '=', 'tg.id')
            ->join('tbdb_tournament as t', 't.id', '=', 'tgt.tournament_id')
            ->where('t.status_flag', true)
            ->groupBy('tg.id');

        if( $date ) {
            $model->where('t.start_date', '>=', $date->startOfDay()->toDateTimeString())->where('t.start_date', '<=', $date->endOfDay()->toDateTimeString())
                ->with('tournaments', function($q) use ($date) {
                    $q->where('status_flag', true)
                        ->where('start_date', '>=', $date->startOfDay()->toDateTimeString())
                        ->where('start_date', '>=', $date->endOfDay()->toDateTimeString());
                });
        } else {
            $model->where('t.start_date', '>=', Carbon::now())
                ->with('tournaments', function($q) {
                    $q->where('status_flag', true)
                        ->where('start_date', '>=', Carbon::now());
                });
        }

        return $model->get(array('tg.*'));
    }

    public function getByName($name)
    {
        return $this->model->where('group_name', $name)->first();
    }

    public function search($term)
    {
        return $this->model->where('group_name', 'LIKE', "%$term%")->paginate();
    }
}