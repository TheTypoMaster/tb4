<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 20/03/2015
 * Time: 2:57 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\MarketOrderingModel;
use TopBetta\Repositories\Contracts\MarketOrderingRepositoryInterface;

class DbMarketOrderingRepository extends BaseEloquentRepository implements MarketOrderingRepositoryInterface
{

    public function __construct(MarketOrderingModel $marketOrderingModel)
    {
        $this->model = $marketOrderingModel;
    }

    public function getMarketOrdering($competitionId = 0, $userId = 0)
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('base_competition_id', $competitionId)
            ->first();
    }

}