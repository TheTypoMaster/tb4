<?php

namespace TopBetta\Models;

use Eloquent;

class MarketOrderingModel extends Eloquent {

    protected $table = 'tb_market_order';

	protected $guarded = array();

	public static $rules = array();

}
