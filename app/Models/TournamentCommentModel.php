<?php

namespace TopBetta\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TournamentCommentModel extends Model
{
    protected $table = 'tbdb_tournament_comment';

    protected $guarded = array();

    protected $fillable = ['tournament_id', 'user_id', 'comment', 'visible'];

    public function tournament()
    {
        return $this->belongsTo('TopBetta\Models\TournamentModel', 'tournament_id');
    }

    public function user()
    {
        return $this->belongsTo('TopBetta\Models\UserModel', 'user_id');
    }

}
