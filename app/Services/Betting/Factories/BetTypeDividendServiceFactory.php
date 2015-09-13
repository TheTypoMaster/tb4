<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 19/05/2015
 * Time: 3:54 PM
 */

namespace TopBetta\Services\Betting\Factories;

use App;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class BetTypeDividendServiceFactory {

    public static function make($type)
    {
        switch($type) {

            case BetTypeRepositoryInterface::TYPE_WIN:
            case BetTypeRepositoryInterface::TYPE_SPORT:
            case BetTypeRepositoryInterface::TYPE_PLACE:
            return App::make('TopBetta\Services\Betting\BetDividend\BetTypeDividend\SingleSelectionBetTypeDividendService');
            case BetTypeRepositoryInterface::TYPE_QUINELLA:
            case BetTypeRepositoryInterface::TYPE_EXACTA:
            case BetTypeRepositoryInterface::TYPE_TRIFECTA:
            case BetTypeRepositoryInterface::TYPE_FIRSTFOUR:
                return App::make('TopBetta\Services\Betting\BetDividend\BetTypeDividend\ExoticBetTypeDividendService');
            case BetTypeRepositoryInterface::TYPE_TWO_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_THREE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_FOUR_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_FIVE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_SIX_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_SEVEN_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_EIGHT_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_NINE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_TEN_LEG_MULTI:
                //return App::make('TopBetta\Services\Betting\BetDividend\BetTypeDividend\MultiBetTypeDividendService');
                throw new \Exception("Invalid Bet Type");
        }

    }
}