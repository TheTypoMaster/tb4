<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 14:06
 * Project: tb4
 */

use TopBetta\Models\UserTokenModel;
use TopBetta\Repositories\Contracts\UserTokenRepositoryInterface;

class DbUserTokenRepository extends BaseEloquentRepository implements UserTokenRepositoryInterface{

    protected $usertoken;

    public function __construct(UserTokenModel $usertoken)
    {
        $this->model = $usertoken;
    }

    public function getTokenRecordByUserId($userID){
        $record = $this->model->where('user_id', $userID)->first();

        if($record) return $record->toArray();

        return false;
    }
}