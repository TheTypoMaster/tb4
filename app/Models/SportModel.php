<?php namespace TopBetta\Models;

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 14:53
 * Project: tb4
 */

use Eloquent;

class SportModel extends Eloquent
{

    protected $table = 'tb_sports';
    protected $guarded = array();

    public function icon()
    {
        return $this->belongsTo('TopBetta\Models\IconModel');
    }

    public function defaultCompetitionIcon()
    {
        return $this->belongsTo('TopBetta\Models\IconModel', 'default_competition_icon_id');
    }

    public function isRacing()
    {
        return in_array($this->name, array('galloping', 'harness', 'greyhounds'));
    }

    public function baseCompetitions()
    {
        return $this->hasMany('TopBetta\Models\BaseCompetitionModel', 'sport_id');
    }

    public function getCompetitions()
    {
        return $this->hasMany('TopBetta\Models\CompetitionModel', 'sport_id');
    }

}