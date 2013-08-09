<?php
namespace TopBetta;

class TournamentPlacesPaid extends \Eloquent {
    protected $guarded = array();

    public static $rules = array();

	/**
	 * Cash prize formula name
	 *
	 * @var string
	 */
	const PRIZE_TYPE_CASH = 'cash';

	/**
	 * Ticket prize formula name
	 *
	 * @var string
	 */
	const PRIZE_TYPE_TICKET = 'ticket';

	/**
	 *  Prize format Winner Takes All
	 *
	 *  @var string
	 */
	const PRIZE_FORMAT_ALL			= 'all';

	/**
	 *  Prize format Top 3
	 *
	 *  @var string
	 */
	const PRIZE_FORMAT_TOP3 		= 'top3';

	/**
	 *  Prize format multiple
	 *
	 *  @var string
	 */
	const PRIZE_FORMAT_MULTIPLE 	= 'multiple';

	/**
	 * Discard remainder
	 *
	 * @var integer
	 */
	const REMAINDER_FORMULA_DISCARD = 0;

	/**
	 * Pay remainder to the next qualifier
	 *
	 * @var string
	 */
	const REMAINDER_FORMULA_NEXT_PLACE = 1;

	/**
	 * Distribute remainder to qualified places using the cash places formula
	 *
	 * @var integer
	 */
	const REMAINDER_FORMULA_DISTRIBUTE = 2;

	/**
	 * Get the relevant places value depending on the entrants number
	 *
	 * @param integer $entrant_count
	 * @param integer $published
	 * @return object
	 */
	public function getPlacesPaid($entrant_count, $published = 1) {

		if($published != 1) {
			$published = 0;
		}

		if($entrant_count > 1000) {
			$entrant_count = 1000;
		}

		$query =
			'SELECT
				places_paid,
				pay_perc AS percentage
			FROM
				tbdb_tournament_places_paid
			WHERE
				entrants >= "' . $entrant_count .'"
			AND
				published = "' . $published . '"
			ORDER BY
				entrants ASC,
				places_paid ASC
			LIMIT 1';

		$result = \DB::select($query);

		return $result[0];
	}

	/**
	 * Get the final prize distribution for a completed tournament
	 *
	 * @param object 	$tournament
	 * @param integer	$prize_pool
	 * @return array
	 */
	public function getPrizeDistribution($tournament, $prize_pool = null) {
		$leaderboard 		= new \TopBetta\TournamentLeaderboard;
		$qualified_list 	= $leaderboard->getLeaderBoardRank($tournament, null, true);

		if(empty($qualified_list)) {
			return false;
		}

		if(is_null($prize_pool)) {
			$tournament_model 	= new \TopBetta\TournamentLeaderboard;
			$prize_pool 		= $tournament_model->calculateTournamentPrizePool($tournament->id);
		}

		if($this->isJackpot($tournament)) {
			//return $this->_getJackpotPrizeDistribution($tournament, $qualified_list, $prize_pool);
		}

		return $this->_getCashPrizeDistribution($tournament, $qualified_list, $prize_pool);
	}

	/**
	 * Get the final prize distribution for a cash tournament
	 *
	 * @param object 	$tournament
	 * @param array 	$qualified_list
	 * @param integer 	$prize_pool
	 * @return array
	 */
	private function _getCashPrizeDistribution($tournament, $qualified_list, $prize_pool) {
		$payout_list 	= $this->_getFinalPayoutList($tournament, $qualified_list);
		$ranking_list 	= $this->formatRankingList($qualified_list);

		$place_list = array('formula' => self::PRIZE_TYPE_CASH, 'place' => array());
		foreach($ranking_list as $rank => $qualified) {
			if(empty($payout_list[$rank])) {
				continue;
			}

			$qualified_count = count($qualified);

			$percentage = 0;
			for($i = $rank; $i < ($rank + $qualified_count); ++$i) {
				if (array_key_exists($i, $payout_list)) {
					$percentage += $payout_list[$i];
				}
			}

			$place_list['place'][$rank][self::PRIZE_TYPE_CASH] 	= (($percentage / 100) * $prize_pool) / $qualified_count;
			$place_list['place'][$rank]['count'] 				= $qualified_count;
		}

		return $place_list;
	}

	/**
	 * Get the final payout list taking into account shifting places based on qualifiers
	 *
	 * @param object 	$tournament
	 * @param array 	$qualified_list
	 * @return array
	 */
	private function _getFinalPayoutList($tournament, $qualified_list) {
		$ticket_model = new \TopBetta\TournamentTicket;
		$count = $ticket_model->countTournamentEntrants($tournament->id);

		$place_list 		= $this->getCashPayoutList($count);
		$place_count 		= count($place_list);
		$qualified_count 	= count($qualified_list);

		if($this->isPrivate($tournament)){
			//$prize_format_model = new \TopBetta\TournamentPrizeFormat;
			//$prize_format = $prize_format_model->getTournamentPrizeFormat($tournament->prize_format_id);
			$prize_format = \TopBetta\TournamentPrizeFormat::find($tournament->prize_format_id);

			if($prize_format->keyword != self::PRIZE_FORMAT_MULTIPLE){
				$place_count = $this->_getPrivateTournamentPaidPlaceCount($prize_format->keyword);
			}
			$place_list = $this->getPercentagePayoutList($place_count);
		}

		if($place_count > $qualified_count) {
			$place_list = $this->getPercentagePayoutList($qualified_count);
		}

		return $place_list;
	}

	/**
	 * Get a cash payout array by loading a record from the database and reformatting it
	 *
	 * @param integer $entrant_count
	 * @return array
	 */
	public function getCashPayoutList($entrant_count) {
		$place = $this->getPlacesPaid($entrant_count);
		return $this->_formatPercentageList($place->percentage);
	}

			/**
	 * Take a comma separated string of percentages and arrive at a payout list
	 *
	 * @param string $percentage
	 * @return array
	 */
	private function _formatPercentageList($percentage) {
		$raw_list = explode(',', $percentage);

		$place_list = array();
		foreach($raw_list as $key => $value) {
			$place_list[$key + 1] = $value;
		}

		return $place_list;
	}

	/**
	 * Check whether a tournament is a jackpot tournament
	 *
	 * @param object $tournament
	 * @return bool
	 */
	private function isJackpot($tournament) {
		return (!empty($tournament->jackpot_flag) && $tournament->parent_tournament_id > 0);
	}

	/**
	 * Check whether a tournament is a private tournament
	 *
	 * @param object $tournament
	 * @return bool
	 */
	private function isPrivate($tournament) {
		return ($tournament->private_flag);
	}

	/**
	 * Get private tournament places paid based on keyword
	 *
	 * @param string $keyword
	 * @return int
	 */
	private function _getPrivateTournamentPaidPlaceCount($keyword){
		switch($keyword){
			case self::PRIZE_FORMAT_TOP3:
				return 3;
			case self::PRIZE_FORMAT_ALL:
				return 1;
		}
	}

	/**
	 * Get a percentage breakdown list
	 *
	 * @param integer $place_count
	 * @return array
	 */
	public function getPercentagePayoutList($place_count) {
		$place = $this->getPercentage($place_count);
		return $this->_formatPercentageList($place->percentage);
	}

	/**
	 * Opposite of get places; this one gets the percentage share when you know projected places
	 *
	 * @param integer $place_count
	 * @return object
	 */
	public function getPercentage($place_count) {

		$query =
			'SELECT
				places_paid,
				pay_perc AS percentage
			FROM
				tbdb_tournament_places_paid
			WHERE
				places_paid <= "' . $place_count . '"
			ORDER BY
				places_paid DESC
			LIMIT 1';

		$result = \DB::select($query);

		return $result;
	}

	/**
	 * Reformat qualifier list from the leaderboard in order to sort prize distribution
	 *
	 * @param array $qualified_list
	 * @return array
	 */
	public function formatRankingList(array $qualified_list) {
		$ranking_list = array();

		foreach($qualified_list as $qualified) {
			if(!isset($ranking_list[$qualified->rank])) {
				$ranking_list[$qualified->rank] = array();
			}

			$ranking_list[$qualified->rank][] = $qualified;
		}

		return $ranking_list;
	}

	/**
	 * Get a list of expected places paid
	 *
	 * @param object 	$tournament
	 * @param integer 	$entrant_count
	 * @param integer 	$prize_pool
	 * @return array
	 */
	public function getPlaceList($tournament, $entrant_count, $prize_pool) {
		if($this->isJackpot($tournament)) {
			return $this->_getJackpotPlaceList($tournament, $entrant_count, $prize_pool);
		}

		return $this->_getCashPlaceList($tournament, $entrant_count, $prize_pool);
	}

	/**
	 * Get a place list for a jackpot tournament
	 *
	 * @param object 	$tournament
	 * @param integer 	$entrant_count
	 * @param integer 	$prize_pool
	 * @return array
	 */
	private function _getJackpotPlaceList($tournament, $entrant_count, $prize_pool) {
		$tournament_model = new \TopBetta\Tournament;
		//$parent = $tournament_model->getTournament($tournament->parent_tournament_id);
		$parent = \TopBetta\Tournament::find($tournament->parent_tournament_id);

		if(is_null($parent)) {
			return false;
		}

		$price 			= $parent->entry_fee + $parent->buy_in;
		$ticket_count 	= floor($prize_pool / $price);

		$place_list = array('formula' => self::PRIZE_TYPE_TICKET, 'place' => array());
		$last_rank = 0;
		for($i = 1; $i <= $ticket_count; ++$i) {
			$place_list['place'][$i][self::PRIZE_TYPE_TICKET] = $parent->id;
			$last_rank = $i;
		}

		$remainder = $prize_pool - ($ticket_count * $price);
		if($remainder > 0) {
			$place_list['place'][$last_rank + 1][self::PRIZE_TYPE_CASH] = $remainder;
		}

		return $place_list;
	}

	/**
	 * Get a place list for a cash tournament
	 *
	 * @param object 	$tournament
	 * @param integer 	$entrant_count
	 * @param integer 	$prize_pool
	 * @return array
	 */
	private function _getCashPlaceList($tournament, $entrant_count, $prize_pool) {
		$payout_list = $this->getCashPayoutList($entrant_count);

		/*
		if($this->isPrivate($tournament)){
			//$prize_format_model =& JModel::getInstance('TournamentPrizeFormat', 'TournamentModel');
			//$private_tournament_model =& JModel::getInstance('TournamentPrivate', 'TournamentModel');
			//$private_tournament = $private_tournament_model->getTournamentPrivateByTournamentID($tournament->id);
			$private_tournament = \TopBetta\TournamentPrivate::find($tournament->id);

			//$prize_format = $prize_format_model->getTournamentPrizeFormat($private_tournament->tournament_prize_format_id);
			$prize_format = \TopBetta\TournamentPrizeFormat::find($private_tournament->tournament_prize_format_id);

			if($prize_format->keyword != self::PRIZE_FORMAT_MULTIPLE){
				$place_count = $this->_getPrivateTournamentPaidPlaceCount($prize_format->keyword);
				$payout_list = $this->getPercentagePayoutList($place_count);
			}
		}
		*/

		$place_list = array('formula' => self::PRIZE_TYPE_CASH, 'place' => array());
		foreach($payout_list as $rank => $percentage) {
			$place_list['place'][$rank][self::PRIZE_TYPE_CASH] = ($percentage / 100) * $prize_pool;
		}

		return $place_list;
	}

}