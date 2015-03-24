<?php

namespace TopBetta\Models;

use Eloquent;

class TeamModel extends Eloquent {

    protected $table = 'tb_teams';

	protected $guarded = array();

	public static $rules = array();

    public function icon()
    {
        return $this->belongsTo('TopBetta\Models\IconModel');
    }
}
