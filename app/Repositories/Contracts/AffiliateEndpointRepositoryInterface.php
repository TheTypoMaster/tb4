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

    public function getByAffiliateAndType($affiliate, $type);
}