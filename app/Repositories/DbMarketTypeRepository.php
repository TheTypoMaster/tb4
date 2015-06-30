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

    protected $order;

    public function __construct(MarketTypeModel $marketTypeModel)
    {
        $this->model = $marketTypeModel;
        $this->order = array(DB::raw('-ordering'), 'DESC');
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

    public function search($searchTerm)
    {
        return $this->searchMarketTypes($searchTerm);
    }

    public function getMarketTypeById($id)
    {
        return $this->model->find($id);
    }

    public function getMarketTypesIn($marketTypes, $orderByIn = true)
    {
        $types = $this->model->whereIn('id', $marketTypes);

        if( $orderByIn ) {
            $types->orderBy(DB::raw("FIELD(id," . implode(",", $marketTypes) . ")"));
        }

        return $types->get();
    }

    public function getMarketTypesForBaseCompetition($competitionId)
    {
        return $this->model
            ->join('tbdb_market', 'tbdb_market.market_type_id', '=', 'tbdb_market_type.id')
            ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_market.event_id')
            ->join('tbdb_event_group', function($join) use ($competitionId) {
                $join->on('tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
                    ->on('tbdb_event_group.base_competition_id', '=', DB::raw($competitionId));
            })
            ->groupBy('tbdb_market_type.id')
            ->get(array(
                'tbdb_market_type.*',
            ));
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