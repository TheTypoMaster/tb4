<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentEventGroupModel extends Model
{
    protected $table = 'tb_tournament_event_group';

    protected $fillable = ['name'];

    public function events()
    {
        return $this->belongsToMany('TopBetta\Models\EventModel', 'tb_tournament_event_group_event', 'tournament_event_group_id', 'event_id');
    }

    public function tournaments()
    {
        return $this->hasMany('TopBetta\Models\TournamentModel', 'event_group_id');
    }
}
