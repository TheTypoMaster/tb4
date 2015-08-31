<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 3:31 PM
 */

namespace TopBetta\Services\Tournaments\Betting\Factories;


use TopBetta\Models\BetProductModel;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class TournamentBetPlacementFactory {

    /**
     * @param $betType
     * @return \TopBetta\Services\Tournaments\Betting\BetPlacement\AbstractTournamentBetPlacementService
     */
    public static function make($betType, $winProduct = null, $placeProduct = null)
    {
        switch($betType) {
            case BetTypeRepositoryInterface::TYPE_WIN:
                $service = \App::make('TopBetta\Services\Tournaments\Betting\BetPlacement\RacingWinPlaceBetPlacementService');
                $service->setProduct(BetProductModel::findOrFail($winProduct))->setBetType($betType);
                return $service;
            case BetTypeRepositoryInterface::TYPE_PLACE:
                $service = \App::make('TopBetta\Services\Tournaments\Betting\BetPlacement\RacingWinPlaceBetPlacementService');
                $service->setProduct(BetProductModel::findOrFail($placeProduct))->setBetType($betType);
                return $service;
            case BetTypeRepositoryInterface::TYPE_EACHWAY:
                $service = \App::make('TopBetta\Services\Tournaments\Betting\BetPlacement\RacingEachWayBetPlacementService');
                $service->setWinProduct(BetProductModel::findOrFail($winProduct))->setBetType($betType)
                    ->setPlaceProduct(BetProductModel::findOrFail($placeProduct));
                return $service;
            case BetTypeRepositoryInterface::TYPE_SPORT:
                return \App::make('TopBetta\Services\Tournaments\Betting\BetPlacement\SportBetPlacementService')->setBetType($betType);
        }

        throw new \InvalidArgumentException("Invalid bet type");
    }
}