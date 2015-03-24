<?php

namespace TopBetta\Models;

use Eloquent;

class BaseCompetitionModel extends Eloquent {

    protected $table = 'tb_base_competition';

	protected $guarded = array();

	public static $rules = array();

    public function defaultEventGroupModel()
    {
        return $this->belongsTo('TopBetta\Models\IconModel', 'default_event_group_icon_id');
    }

    public function icon()
    {
        return $this->belongsTo('Topbetta\Models\IconModel');
    }
}
