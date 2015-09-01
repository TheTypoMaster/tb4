<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 9:49 AM
 */
namespace TopBetta\Repositories\Contracts;

interface AffiliateRepositoryInterface
{
    public function getByCodeOrFail($code);

    public function getAffiliatesInTournamentByTypes($tournament, $types);
}