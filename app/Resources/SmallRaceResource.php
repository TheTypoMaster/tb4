<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 8/09/2015
 * Time: 3:14 PM
 */

namespace TopBetta\Resources;


class SmallRaceResource extends RaceResource {

    protected $attributes = array(
        "id"                => 'id',
        "type"              => "type",
        "start_date"        => 'start_date',
        "number"            => 'number',
        "status"            => 'eventstatus.keyword',
        "resultString" => "resultString",
    );

    protected $includeFullResults = false;

}