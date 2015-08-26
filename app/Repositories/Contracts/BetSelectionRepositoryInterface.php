<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 23/02/2015
 * Time: 12:54 PM
 */
namespace TopBetta\Repositories\Contracts;

interface BetSelectionRepositoryInterface
{
    /**
     * Static function for retrieving an array of selections for an exotic bet
     * This is static for legacy reasons. Should be abstracted to a service.
     * @param $betId
     * @return array
     */
    public static function getUnscratchedExoticSelectionsInPositionForBet($betId, $boxed = false);
}