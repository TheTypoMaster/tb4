<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 20/03/2015
 * Time: 2:59 PM
 */
namespace TopBetta\Repositories\Contracts;

interface MarketOrderingRepositoryInterface
{
    public function getMarketOrdering($competitionId = 0, $userId = 0);
}