<?php namespace TopBetta\Services\UserAccount;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:49
 * Project: tb4
 */

use Carbon\Carbon;

use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Repositories\Contracts\UserTopBettaRepositoryInterface;


class UserAccountService {

    protected $basicUser;
    protected $fullUser;

    function __construct(UserRepositoryInterface $basicUser,
                         UserTopbettaRepositoryInterface $fullUser)
    {
        $this->basicUser = $basicUser;
        $this->fullUser = $fullUser;
    }


    public function createTopbettaUserAccount($input){

        // set some other required fields

        $basicData = array();
        if(isset($input['username'])) $basicData['username'] = $input['username'];
        if(isset($input['email'])) $basicData['email'] = $input['email'];
        if(isset($input['password'])) $basicData['password'] = $input['password'];
        if(isset($input['parent_user_id'])) $basicData['parent_user_id'] = $input['parent_user_id'];
        $basicData['name'] = $input['first_name'].' '.$input['last_name'];
        $basicData['usertype'] = 'Registered';
        $basicData['gid'] = '18';
        $basicData['registerDate'] = Carbon::now();
        $basicData['lastVisitDate'] = Carbon::now();


        // create the basic user record
        $basic = $this->createBasicAccount($basicData);

        // get the user id of the new account
        $input['user_id'] = $basic['id'];

        unset($input['username'], $input['email'], $input['password']);

        if(isset($input['parent_user_id'])) unset($input['parent_user_id']);

        // dd($input);
        // create the full account record
        $full = $this->createFullAccount($input);

        return array_merge($basic, $full);

    }


    public function createBasicAccount($data){
        return $this->basicUser->create($data);
    }

    public function createFullAccount($data){
        return $this->fullUser->create($data);
    }



}