<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class BettingModelBetType extends JModel
{
	public $racing_type_list = array(
		'win',
		'place',
		'eachway',
		'quinella',
		'exacta',
		'trifecta',
		'firstfour'
	);

	/**
	 * Load a single bet type record by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getBetType($id, $active_only = false)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				name,
				description,
				status_flag,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__bet_type') . '
			WHERE
				id = ' . $db->quote($id);

		if($active_only) {
			$query .= ' AND status_flag = 1';
		}

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Load a bet type by name
	 *
	 * @param integer $tournament_id
	 * @return object
	 */
	public function getBetTypeByName($name)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				name,
				description,
				status_flag,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__bet_type') . '
			WHERE
				name = ' . $db->quote($name);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Add Bet Type if does not exist and return id
	 *
	 * @param string $name
	 * @param string $description
	 * return int
	 */
	public function addBetTypeIfNotExist($name, $description = ''){

		$bettype = $this->getBetTypeByName($name);

		if(!$bettype){
			$params = array(
				'name'			=> $name,
				'description' 	=> $description,
				'status_flag'	=> 1);

			return $this->store($params);
		}

		return $bettype->id;
	}


	/**
	 * Load a list of bet types by status. Defaults to active ones only.
	 *
	 * @param integer $status
	 * @return object
	 */
	public function getBetTypesByStatus($status_id = 1, $sport_type = null)
	{
		$db =& $this->getDBO();
		$racing_list = array();
		foreach($this->racing_type_list as $racing) {
			$racing_list[] = $db->quote(strtolower($racing));
		}
		$query =
			'SELECT
				id,
				name,
				description,
				status_flag,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__bet_type') . '
			WHERE
				status_flag = ' . $db->quote($status_id);
		if('racing' == $sport_type) {
			$query .='
			AND LOWER(name) IN (' . implode(',', $racing_list) . ')';
		}
		if('sports' == $sport_type) {
			$query .='
			AND LOWER(name) NOT IN (' . implode(',', $racing_list) . ')';
		}

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Store a bet type record. Will determine whether to insert or update based
	 * on the presence of an ID.
	 *
	 * @param array $params
	 * @return bool
	 */
	public function store($params)
	{
		$db =& $this->getDBO();
		if(empty($params['id'])) {
			$result = $this->_insert($params, $db);
		} else {
			$result = $this->_update($params, $db);
		}

		return $result;
	}

	/**
	 * Insert a new bet type record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _insert($params, $db = null)
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'INSERT INTO ' . $db->nameQuote('#__bet_type') . ' (
				name,
				description,
				status_flag,
				created_date
			) VALUES (
				' . $db->quote($params['name']) . ',
				' . $db->quote($params['description']) . ',
				' . $db->quote($params['status_flag']) . ',
				NOW()
			)';

		$db->setQuery($query);
		$db->query();

		return $db->insertId();
	}

	/**
	 * Update an existing bet type record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _update($params, $db = null)
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'UPDATE
				' . $db->nameQuote('#__bet_type') . '
			SET
				name = ' . $db->quote($params['name']) . ',
				description = ' . $db->quote($params['description']) . ',
				status_flag = ' . $db->quote($params['status_flag']) . ',
				updated_date = NOW()
			WHERE
				id = ' . $db->quote($params['id']);

		$db->setQuery($query);
		return $db->query();
	}
}