<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class TournamentModelSelectionResult extends SuperModel
{
	protected $_table_name = '#__selection_result';

	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'selection_id' => array(
			'name' => 'Selection ID',
			'type' => self::TYPE_INTEGER
		),
		'position' => array(
			'name' => 'Position',
			'type' => self::TYPE_INTEGER
		),
		'win_dividend' => array(
			'name' => 'Win Dividend',
			'type' => self::TYPE_FLOAT
		),
		'place_dividend' => array(
			'name' => 'Place Dividend',
			'type' => self::TYPE_FLOAT
		),
		'created_date' => array(
			'name' => 'Created Date',
			'type' => self::TYPE_DATETIME_CREATED
		),
		'updated_date' => array(
			'name' => 'Updated Date',
			'type' => self::TYPE_DATETIME_UPDATED
		)
	);

	public function getSelectionResult($id)
	{
		return $this->load($id);
	}

	public function getSelectionResultBySelectionID($selection_id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('selection_id', $selection_id)
		),	SuperModel::FINDER_SINGLE);
	}

	private function _getEventIDTable($event_id, $add_column_list = false)
	{
		$event = new DatabaseQueryTable('#__event');
		$event->addWhere('id', $event_id);

		$market = new DatabaseQueryTable('#__market');
		$market->addJoin($event, 'event_id', 'id');

		$selection = new DatabaseQueryTable('#__selection');
		$selection->addJoin($market, 'market_id', 'id');

		$result = ($add_column_list) ? $this->_getTable() : new DatabaseQueryTable($this->_table_name);
		$result->addJoin($selection, 'selection_id', 'id');

		return $result;
	}
	
	public function getSelectionResultListByEventID($event_id)
	{
//		$result = $this	->_getEventIDTable($event_id, true);
//		$result->addOrder('position');
//
//		$db =& $this->getDBO();
//		$query = new DatabaseQuery($result);
//
//		$db->setQuery($query->getSelect());
//		return $this->_loadModelList($db->loadObjectList());

		$db =& $this->getDBO();
		$query =
			'SELECT
				sr.id,
				sr.position,
				sr.win_dividend,
				sr.place_dividend,
				s.id AS selection_id,
				s.number AS runner_number,
				s.name AS selection_name,
				s.external_selection_id,
				sp.win_odds,
				sp.place_odds,
				sp.override_odds,
				mk.id AS market_id,
				mk.external_market_id,
				e.id AS event_id,
				e.external_event_id,
				e.event_status_id,
				e.paid_flag,
				e.trifecta_dividend,
				e.firstfour_dividend,
				e.quinella_dividend,
				e.exacta_dividend,
				e.trifecta_pool,
				e.firstfour_pool,
				e.quinella_pool,
				e.exacta_pool,
				mt.name AS market_name
			FROM
				' . $db->nameQuote('#__selection_result') . ' AS sr
			LEFT JOIN
				' . $db->nameQuote('#__selection') . ' AS s
				ON s.id = sr.selection_id
			LEFT JOIN
				' . $db->nameQuote('#__selection_price') . ' AS sp
				ON sp.selection_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__market') . ' AS mk
				ON mk.id = s.market_id
			INNER JOIN
				' . $db->nameQuote('#__market_type') . ' AS mt
				ON mt.id = mk.market_type_id
			INNER JOIN
				' . $db->nameQuote('#__event') . ' AS e
				ON e.id = mk.event_id
			WHERE
				e.id = ' . $db->quote($event_id) . '
			ORDER BY
				sr.position
			'
		;

		$db->setQuery($query);
		
		return $db->loadObjectList();
	}

	public function getSelectionResultListByPositionAndEventID($position, $event_id)
	{
		$result = $this	->_getEventIDTable($event_id, true)
						->addWhere('position', $position);

		$db =& $this->getDBO();
		$query = new DatabaseQuery($result);

		$db->setQuery($query->getSelect());

		return $this->_loadModelList($db->loadObjectList());
	}

	public function isSelectionResultImported($event_id)
	{
		$result = $this	->_getEventIDTable($event_id)
						->addFunction('*');

		$db =& $this->getDBO();

		$query = new DatabaseQuery($result);
		$db->setQuery($query->getSelect());

		return $db->loadResult();
	}

	public function isSelectionResultChanged($event_id, $result_list)
	{
		$result = $this	->_getEventIDTable($event_id, true);
		$db =& $this->getDBO();

		$query = new DatabaseQuery($result);
		$db->setQuery($query->getSelect());

		$raw_list = $db->loadObjectList();
		if(empty($raw_list)) {
			return true;
		}

		$position_list = array();
		foreach($result_list as $result_item) {
			$position_list[$result_item['selection_id']] = $result['position'];
		}

		$current_list = array();
		foreach($raw_list as $raw) {
			$current_list[$raw->selection_id] = $raw->position;
		}

		return !($current_list == $position_list);
	}

	public function deleteSelectionResultByEventID($event_id)
	{
		$result = $this->_getEventIDTable($event_id);

		$db =& $this->getDBO();
		$query = new DatabaseQuery($result);
		$db->setQuery($query->getDelete());
		return $db->query();
	}
	
	public function deleteSelectionResultBySelectionID($selection_id){
		$selection_result = $this->_getTable();
		$selection_result->addWhere('selection_id', $selection_id);
		
		$db =& $this->getDBO();
		$query = new DatabaseQuery($selection_result);
		$db->setQuery($query->getDelete());
		return $db->query();
	}

	public function replaceResultList($event_id, $result_list)
	{
		$return = false;
		if($this->deleteSelectionResultByEventID($event_id)) {
			$return = true;
			foreach($result_list as $new) {
				$this->store($new);
			}
		}
		return $return;
	}
	
	public function getSelectionResultByMarketID($market_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				sr.selection_id
			FROM
				' . $db->nameQuote('#__selection_result') . ' as sr
			INNER JOIN
				' . $db->nameQuote('#__selection') . ' as s
			ON
				s.id = sr.selection_id
			WHERE
				s.market_id = ' . $db->quote($market_id);

		$db->setQuery($query);
		return $db->loadObject()->selection_id;

	}
	
}
