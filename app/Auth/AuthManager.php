<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/09/2015
 * Time: 2:10 PM
 */

namespace TopBetta\Auth;


use Illuminate\Auth\Guard;

class AuthManager extends \Illuminate\Auth\AuthManager {

    /**
     * @return Guard
     */
    public function createCacheDriver()
    {
        $provider = $this->createCacheProvider();

        return new Guard($provider, $this->app['session.store']);
    }

    /**
     * @return \TopBetta\Auth\CacheUserProvider
     */
    public function createCacheProvider()
    {
        $eloquentProvider = $this->createEloquentProvider();

        return new CacheUserProvider($eloquentProvider);
    }
}