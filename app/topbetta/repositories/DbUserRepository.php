<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:04
 * Project: tb4
 */

use TopBetta\Models\UserModel;
use TopBetta\Services\Validation\UserBasicValidator;

use TopBetta\Repositories\Contracts\UserRepositoryInterface;

class DbUserRepository extends BaseEloquentRepository implements UserRepositoryInterface {

    protected $model;
    protected $validator;

    public function __construct(UserModel $basicUser,
                                UserBasicValidator $validator)
    {
        $this->model = $basicUser;
        $this->validator = $validator;
    }

    public function createBasicUser($input){
        return $this->create($input);
    }

    public function getUserDetailsFromUsername($username){
        $userDetails = $this->model->where('username', $username)->first();

        if ($userDetails) return $userDetails->toArray();

        return null;
    }

    public function getFullUserDetailsFromUsername($username){
        $userDetails = $this->model->with('topbettauser')->where('username', $username)->first();

        if ($userDetails) return $userDetails->toArray();

        return null;
    }

    public function checkMD5PasswordForUser($username, $password){
        $result = $this->model->where('username', $username)->where('password', md5($password))->first();

        if($result) return $result->toArray();

        return null;
    }


}