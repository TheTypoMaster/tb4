<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/09/2015
 * Time: 1:22 PM
 */

namespace TopBetta\Repositories\Cache\Users;


use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use TopBetta\Jobs\Pusher\UserSocketUpdate;
use TopBetta\Repositories\Cache\CachedResourceRepository;

class UserRepository extends CachedResourceRepository {

    const CACHE_KEY_PREFIX = 'users_';

    protected $resourceClass = 'TopBetta\Resources\UserResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $tags = array("users");

    public function getUser($id)
    {
        return $this->get($this->cachePrefix . $id);
    }

    public function addAccountBalance($user, $amount)
    {
        if ($resource = $this->getUser($user)) {
            $resource->addAccountBalance($amount);
            \Bus::dispatch(new UserSocketUpdate($resourceArray = $resource->toArray()));
            $this->put($this->cachePrefix . $resource->id, $resourceArray, $this->getModelCacheTime($resource));
        }
    }

    public function addFreeCreditBalance($user, $amount)
    {
        if ($resource = $this->getUser($user)) {
            $resource->addFreeCreditBalance($amount);
            \Bus::dispatch(new UserSocketUpdate($resourceArray = $resource->toArray()));
            $this->put($this->cachePrefix . $resource->id, $resourceArray, $this->getModelCacheTime($resource));
        }
    }

    public function makeUserResource($model)
    {
        $user = $this->createResource($model);

        $this->put($this->cachePrefix . $model->id, $user->toArray(), $this->getModelCacheTime($model));

        return $user;
    }

    protected function getModelCacheTime($model)
    {
        return Carbon::now()->addMonth()->diffInMinutes();
    }
}