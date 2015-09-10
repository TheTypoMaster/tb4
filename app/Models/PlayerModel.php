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

    public function selections()
    {
        return $this->morphToMany('TopBetta\Models\SelectionModel', 'competitor', 'tb_selection_competitor', 'competitor_id', 'selection_id');
    }

    public function eventTeam($eventId)
    {
        return $this->belongsToMany('TopBetta\Models\TeamModel', 'tb_event_team_player', 'player_id', 'team_id')
            ->where('event_id', $eventId)
            ->first();
    }
}
