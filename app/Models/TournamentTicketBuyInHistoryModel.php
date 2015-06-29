<?php

namespace TopBetta\Models;

use Eloquent;

class TournamentTicketBuyInHistoryModel extends Eloquent {

    protected $table = 'tbdb_tournament_ticket_buyin_history';
	protected $guarded = array();

	public static $rules = array();
}
