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
}