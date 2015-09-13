<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/08/2015
 * Time: 4:47 PM
 */
namespace TopBetta\Repositories\Contracts;

interface AffiliateEndpointRepositoryInterface
{
    const TYPE_TOURNAMENT_ENTRY = 'tournamententry';
    const TYPE_TOURNAMENT_RESULTS = 'tournamentresults';

    public function getByAffiliateAndType($affiliate, $type);
}