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
    public function getDefaultMarketTypes();

    public function getMarketTypesForCompetition($competitionId);

    public function getMarketTypesForUser($userId, $competitionId);
}