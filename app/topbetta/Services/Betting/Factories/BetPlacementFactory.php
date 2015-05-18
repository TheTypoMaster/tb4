<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 3:16 PM
 */

namespace TopBetta\Services\Betting\Factories;

use App;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetPlacement\AbstractBetPlacementService;

class BetPlacementFactory {

    /**
     * Create the bet placement service based on bet type
     * @param $type
     * @return AbstractBetPlacementService
     * @throws \Exception
     */
    public static function make($type)
    {
        switch($type)
        {
            case BetTypeRepositoryInterface::TYPE_WIN:
            case BetTypeRepositoryInterface::TYPE_PLACE:
                return App::make('TopBetta\Services\Betting\BetPlacement\RacingWinPlaceBetPlacementService');
            case BetTypeRepositoryInterface::TYPE_EACHWAY:
                return App::make('TopBetta\Services\Betting\BetPlacement\RacingEachWayBetPlacementService');
            case BetTypeRepositoryInterface::TYPE_QUINELLA:
            case BetTypeRepositoryInterface::TYPE_EXACTA:
            case BetTypeRepositoryInterface::TYPE_TRIFECTA:
            case BetTypeRepositoryInterface::TYPE_FIRSTFOUR:
                return App::make('TopBetta\Services\Betting\BetPlacement\RacingExoticBetPlacementService');
            case BetTypeRepositoryInterface::TYPE_SPORT:
                return App::make('TopBetta\Services\Betting\BetPlacement\SportBetPlacementService');
            case BetTypeRepositoryInterface::TYPE_TWO_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_THREE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_FOUR_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_FIVE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_SIX_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_SEVEN_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_EIGHT_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_NINE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_TEN_LEG_MULTI:
                return App::make('TopBetta\Services\Betting\BetPlacement\MultiBetPlacementService');
            default:
                //TODO: BETTER EXCEPTION
                throw new \Exception;
        }
    }
}