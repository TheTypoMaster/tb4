<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/04/2015
 * Time: 11:01 AM
 */

namespace TopBetta\Repositories;


use TopBetta\Repositories\Contracts\TournamentLabelsRepositoryInterface;
use TopBetta\Models\TournamentLabels;

class DbTournamentLabelsRepository extends BaseEloquentRepository implements TournamentLabelsRepositoryInterface
{

    public function __construct(TournamentLabels $model)
    {
        $this->model = $model;
    }
}