<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentGroupModel extends Model
{
    protected $table = 'tb_tournament_groups';

    protected $guarded = array();

    public $timestamps = false;

    public function tournaments()
    {
        return $this->belongsToMany('TopBetta\Models\TournamentModel', 'tb_tournament_group_tournament', 'tournament_group_id', 'tournament_id');
    }
}
