<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:04
 * Project: tb4
 */

use TopBetta\Models\UserModel;
use TopBetta\Models\TopBettaUserModel;

use TopBetta\Repositories\Contracts\UserRepositoryInterface;

class DbUserRepository extends BaseEloquentRepository implements UserRepositoryInterface{

    protected $user;

    public function __construct(UserModel $user)
    {
        $this->model = $user;
    }

    public function createBasicUser($input){

        // validation
        $rules = array('user_name' => 'required',
                        'first_name' => 'required',
                        'last_name' => 'required',
                        'email' => 'required|email',
                        'password' => 'required',
                        'mobile' => 'required',
                        'source' => 'required',
                        'optbox' => 'required',
                        'btag' => '');

//        // Get user registration details from post.
//        $username	= JRequest::getString('username', null, 'post');
//        $first_name	= JRequest::getString('first_name', null, 'post');
//        $last_name	= JRequest::getString('last_name', null, 'post');
//        $email		= JRequest::getString('email', null, 'post');
//        $email2		= JRequest::getString('email', null, 'post');
//        $password	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
//        $password2	= JRequest::getString('password', null, 'post', JREQUEST_ALLOWRAW);
//        $mobile		= JRequest::getString('mobile', null, 'post');
//        $source		= JRequest::getString('source', null, 'post');
//        $optbox		= JRequest::getVar('optbox', null, 'post');
//        $btag		= JRequest::getString('btag', 'kx8FbVSXTgEWqcfzuvZcQGNd7ZgqdRLk', 'post');

    }

    public function createFullUser($input){



    }

    public function getUserDetailsFromUsername($username){
        $userDetails = $this->model->where('username', $username)->first();

        if ($userDetails) return $userDetails->toArray();

        return false;
    }

}