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

    protected $attributes = array(
        "id"              => "id",
        "betType"         => 'bet_type',
        "amount"          => "bet_amount",
        "paid"            => "win_amount",
        "resulted"        => "resulted_flag",
        "selectionId"     => "selection_id",
        "selectionName"   => "selection_name",
        "marketId"        => "market_id",
        "marketType"      => "market_type",
        "eventId"         => "event_id",
        "eventName"       => "event_name",
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
    );


}