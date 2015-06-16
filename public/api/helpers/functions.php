<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 16/06/2015
 * Time: 10:32 AM
 */

if( ! function_exists('array_flatten') ) {

    function array_flatten(array $array)
    {
        $flattened = array();

        array_walk_recursive($array, function($v) use (&$flattened) {
            $flattened[] = $v;
        });

        return $flattened;
    }
}