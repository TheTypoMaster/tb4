<?php namespace TopBetta\Models;

use Eloquent;

class TournamentTicketModel extends Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_tournament_ticket';

    public function user()
    {
        return $this->belongsTo('TopBetta\Models\UserModel', 'user_id');
    }

    public function tournament()
    {
        return $this->belongsTo('TopBetta\Models\TournamentModel', 'tournament_id');
    }

}
