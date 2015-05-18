<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/05/2015
 * Time: 12:09 PM
 */

namespace TopBetta\Services\Betting\Factories;


use Symfony\Component\Debug\Exception\ClassNotFoundException;
use TopBetta\libraries\exotic\ExoticBet;
use TopBetta\libraries\exotic\ExoticBetExacta;
use TopBetta\libraries\exotic\ExoticBetFirstfour;
use TopBetta\libraries\exotic\ExoticBetQuinella;
use TopBetta\libraries\exotic\ExoticBetTrifecta;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class ExoticBetLibraryFactory {

    /**
     * Creates the exotic bet library based on type
     * @param $type
     * @param int $amount
     * @param array $selections
     * @return ExoticBet
     * @throws ClassNotFoundException
     */
    public static function make($type, $amount = 0, $selections = array())
    {
        switch ($type) {
            case BetTypeRepositoryInterface::TYPE_QUINELLA:
                $library = new ExoticBetQuinella;
                break;
            case BetTypeRepositoryInterface::TYPE_EXACTA:
                $library = new ExoticBetExacta;
                break;
            case BetTypeRepositoryInterface::TYPE_TRIFECTA:
                $library = new ExoticBetTrifecta;
                break;
            case BetTypeRepositoryInterface::TYPE_FIRSTFOUR:
                $library = new ExoticBetFirstfour;
                break;
            default:
                throw new ClassNotFoundException("Library not found", null);
        }

        $library->selections = $selections;
        $library->betAmount = $amount;

        return $library;
    }
}