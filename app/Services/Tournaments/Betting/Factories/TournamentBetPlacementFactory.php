<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 3:31 PM
 */

namespace TopBetta\Services\Tournaments\Betting\Factories;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class TournamentBetPlacementFactory {

    /**
     * @param $betType
     * @return \TopBetta\Services\Tournaments\Betting\BetPlacement\AbstractTournamentBetPlacementService
     */
    public static function make($betType)
    {
        switch($betType) {
            case BetTypeRepositoryInterface::TYPE_WIN:
            case BetTypeRepositoryInterface::TYPE_PLACE:
                return \App::make('TopBetta\Services\Tournaments\Betting\BetPlacement\RacingWinPlaceBetPlacementService');
            case BetTypeRepositoryInterface::TYPE_EACHWAY:
                return \App::make('TopBetta\Services\Tournaments\Betting\BetPlacement\RacingEachWayBetPlacementService');
            case BetTypeRepositoryInterface::TYPE_SPORT:
                return \App::make('TopBetta\Services\Tournaments\Betting\BetPlacement\SportBetPlacementService');
        }

        throw new \InvalidArgumentException("Invalid bet type");
    }
}