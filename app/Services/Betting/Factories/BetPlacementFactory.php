<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 3:16 PM
 */

namespace TopBetta\Services\Betting\Factories;

use App;
use TopBetta\Models\BetProductModel;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Services\Betting\BetPlacement\AbstractBetPlacementService;

class BetPlacementFactory
{

    /**
     * Create the bet placement service based on bet type
     * @param $type
     * @return AbstractBetPlacementService
     * @throws \Exception
     */
    public static function make($type, $winProduct = null, $placeProduct = null)
    {
        switch ($type) {
            case BetTypeRepositoryInterface::TYPE_WIN:
                $service = App::make('TopBetta\Services\Betting\BetPlacement\RacingWinPlaceBetPlacementService');
                $service->setProduct(BetProductModel::findorFail($winProduct));
                break;
            case BetTypeRepositoryInterface::TYPE_PLACE:
                $service = App::make('TopBetta\Services\Betting\BetPlacement\RacingWinPlaceBetPlacementService');
                $service->setProduct(BetProductModel::findOrFail($placeProduct));
                break;
            case BetTypeRepositoryInterface::TYPE_EACHWAY:
                $service = App::make('TopBetta\Services\Betting\BetPlacement\RacingEachWayBetPlacementService');
                $service->setWinProduct(BetProductModel::findOrFail($winProduct));
                $service->setPlaceProduct(BetProductModel::findOrFail($placeProduct));
                break;
            case BetTypeRepositoryInterface::TYPE_QUINELLA:
            case BetTypeRepositoryInterface::TYPE_EXACTA:
            case BetTypeRepositoryInterface::TYPE_TRIFECTA:
            case BetTypeRepositoryInterface::TYPE_FIRSTFOUR:
                $service = App::make('TopBetta\Services\Betting\BetPlacement\RacingExoticBetPlacementService');
                $service->setProduct(BetProductModel::findOrFail($winProduct));
                break;
            case BetTypeRepositoryInterface::TYPE_SPORT:
                $service = App::make('TopBetta\Services\Betting\BetPlacement\SportBetPlacementService');
                break;
            case BetTypeRepositoryInterface::TYPE_TWO_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_THREE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_FOUR_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_FIVE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_SIX_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_SEVEN_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_EIGHT_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_NINE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_TEN_LEG_MULTI:
                //return App::make('TopBetta\Services\Betting\BetPlacement\MultiBetPlacementService');
                Throw new \Exception("Bet type not supported currently");
            default:
                //TODO: BETTER EXCEPTION
                throw new \Exception;
        }

        $service->setBetType($type);

        return $service;
    }
}

