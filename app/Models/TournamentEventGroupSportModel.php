<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentEventGroupSportModel extends Model
{
    protected $table = 'tb_tournament_event_group_sport';

    protected $fillable = ['tournament_event_group_id', 'sport_id'];


}
