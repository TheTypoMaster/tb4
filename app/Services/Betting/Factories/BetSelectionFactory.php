<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/07/2015
 * Time: 11:01 AM
 */

namespace TopBetta\Services\Betting\Factories;

use App;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class BetSelectionFactory {

    public static function make($betType)
    {
        switch ($betType) {
            case BetTypeRepositoryInterface::TYPE_WIN:
            case BetTypeRepositoryInterface::TYPE_PLACE:
            case BetTypeRepositoryInterface::TYPE_EACHWAY:
                return App::make('TopBetta\Services\Betting\BetSelection\RacingBetSelectionService');
            case BetTypeRepositoryInterface::TYPE_SPORT:
                return App::make('TopBetta\Services\Betting\BetSelection\SportBetSelectionService');
            default:
                throw new \InvalidArgumentException("Invalid tournament bet type " . $betType);
        }
    }
}