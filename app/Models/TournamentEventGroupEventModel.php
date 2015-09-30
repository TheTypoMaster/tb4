<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentEventGroupEventModel extends Model
{
    protected $table = 'tb_tournament_event_group_event';

    protected $fillable = ['tournament_event_group_id', 'event_id'];

}
