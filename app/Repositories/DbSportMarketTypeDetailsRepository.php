<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/08/2015
 * Time: 3:25 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\SportMarketTypeDetailsModel;
use TopBetta\Repositories\Contracts\SportMarketTypeDetailsRepositoryInterface;

class DbSportMarketTypeDetailsRepository extends BaseEloquentRepository implements SportMarketTypeDetailsRepositoryInterface
{

    public function __construct(SportMarketTypeDetailsModel $model)
    {
        $this->model = $model;
    }

    public function getBySportAndMarketType($sport, $marketType)
    {
        return $this->model
            ->where('sport_id', $sport)
            ->where('market_type_id', $marketType)
            ->first();
    }
}