<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/1/15
 * File creation time: 21:04
 * Project: tb4
 */

use TopBetta\Models\TopbettaUserModel;
use TopBetta\Services\Validation\UserFullValidator;

use TopBetta\Repositories\Contracts\UserTopbettaRepositoryInterface;

class DbUserTopbettaRepository extends BaseEloquentRepository implements UserTopbettaRepositoryInterface {

    protected $model;
    protected $validator;

    public function __construct(TopBettaUserModel $fullUser,
                                UserFullValidator $validator)
    {
        $this->model = $fullUser;
        $this->validator = $validator;
    }

    public function createFullUser($input){
        return $this->create($input);
    }

    public function getUserDetailsFromUserId($userId){
        $userDetails = $this->model->where('user_id', $userId)->first();

        if ($userDetails) return $userDetails->toArray();

        return false;
    }

    public function updateBalanceToTurnOver($userId, $amount)
    {
        $user = $this->model->where('user_id', $userId)->first();

        $user->balance_to_turnover = max($user->balance_to_turnover + $amount, 0);

        return $user->save();
    }

}