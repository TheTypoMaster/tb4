<?php
namespace TopBetta\Models;

class TournamentPrizeFormat extends \Eloquent {
    protected $table = 'tbdb_tournament_prize_format';	
    protected $guarded = array();

    protected $fillable = ['short_name', 'icon'];

    public static $rules = array();
		
}