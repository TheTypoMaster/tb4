<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/05/2015
 * Time: 12:07 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Repositories\Contracts\BetSelectionRepositoryInterface;

class DbBetSelectionRepository extends BaseEloquentRepository implements BetSelectionRepositoryInterface
{

    public function __construct(BetSelectionModel $betSelectionModel)
    {
        $this->model = $betSelectionModel;
    }
}