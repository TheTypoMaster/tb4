<?php

namespace TopBetta\Models;

use Eloquent;

class TournamentBuyInTypeModel extends Eloquent {

    protected $table = 'tbdb_tournament_buyin_type';

	protected $guarded = array();

	public static $rules = array();
}
