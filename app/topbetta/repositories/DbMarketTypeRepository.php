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
use DB;

class DbMarketTypeRepository extends BaseEloquentRepository implements MarketTypeRepositoryInterface {

    public function __construct(MarketTypeModel $marketTypeModel)
    {
        $this->model = $marketTypeModel;
    }

    public function allMarketTypes()
    {
        //Use negative ordering to place records with ordering of NULL at the enbd
        return $this->model->orderBy(DB::raw('-ordering'), 'DESC')->paginate(15);
    }

    public function searchMarketTypes($searchTerm)
    {
        return $this->model
                    ->where("name", "LIKE", "%$searchTerm%")
                    ->orWhere("description", "LIKE", "%$searchTerm%")
                    ->orderBy(DB::raw('-ordering'), 'DESC')
                    ->paginate(15);
    }

    public function getMarketTypeById($id)
    {
        return $this->model->find($id);
    }
}