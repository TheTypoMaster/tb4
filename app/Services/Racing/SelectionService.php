<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 11:17 AM
 */

namespace TopBetta\Services\Racing;


class SelectionService extends RacingResourceService {

    /**
     * Relations to eager load
     * @var array
     */
    protected static $includes = array(
        "result",
        "price",
        "runner",
        "runner.trainer",
        "runner.owner"
    );

}