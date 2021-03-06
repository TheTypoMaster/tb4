<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/02/2015
 * Time: 4:20 PM
 */
namespace TopBetta\Repositories\Contracts;

interface EventModelRepositoryInterface
{
    public function setDisplayFlagForEvent($eventId, $displayFlag);

    public function setFixedOddsFlagForEvent($eventId, $fixedOddsFlag);

    public function getAllSportEvents($paged = false);

    public function searchSportEvents($term, $paged = false);
}