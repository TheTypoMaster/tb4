<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 13/08/2015
 * Time: 3:16 PM
 */
namespace TopBetta\Repositories\Contracts;

use TopBetta\Models\CompetitionModel;

interface ProductProviderMatchRepositoryInterface
{
    public function getProductAndBetTypeByCompetition(CompetitionModel $competition);
}