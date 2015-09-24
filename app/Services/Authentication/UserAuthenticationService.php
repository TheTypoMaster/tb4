<?php namespace TopBetta\Services\Authentication; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 21/01/15
 * File creation time: 14:12
 * Project: tb4
 */

use Regulus\ActivityLog\Models\Activity;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Services\Exceptions\UnauthorizedAccessException;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class UserAuthenticationService {

    protected $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    public function checkMD5PasswordUser($username, $password)
    {
        $userDetails = $this->user->checkMD5PasswordForUser($username, $password);

        if(!$userDetails) throw new ValidationException("Validation Failed", 'Login details incorrect');

        return $userDetails;

    }

    public function login($input)
    {
        $rules = array('username' => 'required', 'password' => 'required');

        $validator = \Validator::make($input, $rules);
        //return $this->response->failed($validator->messages()->all(), 400, 101, 'User Login Failed', 'User Login Failed - check errors');
        if ($validator->fails()) {
            throw new ValidationException("Validation exception", $validator->errors());
        }

        // topbetta currently has MD5 hashed passwords
        try{
            $userDetails = $this->checkMD5PasswordUser($input['username'], $input['password']);
        }catch (ValidationException $e){
            throw new UnauthorizedAccessException("Login failed. Please check username and password");
        }

        if( ! $userDetails['activated_flag'] ) {
            throw new UnauthorizedAccessException("Account is not activated");
        }

        $user = \Auth::loginUsingId($userDetails['id']);

        //  $ua = $this->clientDetails->getBrowser();
        //  $user_details = "Browser: " . $ua['name'] . ", Version: " . $ua['version'] . ", Platform: " .$ua['platform'] . ", User Agent:" . $ua['userAgent'];

        if (\Auth::check()) {
            // record the logout to the activity table
            Activity::log([
                'contentId'   => \Auth::user()->id,
                'contentType' => 'User',
                'action'      => 'Log In',
                'description' => 'User logged into TopBetta',
                'details'     => 'Username: ' . \Auth::user()->username, //. ' - '.$user_details,
                //'updated'     => $id ? true : false,
            ]);
        }

        return $user->setRelation('topbettauser', $user->getModel()->topbettauser);
    }

}