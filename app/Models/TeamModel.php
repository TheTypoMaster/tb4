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

    public function players()
    {
        return $this->belongsToMany('TopBetta\Models\PlayerModel', 'tb_player_tb_team', 'tb_team_id', 'tb_player_id');
    }

    public function selections()
    {
        return $this->morphToMany('TopBetta\Models\SelectionModel', 'competitor', 'tb_selection_competitor', 'competitor_id', 'selection_id');
    }
}
