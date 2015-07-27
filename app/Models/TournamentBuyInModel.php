<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/04/2015
 * Time: 10:04 AM
 */

namespace TopBetta\Models;

use Eloquent;

class TournamentBuyInModel extends Eloquent {

    protected $table = 'tbdb_tournament_buyin';

    protected $guarded = array();

    public static $rules = array();
}