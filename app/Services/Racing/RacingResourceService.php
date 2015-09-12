<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 12:48 PM
 */

namespace TopBetta\Services\Racing;


abstract class RacingResourceService {

    public function formatCollectionsForResponse($collection)
    {
        $response = array();

        foreach($collection as $model) {
            $response[] = $this->formatForResponse($model);
        }

        return $response;
    }

    abstract public function formatForResponse($model);
}