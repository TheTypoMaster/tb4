<?php namespace TopBetta\Services\Betting;
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 2:05 PM
 */

use TopBetta\Libraries\Maths\Combinatorics;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class MultiBetService {


    /**
     * @var MarketService
     */
    private $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    public static function neededWinners($type)
    {
        switch($type)
        {
            case BetTypeRepositoryInterface::TYPE_TWO_LEG_MULTI:
                return 2;
            case BetTypeRepositoryInterface::TYPE_THREE_LEG_MULTI:
                return 3;
            case BetTypeRepositoryInterface::TYPE_FOUR_LEG_MULTI:
                return 4;
            case BetTypeRepositoryInterface::TYPE_FIVE_LEG_MULTI:
                return 5;
            case BetTypeRepositoryInterface::TYPE_SIX_LEG_MULTI:
                return 6;
            case BetTypeRepositoryInterface::TYPE_SEVEN_LEG_MULTI:
                return 7;
            case BetTypeRepositoryInterface::TYPE_EIGHT_LEG_MULTI:
                return 8;
            case BetTypeRepositoryInterface::TYPE_NINE_LEG_MULTI:
                return 9;
            case BetTypeRepositoryInterface::TYPE_TEN_LEG_MULTI:
                return 10;
        }

        throw new \Exception("Invalid bet type");
    }

    public static function calculateCombinations($type, $selections)
    {
        $neededWinners = self::neededWinners($type);

        return Combinatorics::combinations(count($selections), $neededWinners);
    }

    public static function isBetTypeMulti($type)
    {
        switch($type)
        {
            case BetTypeRepositoryInterface::TYPE_TWO_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_THREE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_FOUR_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_FIVE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_SIX_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_SEVEN_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_EIGHT_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_NINE_LEG_MULTI:
            case BetTypeRepositoryInterface::TYPE_TEN_LEG_MULTI:
                return true;
        }

        return false;
    }

    public function isBetPaying($bet)
    {
        foreach($bet->selection as $selection) {
            if( ! $this->marketService->isMarketPaying($selection->market)) {
                return false;
            }
        }

        return true;
    }
}