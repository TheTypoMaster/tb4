<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 12:05 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\WithdrawalType;
use TopBetta\Repositories\Contracts\WithdrawalTypeRepositoryInterface;

class DbWithdrawalTypeRepository implements WithdrawalTypeRepositoryInterface
{

    /**
     * @var WithdrawalType
     */
    private $model;

    public function __construct(WithdrawalType $model)
    {
        $this->model = $model;
    }

    public function getByName($name)
    {
        return $this->model->where('keyword', $name)->first();
    }
}