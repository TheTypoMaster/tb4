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

    public function getMarketTypesForCompetition($competitionId)
    {
        return $this->model
            ->join('tbdb_market', 'tbdb_market.market_type_id', '=', 'tbdb_market_type.id')
            ->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_market.event_id')
            ->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
            ->where('tbdb_event_group_event.event_group_id', $competitionId)
            ->groupBy('tbdb_market_type.id')
            ->get(array("tbdb_market_type.*"));
    }
}