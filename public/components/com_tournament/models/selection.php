<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class TournamentModelSelection extends SuperModel
{
	protected $_table_name = '#__selection';

	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'market_id' => array(
			'name' => 'Market ID',
			'type' => self::TYPE_INTEGER
		),
		'external_selection_id' => array(
			'name' => 'External Selection ID',
			'type' => self::TYPE_STRING
		),
		'wager_id' => array(
			'name' => 'Internal Runner ID',
			'type' => self::TYPE_INTEGER
		),
		'wagering_api_id' => array(
			'name' => 'Wagering Api ID',
			'type' => self::TYPE_INTEGER
		),
		'selection_status_id' => array(
			'name' => 'Selection Status ID',
			'type' => self::TYPE_INTEGER
		),
		'silk_id' => array(
			'name' => 'Risa Silk ID',
			'type' => self::TYPE_INTEGER
		),
		'name' => array(
			'name' => 'Name',
			'type' => self::TYPE_STRING
		),
		'created_date' => array(
			'name' => 'Created Date',
			'type' => self::TYPE_DATETIME_CREATED
		),
		'updated_date' => array(
			'name' => 'Updated Date',
			'type' => self::TYPE_DATETIME_UPDATED
		),
		'weight' => array(
			'name' => 'Weight',
			'type' => self::TYPE_FLOAT
		)
	);
	
	/**
	* Not scratched
	*
	* @var string
	*/
	const STATUS_NOT_SCRATCHED = 'not scratched';
	
	/**
	 * Scratched
	 *
	 * @var string
	 */
	const STATUS_SCRATCHED = 'scratched';
	
	/**
	 * Late scratching
	 *
	 * @var string
	 */
	const STATUS_LATE_SCRATCHING = 'late scratching';
	
	/**
	 * Feed statuses for Selections
	 *
	 * @var array
	 */
	protected $feed_status_list = array(
	0 => self::STATUS_NOT_SCRATCHED,
	1 => self::STATUS_SCRATCHED,
	2 => self::STATUS_LATE_SCRATCHING,
	3 => self::STATUS_SCRATCHED
	);
	
	/**
	* Statuses which indicating a scratching
	*
	* @var array
	*/
	protected $scratch_status_list = array(
	self::STATUS_SCRATCHED,
	self::STATUS_LATE_SCRATCHING
	);
	
	public function getSelection($id)
	{
		return $this->load($id);
	}
	
	public function getSelectionByExternalSelectionIdAndWageringApiId($id, $wagering_api_id)
	{
	    return $this->find(array(
			SuperModel::getFinderCriteria('external_selection_id', $id),
			SuperModel::getFinderCriteria('wagering_api_id', $wagering_api_id)
		),	SuperModel::FINDER_SINGLE);
	}

	public function getSelectionByExternalIDAndMarketID($id, $market_id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('external_selection_id', $id),
			SuperModel::getFinderCriteria('market_id', $market_id)
		),	SuperModel::FINDER_SINGLE);
	}

	public function getSelectionListByMarketID($market_id, $include_price = true)
	{
		if($include_price) {
			$price = new DatabaseQueryTable('#__selection_price');
			$price	->addColumn('bet_product_id')
					->addColumn('win_odds')
					->addColumn('place_odds')
					->addColumn('override_odds');

			$table = $this->_getTable();
			$table	->addWhere('market_id', $market_id)
					->addJoin($price, 'id', 'selection_id');

			$query = new DatabaseQuery($table);
			$db =& $this->getDBO();

			$db->setQuery($query->getSelect());
			return $db->loadObjectList();
		}

		return $this->find(array(
			SuperModel::getFinderCriteria('market_id', $market_id)
		),	SuperModel::FINDER_LIST, 'id');
	}

	public function getSelectionListByEventID($event_id)
	{
		$join = new DatabaseQueryTable('#__market');
		$join->addWhere('event_id', $event_id);

		$table = $this	->_getTable()
						->addJoin($join, 'market_id', 'id');

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table);

		$db->setQuery($query->getSelect());
		return $this->_loadModelList($db->loadObjectList('id'));
	}

	public function deleteSelectionList($selection_list)
	{
		$table = new DatabaseQueryTable($this->_table_name);
		$table->addInWhere('id', implode(', ', $selection_list));

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table);

		$db->setQuery($query->getDelete());
		return $db->query();
	}
	
	/**
	 * Get selection list which odds has been updated against market timestamp
	 *
	 * @param integer $market_id
	 * @param timestamp $last_updated_time
	 * @return array
	 */
	public function getUpdatedSelectionListByMarketID($market_id, $last_updated_time)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				s.id,
				s.name,
				mt.name as market_type,
				m.id as market_id,
				sp.override_odds,
				sp.win_odds,
				sp.bet_product_id
			FROM
				' . $db->nameQuote('#__market') . ' as m
			LEFT JOIN
				' . $db->nameQuote('#__selection') . ' as s
			ON
				s.market_id = m.id
						
			LEFT JOIN
				' . $db->nameQuote('#__market_type') . ' as mt
			ON
				mt.id = m.market_type_id
						
			LEFT JOIN
				' . $db->nameQuote('#__selection_price') . ' as sp
			ON
				sp.selection_id = s.id
			WHERE
				m.id = ' . $db->quote($market_id) . '
			AND
				UNIX_TIMESTAMP(sp.updated_date) > ' . $db->quote($last_updated_time);
		$db->setQuery($query);
		return $db->loadObjectList('selection_id');

	}
	
	public function getSelectionDetails($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				s.id,
				s.name,
				mt.name as market_type,
				m.id as market_id,
				sp.override_odds,
				sp.win_odds,
				sp.bet_product_id
			FROM
				' . $db->nameQuote('#__selection') . ' as s
			LEFT JOIN
				' . $db->nameQuote('#__market') . ' as m
			ON
				s.market_id = m.id
			LEFT JOIN
				' . $db->nameQuote('#__market_type') . ' as mt
			ON
				mt.id = m.market_type_id
			LEFT JOIN
				' . $db->nameQuote('#__selection_price') . ' as sp
			ON
				sp.selection_id = s.id
			WHERE
				s.id = ' . $db->quote($id);
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Get active selection list by event_id
	 *
	 * @param integer $event_id
	 * @return array
	 */
	public function getActiveTournamentSelectionListByEventID($id)
	{
		$db =& $this->getDBO();
		$query = '
			SELECT
				s.id,
				s.name,
				s.external_selection_id,
				mt.name as market_type,
				m.id as market_id,
				sp.id as selection_price_id,
				sp.win_odds,
				sp.place_odds,
				sp.override_odds,
				sp.bet_product_id
			FROM
				' . $db->nameQuote('#__selection') . ' as s
			LEFT JOIN
				' . $db->nameQuote('#__tournament_bet_selection') . ' as bs
			ON
				s.id = bs.selection_id
			LEFT JOIN
				' . $db->nameQuote('#__selection_price') . ' as sp
			ON
				sp.selection_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' as m
			ON
				m.id = s.market_id
			INNER JOIN
				' . $db->nameQuote('#__market_type') . ' as mt
			ON
				mt.id = m.market_type_id
			WHERE
				m.event_id = ' . $db->quote($id) . '
			AND
				m.market_type_id IN
					(SELECT market_type_id
						FROM
							' . $db->nameQuote('#__event_group_market_type') . ' as egmt
						LEFT JOIN
							' . $db->nameQuote('#__event_group_event') . ' as ege
						ON
							egmt.event_group_id = ege.event_group_id
					 	WHERE
					 		ege.event_id = ' . $db->quote($id) . '
					 )
			GROUP BY
				s.id
			ORDER BY market_id 				
					
		';
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* Check if a status indicates a scratching
	*
	* @param string $status
	* @return array
	*/
	public function isScratched($status)
	{
		return (in_array(strtolower($status), $this->scratch_status_list));
	}
}
