<?php
namespace TopBetta;

class Tournament extends \Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_tournament';

	/**
	 * Array to content the Racing Sports to exclude from the list
	 * @var Array
	 */
	public $excludeSports = array('galloping', 'greyhounds', 'harness');

	// model relationships
    public function tournamentlabels(){
        return $this->belongsToMany('TopBetta\TournamentLabels', 'tb_tournament_label_tournament', 'tournament_id', 'tournament_label_id');
    }

	static public function getTournamentWithEventGroup($eventGroupId){
		return Tournament::where('event_group_id', '=', $eventGroupId)->get();
	}

    static public function isTournamentFeatured($tournamentId){
        return \DB::table('tbdb_tournament')
                        ->join('tb_tournament_label_tournament', 'tb_tournament_label_tournament.tournament_id', '=', 'tbdb_tournament.id')
                        ->join('tb_tournament_labels', 'tb_tournament_labels.id', '=', 'tb_tournament_label_tournament.tournament_label_id')
                        ->where('tbdb_tournament.id', $tournamentId)
                        ->where('tb_tournament_labels.label', 'Featured')->pluck('tb_tournament_labels.id');

        //return static::with('tournamentlabels')->where('id', $tournamentId)->get();
    }

	public function getTournamentActiveList($list_params = array()) {

		//TODO: this code is mostly taken straight from joomla - replace it!

		$sport_id = isset($list_params['sport_id']) ? $list_params['sport_id'] : null;
		$competition_id = isset($list_params['competition_id']) ? $list_params['competition_id'] : null;
		$jackpot = isset($list_params['jackpot']) ? $list_params['jackpot'] : false;
		$private = isset($list_params['private']) ? $list_params['private'] : false;
		$limit = isset($list_params['limit']) ? $list_params['limit'] : null;
		$type = isset($list_params['type']) ? $list_params['type'] : null;
		$sub_type = isset($list_params['sub_type']) ? $list_params['sub_type'] : null;
		$event_group_id = isset($list_params['event_group_id']) ? $list_params['event_group_id'] : array();
		$order = isset($list_params['order']) ? $list_params['order'] : null;
		$today = \Carbon\Carbon::today('Australia/Sydney');
		
		$query = "
			SELECT
				t.id,
				t.tournament_sport_id,
				t.parent_tournament_id,
				t.event_group_id,
				t.name,
				t.description,
				t.start_currency,
				t.start_date,
				t.end_date,
				t.jackpot_flag,
				t.buy_in,
				t.entry_fee,
				t.minimum_prize_pool,
				t.free_credit_flag,
				t.tod_flag,
				t.paid_flag,
				t.auto_create_flag,
				t.cancelled_flag,
				t.cancelled_reason,
				t.status_flag,
				t.created_date,
				t.updated_date,
				t.private_flag,
				t.bet_limit_flag,
				t.tournament_sponsor_name,
				t.tournament_sponsor_logo,
				t.tournament_sponsor_logo_link,
				s.name AS sport_name,
				s.description AS sport_description,
				eg.id AS event_group_id,
				eg.name AS event_group_name,
				eg.state AS state,
				eg.meeting_code,
				eg.events,
				eg.track,
				eg.weather,
				c.name AS competition_name,
				tl.label AS featured

			FROM
				tbdb_tournament AS t
			INNER JOIN
				tbdb_tournament_sport AS s
			ON
				t.tournament_sport_id = s.id
			INNER JOIN
				tbdb_event_group AS eg
			ON
				t.event_group_id = eg.id
			INNER JOIN
				tbdb_tournament_competition AS c
			ON
				c.id = eg.tournament_competition_id
			LEFT JOIN
			    tb_tournament_label_tournament AS tlt
	        ON
	            tlt.tournament_id = t.id
			LEFT JOIN
			    tb_tournament_labels AS tl
	        ON
	            tl.id = tlt.tournament_label_id
			WHERE 
				t.end_date > '" . $today;
				
		//		t.end_date > NOW()

		$query .= "'	AND
				t.status_flag = 1
			AND
				t.cancelled_flag = 0
		";
		/*
		 if ($sport_id !== null) {
		 $query .= ' AND t.tournament_sport_id = ' . $db -> quote($sport_id);
		 }

		 if ($competition_id !== null) {
		 $query .= ' AND c.id = ' . $db -> quote($competition_id);
		 }

		 if ($jackpot !== false) {
		 $query .= ' AND t.jackpot_flag = ' . $db -> quote($jackpot);
		 }
		 */
		 
		 if ($private !== false) {
		 $query .= ' AND t.private_flag = "' . $private . '"';
		 }

		// start with a sub_type, then fall back to type
		if ($sub_type) {
			$query .= ' AND LOWER(s.name) = "' . $sub_type . '"';
		} else {
			switch ($type) {
				case 'sports' :
					$query .= ' AND LOWER(s.name) NOT IN ("galloping", "harness", "greyhounds")';
					break;
				case 'racing' :
					$query .= ' AND LOWER(s.name) IN ("galloping", "harness", "greyhounds")';
					break;
			default:
				//return array();
			}
		}

		/*
		 if (!empty($event_group_id)) {
		 if (is_string($event_group_id)) {
		 $event_group_id = array($event_group_id);
		 }

		 $clean_event_group_id = array();
		 foreach ($event_group_id as $eg_id) {
		 $clean_event_group_id[] = $db -> quote($eg_id);
		 }

		 $query .= ' AND t.event_group_id IN (' . implode(', ', $clean_event_group_id) . ')';
		 }
		 */
		if (empty($order)) {
			$query .= '
				ORDER BY
					t.start_date,
					eg.name,
					t.entry_fee';
		} else {
			$query .= '
				ORDER BY ' . $order;
		}

		if ($limit !== null) {
			$query .= '
				LIMIT ' . (int)$limit;
		}

		$result = \DB::select($query);

		return $result;
	}

	/**
	 * Get a user's tournament tickets
	 *
	 * @param integer $userId
	 * @return object
	 */
	public function getMyTournamentListByUserID($userId, $order = false, $includeRefunded = false, $enteredFromToday = false)
	{

		$query =
			'SELECT
				t.id
			FROM
				tbdb_tournament_ticket AS tk
			INNER JOIN
				tbdb_tournament AS t
			ON
				t.id = tk.tournament_id
			WHERE
				user_id = "' . $userId . '"
				AND t.cancelled_flag = 0';

		if($enteredFromToday) {
			$query .= " AND t.end_date > '" . date('Y-m-d') . "'";
		} else {
			$query .= " AND t.paid_flag <> 1";
		}

		if(!$includeRefunded) {
			$query .= ' AND tk.refunded_flag != 1';
		}

		if($order) {
			$query .= ' ORDER BY ' . $order;
		} else {
			$query .= ' ORDER BY t.start_date ASC, tk.created_date DESC';
		}

		$result = \DB::select($query);

		return $result;
	}

	/**
	 * Use the number of tickets purchased for a tournament to determine the current prize pool
	 * in cents.
	 *
	 * @param integer $tournament_id
	 * @return integer
	 */
	public function calculateTournamentPrizePool($tournamentId) {
		$tournament = Tournament::find($tournamentId);

		$query = 'SELECT
				t.buy_in AS buy_in,
				COUNT(tt.user_id) AS entrants
			FROM
				tbdb_tournament_ticket AS tt
			INNER JOIN
				tbdb_tournament AS t
			ON
				tt.tournament_id = t.id
			WHERE
				tt.tournament_id = ' . $tournamentId . '
			AND
				tt.refunded_flag = 0
			GROUP BY
				tt.tournament_id';

		$result = \DB::select($query);

		$current_prize_pool = empty($result) ? 0 : ($result[0] -> buy_in) * $result[0] -> entrants;
		return ($current_prize_pool > $tournament -> minimum_prize_pool) ? $current_prize_pool : $tournament -> minimum_prize_pool;
	}

	/**
	 * Calculate the number of places paid for a tournament, and the payout if cash.
	 *
	 * @param object 	$tournament
	 * @param object 	$entrant_count
	 * @param int 		$prize_pool
	 * @return array
	 */
	public function calculateTournamentPlacesPaid($tournament, $entrant_count, $prize_pool)
	{
		$payout_model = new \TopBetta\TournamentPlacesPaid;
		$final = $this->isFinished($tournament);

		if($final) {
			return $payout_model->getPrizeDistribution($tournament, $prize_pool);
		}

		return $payout_model->getPlaceList($tournament, $entrant_count, $prize_pool);
	}

	/**
	 * Check out if a tournament is racing
	 *
	 * @param int $tournament
	 * @return boolean
	 */
	public function isRacing($tournamentId) {

		$query = 'SELECT
				ts.name
			FROM
				tbdb_tournament_sport AS ts
			INNER JOIN
				tbdb_tournament AS t
			ON
				ts.id = t.tournament_sport_id
			WHERE
				t.id = ' . $tournamentId;

		$result = \DB::select($query);
		$sport_name = $result[0] -> name;

		return in_array($sport_name, $this -> excludeSports);
	}

	/**
	 * Determine if a tournament has finished
	 *
	 * @param object $tournament
	 * @return bool
	 */
	public function isFinished($tournament)
	{
		return (!empty($tournament->cancelled_flag) || strtotime($tournament->end_date) < time());
	}

	/**
         * Gets the next event start time (sport/racing) for an event group id
         * 
         * @param type $groupId
         * @return type string
         */
        public static function getNextEventStartTimeForEventGroupId($groupId) {
		
		return \DB::table('tbdb_event')
                    ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
                    ->join('tbdb_event_group',  'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
                    ->where('tbdb_event_group.id', $groupId)
                    ->where('tbdb_event.event_status_id',1)
                    ->orderBy('tbdb_event.start_date', 'asc')
                    ->take(1)
                    ->pluck('tbdb_event.start_date');
	}         
}
