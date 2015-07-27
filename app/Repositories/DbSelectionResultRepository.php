<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 19:10
 * Project: tb4
 */

use TopBetta\Models\SelectionResultModel;
use TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface;

class DbSelectionResultRepository extends BaseEloquentRepository implements SelectionResultRepositoryInterface{

    protected $selectionresults;

    public function __construct(SelectionResultModel $selectionresults)
    {
        $this->model = $selectionresults;
    }

    public function getResultForSelectionId($selectionId)
    {
        return $this->model->where('selection_id', $selectionId)->first();
    }

    public function deleteResultsForRaceId($raceId) {

        //$this->model->join
        return \DB::statement('DELETE sr.* FROM tbdb_selection_result as sr INNER JOIN tbdb_selection as s on s.id = selection_id INNER JOIN tbdb_market as mk on mk.id = s.market_id INNER JOIN tbdb_event as e on e.id = mk.event_id WHERE e.id = '. $raceId);

    }

    public function deleteResultsForMarket($marketId)
    {
        return \DB::statement('DELETE sr.* FROM tbdb_selection_result as sr INNER JOIN tbdb_selection as s on s.id = selection_id INNER JOIN tbdb_market as mk on mk.id = s.market_id WHERE mk.id = '. $marketId);
    }
} 