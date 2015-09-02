<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/06/2015
 * Time: 3:58 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\BetLimitTypeModel;
use TopBetta\Repositories\Contracts\BetLimitTypeRepositoryInterface;

class DbBetLimitTypeRepository extends BaseEloquentRepository implements BetLimitTypeRepositoryInterface
{

    public function __construct(BetLimitTypeModel $model)
    {
        $this->model = $model;
    }

    public function getByName($name)
    {
        return $this->model->where('name', $name)->get();
    }

    public function getLimitForUser($user, $betType, $limitType)
    {
        return $this->model
            ->leftJoin('tbdb_bet_limit_users as blu', function($q) use ($user) {
                $q->on('blu.bet_limit_type_id', '=', 'tbdb_bet_limit_types.id')
                    ->on('blu.user_id','=', \DB::raw($user));
            })
            ->where('tbdb_bet_limit_types.value', $betType)
            ->where('name', $limitType)
            ->orderBy('blu.amount')
            ->first();
    }
}