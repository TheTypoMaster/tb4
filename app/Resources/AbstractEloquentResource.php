<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 8/07/2015
 * Time: 2:57 PM
 */

namespace TopBetta\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractEloquentResource implements ResourceInterface {

    /**
     * @var Model
     */
    protected $model;

    protected $attributes = array();

    protected $relations = array();

    protected $loadIfRelationExists = array();

    protected static $defaultRelations = array();

    protected static $modelClass;

    protected $types = array(
        "id" => "int"
    );

    public function __construct(Model $model = null)
    {
        if ($model) {
            $this->model = $model;
            $this->initialize();
        }
    }

    public static function getDefaultRelations()
    {
        return self::$defaultRelations;
    }

    public static function createResourceFromArray($array, $resource = null)
    {
        $resource = new static;
        $resource->setModel(new static::$modelClass($array));
        $resource->loadExistingRelations();

        return $resource;
    }

    public function loadExistingRelations()
    {
        foreach( $this->loadIfRelationExists as $key => $attribute ) {
            if( $this->checkKey($key) ) {
                $this->loadRelation($attribute);
            }
       }
    }

    public function toArray()
    {
        $array = array();

        foreach( $this->attributes as $attribute => $modelAttribute ) {
            $array[snake_case($attribute)] = $this->load($attribute);
        }

        foreach( $this->relations as $name => $relation ) {
            $array[snake_case($name)] = $relation ? $relation->toArray() : null;
        }

        return $array;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }

    public function setRelation($relation, $object)
    {
        $this->relations[$relation] = $object;

        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function __get($name)
    {
        return $this->load($name);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->model, $name), $arguments);
    }

    protected function item($name, $class, $model)
    {
        if( ! array_get($this->relations, $name) ) {
            $array = object_get($this->model, $name) ? : object_get($this->model, snake_case($name));
            if (is_array($array)) {
                $this->relations[$name] = $class::createResourceFromArray($array, $class);
                return $this->relations[$name];
            }

            if (is_string($model)) {
                $model = object_get($this->model, $model);
            }

            if( ! $model ) {
                return null;
            }

            $this->relations[$name] = new $class($model);
        }

        return $this->relations[$name];
    }

    protected function collection($name, $class, $collection)
    {
        if( ! array_get($this->relations, $name) ) {
            $array = object_get($this->model, $name) ? : object_get($this->model, snake_case($name));

            if (is_array($array)) {
                $this->relations[$name] = EloquentResourceCollection::createFromArray($array, $class);
                return $this->relations[$name];
            }

            if (is_string($collection)) {
                $collection = object_get($this->model, $collection);
            }

            if( ! $collection ) {
                return new EloquentResourceCollection(new Collection(), $class);
            }

            $this->relations[$name] = new EloquentResourceCollection($collection, $class);
        }

        return $this->relations[$name];
    }

    protected function initialize()
    {
        foreach( $this->loadIfRelationExists as $key => $attribute ) {

            if( $this->checkKey($key) ) {
                $this->loadRelation($attribute);
            }
        }
    }

    private function load($attribute)
    {
        if ( method_exists($this, $attribute) ) {
            return $this->parseType($attribute, call_user_func(array($this, $attribute)));
        } else if ( method_exists($this, 'get' . ucfirst($attribute)) ) {
            return $this->parseType($attribute, call_user_func(array($this, 'get' . ucfirst($attribute))));
        } else if ( ($modelAttribute = array_get($this->attributes, $attribute)) &&  object_get($this->model, $modelAttribute)) {
            return $this->parseType($attribute, object_get($this->model, $modelAttribute));
        } else if (object_get($this->model, snake_case($attribute))) {
            return $this->parseType($attribute, object_get($this->model, snake_case($attribute)));
        }

        return $this->parseType($attribute, object_get($this->model, $attribute));
    }

    public function loadRelation($relation)
    {
        $this->relations[$relation] = $this->{$relation}();

        return $this->relations[$relation];
    }

    private function parseType($attribute, $value)
    {
        if( $type = array_get($this->types, $attribute) ) {
            switch($type)
            {
                case "int":
                    return intval($value);
                case "bool":
                case "boolean":
                    return boolval($value);
                case "float":
                    return floatval($value);
            }
        }

        return $value;
    }

    protected function checkKey($key)
    {
        $model = $this->model;
        $keyArray = explode('.', $key);

        foreach($keyArray as $nextKey) {
            if ( is_numeric($nextKey) ) {
                $model = $model->get($nextKey);
            } else {
                $model = data_get($model, $nextKey);
            }

            if( ! $model ) {
                return false;
            }
        }

        return true;
    }

}