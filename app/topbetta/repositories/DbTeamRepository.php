<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/03/2015
 * Time: 1:03 PM
 */

namespace TopBetta\Repositories;

use TopBetta\Models\TeamModel;
use TopBetta\Repositories\Contracts\TeamRepositoryInterface;

class DbTeamRepository extends BaseEloquentRepository implements TeamRepositoryInterface
{

    public function __construct(TeamModel $teamModel)
    {
        $this->model = $teamModel;
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