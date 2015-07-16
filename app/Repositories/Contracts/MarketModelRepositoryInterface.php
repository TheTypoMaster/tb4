<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 2:22 PM
 */
namespace TopBetta\Repositories\Contracts;

interface MarketModelRepositoryInterface
{
    public function getMarketsForCompetition($competition, $types = null);
}