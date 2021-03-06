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
    protected $collection;

    /**
     * @var
     */
    private $class;

    public function __construct(Collection $collection, $class)
    {
        $this->collection = $collection->map(function($model) use ($class) {
            return new $class($model);
        });
        $this->class = $class;
    }

    public static function createFromArray($array, $class)
    {
        $collection = new EloquentResourceCollection(new Collection(), $class);

        foreach ($array as $modelArray) {
            $collection->push($class::createResourceFromArray($modelArray, $class));
        }

        return $collection;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setRelations($relationName, $key, $relations)
    {
        //create empty collections for each markets
        $this->collection->each(function($event) use ($relationName) {
            $event->setRelation($relationName, new EloquentResourceCollection(new Collection(), 'TopBetta\Resources\MarketResource'));
        });

        //get dictionary
        $dictionary = $this->collection->getDictionary();

        foreach($relations as $relation) {
            //push each relation onto correct model relation
            $dictionary[$relation->{$key}]->{$relationName}->push($relation);
        }

        return $this;
    }

    public function merge(EloquentResourceCollection $collection)
    {
        $this->collection = $this->collection->merge($collection);

        return $this;
    }

    public function keyBy($key)
    {
        $this->collection = $this->collection->keyBy(function($v) use ($key) { return $v->{$key}; });
        return $this;
    }

    public function values()
    {
        $this->collection = $this->collection->values();
        return $this;
    }

    public function filter(\Closure $callback)
    {
        return $this->newCollection(
            $this->collection->filter($callback)
        );
    }

    public function map(\Closure $callback)
    {
        return $this->newCollection(
            $this->collection->map($callback)
        );
    }

    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    public function toArray()
    {
        return $this->collection->values()->toArray();
    }

    public function toKeyedArray()
    {
        return $this->collection->toArray();
    }

    public function toJson($options = 0)
    {
        return $this->collection->toJson($options);
    }

    public function newCollection($collection)
    {
        $newCollection = new EloquentResourceCollection(new Collection(), $this->class);

        $newCollection->setCollection($collection);

        return $newCollection;
    }

    /**
     * @param Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }



    public function __call($name, $arguments)
    {
        if( method_exists($this, $name) ) {
            return call_user_func_array(array($this, $name), $arguments);
        }

        return call_user_func_array(array($this->collection, $name), $arguments);
    }
}