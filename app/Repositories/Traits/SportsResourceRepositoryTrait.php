<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 3:00 PM
 */

namespace TopBetta\Repositories\Traits;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;

trait SportsResourceRepositoryTrait {

    /**
     * @return Builder
     */
    protected function getVisibleSportsEventBuilder()
    {
        $builder =  \DB::table('tbdb_event as e')
            ->join('tbdb_event_group_event as ege', 'ege.event_id', '=', 'e.id')
            ->join('tbdb_event_group as eg', 'eg.id', '=', 'ege.event_group_id')
            ->join('tb_base_competition as bc', 'bc.id', '=', 'eg.base_competition_id')
            ->join('tb_sports', 'tb_sports.id', '=', 'bc.sport_id')
            ->join('tbdb_market as m', 'm.event_id', '=', 'e.id')
            ->join('tbdb_selection as s', 's.market_id', '=', 'm.id')
            ->join('tbdb_selection_price as sp', 'sp.selection_id', '=', 's.id')
            ->where('tb_sports.display_flag', true)
            ->where('bc.display_flag', true)
            ->where('eg.display_flag', true)
            ->where('e.display_flag', true)
            ->where('m.display_flag', true)
            ->whereNotIn('m.market_status', array('D', 'S'))
            ->where(function($q) {
                $q
                    ->where(function($p) {
                        $p->where('sp.win_odds', '>', '1')->whereNull('sp.override_type');
                    })
                    ->orWhere(function($p) {
                        $p->where('sp.override_odds', '>', 1)->where('sp.override_type', '=', 'price');
                    })
                    ->orWhere(function($p) {
                        $p->where(\DB::raw('sp.override_odds * sp.win_odds'), '>', '1')->where('sp.override_type', 'percentage');
                    });
            })
            ->where('s.selection_status_id', 1);

        return $builder;
    }
}