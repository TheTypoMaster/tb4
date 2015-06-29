<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/06/2015
 * Time: 2:45 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\TrainerModel;
use TopBetta\Repositories\Contracts\TrainerRepositoryInterface;

class DbTrainerRepository extends BaseEloquentRepository implements TrainerRepositoryInterface
{

    public function __construct(TrainerModel $model)
    {
        $this->model = $model;
    }

    public function getByExternalId($externalId)
    {
        return $this->model->where('external_trainer_id', $externalId)->first();
    }
}