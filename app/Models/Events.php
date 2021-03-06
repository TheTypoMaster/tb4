<?php namespace TopBetta\Models;

use Carbon\Carbon;
use Eloquent;

class Events extends Eloquent {

	protected $table = 'tbdb_event';

	protected $guarded = array();

	public static $rules = array();

    public function competitions(){
        return $this->belongsToMany('\TopBetta\Models\SportsComps', 'tbdb_event_group_event', 'event_id', 'event_group_id')->withTimestamps();
    }

    public function competitionpivot(){
        return $this->belongsTo('\TopBetta\Models\SportEventGroupEvent', 'id', 'event_id');
    }

    public function eventstatus(){
        return $this->belongsTo('\TopBetta\Models\SportEventStatus', 'event_status_id', 'id');
    }

    public function teams()
    {
        return $this->belongsToMany('\TopBetta\Models\TeamModel', 'tb_team_tbdb_event', 'tbdb_event_id', 'tb_team_id')->withPivot('team_position');
    }

    public function competition(){
        return $this->belongsToMany('\TopBetta\Models\CompetitionModel', 'tbdb_event_group_event', 'event_id', 'event_group_id');
    }

    public function resultPrices()
    {
        return $this->hasMany('TopBetta\Models\ResultPriceModel', 'event_id');
    }

    public function teamPlayers()
    {
        return $this->hasMany('TopBetta\Models\EventTeamPlayerModel', 'event_id');
    }

    /**
     * get all events that start from today
     * @param $query
     * @return mixed
     */
    public function scopeFromToday($query) {
        $date = Carbon::today();
        $query->where('start_date', '>=', $date);
        return $query;
    }

}
