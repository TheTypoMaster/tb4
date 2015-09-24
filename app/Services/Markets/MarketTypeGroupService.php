<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 20/03/2015
 * Time: 3:01 PM
 */

namespace TopBetta\Services\Markets;

use TopBetta\Repositories\DbMarketTypeGroupRepository;

Class MarketTypeGroupService {

    public function __construct(DbMarketTypeGroupRepository $marketTypeGroupRepository) {

        $this->marketTypeGroupRepository = $marketTypeGroupRepository;

    }


    /**
     * get all market type groups with pagination
     * @return mixed
     */
    public function getMarketTypeGroups() {
        return $this->marketTypeGroupRepository->getMarketTypeGroups();
    }

    /**
     * create market type group
     * @param $marketTypeGroup
     */
    public function createMarketTypeGroup($marketTypeGroup) {
        $this->marketTypeGroupRepository->createMarketTypeGroup($marketTypeGroup);
    }

    /**
     * update market type goup model
     * @param $group_id
     * @param $data
     * @return mixed
     */
    public function updateMarketTypeGroup($group_id, $data) {
        return $this->marketTypeGroupRepository->updateMarketTypeGroup($group_id,$data);
    }

    /**
     * delete market type group
     * @param $group_id
     * @return mixed
     */
    public function deleteMarketTypeGroup($group_id) {
        return $this->marketTypeGroupRepository->deleteMarketTypeGroup($group_id);
    }

    public function getGroupById($group_id) {
        return $this->marketTypeGroupRepository->getGroupById($group_id);
    }

    /**
     * get all market type groups without pagination
     * @return mixed
     */
    public function getMarketTypeGroupList() {
        $market_type_groups = $this->marketTypeGroupRepository->getMarketTypeGroupsWithoutPaginated();
        $market_type_group_list = array();
        foreach($market_type_groups as $group) {
            $market_type_group_list[$group->market_type_group_id] = $group->market_type_group_name;
        }

        return $market_type_group_list;
    }
}