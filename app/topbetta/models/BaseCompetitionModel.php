<?php

namespace TopBetta\Models;

use Eloquent;

class BaseCompetitionModel extends Eloquent {

    protected $table = 'tb_base_competition';

	protected $guarded = array();

	public static $rules = array();
}
