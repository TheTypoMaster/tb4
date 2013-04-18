<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
require_once 'selection.php';

class TournamentModelRunner extends TournamentModelSelection
{
	protected $_member_list = array(
		'associate' => array(
			'name' 		=> 'Associate',
			'type' 		=> self::TYPE_STRING
		),
		'barrier' => array(
			'name' 		=> 'Barrier',
			'type' 		=> self::TYPE_STRING
		),
		'handicap' => array(
			'name' 		=> 'Handicap',
			'type' 		=> self::TYPE_STRING
		),
		'ident' => array(
			'name' 		=> 'Ident',
			'type' 		=> self::TYPE_STRING
		),
		'number' => array(
			'name' 		=> 'Number',
			'type' 		=> self::TYPE_INTEGER
		), 
		'wager_id' => array(
			'name' 		=> 'Wager ID',
			'type' 		=> self::TYPE_INTEGER
		)
	);

	


	/**
	 * Load a single record from the #__runner table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getRunner($id)
	{
		return $this->load($id);
	}

	private function _getRaceIDTable($race_id, $add_columns = false)
	{
		$event = new DatabaseQueryTable('#__event');
		$event->addWhere('id', $race_id);

		$market = new DatabaseQueryTable('#__market');
		$market->addJoin($event, 'event_id', 'id');

		$selection_price = new DatabaseQueryTable('#__selection_price');
		$selection_price->addColumn('win_odds')
						->addColumn('place_odds')
						->addColumn('override_odds')
						->addColumn('bet_product_id');

		$selection = ($add_columns) ? $this->_getTable() : new DatabaseQueryTable($this->_table_name);

		$selection->addJoin($market, 'market_id', 'id');
		$selection->addJoin($selection_price, 'id', 'selection_id');

		return $selection;
	}

	/**
	 * Load a single runner record by race ID and runner number
	 *
	 * @param integer $race_id
	 * @param integer $number
	 * @return object
	 */
	public function getRunnerByRaceIDAndNumber($race_id, $number)
	{
		$table = $this->_getRaceIDTable($race_id, true);
		$table->addWhere('external_selection_id', $number);

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table);

		$db->setQuery($query->getSelect());
		return $this->_loadModel($db->loadObject());
	}

	/**
	 * Load a single runner record by race ID and runner number
	 *
	 * @param integer $race_id
	 * @param integer $number
	 * @return object
	 */
	public function getRunnerDetailsByRaceIDAndNumber($race_id, $number)
	{
		/*$table = $this->_getRaceIDTable($race_id, true);
		$table->addWhere('external_selection_id', $number);

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table);

		$db->setQuery($query->getSelect());
		return $this->_loadModel($db->loadObject());*/
		$db =& $this->getDBO();
		$query = '
		SELECT
			s.*,
			s.number,
			sp.win_odds,
			sp.place_odds,
			sp.bet_product_id,
			sp.w_product_id,
			sp.p_product_id,
			sp.override_odds,
			ss.name AS status,
			sr.win_dividend,
			sr.place_dividend
		FROM
			' . $db->nameQuote('#__selection') . ' AS s
		INNER JOIN
			' . $db->nameQuote('#__market') . ' AS m
		ON
			s.market_id = m.id
		LEFT JOIN
			' . $db->nameQuote('#__selection_price') . ' AS sp
		ON
			s.id = sp.selection_id
		LEFT JOIN
			' . $db->nameQuote('#__selection_result') . ' AS sr
		ON
			sr.selection_id = s.id
		INNER JOIN
			' . $db->nameQuote('#__selection_status') . ' AS ss
		ON
			s.selection_status_id = ss.id
		INNER JOIN
			' . $db->nameQuote('#__event') . ' AS e
		ON
			m.event_id = e.id
		WHERE
			e.id = ' . $db->quote($race_id) . ' 
			AND s.number = ' . $db->quote($number) . '
		ORDER
			BY number ASC
		';
		$db->setQuery($query);
		return $db->loadObjectList();

	}

	/**
	 * Get a list of runners for a specific race ID
	 *
	 * @param integer $id
	 * @return array
	 */
	public function getRunnerListByRaceID($id)
	{
//		$table = $this->_getRaceIDTable($id);
//		
//		$status_table = new DatabaseQueryTable('#__selection_status');
//		$status_table	->addColumn(new DatabaseQueryTableColumn('name', 'status'));
//		
//		$table	->addJoin($status_table, 'selection_status_id', 'id')
//				->addOrder('external_selection_id');
//
//		$db =& $this->getDBO();
//		$query = new DatabaseQuery($table);
//
//		$db->setQuery($query->getSelect());
		
		//XXX: use super model
		$db =& $this->getDBO();
		$query = '
		SELECT
			s.*,
			sp.win_odds,
			sp.place_odds,
			sp.bet_product_id,
			sp.w_product_id,
			sp.p_product_id,
			sp.override_odds,
			ss.name AS status,
			sr.win_dividend,
			sr.place_dividend
		FROM
			' . $db->nameQuote('#__selection') . ' AS s
		INNER JOIN
			' . $db->nameQuote('#__market') . ' AS m
		ON
			s.market_id = m.id
		LEFT JOIN
			' . $db->nameQuote('#__selection_price') . ' AS sp
		ON
			s.id = sp.selection_id
		LEFT JOIN
			' . $db->nameQuote('#__selection_result') . ' AS sr
		ON
			sr.selection_id = s.id
		INNER JOIN
			' . $db->nameQuote('#__selection_status') . ' AS ss
		ON
			s.selection_status_id = ss.id
		INNER JOIN
			' . $db->nameQuote('#__event') . ' AS e
		ON
			m.event_id = e.id
		WHERE
			e.id = ' . $db->quote($id) . '
		ORDER
			BY number ASC
		';
		$db->setQuery($query);
		return $db->loadObjectList('number');
	}

	/**
	 * Get runner ratings from a 12Follow snapshot
	 *
	 * @param array $runner_list
	 * @return array
	 */
	public function getRunnerRatings($runner_list)
	{
		$db =& $this->getDBO();
//
//		$clean_list = array();
//		foreach($runner_list as $runner) {
//			$clean_list[] = $db->quote($runner);
//		}
//
//		$table = new DatabaseQueryTable('subscription');
//		$table	->addColumn('ident')
//				->addFunction('ident')
//				->addInWhere('ident', $clean_list)
//				->addGroup('ident');
//
//		$query = new DatabaseQuery($table);
//		$db->setQuery($query->getSelect());
//		
//		return $this->_loadModelList($db->loadObjectList('ident'));	

		$clean = array();
		foreach($runner_list as $runner) {
			$clean[] = $db->quote($runner);
		}

		$query =
			'SELECT
				ident,
				COUNT(ident) AS rating
			FROM
				' . $db->nameQuote('subscription') . '
			WHERE
				ident IN(' . implode(',', $clean) . ')
			GROUP BY
				ident';

		$db->setQuery($query);
		return $db->loadObjectList('ident');
	}

	/**
	 * Update one specific odds field for a runner
	 *
	 * @param integer 	$id
	 * @param string 	$field
	 * @param string 	$value
	 * @return bool
	 */
	public function updateOdds($id, $field, $value)
	{
		$table = new DatabaseQueryTable('#__selection_price');
		$table	->addColumn($field, null, $value)
				->addWhere('id', $id);

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table);

		$db->setQuery($query->getSelect());
		return $db->query();
	}

	/**
	 * Apply the ident formatting rules to a runner name
	 *
	 * @param string $name
	 * @return string
	 */
	public function formatIdentFromName($name)
	{
		return strtolower(preg_replace('/[^a-z]/i', '', $name));
	}

	/**
	 * Take a feed status ID and return the local name for it
	 *
	 * @param integer $id
	 * @return string
	 */
	public function getFeedStatusNameByID($id)
	{
		if(empty($this->feed_status_list[$id])) {
			$id = 0;
		}

		return $this->feed_status_list[$id];
	}


}
?>
