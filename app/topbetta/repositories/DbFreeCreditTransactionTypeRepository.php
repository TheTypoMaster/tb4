<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/03/2015
 * Time: 4:34 PM
 */

namespace TopBetta\Repositories;


use TopBetta\models\FreeCreditTransactionTypeModel;
use TopBetta\Repositories\Contracts\FreeCreditTransactionTypeRepositoryInterface;

class DbFreeCreditTransactionTypeRepository extends BaseEloquentRepository implements FreeCreditTransactionTypeRepositoryInterface
{

    public function __construct(FreeCreditTransactionTypeModel $freeCreditTransactionTypeModel)
    {
        $this->model = $freeCreditTransactionTypeModel;
    }

    public function getIdByName($name)
    {
        return $this->model->where('keyword', $name)->first()->id;
    }
}