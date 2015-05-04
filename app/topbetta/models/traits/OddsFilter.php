<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/05/2015
 * Time: 2:09 PM
 */

namespace TopBetta\Models\Traits;


trait OddsFilter {

    public function filterOdds($value)
    {
        if( $overrideType = object_get($value, 'override_type', null) ) {
            if( $overrideType == 'percentage') {
                return (object_get($value, 'override_odds', 0) * object_get($value, 'win_odds', 0)) > 1;
            } else {
                return object_get($value, 'override_odds', 0) > 1;
            }
        }

        return object_get($value, 'win_odds', 0) > 1;
    }
}