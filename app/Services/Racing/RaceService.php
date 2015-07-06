<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 11:16 AM
 */

namespace TopBetta\Services\Racing;


class RaceService extends RacingResourceService {

    const RELATION_SELECTIONS = 'markets.selections';

    /**
     * Relations to eager load
     * @var array
     */
    protected static $includes = array(
        "eventstatus"
    );
}