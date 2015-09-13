<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 8/07/2015
 * Time: 2:55 PM
 */

namespace TopBetta\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

interface ResourceInterface extends Arrayable, Jsonable {
}