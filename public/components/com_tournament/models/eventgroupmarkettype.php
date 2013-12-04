<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class TournamentModelEventGroupMarketType extends SuperModel
{
	protected $_table_name = '#__event_group_market_type';

	protected $_member_list = array(
		'event_group_id' => array(
			'name' => 'Event Group ID',
			'type' => self::TYPE_INTEGER
		),
		'market_type_id' => array(
			'name' => 'Market Type ID',
			'type' => self::TYPE_INTEGER
		)
	);

	public function isEventGroupMarketTypeAdded($event_group_id, $market_type_id)
	{
//		$table = new DatabaseQueryTable($this->_table_name);
//		$table	->addFunction('*')
//				->addWhere('event_group_id', $event_group_id)
//				->addWhere('tournament_id', $tournament_id);
//
//		$db =& $this->getDBO();
//		$query = new DatabaseQuery($table);
//
//		$db->setQuery($query->getSelect());

		$db =& $this->getDBO();
		$query = '
			SELECT
				count(*)
			FROM
				' . $db->nameQuote('#__event_group_market_type') . '
			WHERE
				event_group_id = ' . $db->quote($event_group_id) . '
			AND
				market_type_id = ' . $db->quote($market_type_id);
		
		$db->setQuery($query);
		
		return (bool)$db->loadResult();
	}
	
	public function isEventGroupMarketAdded($event_group_id, $market_id)
	{

		$db =& $this->getDBO();
		$query = '
			SELECT
				count(*)
			FROM
				' . $db->nameQuote('#__event_group_market_type') . '
			WHERE
				event_group_id = ' . $db->quote($event_group_id) . '
			AND
				market_id = ' . $db->quote($market_id);
	
		$db->setQuery($query);
	
		return (bool)$db->loadResult();
	}

	public function addEventGroupMarketType($event_group_id, $market_type_id)
	{
		$this->event_group_id 	= (int)$event_group_id;
		$this->market_type_id	= (int)$market_type_id;

		return $this->save(true);
	}
	
	public function addEventGroupMarket($event_group_id, $market_type_id, $market_id)
	{
		$this->event_group_id 	= (int)$event_group_id;
		$this->market_type_id	= (int)$market_type_id;
		$this->market_id	= (int)$market_id;
	
		return $this->save(true);
	}

	public function removeEventGroupMarketType($event_group_id, $market_type_id)
	{
		$table = $this	->_getTable()
						->addWhere('event_group_id', $event_group_id)
						->addWhere('market_type_id', $market_type_id);

		$query = new DatabaseQuery($table);
		$db =& $this->getDBO();

		$db->setQuery($query->getDelete());
		return $db->query();
	}
	
	public function removeEventGroupMarket($event_group_id, $market_Type_id, $market_id)
	{
		$table = $this	->_getTable()
		->addWhere('event_group_id', $event_group_id)
		->addWhere('market_Type_id', $market_Type_id)
		->addWhere('market_id', $market_id);
	
		$query = new DatabaseQuery($table);
		$db =& $this->getDBO();
	
		$db->setQuery($query->getDelete());
		return $db->query();
	}

	public function getEventGroupMarketTypeListByEventGroupID($event_group_id)
	{
		$table = new DatabaseQueryTable($this->_table_name);
		$table	->addColumn('market_type_id')
				->addWhere('event_group_id', $event_group_id);

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table);

		$db->setQuery($query->getSelect());
		return $db->loadResultArray();
	}
	
	public function getEventGroupMarketListByEventGroupID($event_group_id)
	{
		$table = new DatabaseQueryTable($this->_table_name);
		$table 	->addColumn('market_id')
				->addWhere('event_group_id', $event_group_id);
	
		$db =& $this->getDBO();
		$query = new DatabaseQuery($table);
	
		$db->setQuery($query->getSelect());
		return $db->loadResultArray();
	}
}