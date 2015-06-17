<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/04/2015
 * Time: 10:49 AM
 */

namespace TopBetta\Models;

use Eloquent;

class TODModel extends Eloquent {

    protected $table = 'tbdb_tournament_of_day_venue';

    protected $guarded = array();

    public static $rules = array();
}