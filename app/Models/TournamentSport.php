<?php

namespace TopBetta\Models;

class TournamentSport extends \Eloquent
{

    protected $table = 'tbdb_tournament_sport';
    protected $guarded = array();
    public static $rules = array();

    public static function getActiveSports()
    {
        return TournamentSport::where('status_flag', '1')
                        ->where('racing_flag', '0')
                        ->select(array('id','name'))
                        ->get();
    }

    public function competitions()
    {
        return $this->hasMany('TopBetta\Models\TournamentCompetition', 'tournament_sport_id');
    }

}
