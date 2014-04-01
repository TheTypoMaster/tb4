<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
// Import Joomla! libraries
require_once 'tournament.php';

class TournamentModelTournamentRacing extends TournamentModelTournament
{
	/**
	 * One letter code for greyhound racing in a TAB ID
	 *
	 * @var string
	 */
	const RACING_SPORT_CODE_GREYHOUND = 'g';

	/**
	 * One letter code for harness racing in a TAB ID
	 *
	 * @var string
	 */
	const RACING_SPORT_CODE_HARNESS   = 'h';

	/**
	 * One letter code for horse racing in a TAB ID
	 *
	 * @var string
	 */
	const RACING_SPORT_CODE_GALLOPING = 'r';

	/**
	 * Sport names by string code
	 *
	 * @var array
	 */
	public $sport = array(
		self::RACING_SPORT_CODE_GREYHOUND => 'greyhounds',
		self::RACING_SPORT_CODE_HARNESS   => 'harness',
		self::RACING_SPORT_CODE_GALLOPING => 'galloping'
	);

	/**
	 * Load a full tournament data record including joined tables (tournament, tournament_sport)
	 *
	 * @param integer $tournament_id
	 * @return object
	 */
	public function getTournamentRacingByTournamentID($tournament_id = null)
	{
		if(empty($tournament_id)) {
			$tournament_id = $this->tournament_id;
		}

		$db =& $this->getDBO();
		$query =
			'SELECT
				t.id,
				t.tournament_sport_id,
				t.parent_tournament_id,
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
				t.private_flag,
				t.created_date,
				t.updated_date,
				t.betting_closed_date,
				t.reinvest_winnings_flag,
				eg.id AS meeting_id,
				eg.tournament_competition_id,
				s.name AS sport_name,
				s.description AS sport_description,
				eg.id AS meeting_id,
				eg.name AS meeting_name,
				eg.meeting_code,
				eg.events,
				eg.track,
				eg.weather
			FROM
				' . $db->nameQuote('#__tournament') . ' AS t
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			ON
				t.tournament_sport_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				t.event_group_id = eg.id
			WHERE
				s.name IN ("galloping", "harness", "greyhounds")
			AND
				t.id = ' . $db->quote($tournament_id);

		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 * Load a list of racing tournaments which are still active and taking bets
	 *
	 * @param int $limit
	 * @param int $jackpot
	 * @return array
	 */
	public function getTournamentRacingActiveList($list_params = array())
	{
		$list_params['type'] = 'racing';
		return $this->getTournamentActiveList($list_params);
	}
}
