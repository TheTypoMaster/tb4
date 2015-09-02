<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 10:26 AM
 */

namespace TopBetta\Services\Resources\Cache;


abstract class CachedResourceService {

    protected $resourceService;

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->resourceService, $name), $args);
    }
}