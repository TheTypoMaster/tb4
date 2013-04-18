<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelMeetingTerritory extends JModel
{
	/**
	 * Load a single record from the #__territory table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */

	public function getMeetingTerritory($id) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				name
			FROM
				' . $db->nameQuote('#__meeting_territory') . '
			WHERE
				id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObject();

	}
	
	/**
	 * Get a territory record by name
	 *
	 * @param string 	$name
	 * @return object
	 */
	public function getMeetingTerritoryByName($name) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				name
			FROM
				' . $db->nameQuote('#__meeting_territory') . '
			WHERE
				lower(name) = lower(' . $db->quote($name) . ')';
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	* Load all the territories from the Table
	*
	* @return array
	*/
	public function getMeetingTerritoryList() {
		$db =& $this->getDBO();

		$query =
			'SELECT
				id,
				name
			FROM
				' . $db->nameQuote('#__meeting_territory') . '
			ORDER BY
				name ASC';

		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	
	/**
	 * Store a territory record. Will determine whether to insert or update based
	 * on the presence of an ID.
	 *
	 * @param array $params
	 * @return bool
	 */
	public function store($params) {
		$db =& $this->getDBO();
		if(empty($params['id'])) {
			$result = $this->_insert($params, $db);
		} else {
			$result = $this->_update($params, $db);
		}

		return $result;
	}

	/**
	 * Insert a new territory record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _insert($params, $db = null) {
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'INSERT INTO ' . $db->nameQuote('#__meeting_territory') . ' (
				id,
				name
			) VALUES (
				' . $db->quote($params['id']) . ',
				' . $db->quote($params['name']) . '
			)';

		$db->setQuery($query);
		$db->query();

		return $db->insertId();
	}

	/**
	 * Update an existing territory record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _update($params, $db = null) {
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'UPDATE
				' . $db->nameQuote('#__meeting_territory') . '
			SET
				id = ' . $db->quote($params['id']) . ',
				name = ' . $db->quote($params['name']) . '
			WHERE
				id = ' . $db->quote($params['id']);

		$db->setQuery($query);
		return $db->query();
	}
}
