<?php

namespace TopBetta\Models;

use Eloquent;

class IconModel extends Eloquent {

    protected $table='tb_icons';

    protected $guarded = array();

	public static $rules = array();
}
