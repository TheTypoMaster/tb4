<?php

namespace TopBetta\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentPlacesPaidModel extends Model
{
    protected $table = 'tbdb_tournament_places_paid';

    public function getPayPercAttribute($attribute)
    {
        return explode(',', $attribute);
    }
}
