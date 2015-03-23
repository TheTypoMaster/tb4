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

    public function __construct(BaseCompetitionModel $baseCompetitionModel)
    {
        $this->model = $baseCompetitionModel;
    }

}