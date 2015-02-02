<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/02/2015
 * Time: 4:09 PM
 */

namespace TopBetta\Repositories;

use TopBetta\Repositories\Contracts\MarketTypeRepositoryInterface;
use TopBetta\Models\MarketTypeModel;

class DbMarketTypeRepository extends BaseEloquentRepository implements MarketTypeRepositoryInterface {

    public function __construct(MarketTypeModel $marketTypeModel)
    {
        $this->model = $marketTypeModel;
    }

    public function allMarketTypes()
    {
        return $this->model->all()->paginate();
    }

    public function searchMarketTypes($searchTerm)
    {
        return $this->model
                    ->where("name", "LIKE", "%$searchTerm%")
                    ->orWhere("description", "LIKE", "%$searchTerm%")
                    ->paginate();
    }
}