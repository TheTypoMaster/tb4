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
        "buy_in" => "required|numeric",
        "entry_fee" => "required|numeric",
        "start_currency" => "numeric",
        "minimum_prize_pool" => "numeric",
        "tournament_prize_format" => "required|exists:tbdb_tournament_prize_format,id",
        "start_date" => "required",
        "end_date" => "required",

        //rebuy info
        "rebuys" => "min:0",
        "rebuy_currency" => "numeric|required_with:rebuys",
        "rebuy_buyin" => "numeric|required_with:rebuys",
        "rebuy_entry" => "numeric|required_with:rebuys",
        "rebuy_end" => "required_with:rebuys",

        //topup info
        "topups" => "min:0",
        "topup_currency" => "numeric|required_with:topups",
        "topup_entry" => "numeric|required_with:topups",
        "topup_buyin" => "numeric|required_with:topups",
        "topup_start_date" => "required_with:topups",
        "topup_end_date" => "required_with:topups",
    );

    protected $createRules = array();

    protected $updateRules = array();
}