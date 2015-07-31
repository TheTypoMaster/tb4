<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/05/2015
 * Time: 1:22 PM
 */

namespace TopBetta\Services\Betting\Exceptions;

use Lang;

class BetLimitExceededException extends BetPlacementException {

    public function __construct($data, $selection = null)
    {
        $message = $this->constructMessage($data, $selection);
        parent::__construct($message);
    }

    public function constructMessage($data, $selection = null)
    {
        if(array_get($data, 'betValueLimit', null)) {
            $data['betValueLimit'] = number_format($data['betValueLimit']/100, 2);
        }

        if( array_get($data, 'betValueLimit', null) && array_get($data, 'flexiLimit', null) ) {
            return Lang::get('bets.exceed_bet_limit_value_and_flexi', $data);
        } else if( array_get($data, 'flexiLimit', null) ) {
            return Lang::get('bets.exceed_bet_limit_flexi', $data);
        } else if( array_get($data, 'userBetLimit', null) ) {
            return Lang::get('bets.exceed_user_bet_limit', array("userBetLimit" => '$' . number_format($data['userBetLimit']/100,2)));
        }

        return Lang::get('bets.exceed_bet_limit_value', $data);
    }
}