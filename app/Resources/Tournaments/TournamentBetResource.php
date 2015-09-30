<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 9:28 AM
 */

namespace TopBetta\Resources\Tournaments;


use TopBetta\Resources\AbstractEloquentResource;
use TopBetta\Resources\Betting\BetResource;

class TournamentBetResource extends BetResource
{
    protected static $modelClass = 'TopBetta\Models\TournamentBetModel';

    protected $attributes = array(
        "id"              => "id",
        "betType"         => 'bet_type',
        "amount"          => "bet_amount",
        "paid"            => "win_amount",
        "resulted"        => "resulted_flag",
        "selectionId"     => "selection_id",
        "selectionName"   => "selection_name",
        "selectionNumber" => "selection_number",
        "marketId"        => "market_id",
        "marketType"      => "market_type",
        "eventId"         => "event_id",
        "eventName"       => "event_name",
        "eventStatus"     => "event_status",
        'selectionString' => 'selection_string',
        'competitionId'   => 'competition_id',
        'competitionName' => 'competition_name',
        'status'          => 'status',
        'odds'            => 'odds',
        'productId'       => 'product_id',
        'productCode'     => 'provider_product_name',
        'isExotic'        => 'isExotic',
        'isFixed'         => 'isFixed',
        'boxedFlag'       => 'boxed',
        'dividend'        => 'dividend',
        'percentage'      => 'percentage',
        'eventType'       => 'eventType',
        'date'            => 'start_date',
        'tournamentId'    => 'tournamentId',
        'tournamentTicketId' => 'tournament_ticket_id',
    );

    public function paid()
    {
        if (! is_null($this->model->paid)) {
            return $this->model->paid;
        }

        return  ! is_null($this->model->win_amount) ? $this->model->win_amount : 0;
    }

    public function getTournamentId()
    {
        if ($this->model->tournament_id) {
            return $this->model->tournament_id;
        }

        return $this->model->ticket->tournament_id;
    }


    public function __isset($name)
    {
        if ($name == 'eventId') {
            return true;
        }

        return false;
    }
}