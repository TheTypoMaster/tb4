<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/04/2015
 * Time: 1:43 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\ConfigurationModel;
use TopBetta\Repositories\Contracts\ConfigurationRepositoryInterface;

class DbConfigurationRepository extends BaseEloquentRepository implements ConfigurationRepositoryInterface
{

    public function __construct(ConfigurationModel $model)
    {
        $this->model = $model;
    }

    public function getConfigByName($name, $asArray=false)
    {
        $config = $this->model->where('name', $name)->first();

        return json_decode($config->values, $asArray);
    }

    public function getIdByName($name)
    {
        return $this->model->where('name', $name)->value('id');
    }

}