<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 28/04/2014
 * File creation time: 5:32 PM
 * Project: tb4
 */

use TopBetta\RaceMarket;


class DbMarketsRepository extends BaseEloquentRepository {

    protected $model;

    public function __construct(RaceMarket $model){
        $this->model = $model;
    }

    public function getMarketEventStartTime($marketId){
        return RaceMarket::find($marketId)->event()->pluck('start_date');
    }

} 