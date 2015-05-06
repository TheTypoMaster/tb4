<?php namespace TopBetta\Repositories\Contracts;
/**
 * Created by PhpStorm.
 * User: Oliver Shanahan
 * Date: 12/03/2015
 * Time: 1:58 PM
 */

interface MarketRepositoryInterface  {

    public function getMarketForSelection($selectionId);
}