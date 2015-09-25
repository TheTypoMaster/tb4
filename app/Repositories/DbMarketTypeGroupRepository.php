<?php

namespace TopBetta\Repositories;

/**
 * Coded by Oliver Shanahan
 * File creation date: 24/09/15
 * File creation time: 08:36
 * Project: tb4
 */


use TopBetta\Models\MarketTypeGroupModel;
use TopBetta\Repositories\Contracts\MarketTypeGroupRepositoryInterface;


class DbMarketTypeGroupRepository extends BaseEloquentRepository implements MarketTypeGroupRepositoryInterface {

    protected $model;

    public function __construct(MarketTypeGroupModel $model){
        $this->model = $model;
    }

    /**
     * get all market type groups with pagination
     * @return mixed
     */
    public function getMarketTypeGroups() {
        return $this->model->paginate();
    }

    /**
     * create market type group
     * @param $marketTypeGroup
     */
    public function createMarketTypeGroup($marketTypeGroup) {
//        dd($marketTypeGroup);
        $this->model->create($marketTypeGroup);
    }

    /**
     * update market type group
     * @param $group_id
     * @param $data
     * @return mixed
     */
    public function updateMarketTypeGroup($group_id, $data) {
        $market_type_group = $this->model->where('market_type_group_id', $group_id)->update($data);
        return $market_type_group;
    }

    /**
     * delete market type group
     * @param $group_id
     * @return mixed
     */
    public function deleteMarketTypeGroup($group_id) {
        return $this->model->where('market_type_group_id', $group_id)->delete();
    }

    /**
     * get market type group by id
     * @param $group_id
     * @return mixed
     */
    public function getGroupById($group_id) {
        return $this->model->where('market_type_group_id', $group_id)->first();
}

    /**
     * get all market type groups without pagination
     * @return mixed
     */
    public function getMarketTypeGroupsWithoutPaginated() {
        return $this->model->all();

    }

}