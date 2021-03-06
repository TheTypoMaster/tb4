<?php namespace TopBetta\Repositories; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 10:25
 * Project: tb4
 */

use Carbon\Carbon;
use TopBetta\Models\BetModel;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface;


class DbBetRepository extends BaseEloquentRepository implements BetRepositoryInterface{

    protected $order = array('start_date', 'DESC');

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
            ->join('tbdb_bet_result_status', 'tbdb_bet_result_status.id', '=', 'tbdb_bet.bet_result_status_id')
            ->where('event_id', $event)
            ->where('tbdb_bet_result_status.name', $status);

        if( $type ) {
            $model->where('bet_type_id', $type);
        }

        return $model->get();
    }

    public function getBetsForEventByStatusAndProduct($event, $status, $product, $type = null)
    {
        $model =  $this->model
            ->where('event_id', $event)
            ->where('bet_product_id', $product);

        if (is_array($status)) {
            $model->whereIn('bet_result_status_id', $status);
        } else {
            $model->where('bet_result_status_id', $status);
        }

        if( $type ) {
            $model->where('bet_type_id', $type);
        }

        return $model->get();
    }

	
    public function getBetsForUserByEvent($userId, $eventId, $type = null)
    {
        $model = $this->getBetBuilder()
            ->where('user_id', $userId)
            ->where('b.event_id', $eventId);

        if( $type ) {
            $model->where('bet_type_id', $type);
        }

        return $model->get();
    }

    public function getBetsForUserBySelection($userId, $selection, $type = null)
    {
        $model = $this->model
            ->join('tbdb_bet_selection', 'tbdb_bet_selection.bet_id', '=', 'tbdb_bet.id')
            ->where('user_id', $userId)
            ->where('selection_id', $selection);

        if( $type ) {
            $model->where('bet_type_id', $type);
        }

        return $model->get(array('tbdb_bet.*'));
    }

    public function getAllBetsForUser($user)
    {
        $model = $this->getBetBuilder()
            ->where('b.user_id', $user)
            ->orderBY('e.start_date', 'DESC');


        return $model->paginate();
    }

    public function getUnresultedBetsForUser($user, $page = true)
    {
        $model = $this->getBetBuilder()
            ->where('b.user_id', $user)
            ->where('resulted_flag', false)
            ->orderBY('e.start_date', 'DESC');

        if( $page ) {
            return $model->paginate();
        }

        return $model->get();
    }

    public function getWinningBetsForUser($user)
    {
        $model = $this->getBetBuilder()
            ->where('b.user_id', $user)
            ->where('resulted_flag', true)
            ->where('result_transaction_id', '>', 0)
            ->orderBY('e.start_date', 'DESC');

        return $model->paginate();
    }

    public function getLosingBetsForUser($user)
    {
        $model = $this->getBetBuilder()
            ->where('b.user_id', $user)
            ->where('resulted_flag', true)
            ->where('brs.name', BetResultStatusRepositoryInterface::RESULT_STATUS_PAID)
            ->whereNull('result_transaction_id')
            ->orderBY('e.start_date', 'DESC');

        return $model->paginate();
    }

    public function getRefundedBetsForUser($user)
    {
        $model = $this->getBetBuilder()
            ->where('b.user_id', $user)
            ->where('refunded_flag', true)
            ->orderBY('e.start_date', 'DESC');

        return $model->paginate();
    }

    public function getBetsOnDateForUser($user, Carbon $date, $resulted = null)
    {
        $model = $this->getBetBuilder()
            ->where('b.user_id', $user)
            ->where('e.start_date', '>=', $date->startOfDay()->toDateTimeString())
            ->where('e.start_date', '<=', $date->endOfDay()->toDateTimeString());

        if( ! is_null($resulted) ) {
            $model->where('resulted_flag', $resulted);
        }

        return $model->get();
    }

    public function getBetsForEventGroup($user, $eventGroup)
    {
        return $this->getBetBuilder()
            ->where('b.user_id', $user)
            ->where('eg.id', $eventGroup)
            ->get();
    }

    public function getByResultTransaction($transaction)
    {
        return $this->getBetBuilder()
            ->where('b.result_transaction_id', $transaction)
            ->first();
    }

    public function getByRefundTransaction($transaction)
    {
        return $this->getBetBuilder()
            ->where('b.refund_transaction_id', $transaction)
            ->first();
    }

    public function getByEntryTransaction($transaction)
    {
        return $this->getBetBuilder()
            ->where('b.bet_transaction_id', $transaction)
            ->first();
    }

    public function findBets($bets)
    {
        return $this->getBetBuilder()
            ->whereIn('b.id', $bets)
            ->get();
    }

    protected function getBetBuilder()
    {
        return $this->model
            ->from('tbdb_bet as b')
            ->join('tbdb_bet_product as bp', 'bp.id', '=', 'b.bet_product_id')
            ->leftJoin('tb_product_provider_match as ppm', 'ppm.tb_product_id', '=', 'bp.id')
            ->join('tbdb_bet_type as bt', 'bt.id', '=', 'b.bet_type_id')
            ->join('tbdb_bet_result_status as brs', 'brs.id', '=', 'b.bet_result_status_id')
            ->leftJoin('tbdb_account_transaction as at', 'at.id', '=', 'b.result_transaction_id')
            ->join('tbdb_bet_selection as bs', 'bs.bet_id', '=', 'b.id')
            ->join('tbdb_selection as s', 's.id', '=', 'bs.selection_id')
            ->leftJoin('tbdb_selection_price as sp', function ($q) {
                $q->on('sp.selection_id', '=', 's.id')
                    ->on('sp.bet_product_id', '=', 'b.bet_product_id');
            })
            ->leftJoin('tbdb_selection_result as sr', 'sr.selection_id', '=', 's.id')
            ->join('tbdb_market as m', 'm.id', '=', 's.market_id')
            ->join('tbdb_market_type as mt', 'mt.id', '=', 'm.market_type_id')
            ->join('tbdb_event as e', 'e.id', '=', 'm.event_id')
            ->join('tbdb_event_group_event as ege', 'ege.event_id', '=', 'e.id')
            ->join('tbdb_event_group as eg', 'eg.id', '=', 'ege.event_group_id')
            ->groupBy('b.id')
            ->orderBy($this->order[0], $this->order[1])
            ->select(array(
                'b.id', 'b.bet_amount', 'b.bet_freebet_amount', 's.id as selection_id', 's.name as selection_name', 'm.id as market_id', 'mt.name as market_name',
                'e.id as event_id', 'e.name as event_name', 'eg.id as competition_id', 'eg.name as competition_name', 'brs.name as status', 'at.amount as won_amount',
                'bt.name as bet_type', 'b.selection_string', 'e.start_date as start_date', 'eg.type_code as event_type', 'sp.win_odds as win_odds', 'sp.place_odds as place_odds',
                'sr.win_dividend', 'sr.place_dividend', 's.number as selection_number', 'b.boxed_flag', 'bs.fixed_odds', 'b.percentage as percentage', 'bp.is_fixed_odds as fixed',
                'ppm.provider_product_name', 'bp.id as product_id',
            ));
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