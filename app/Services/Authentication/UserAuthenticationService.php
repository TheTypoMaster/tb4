<?php namespace TopBetta\Services\Authentication; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 21/01/15
 * File creation time: 14:12
 * Project: tb4
 */

use TopBetta\Repositories\Contracts\UserRepositoryInterface;
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

}