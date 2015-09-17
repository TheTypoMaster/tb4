<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/09/2015
 * Time: 2:03 PM
 */

namespace TopBetta\Auth;


use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use TopBetta\Models\UserModel;
use TopBetta\Repositories\Cache\Users\UserRepository;

class CacheUserProvider implements UserProvider {

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EloquentUserProvider
     */
    private $eloquentUserProvider;

    public function __construct(EloquentUserProvider $eloquentUserProvider)
    {
        $this->userRepository = \App::make('TopBetta\Repositories\Cache\Users\UserRepository');
        $this->eloquentUserProvider = $eloquentUserProvider;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $user = $this->userRepository->getUser($identifier);

        if (!$user) {
            $resource = $this->userRepository->makeUserResource($this->eloquentUserProvider->retrieveById($identifier));
            return $resource;
        }

        return $user;
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed   $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return $this->eloquentUserProvider->retrieveByToken($identifier, $token);
    }


    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $userModel = new UserModel(array("id" => $user->id, "remember_token" => $token));
        $userModel->exists = true;
        $userModel->save();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return $this->eloquentUserProvider->retrieveByCredentials($credentials);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->validateCredentials($user, $credentials);
    }
}