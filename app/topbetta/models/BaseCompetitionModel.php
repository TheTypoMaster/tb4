<?php

namespace TopBetta\Models;

use Eloquent;

class BaseCompetitionModel extends Eloquent {

    protected $table = 'tb_base_competition';

	protected $guarded = array();

	public static $rules = array();

    public function defaultEventGroupIcon()
    {
        return $this->belongsTo('TopBetta\Models\IconModel', 'default_event_group_icon_id');
    }

    public function icon()
    {
        return $this->belongsTo('TopBetta\Models\IconModel');
    }

    public function region()
    {
        return $this->belongsTo('TopBetta\Models\CompetitionRegionModel');
    }

    public function sport()
    {
        return $this->belongsTo('TopBetta\Models\SportModel');
    }
}
