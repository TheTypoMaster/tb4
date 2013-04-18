<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelResult extends JModel
{
	/**
	 * Load a single record from the #__result table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getResult($id) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				race_id,
				runner_id,
				position,
				payout_flag,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__result') . '
			WHERE
				id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Get a result record for a race and runner
	 *
	 * @param integer $race_id
	 * @param integer $runner_id
	 * @return object
	 */
	public function getResultByRaceAndRunnerID($race_id, $runner_id) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				race_id,
				runner_id,
				position,
				payout_flag,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__result') . '
			WHERE
				race_id = ' . $db->quote($race_id) . '
			AND
				runner_id = ' . $db->quote($runner_id);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Get result records for a race and place position. Returns an array since
	 * multiple runners may occupy the same position.
	 *
	 * @param integer $position
	 * @param integer $race_id
	 * @return array
	 */
	public function getResultListByPositionAndRaceID($position, $race_id) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				race_id,
				runner_id,
				position,
				payout_flag,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__result') . '
			WHERE
				position = ' . $db->quote($position) . '
			AND
				race_id = ' . $db->quote($race_id);

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Checks for 1 or more results in the result table for a race
	 *
	 * @param integer $race_id
	 * @return bool
	 */
	public function isResultImported($race_id) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				COUNT(*)
			FROM
				' . $db->nameQuote('#__result') . '
			WHERE
				race_id = ' . $db->quote($race_id);

		$db->setQuery($query);
		$count = $db->loadResult();

		return ($count > 0);
	}

	/**
	 * Compare a result list with the current data
	 *
	 * @param array $result_list
	 * @param bool True if the results match
	 */
	public function isResultChanged($race_id, $result_list) {
		$position_list = array();

		foreach($result_list as $result) {
			$position_list[$result['runner_id']] = $result['position'];
		}

		$db =& $this->getDBO();
		$query =
			'SELECT
				runner_id,
				position
			FROM
				' . $db->nameQuote('#__result') . '
			WHERE
				race_id = ' . $db->quote($race_id);

		$db->setQuery($query);
		$raw_list = $db->loadObjectList();

		if(empty($raw_list)) {
			return true;
		}

		$current_list = array();
		foreach($raw_list as $raw) {
			$current_list[$raw->runner_id] = $raw->position;
		}

		return !($current_list == $position_list);
	}

	/**
	 * Delete results for a race. This should only happen in cases of disqualification
	 * where the results need to be completely replaced.
	 *
	 * @param integer $race_id
	 * @return bool
	 */
	public function deleteResultByRaceID($race_id) {
		$db =& $this->getDBO();
		$query =
			'DELETE FROM
				' . $db->nameQuote('#__result') . '
			WHERE
				race_id = ' . $db->quote($race_id);

		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Delete an old race result if it's changed and insert a new result
	 *
	 * @param integer 	$race_id
	 * @param array 	$result_list
	 * @return bool
	 */
	public function replaceResultList($race_id, $result_list) {
		$db =& $this->getDBO();
		$query =
			'INSERT INTO ' . $db->nameQuote('#__result') . ' (
				race_id,
				runner_id,
				position,
				payout_flag,
				created_date
			) VALUES ';

		$insert_list = array();
		foreach($result_list as $result) {
			$insert_list[] = '(
				' . $db->quote($result['race_id']) . ',
				' . $db->quote($result['runner_id']) . ',
				' . $db->quote($result['position']) . ',
				' . $db->quote($result['payout_flag']) . ',
				NOW()
			)';
		}

		$query .= implode(', ', $insert_list);

		if($this->deleteResultByRaceID($race_id)) {
			$db->setQuery($query);
			return $db->query();
		}

		return false;
	}

	/**
	 * Get all of the results for a single race
	 *
	 * @param integer $id
	 * @return array
	 */
	public function getResultListByRaceID($id) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				r.position,
				r.payout_flag,
				n.name,
				n.number,
				n.win_odds,
				n.place_odds
			FROM
				' . $db->nameQuote('#__result') . ' AS r
			INNER JOIN
				' . $db->nameQuote('#__runner') . ' AS n
			ON
				n.id = r.runner_id
			WHERE
				r.race_id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Store a result record. Will determine whether to insert or update based
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
	 * Insert a new result record.
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
			'INSERT INTO ' . $db->nameQuote('#__result') . ' (
				race_id,
				runner_id,
				position,
				payout_flag,
				created_date
			) VALUES (
				' . $db->quote($params['race_id']) . ',
				' . $db->quote($params['runner_id']) . ',
				' . $db->quote($params['position']) . ',
				' . $db->quote($params['payout_flag']) . ',
				NOW()
			)';

		$db->setQuery($query);
		$db->query();

		return $db->insertId();
	}

	/**
	 * Update an existing result record.
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
				' . $db->nameQuote('#__result') . '
			SET
				id = ' . $db->quote($params['id']) . ',
				race_id = ' . $db->quote($params['race_id']) . ',
				runner_id = ' . $db->quote($params['runner_id']) . ',
				position = ' . $db->quote($params['position']) . ',
				payout_flag = ' . $db->quote($params['payout_flag']) . '
				updated_date = NOW()
			WHERE
				id = ' . $db->quote($params['id']);

		$db->setQuery($query);
		return $db->query();
	}

}

?>