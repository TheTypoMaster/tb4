<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/04/2015
 * Time: 10:05 AM
 */

namespace TopBetta\Repositories;

use TopBetta\models\TournamentBuyInModel;
use TopBetta\Repositories\Contracts\TournamentBuyInRepositoryInterface;

class DbTournamentBuyInRepository extends BaseEloquentRepository implements TournamentBuyInRepositoryInterface
{

    public function __construct(TournamentBuyInModel $model)
    {
        $this->model = $model;
    }

    public function findAll()
    {
        return $this->model->orderBy('buy_in')->orderBy('entry_fee')->get();
    }
}