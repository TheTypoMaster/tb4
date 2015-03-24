<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 20/03/2015
 * Time: 4:05 PM
 */

namespace TopBetta\Repositories;

use TopBetta\Models\BaseCompetitionModel;
use TopBetta\Repositories\Contracts\BaseCompetitionRepositoryInterface;

class DbBaseCompetitionRepository extends BaseEloquentRepository implements BaseCompetitionRepositoryInterface
{
    protected $order = array('name', 'ASC');

    public function __construct(BaseCompetitionModel $baseCompetitionModel)
    {
        $this->model = $baseCompetitionModel;
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