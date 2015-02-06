<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/02/2015
 * Time: 12:46 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Repositories\Contracts\PoliTransactionRepositoryInterface;
use TopBetta\Models\PoliTransactionModel;

class DbPoliTransactionRepository extends BaseEloquentRepository implements PoliTransactionRepositoryInterface{

    public function __construct(PoliTransactionModel $poliTransaction) {
        $this->model= $poliTransaction;
    }
}