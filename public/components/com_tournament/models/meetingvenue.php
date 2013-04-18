<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TournamentModelMeetingVenue extends JModel
{
	/**
	 * Get a meeting venue record by ID
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getMeetingVenue($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				name,
				meeting_state_id,
				meeting_territory_id
			FROM
				' . $db->nameQuote('#__meeting_venue') . '
			WHERE
				id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Get a meeting venue record by name
	 *
	 * @param string 	$name
	 * @param bool 		$exact
	 * @return object
	 */
	public function getMeetingVenueByName($name, $exact = true)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				v.id,
				v.name,
				v.meeting_state_id,
				v.meeting_territory_id,
				s.name as state,
				t.name as territory
			FROM
				' . $db->nameQuote('#__meeting_venue') . ' v
			LEFT JOIN ' . $db->nameQuote('#__meeting_state') . ' s
				ON s.id = v.meeting_state_id
			LEFT JOIN ' . $db->nameQuote('#__meeting_territory') . ' t
				ON t.id = v.meeting_territory_id
			WHERE
				v.name ';

		if($exact) {
			$query .= '= ' . $db->quote($name);
		} else {
			$name = "%{$name}%";
			$query .= 'LIKE(' . $db->quote($name) . ')';
		}

		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 * Get a meeting venue record by name
	 *
	 * @param int		$state_id
	 * @param int		$territory_id
	 * @param string 	$name
	 * @param bool 		$exact
	 * @return int
	 */
	public function getMeetingVenueListByStateIDTerritoryIDAndVenueName($state_id = null, $territory_id = null, $name = null, $order = null, $direction = null, $limit = null, $offset = null)
	{
		if(is_null($order)) {
			$order = (empty($this->order)) ? 'tm.name' : $this->order;
		}
		if(is_null($direction)) {
			$direction = (empty($this->direction)) ? 'ASC' : $this->direction;
		}
		if(is_null($limit)) {
			$limit = (empty($this->limit)) ? 0 : $this->limit;
		}
		if(is_null($offset)) {
			$offset = (empty($this->offset)) ? 0 : $this->offset;
		}
		
		$db =& $this->getDBO();
		$query =
			'SELECT
				v.id,
				v.name,
				v.meeting_state_id,
				v.meeting_territory_id,
				s.name as state,
				t.name as territory
			FROM
				' . $db->nameQuote('#__meeting_venue') . ' v
			LEFT JOIN ' . $db->nameQuote('#__meeting_state') . ' s
				ON s.id = v.meeting_state_id
			LEFT JOIN ' . $db->nameQuote('#__meeting_territory') . ' t
				ON t.id = v.meeting_territory_id';
		$cond = array();
		
		if(!empty($state_id)) {
			$cond[] = 'meeting_state_id = ' . $db->quote($state_id);
		}
		
		if(!empty($territory_id)) {
			$cond[] = 'meeting_territory_id = ' . $db->quote($territory_id);
		}
		
		if(!empty($name)) {
			$cond[] = 'lower(v.name) LIKE lower(' . $db->quote('%' . $name . '%').')';
		}
		
		if($cond) {
			$cond	= join(' AND ', $cond);
			$query	.= ' WHERE '. $cond;
		}
		
		if(!empty($order)) {
			$query .= ' ORDER BY ' . $db->nameQuote($order);
		}

		if(!empty($direction)) {
			$query .= ' ' . $direction;
		}

		$db->setQuery($query, $offset, $limit);
		return $db->loadObjectList();
	}
	
	public function getMeetingVenueListByTerritoryName($territory_list)
	{
		$db =& $this->getDBO();
		
		if (!is_array($territory_list)) {
			$territory_list = array($territory_list);
		}
		
		$territory_clean = array();
		foreach ($territory_list as $territory) {
			$territory_clean[] = $db->quote(strtolower($territory));
		}
		
		$query = '
			SELECT
				v.name,
				v.meeting_state_id,
				v.meeting_territory_id
			FROM
				' . $db->nameQuote('#__meeting_venue') . ' AS v
			LEFT JOIN
				' . $db->nameQuote('#__meeting_territory') . ' AS t
			ON
				v.meeting_territory_id = t.id
			';
		
		if ($territory_list) {
			$query .= '
				WHERE
					LOWER(t.name) IN (' . implode(',', $territory_clean) . ')
			';
		}
		
		$query .= '
			ORDER BY
				v.name
		';
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getMeetingVenueList(){
		$db =& $this->getDBO();
				
		$query = '
			SELECT
				name,
				meeting_state_id,
				meeting_territory_id
			FROM
				' . $db->nameQuote('#__meeting_venue') .' ORDER BY name';
				
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function getTotalMeetingVenueCount($state_id = null, $territory_id = null, $name = null)
	{
		$db =& $this->getDBO();
		$query = 'SELECT count(v.id)
			FROM
				' . $db->nameQuote('#__meeting_venue') . ' v
			LEFT JOIN ' . $db->nameQuote('#__meeting_state') . ' s
				ON s.id = v.meeting_state_id
			LEFT JOIN ' . $db->nameQuote('#__meeting_territory') . ' t
				ON t.id = v.meeting_territory_id';
		
		$cond = array();
		
		if(!empty($state_id)) {
			$cond[] = 'meeting_state_id = ' . $db->quote($state_id);
		}
		
		if(!empty($territory_id)) {
			$cond[] = 'meeting_territory_id = ' . $db->quote($territory_id);
		}
		
		if(!empty($name)) {
			$cond[] = 'lower(v.name) LIKE lower(' . $db->quote('%' . $name . '%') . ')';
		}
		
		if($cond) {
			$cond	= join(' AND ', $cond);
			$query	.= ' WHERE '. $cond;
		}
		
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Store a meeting record
	 *
	 * @param array $data
	 * @return mixed Will be the insert ID if a new record was created
	 */
	public function store($params)
	{
		$db =& $this->getDBO();
		if(empty($params['id'])) {
			return $this->_insert($params, $db);
		} else {
			return $this->_update($params, $db);
		}
	}

	/**
	 * Insert a new meeting venue record
	 *
	 * @param array 	$data
	 * @param JDatabase $db
	 * @param mixed
	 */
	private function _insert($params, $db = null)
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'INSERT INTO ' . $db->nameQuote('#__meeting_venue') . ' (
				name,
				meeting_state_id,
				meeting_territory_id
			) VALUES (
				' . $db->quote($params['name']) . ',
				' . (isset($params['meeting_state_id']) ? $db->quote($params['meeting_state_id']) : 'NULL') . ',
				' . (isset($params['meeting_territory_id']) ? $db->quote($params['meeting_territory_id']) : 'NULL') . '
			)';

		$db->setQuery($query);
		if($db->query()) {
			return $db->insertId();
		}

		return false;
	}

	/**
	 * Update an existing meeting venue record
	 *
	 * @param array 	$data
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
				' . $db->nameQuote('#__meeting_venue') . '
			SET ';

		$values = array();
		foreach($params as $field => $data){
			$values[] = $field.' = '.$db->quote($data);
		}
		$query .= join( ',', $values);

		$query .='
			WHERE
				id = ' . $db->quote($params['id']);

		$db->setQuery($query);
		return $db->query();
	}
}