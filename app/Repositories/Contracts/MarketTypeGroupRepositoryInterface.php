<?php namespace TopBetta\Repositories\Contracts;

/**
 * Coded by Oliver Shanahan
 * File creation date: 24/09/15
 * File creation time: 08:39
 * Project: tb4
 */

interface MarketTypeGroupRepositoryInterface
{

    public function getMarketTypeGroups();

    public function createMarketTypeGroup($marketTypeGroup);

    public function updateMarketTypeGroup($group_id, $data);

    public function deleteMarketTypeGroup($group_id);

    public function getGroupById($group_id);
}