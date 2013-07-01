<?php
namespace TopBetta;

class TournamentBet extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();
	
	public function getTournamentBetListByTicketID($ticketId)
	{
			
		$query =
			'SELECT
				b.id,
				b.tournament_ticket_id,
				e.id AS event_id,				
				b.bet_amount,
				b.win_amount,
				b.fixed_odds,
				b.flexi_flag,
				b.resulted_flag,
				s.name AS bet_status,
				t.id AS bet_type,
				mt.name AS market_type,
				selection.id AS selection_id,
				selection.number AS runner_number,
				selection.name AS selection_name,
				sp.win_odds,
				sp.place_odds,
				sp.bet_product_id,
				sr.win_dividend,
				sr.place_dividend,
				b.created_date
			FROM
				tbdb_tournament_bet AS b
			INNER JOIN
				tbdb_tournament_ticket AS ticket
			ON
				b.tournament_ticket_id = ticket.id
			INNER JOIN
				tbdb_bet_result_status AS s
			ON
				b.bet_result_status_id = s.id
			INNER JOIN
				tbdb_bet_type AS t
			ON
				b.bet_type_id = t.id
			INNER JOIN
				tbdb_tournament_bet_selection AS ts
			ON
				ts.tournament_bet_id = b.id
			INNER JOIN
				tbdb_selection AS selection
			ON
				ts.selection_id = selection.id
			LEFT JOIN
				tbdb_selection_price AS sp
			ON
				sp.selection_id = selection.id
			LEFT JOIN
				tbdb_selection_result AS sr
			ON
				sr.selection_id = selection.id
			INNER JOIN
				tbdb_market AS m
			ON
				selection.market_id = m.id
			INNER JOIN
				tbdb_market_type AS mt
			ON
				mt.id = m.market_type_id		
			INNER JOIN
				tbdb_event AS e
			ON
				m.event_id = e.id
			WHERE
				ticket.id = "' . $ticketId . '" ORDER BY b.id ASC';

		$result = \DB::select($query);

		return $result;	
	}	
	
}