<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/07/2015
 * Time: 11:57 AM
 */

namespace TopBetta\Resources\Betting;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Resources\AbstractEloquentResource;

class BetResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id'               => 'id',
        'amount'           => 'bet_amount',
        'freeCreditAmount' => 'bet_freebet_amount',
        'selectionId'      => 'selection_id',
        'selectionName'    => 'selection_name',
        'selectionString'  => 'selection_string',
        'marketName'       => 'market_name',
        'marketId'         => 'market_id',
        'eventId'          => 'event_id',
        'eventName'        => 'event_name',
        'competitionId'    => 'competition_id',
        'competitionName'  => 'competition_name',
        'betType'          => 'bet_type',
        'status'           => 'status',
        'paid'             => 'won_amount',
        'date'             => 'start_date',
    );


    public function paid()
    {
        return  ! is_null($this->model->won_amount) ? $this->model->won_amount : 0;
    }

    public function selectionName()
    {
        return ! $this->isexotic() ? $this->model->selection_name : null;
    }

    public function selectionId()
    {
        return ! $this->isExotic() ? $this->model->selection_id : null;
    }

    public function selectionString()
    {
        return $this->isExotic() ? $this->model->selection_string : null;
    }

    public function isExotic()
    {
        return in_array($this->betType, array(
            BetTypeRepositoryInterface::TYPE_QUINELLA,
            BetTypeRepositoryInterface::TYPE_EXACTA,
            BetTypeRepositoryInterface::TYPE_TRIFECTA,
            BetTypeRepositoryInterface::TYPE_FIRSTFOUR
        ));
    }
}