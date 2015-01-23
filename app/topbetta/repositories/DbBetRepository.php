<?php namespace TopBetta\Repositories; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 10:25
 * Project: tb4
 */

use TopBetta\Models\BetModel;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;

 
class DbBetRepository extends BaseEloquentRepository implements BetRepositoryInterface{

    protected $bet;

    public function __construct(BetModel $bet)
    {
        $this->model = $bet;
    }

    public function getBetWithSelectionsByBetId($betId){
        $details = $this->model->with(array('status', 'type', 'source', 'user', 'result',
                                          //  'selection'
                                            'betselection.selection.market.event.competition.sport',
                                            'betselection.selection.market.markettype'
//                                            'betselection.selection.prices',
//                                            'betselection.selection.result'
                                            ))->where('id', $betId)->first();

//        $details = $this->model->join('tbdb_bet_selection', 'tbdb_bet_selection.bet_id', '=', 'tbdb_bet.id')
//                                ->join('tbdb_selection', 'tbdb_selection.id', '=', 'tbdb_bet_selection.selection_id')
//                                ->join('tbdb_market', 'tbdb_merket.id', '=', 'tbdb_selection.market_id')
//                                ->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_market.event_id')
//                                ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
//                                ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
//                                ->join('tbdb_tournament_sport', 'tbdb_tournament_sport.id', '=', 'tbdb_event_group.sport_id')->get
//


        if(!$details) return null;

        return $details->toArray();
    }

}