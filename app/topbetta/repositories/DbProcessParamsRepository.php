<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 3:52 PM
 */

namespace TopBetta\Repositories;



use TopBetta\models\ProcessParamsModel;
use TopBetta\Repositories\Contracts\ProcessParamsRepositoryInterface;

class DbProcessParamsRepository extends BaseEloquentRepository implements ProcessParamsRepositoryInterface
{


    public function __construct(ProcessParamsModel $processParams){
        $this->model = $processParams;
    }


    public function getProcessParamsByName($processName)
    {
        return $this->model->where("process_name", "=", $processName)->first();
    }
}