<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class TournamentModelMarket extends SuperModel
{
	public $offer_market_limit = array(
		1 => 'unlimited',
		2 => 50000,
		3 => 50000,
		4 => 25000,
		5 => 25000,
		6 => 25000,
		7 => 25000,
		8 => 25000,
		9 => 10000,
	);
	
	protected $_table_name = '#__market';

	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
		),
		'event_id' => array(
			'name' => 'Event ID',
			'type' => self::TYPE_INTEGER
		),
		'market_type_id' => array(
			'name' => 'Market Type ID',
			'type' => self::TYPE_INTEGER
		),
		'external_market_id' => array(
			'name' => 'External Market ID',
			'type' => self::TYPE_INTEGER
		),
		'wagering_api_id' => array(
			'name' => 'Wagering API Id',
			'type' => self::TYPE_INTEGER
		),
		'refund_flag' => array(
			'name' => 'Refund Flag',
			'type' => self::TYPE_INTEGER
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

	public function getMarket($id)
	{
		return $this->load($id);
	}
	
	public function getMarketByExternalMarketID($event_id)
	{
		return $this->find(array(
		SuperModel::getFinderCriteria('external_market_id', $event_id)
		), 	SuperModel::FINDER_SINGLE);
	}

	public function getMarketListByEventID($event_id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('event_id', $event_id)
		), 	SuperModel::FINDER_LIST);
	}
	
	public function getMarketByEventID($event_id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('event_id', $event_id)
		), 	SuperModel::FINDER_SINGLE);
	}
	
	public function getExternalMarketIDListByEvenGroupID($event_group_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				external_market_id
			FROM
				' . $db->nameQuote('#__market') . ' AS m
			INNER JOIN
				' . $db->nameQuote('#__event_group_event') . ' AS ege
			ON
				m.event_id = ege.event_id
			WHERE
				ege.event_group_id = ' . $db->quote($event_group_id);
		$db->setQuery($query);

		return $db->loadResultArray();
	}
	
	/**
	 * Load tournament market array list by match id
	 *
	 * @param integer $tournament_match_id
	 * @return array
	 */
	public function getMarketArrayByEventID($event_id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				m.id,
				m.market_type_id,
				m.line,
				mt.name,
				mt.description
			FROM
				' . $db->nameQuote('#__market') . ' AS m
			INNER JOIN
				' . $db->nameQuote('#__market_type') . ' AS mt
			ON m.market_type_id = mt.id
			WHERE
				m.event_id = ' . $db->quote($event_id);
		$db->setQuery($query);

		return $db->loadAssocList();
	}
	
	/**
	 * Get market Ids by Match ID & Event ID
	 */
	public function getMarketIDsByEventID($event_id)
	{
		$db =& $this->getDBO();
		$query =
				'SELECT GROUP_CONCAT(id) AS markets FROM
					' . $db->nameQuote('#__market') . '
				WHERE
					event_id = ' . $db->quote($event_id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public function getMarketListByEventIDAndEventGroupID($event_id, $event_group_id)
	{
		$db =& $this->getDBO();
		$query = '
			SELECT
				m.id,
				m.event_id,
				m.market_type_id,
				m.external_market_id,
				m.refund_flag,
				mt.name,
				mt.description
			FROM
				' . $db->nameQuote('#__event_group_market_type') . ' AS egbt
			LEFT JOIN
				' . $db->nameQuote('#__market') . ' AS m
				ON m.market_type_id = egbt.market_type_id
			INNER JOIN
				' . $db->nameQuote('#__market_type') . ' AS mt
				ON mt.id = egbt.market_type_id
			WHERE
				egbt.event_group_id = ' . $db->quote($event_group_id) . '
			AND
				m.event_id = ' . $db->quote($event_id);
		;
		$db->setQuery($query);

		return $db->loadObjectList('id');
	}
	
	/*
	 * DELETE All Markets when Match gets Deleted
	 */
	public function deleteMarketByID($market_ids = NULL){
		if(!empty($market_ids)){
			$db =& $this->getDBO();

			$query =
			'DELETE FROM
			' . $db->nameQuote('#__market') . '
			WHERE
				id IN(' . $market_ids .')';
			$db->setQuery($query);
			return $db->query();
		}
	}
	
	/**
	* Get oldest active market
	*
	* @return object
	*/
	public function getActiveTournamentMarketByUpdatedDate($sports_only=false)
	{
		$db =& $this->getDBO();
		$query = '
				SELECT
					mt.name,
					mk.id,
					mk.event_id,
					mk.market_type_id,
					mk.external_market_id,
					mk.wagering_api_id,
					mk.refund_flag
				FROM
					' . $db->nameQuote('#__market'). ' AS mk
				INNER JOIN
					' . $db->nameQuote('#__market_type'). ' AS mt
				ON
					mk.market_type_id=mt.id
				INNER JOIN
					' . $db->nameQuote('#__event'). ' AS e
				ON
					mk.event_id = e.id
				WHERE
					e.start_date > NOW()
				';
		if ($sports_only) {
			$query .= '
				AND
					mt.name != "Racing"
			';
		}
		$query .='
				ORDER BY
					mk.updated_date ASC';
	
		$db->setQuery($query);
		return $this->_loadModel($db->loadObject());
	}
	
	/**
	 * Set market refund flag
	 *
	 * @param integer $market_id
	 * @return void
	 */
	public function setMarketToRefund($market_id)
	{
		$params = array(
			'id' 			=> $market_id,
			'refund_flag'	=> 1
		);

		$this->store($params);
	}
	
}
