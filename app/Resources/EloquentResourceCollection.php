<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/07/2015
 * Time: 10:58 AM
 */

namespace TopBetta\Resources;

use Illuminate\Database\Eloquent\Collection;

class EloquentResourceCollection implements ResourceCollectionInterface {

    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $collection;

    public function __construct(Collection $collection, $class)
    {
        $this->collection = $collection->map(function($model) use ($class) {
            return new $class($model);
        });
    }

    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    public function toArray()
    {
        return $this->collection->values()->toArray();
    }

    public function toJson($options = 0)
    {
        return $this->collection->toJson($options);
    }

    public function __call($name, $arguments)
    {
        if( method_exists($this, $name) ) {
            return call_user_func_array(array($this, $name), $arguments);
        }

        return call_user_func_array(array($this->collection, $name), $arguments);
    }
}