<?php namespace TopBetta\Repositories;

/**
 * Coded by Oliver Shanahan
 * File creation date: 24/09/15
 * File creation time: 08:36
 * Project: tb4
 */


use TopBetta\Models\MarketTypeGroupModel;


class DbMarketTypeGroupRepository extends BaseEloquentRepository implements MarketTypeGroupRepositoryInterface {

    protected $model;

    public function __construct(MarketTypeGroupModel $model){
        $this->model = $model;
    }

}