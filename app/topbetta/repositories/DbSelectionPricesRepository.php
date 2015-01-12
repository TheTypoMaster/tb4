<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 21/11/14
 * File creation time: 11:08
 * Project: tb4
 */


use TopBetta\Models\SelectionPricesModel;

class DbSelectionPricesRepository extends BaseEloquentRepository
{

    protected $selectionprices;

    function __construct(SelectionPricesModel $selectionprices)
    {
        $this->model = $selectionprices;
    }

    /**
     * Selection with type name and event name used for filtered list
     * @param $search
     * @return mixed
     */
    public function search($search)
    {
        return $this->model->join('tbdb_selection', 'tbdb_selection.id', '=', 'tbdb_selection_price.selection_id')
            ->join('tbdb_markett', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
            ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
            ->join('tbdb_selection_status', 'tbdb_selection_status.id', '=', 'tbdb_selection.selection_status_id')

            ->where('tbdb_selection.name', 'LIKE', "%$search%")
            ->orWhere('tbdb_event.name', 'LIKE', "%$search%")
            ->orWhere('tbdb_event_group.name', 'LIKE', "%$search%")

            ->select('tbdb_selection.*', 'tbdb_selection_status.name as status_name',
                'tbdb_event_group.name as competition_name', 'tbdb_event.name as event_name',
                'tbdb_selection_price.win_odds as win_odds', 'tbdb_selection_price.place_odds as place_odds')
            ->paginate();
    }

    /**
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function allSelectionPrices($page = 1, $limit = 10)
    {

        $results = new \stdClass();
        $results->page = $page;
        $results->limit = $limit;
        $results->totalItems = 0;
        $results->items = array();

        $selectionprices =  $this->model->join('tbdb_selection', 'tbdb_selection.id', '=', 'tbdb_selection_price.selection_id')
                    ->join('tbdb_market', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
                    ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
                    ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
                    ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
                    ->join('tbdb_selection_status', 'tbdb_selection_status.id', '=', 'tbdb_selection.selection_status_id')

                    ->select('tbdb_selection.name as selection_name', 'tbdb_selection_status.name as status_name',
                        'tbdb_event_group.name as competition_name', 'tbdb_event.name as event_name', 'tbdb_selection_price.id as id',
                        'tbdb_selection_price.win_odds as win_odds', 'tbdb_selection_price.place_odds as place_odds',
                        'tbdb_selection_price.created_at as created_at', 'tbdb_selection_price.updated_at as updated_at')
                    //->orderBy('tbdb_selection.id', 'DESC')
                    ->skip($limit * ($page - 1))->take($limit)->get();

        $results->totalItems = $this->model->count();
        $results->items = $selectionprices->all();

        return $results;
    }

    /**
     * Single Selections Price with extra details
     * @param $id
     * @return mixed
     */
    public function findWithSelection($id)
    {
        return $this->model->join('tbdb_selection', 'tbdb_selection.id', '=', 'tbdb_selection_price.selection_id')
                ->join('tbdb_market', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
                ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
                ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
                ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
                ->join('tbdb_selection_status', 'tbdb_selection_status.id', '=', 'tbdb_selection.selection_status_id')

            ->select('tbdb_selection.name as selection_name', 'tbdb_selection_status.name as status_name',
                'tbdb_event_group.name as competition_name', 'tbdb_event.name as event_name', 'tbdb_selection_price.id as id',
                'tbdb_selection_price.win_odds as win_odds', 'tbdb_selection_price.place_odds as place_odds')

            ->where('tbdb_selection_price.id', $id)
            ->first();

    }
}