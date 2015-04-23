<?php

namespace TopBetta\Models;

use Eloquent;

class PlayerModel extends Eloquent {

    public $table = 'tb_players';

    protected $guarded = array();

	public static $rules = array();

    public function teams()
    {
        return $this->belongsToMany('TopBetta\Models\TeamModel', 'tb_player_tb_team', 'tb_player_id', 'tb_team_id');
    }
}
