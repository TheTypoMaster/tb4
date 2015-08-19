<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/07/2015
 * Time: 12:06 PM
 */

namespace TopBetta\Resources;


use Illuminate\Database\Eloquent\Collection;

abstract class EloquentLeaderboardCollection extends EloquentResourceCollection {

    public function __construct(Collection $collection, $class)
    {
        parent::__construct($collection, $class);
        $this->sort();
    }

    abstract public function sort();
}