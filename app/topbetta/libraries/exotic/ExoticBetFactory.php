<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 20/02/2015
 * Time: 4:05 PM
 */

namespace TopBetta\libraries\exotic;


use TopBetta\Bet;

class ExoticBetFactory {

    const

        EXOTIC_TYPE_QUINELLA    = "quinella",

        EXOTIC_TYPE_EXACTA      = "exacta",

        EXOTIC_TYPE_TRIFECTA    = "trifecta",

        EXOTIC_TYPE_FIRST_FOUR  = "firstfour";

    public static function make($betType, $amount, $selections) {

        $exoticBet = null;

        switch($betType) {
            case self::EXOTIC_TYPE_QUINELLA:
                $exoticBet = new ExoticBetQuinella;
                break;
            case self::EXOTIC_TYPE_EXACTA:
                $exoticBet = new ExoticBetExacta;
                break;
            case self::EXOTIC_TYPE_TRIFECTA:
                $exoticBet = new ExoticBetTrifecta;
                break;
            case self::EXOTIC_TYPE_FIRST_FOUR:
                $exoticBet = new ExoticBetFirstfour;
                break;
            default:
                return null;
        }

        $exoticBet->betAmount = $amount;
        $exoticBet->selections = $selections;

        return $exoticBet;
    }
}