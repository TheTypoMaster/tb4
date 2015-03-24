<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 3:48 PM
 */

namespace TopBetta\Repositories;

use TopBetta\Models\CompetitionRegionModel;
use TopBetta\Repositories\Contracts\CompetitionRegionRepositoryInterface;

class DbCompetitionRegionRepository extends BaseEloquentRepository implements CompetitionRegionRepositoryInterface
{

    public function __construct(CompetitionRegionModel $competitionRegionModel)
    {
        $this->model = $competitionRegionModel;
    }

    public function search($searchTerm)
    {
        return $this->model
            ->where('name', 'LIKE', "%$searchTerm%")
            ->orWhere('short_name', 'LIKE', "%$searchTerm%")
            ->orWhere('default_name', 'LIKE', "%$searchTerm%")
            ->get();
    }
}