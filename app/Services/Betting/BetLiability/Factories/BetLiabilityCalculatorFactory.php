<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/08/2015
 * Time: 10:23 AM
 */

namespace TopBetta\Services\Betting\BetLiability\Factories;

use App;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class BetLiabilityCalculatorFactory {

    public static function make($betType)
    {
        switch ($betType) {
            case BetTypeRepositoryInterface::TYPE_WIN:
                return App::make('TopBetta\Services\Betting\BetLiability\WinBetLiabilityCalculator');
            case BetTypeRepositoryInterface::TYPE_PLACE:
                return App::make('TopBetta\Services\Betting\BetLiability\PlaceBetLiabilityCalculator');
            case BetTypeRepositoryInterface::TYPE_SPORT:
                return App::make('TopBetta\Services\Betting\BetLiability\SportBetLiabilityCalculator');
        }

        throw new \InvalidArgumentException("Invalid bet type for liability calculation, type: " . $betType);
    }
}