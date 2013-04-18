<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

/**
 * tournament event Model
 */
class TournamentModelTournamentComment extends JModel
{
	/**
	 * Get a comment record by ID
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentComment($id)
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				tournament_id,
				user_id,
				comment,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__tournament_comment') . '
			WHERE
				id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObject();
	}
	
	
	/**
	 * Get a single tournament event Match record by Match ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentCommentListByTournamentId( $tournament_id )
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				tc.user_id,
				u.username,
				tc.tournament_id,
				tc.comment
			FROM
				' . $db->nameQuote( '#__tournament_comment' ) . ' AS tc
			INNER JOIN
				' . $db->nameQuote( '#__users' ) . ' AS u
			ON
				u.id = tc.user_id
			WHERE
				tournament_id = ' . $db->quote( $tournament_id ) .'
			ORDER BY tc.id DESC';

		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Get tournament list by tournament id and username.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentCommentListByTournamentIDAndUsername($tournament_id = null, $username = null, $params)
	{
		$order		= isset($params['order']) ? $params['order'] : 'tc.id';
		$direction	= isset($params['direction']) ? $params['direction'] : 'ASC';
		$limit		= isset($params['limit']) ? $params['limit'] : null;
		$offset		= isset($params['offset']) ? $params['offset'] : null;
		$visible	= isset($params['visible']) ? $params['visible'] : null;
		
		$db =& $this->getDBO();
		$query = '
			SELECT
				tc.id,
				tc.tournament_id,
				tc.user_id,
				tc.comment,
				tc.created_date,
				tc.updated_date,
				u.username,
				t.name AS tournament_name,
				t.private_flag,
				t.end_date AS tournament_end_date,
				tp.display_identifier
			FROM
				' . $db->nameQuote( '#__tournament_comment' ) . ' AS tc
			INNER JOIN
				' . $db->nameQuote( '#__users' ) . ' AS u
			ON
				u.id = tc.user_id
			INNER JOIN
				' . $db->nameQuote( '#__tournament' ) . ' AS t
			ON
				t.id = tc.tournament_id
			LEFT JOIN
				' . $db->nameQuote( '#__tournament_private' ) . ' AS tp
			ON
				tp.tournament_id = tc.tournament_id
			';
		
		$where = array();
		if (!empty($tournament_id)) {
			$where[] = 't.id = ' . $db->quote($tournament_id);
		}
		if (!empty($username)) {
			$where[] = 'u.username = ' . $db->quote($username);
		}
		if ($visible) {
			$where[] = '(UNIX_TIMESTAMP(t.end_date) + 48 * 60 * 60) >= UNIX_TIMESTAMP()';
		}
		
		if (!empty($where)) {
			$query .= '
				WHERE
			';
			$query .= implode(' AND ', $where);
		}
		
		if (!is_null($order)) {
			$query .= ' ORDER BY ' . $db->nameQuote($order);
		}

		if (!is_null($direction)) {
			$query .= ' ' . $direction;
		}

		$db->setQuery($query, $offset, $limit);
		return $db->loadObjectList();
	}
	
	/**
	 * Get tournament count by tournament id and username.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTotalTournamentCommentCountByTournamentIDAndUsername($tournament_id = null, $username = null, $params = array())
	{
		$visible = isset($params['visible']) ? $params['visible'] : null;
		
		$db =& $this->getDBO();
		$query = '
			SELECT
				count(tc.id)
			FROM
				' . $db->nameQuote( '#__tournament_comment' ) . ' AS tc
			INNER JOIN
				' . $db->nameQuote( '#__users' ) . ' AS u
			ON
				u.id = tc.user_id
			INNER JOIN
				' . $db->nameQuote( '#__tournament' ) . ' AS t
			ON
				t.id = tc.tournament_id
			';
		
		$where = array();
		if (!empty($tournament_id)) {
			$where[] = 't.id = ' . $db->quote($tournament_id);
		}
		if (!empty($username)) {
			$where[] = 'u.username = ' . $db->quote($username);
		}
		if ($visible) {
			$where[] = '(UNIX_TIMESTAMP(t.end_date) + 48 * 60 * 60) >= UNIX_TIMESTAMP()';
		}
		
		if (!empty($where)) {
			$query .= '
				WHERE
			';
			$query .= implode(' AND ', $where);
		}
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Store the comment
	 * @param Array $params
	 */
	public function store( $params )
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
	 * Insert a new tournament comment.
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _insert( $params, $db = null )
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'INSERT INTO ' . $db->nameQuote('#__tournament_comment') . ' (
				tournament_id,
				user_id,
				comment,
				created_date
			) VALUES (
				' . $db->quote($params['tournament_id']) . ',
				' . $db->quote($params['user_id']) . ',
				' . $db->quote($params['comment']) . ',
				NOW()
			)';

		$db->setQuery($query);
		$db->query();

		return $db->insertId();
	}
	/**
	 * Update an existing tournament comment.
	 *  @param array $params
	 *  @param JDatabase $db
	 *  @return bool
	 */
	private function _update( $params, $db = null )
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}
		$query =
			'UPDATE
			' . $db->nameQuote('#__tournament_comment') . '
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
	 * Delete a record from the comment
	 *
	 * @param integer $id
	 * @return bool
	 */
	public function delete($id)
	{
		$db =& $this->getDBO();
		$query =
			'DELETE FROM
				' . $db->nameQuote('#__tournament_comment') . '
			WHERE
				id =  ' . $db->quote($id);

		$db->setQuery($query);
		return $db->query();
	}
}