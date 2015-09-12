<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/07/2015
 * Time: 10:58 AM
 */

namespace TopBetta\Resources;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

interface ResourceCollectionInterface extends Arrayable, Jsonable, \IteratorAggregate {

}