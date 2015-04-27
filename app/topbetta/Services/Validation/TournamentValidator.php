<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/04/2015
 * Time: 11:35 AM
 */

namespace TopBetta\Services\Validation;


class TournamentValidator extends Validator {

    public $rules = array(
        "tournament_sport_id" => "required|exists:tbdb_tournament_sport,id",
        "event_group_id" => "required|exists:tbdb_event_group,id",
        "buy_in" => "numeric",
        "entry_fee" => "numeric",
        "start_currency" => "numeric",
        "minimum_prize_pool" => "numeric",
        "tournament_prize_format" => "required|exists:tbdb_tournament_prize_format,id"
    );

    protected $createRules = array();

    protected $updateRules = array();
}