<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/08/2015
 * Time: 9:17 AM
 */

namespace TopBetta\Services\Betting\BetLiability;


interface LiabilityCalculator {

    /**
     * Returns the max liability for a given bet
     * @param $betData
     * @return int
     */
    public function calculateLiability($betData);
}