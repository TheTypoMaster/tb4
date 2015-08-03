<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 9:28 AM
 */

namespace TopBetta\Resources\Tournaments;


use TopBetta\Resources\AbstractEloquentResource;

class TournamentBetResource extends AbstractEloquentResource
{

    protected $attributes = array(
        "id"            => "id",
        "betType"       => 'bet_type',
        "amount"        => "bet_amount",
        "winAmount"     => "win_amount",
        "fixedOdds"     => "fixed_odds",
        "resulted"      => "resulted_flag",
        "selectionId"   => "selection_id",
        "selectionName" => "selection_name",
        "marketId"      => "market_id",
        "marketType"    => "market_type",
        "eventId"       => "event_id",
        "eventName"     => "event_name",
    );

    protected $types = array(
        "id"       => "int",
        "resulted" => "bool"
    );

}