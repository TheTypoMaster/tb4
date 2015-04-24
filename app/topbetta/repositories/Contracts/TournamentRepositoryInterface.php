<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/04/2015
 * Time: 4:28 PM
 */
namespace TopBetta\Repositories\Contracts;

interface TournamentRepositoryInterface
{
    public function updateTournamentByEventGroupId($eventGroupId, $closeDate);
}