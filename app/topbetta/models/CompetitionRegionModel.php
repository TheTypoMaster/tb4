<?php

namespace TopBetta\Models;

use Eloquent;

class CompetitionRegionModel extends Eloquent {

    protected $table = 'tb_competition_region';

	protected $guarded = array();

	public static $rules = array();

    public function icon()
    {
        return $this->belongsTo('TopBetta\Models\CompetitionRegionModel', 'icon_id');
    }
}
