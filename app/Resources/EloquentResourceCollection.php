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