<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelTournamentOffer extends JModel
{
	/**
	 * Load an Offer by Tournament Offer ID
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentOffer($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				tournament_market_id,
				external_offer_id,
				name,
				external_odds,
				override_odds,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__tournament_offer') . '
			WHERE
				id = ' . $db->quote($id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	/**
	 * Load an offer by External ID
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentOfferByExternalIDAndMarketId($id, $market_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				tournament_market_id,
				external_offer_id,
				name,
				external_odds,
				override_odds,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__tournament_offer') . '
			WHERE
				external_offer_id = ' . $db->quote($id) .'
			AND
				tournament_market_id  = ' . $db->quote($market_id);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
		 * Load offer list by Market ID
		 *
		 * @param integer $tournament_id
		 * @return object
		 */
		public function getTournamentOfferListByMarketID($id)
		{
			$db =& $this->getDBO();
			$query =
				'SELECT
					id,
					tournament_market_id,
					external_offer_id,
					name,
					external_odds,
					override_odds,
					created_date,
					updated_date
				FROM
					' . $db->nameQuote('#__tournament_offer') . '
				WHERE
					tournament_market_id = ' . $db->quote($id);
			$db->setQuery($query);
			return $db->loadObjectList('id');
		}
	/**
	 * Get offer list by tournament_match_id
	 *
	 * @param integer $tournament_match_id
	 * @return array
	 */
	public function getTournamentOfferListByTournamentMatchID($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				o.id as offer_id,
				o.name as offer,
				bt.name as bet_type,
				m.id as market_id,
				o.override_odds ,
				o.external_odds
			FROM
				' . $db->nameQuote('#__tournament_market') . ' as m
			LEFT JOIN
				' . $db->nameQuote('#__tournament_offer') . ' as o
			ON
				o.tournament_market_id = m.id
			INNER JOIN
				' . $db->nameQuote('#__bet_type') . ' as bt
			ON
				m.bet_type_id = bt.id
			WHERE
				m.tournament_match_id = ' . $db->quote($id);
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Get active offer list by tournament_match_id
	 *
	 * @param integer $tournament_match_id
	 * @return array
	 */
	public function getActiveTournamentOfferListByTournamentMatchID($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				o.id as offer_id,
				o.name as offer,
				bt.name as bet_type,
				m.id as market_id,
				o.override_odds ,
				o.external_odds
			FROM
				' . $db->nameQuote('#__tournament_market') . ' as m
			LEFT JOIN
				' . $db->nameQuote('#__tournament_offer') . ' as o
			ON
				o.tournament_market_id = m.id
			INNER JOIN
				' . $db->nameQuote('#__bet_type') . ' as bt
			ON
				m.bet_type_id = bt.id
			WHERE
				m.tournament_match_id = ' . $db->quote($id) . '
			AND
				m.bet_type_id IN
					(SELECT bet_type_id
						FROM
							' . $db->nameQuote('#__tournament_event_bet_type') . ' as ebt
						LEFT JOIN
							' . $db->nameQuote('#__tournament_event_match') . ' as em
						ON
							ebt.tournament_sport_event_id = em.tournament_event_id
					 	WHERE
					 		em.tournament_match_id = ' . $db->quote($id) . '
					 )
			';
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Get offer list which odds has been updated against market timestamp
	 *
	 * @param integer $market_id
	 * @param timestamp $last_updated_time
	 * @return array
	 */
	public function getUpdatedOfferListByMarketID($market_id, $last_updated_time)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				o.id as offer_id,
				o.name as offer,
				m.name as bet_type,
				m.id as market_id,
				o.override_odds ,
				o.external_odds
			FROM
				' . $db->nameQuote('#__tournament_market') . ' as m
			LEFT JOIN
				' . $db->nameQuote('#__tournament_offer') . ' as o
			ON
				o.tournament_market_id = m.id
			WHERE
				m.id = ' . $db->quote($market_id) . '
			AND
				UNIX_TIMESTAMP(o.updated_date) > ' . $db->quote($last_updated_time);
		$db->setQuery($query);
		return $db->loadObjectList('offer_id');

	}

	/**
	 * Store Method to store Market map data into DB
	 */
	public function store($params)
	{
		$db =& $this->getDBO();
		if($params['id'] > 0) {
			$result = $this->_update($params, $db);
		} else {
				$result = $this->_insert($params, $db);
		}
		return $result;
	}
	/**
	 * Insert a new Offer Data.
	 * @param array $params
	 * @param JDatabase $db
	 * @return integer [insert ID]
	 */
	private function _insert($params, $db = null)
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'INSERT INTO ' . $db->nameQuote('#__tournament_offer') . ' (
				tournament_market_id,
				external_offer_id,
				name,
				external_odds,
				override_odds,
				created_date,
				updated_date
			) VALUES (
				' . $db->quote($params['tournament_market_id']) . ',
				' . $db->quote($params['external_offer_id']) . ',
				' . $db->quote($params['name']) . ',
				' . $db->quote($params['external_odds']) . ',
				' . $db->quote($params['override_odds']) . ',
				NOW(),
				NOW()
			)';

		$db->setQuery($query);
		$db->query();

		return $db->insertId();
	}

	/**
	 * Update a Offer Data.
	 * @param array $params [data]
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _update( $params, $db = null )
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}
		$query =
			'UPDATE
			' . $db->nameQuote('#__tournament_offer') . '
			SET ';
			foreach($params as $field => $data){
				$query .= $field.' = '.$db->quote($data).', ';
			}
			$query .='
				updated_date = NOW()
			WHERE
			id = ' . $db->quote($params['id']);
		$db->setQuery($query);
		return $db->query();
	}
	/**
	 * Delete Offers
	 */
	public function delete( $tournament_market_ids ){
		$db =& $this->getDBO();
		$query =
			'DELETE FROM
				' . $db->nameQuote('#__tournament_offer') . '
			WHERE
				tournament_market_id
				IN(' . $tournament_market_ids . ')';
		$db->setQuery($query);
		return $db->query();
	}
}