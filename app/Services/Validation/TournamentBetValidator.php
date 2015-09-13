<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 3:15 PM
 */

namespace TopBetta\Services\Validation;


class TournamentBetValidator extends Validator {

    protected $rules = array(
        'amount' => 'required|numeric',
        'bet_type' => 'required',
        'selections' => 'required',
        'ticket_id' => 'required',
        'win_product' => 'required_without:place_product',
        'place_product' => 'required_without:win_product',
    );

    protected $createRules = array();

    protected $updateRules = array();
}