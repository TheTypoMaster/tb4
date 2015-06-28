<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 11:32 AM
 */
namespace TopBetta\Repositories\Contracts;

interface BetTypeRepositoryInterface
{
    const TYPE_WIN = 'win';
    const TYPE_PLACE = 'place';
    const TYPE_EACHWAY = 'eachway';

    //exotics
    const TYPE_QUINELLA = 'quinella';
    const TYPE_EXACTA = 'exacta';
    const TYPE_TRIFECTA = 'trifecta';
    const TYPE_FIRSTFOUR = 'firstfour';

    //sport
    const TYPE_SPORT = 'sport';

    //multis
    const TYPE_TWO_LEG_MULTI = 'two_leg_multi';
    const TYPE_THREE_LEG_MULTI = 'three_leg_multi';
    const TYPE_FOUR_LEG_MULTI = 'four_leg_multi';
    const TYPE_FIVE_LEG_MULTI = 'five_leg_multi';
    const TYPE_SIX_LEG_MULTI = 'six_leg_multi';
    const TYPE_SEVEN_LEG_MULTI = 'seven_leg_multi';
    const TYPE_EIGHT_LEG_MULTI = 'eight_leg_multi';
    const TYPE_NINE_LEG_MULTI = 'nine_leg_multi';
    const TYPE_TEN_LEG_MULTI = 'ten_leg_multi';


    public function getBetTypeByName($name);
}