<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 28/04/2015
 * Time: 12:41 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\TournamentBuyInTypeModel;
use TopBetta\Repositories\Contracts\TournamentBuyInTypeRepositoryInterface;

class DbTournamentBuyInTypeRepository extends BaseEloquentRepository implements TournamentBuyInTypeRepositoryInterface
{

    public function __construct(TournamentBuyInTypeModel $model)
    {
        $this->model = $model;
    }

    public function getIdByKeyword($keyword)
    {
        return $this->model->where('keyword', $keyword)->value('id');
    }
}