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
            default:
                //TODO: BETTER EXCEPTION
                throw new \Exception;
        }
    }
}