<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/08/2015
 * Time: 4:14 PM
 */

namespace TopBetta\Services\Tournaments\Resulting;


interface TournamentPrizeFactory {

    /**
     * Creates a tournament prize
     * @param array $data
     * @return mixed
     */
    public static function make(array $data);
}