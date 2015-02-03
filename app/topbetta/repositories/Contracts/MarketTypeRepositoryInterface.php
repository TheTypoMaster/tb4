<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/02/2015
 * Time: 3:58 PM
 */

namespace TopBetta\Repositories\Contracts;

interface MarketTypeRepositoryInterface  {

    public function allMarketTypes();

    public function searchMarketTypes($searchTerm);

    public function getMarketTypeById($id);
}