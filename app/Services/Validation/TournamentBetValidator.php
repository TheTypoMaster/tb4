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
        'ticket_id' => 'required'
    );

    protected $createRules = array();

    protected $updateRules = array();
}