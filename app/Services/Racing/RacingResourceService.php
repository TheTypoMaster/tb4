<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 12:48 PM
 */

namespace TopBetta\Services\Racing;


class RacingResourceService {

    /**
     * Relations to eager load
     * @var array
     */
    protected static $includes = array();

    public static function getIncludes() {
        return self::$includes;
    }
}