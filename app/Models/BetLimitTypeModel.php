<?php

namespace TopBetta\Models;

use Eloquent;

class BetLimitTypeModel extends Eloquent {

    protected $table = 'tbdb_bet_limit_types';

	protected $guarded = array();

	public static $rules = array();
}
