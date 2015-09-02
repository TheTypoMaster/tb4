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
        $details = $this->model->with(array('status', 'type', 'source', 'user', 'refund',
                                            'betselection.selection.price',
                                            'betselection.selection.result',
                                            'betselection.selection.market.event.competition.sport',
                                            'betselection.selection.market.markettype'
                                            ))->where('id', $betId)->first();

        if(!$details) return null;

        return $details->toArray();
    }

    public function getBetDetailsWhenResulted($betId){
        $details = $this->model->with(array('status', 'type', 'source', 'result', 'refund',
            'betselection.selection.price',
            'betselection.selection.result',
            'betselection.selection.market.event.competition.sport',
            'betselection.selection.market.markettype'
        ))->where('id', $betId)->first();

        if(!$details) return null;

        return $details->toArray();
    }

    /**
     * @param $eventId
     * @return null
     */
    public function getUnresultedBetsByEventID($eventId){
        // we only want bets that are "unresulted" status id: 1
        $bets = $this->model->where('event_id', $eventId)
                            ->where('bet_result_status_id', 1)
                            ->where('resulted_flag', 0)
                            ->with('selection')
                            ->get();
        if(!$bets) return null;

        return $bets->toArray();
    }

    public function getBetWithSelectionsAndEventDetailsByBetId($betId){
        $details = $this->model->with(array('type','user', 'user.topbettauser',
            'betselection.selection',
            'betselection.selection.price',
            'betselection.selection.result',
            'betselection.selection.market.event',
            'betselection.selection.market.event.competition',
            'betselection.selection.market.event.competition.sport',
            'betselection.selection.market.markettype'
        ))->where('id', $betId)->first();

        if(!$details) return null;

        return $details->toArray();
    }

    public function getBetsForMarketByStatus($marketId, $status, $type = null)
    {
        return $this->model
            ->join('tbdb_bet_selection', 'tbdb_bet.id', '=', 'tbdb_bet_selection.bet_id')
            ->join('tbdb_selection', 'tbdb_bet_selection.selection_id', '=', 'tbdb_selection.id')
            ->where('tbdb_selection.market_id', $marketId)
            ->where('bet_result_status_id', $status)
            ->groupBy('tbdb_bet.id')
            ->get(array('tbdb_bet.*'));
    }

    public function getBetsForEventByStatus($event, $status, $type = null)
    {
        $model =  $this->model
            ->where('event_id', $event)
            ->where('bet_result_status_id', $status);

        if( $type ) {
            $model->where('bet_type_id', $type);
        }

        return $model->get();
    }

    public function getBetsForSelectionsByBetType($user, $selections, $betType)
    {
        return $this->model
            ->join('tbdb_bet_selection', 'tbdb_bet_selection.bet_id', '=', 'tbdb_bet.id')
            ->whereIn('selection_id', $selections)
            ->where('bet_type_id', $betType)
            ->where('user_id', $user)
            ->groupBy('tbdb_bet.id')
            ->get(array('tbdb_bet.*', 'selection_id'));
    }

    public function getBetsByTypeForEvent($user, $event, $type)
    {
        return $this->model
            ->where('bet_type_id', $type)
            ->where('user_id', $user)
            ->where('event_id', $event)
            ->with('betselection')
            ->get();
    }

    public function getBetsForUserByEvents($user, $events, $type = null)
    {
        $model = $this->model
            ->where('user_id', $user)
            ->whereIn('event_id', $events)
            ->with('betselection');

        if ($type) {
            $model->where('bet_type_id', $type);
        }

        return $model->get();
    }

    public function getBetsForUserByMarket($user, $market, $type = null)
    {
        $model =  $this->model
            ->join('tbdb_bet_selection', 'tbdb_bet_selection.bet_id', '=', 'tbdb_bet.id')
            ->join('tbdb_selection', 'tbdb_bet_selection.selection_id', '=', 'tbdb_selection.id')
            ->where('tbdb_selection.market_id', $market)
            ->where('user_id', $user)
            ->groupBy('tbdb_bet.id');

        if ($type) {
            $model->where('bet_type_id', $type);
        }

        return $model->get(array('tbdb_bet.*'));
    }
}