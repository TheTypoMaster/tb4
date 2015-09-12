<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/06/2015
 * Time: 2:24 PM
 */

namespace TopBetta\Repositories;

use TopBetta\Models\BetLimitUserModel;
use TopBetta\Repositories\Contracts\BetLimitRepositoryInterface;

class DbBetLimitRepository extends BaseEloquentRepository implements BetLimitRepositoryInterface
{

    public function __construct(BetLimitUserModel $model)
    {
        $this->model = $model;
    }

    public function getLimitForUserAndBetType($user, $betType, $limitType='bet_type')
    {
        $amount =  $this->model
            ->join('tbdb_bet_limit_types', 'tbdb_bet_limit_users.bet_limit_type_id', '=', 'tbdb_bet_limit_types.id')
            ->where('user_id', $user)
            ->where('value', $betType)
            ->where('name', $limitType)
            ->first(array(\DB::raw('MIN(amount) as amount')));

        return $amount->amount;
    }
}