<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

/**
 * Private Tournament Model
 */
class TournamentModelTournamentPrivate extends JModel
{
	/**
   	* Get a single Private tournament record by ID.
   	*
   	* @param integer $id
   	* @return object
   	*/
	public function getTournamentPrivate($id)
	{
		$db =& $this->getDBO();
		$query =
	      'SELECT
	        id,
	        tournament_id,
	        user_id,
	        tournament_prize_format_id,
	        display_identifier,
	        password
	      FROM
	        ' . $db->nameQuote('#__tournament_private') . '
	      WHERE
	        id = ' . $db->quote( $id );

	    $db->setQuery($query);
	    return $db->loadObject();
  	}
	/**
   	* Get a single Private tournament record by tournament ID.
   	*
   	* @param integer $id
   	* @return object
   	*/
	public function getTournamentPrivateByTournamentID($id)
	{
		$db =& $this->getDBO();
		$query =
	      'SELECT
	        id,
	        tournament_id,
	        user_id,
	        tournament_prize_format_id,
	        display_identifier,
	        password
	      FROM
	        ' . $db->nameQuote('#__tournament_private') . '
	      WHERE
	        tournament_id = ' . $db->quote($id);

	    $db->setQuery($query);
	    return $db->loadObject();
  	}
	/**
   	* Get a Private tournament records by Indentifier.
   	*
   	* @param string $identifier
   	* @return object
   	*/
	public function getTournamentPrivateByIdentifier($identifier)
	{
		$db =& $this->getDBO();
		$query =
	      'SELECT
	        id,
	        tournament_id,
	        user_id,
	        tournament_prize_format_id,
	        display_identifier,
	        password
	      FROM
	        ' . $db->nameQuote('#__tournament_private') . '
	      WHERE
	        display_identifier = ' . $db->quote($identifier);
	    $db->setQuery($query);
	    return $db->loadObject();
  	}
	/**
   	* Get a Private tournament records by User ID.
   	*
   	* @param integer $id
   	* @return Array
   	*/
	public function getTournamentPrivateByUserID($user_id, $exclude_tournament = null)
	{
		$db =& $this->getDBO();
		$query =
		    'SELECT
		    	pt.id,
		    	pt.tournament_id,
		    	t.name,
		    	t.start_date,
		    	pt.user_id,
		    	pt.tournament_prize_format_id,
		    	pt.display_identifier,
		    	pt.password
			FROM
		      	' . $db->nameQuote( '#__tournament_private' ) . ' AS pt
		    INNER JOIN
		      	' . $db->nameQuote( '#__tournament' ) . ' AS t
		    ON
		    	pt.tournament_id = t.id
		    WHERE
		    	t.cancelled_flag = 0
		    	AND pt.user_id = ' . $db->quote($user_id);

			if(!empty($exclude_tournament)){
				$query .= '
					AND pt.tournament_id != ' . $db->quote($exclude_tournament);
			}
			$query .= '
		    ORDER BY
		    	t.created_date DESC
		    ';

	    $db->setQuery($query);
	    return $db->loadObjectList();
  	}

  	/**
  	 * Get tournamnet Creator Info
  	 */
  	public function getPrivateTournamentCreatorInfoByTournamentID($tournament_id){
  		$db =& $this->getDBO();
  		$query =
			'SELECT
				tp.user_id,
				tp.display_identifier,
				tp.password,
				tp.tournament_prize_format_id as prize_format_id,
				tu.first_name,
				tu.last_name,
				u.name,
				u.username,
				u.email
			FROM
				' . $db->nameQuote( '#__tournament_private' ) . ' AS tp
			INNER JOIN
				' . $db->nameQuote( '#__users' ) . ' AS u
			ON
				tp.user_id = u.id
			LEFT JOIN
				' . $db->nameQuote( '#__topbetta_user' ) . ' AS tu
			ON
				tu.user_id = u.id
			WHERE
				tp.tournament_id = ' . $db->quote($tournament_id);
  		$db->setQuery($query);
	    return $db->loadObject();
  	}
  	/**
  	 * Get previous registered users emails from other private tournament
  	 * by tournament id and
  	 * by User id
  	 * And excludes current Tournament entrants
  	 */
	public function getFriendsEmailForPrivateTournamentByTournamentID($tournament_id, $exclude_list = 0){
		$db =& $this->getDBO();
		$query =
		    'SELECT
		    	DISTINCT(u.email) AS emails
			FROM
		      	' . $db->nameQuote( '#__users' ) . ' AS u
		    INNER JOIN
		      	' . $db->nameQuote( '#__tournament_ticket' ) . ' AS tt
		    ON
		    	u.id = tt.user_id
		    WHERE
		    	tt.tournament_id = ' . $db->quote($tournament_id) ;

	    $db->setQuery($query);
	    return $db->loadResultArray();
	}

	/**
	 * Store a Private tournament record.
	 * Get a user's private tournament list
	 *
	 * @param integer $user_id
	 * @param integer $order
	 * @param integer $direction
	 * @param integer $limit
	 * @param integer $offset
	 * @return array
	 */
	public function getUserTournamentPrivateList($user_id, $order = null, $direction = null, $limit = null, $offset = null) {
		if(is_null($order)) {
			$order = (empty($this->order)) ? 't.id' : $this->order;
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
				t.id,
				t.buy_in,
				t.entry_fee,
				t.start_date,
				t.end_date,
				t.cancelled_flag,
				t.paid_flag,
				t.start_currency,
				t.name AS tournament_name,
				t.jackpot_flag,
				t.parent_tournament_id,
				t.created_date,
				s.name AS sport_name,
				c.name AS competition_name,
				p.tournament_prize_format_id,
				p.user_id,
				p.display_identifier,
				p.password
			FROM
				' . $db->nameQuote('#__tournament_private') . ' AS p
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.id = p.tournament_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			ON
				t.tournament_sport_id = s.id
			INNER JOIN
				' . $db->nameQuote('#__event_group') . ' AS eg
			ON
				eg.id = t.event_group_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_competition') . ' AS c
			ON
				c.id = eg.tournament_competition_id
			WHERE
				p.user_id = ' . $db->quote($user_id) . '
			AND
				t.cancelled_flag != 1';

		if(!is_null($order)) {
			$query .= ' ORDER BY ' . $db->nameQuote($order);
		}

		if(!is_null($direction)) {
			$query .= ' ' . $direction;
		}

		$db->setQuery($query, $offset, $limit);
		return $db->loadObjectList();
	}
	/**
	 * Count the total number of user's private tournaments.
	 *
	 * @return integer
	 */
	public function getUserTournamentPrivateCount($user_id) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				COUNT(p.id)
			FROM
				' . $db->nameQuote('#__tournament_private') . ' AS p
			INNER JOIN
				' . $db->nameQuote('#__tournament') . ' AS t
			ON
				t.id = p.tournament_id
			INNER JOIN
				' . $db->nameQuote('#__tournament_sport') . ' AS s
			ON
				t.tournament_sport_id = s.id
			WHERE
				p.user_id = ' . $db->quote($user_id) . '
			AND
				t.cancelled_flag != 1';

		$db->setQuery($query);
		return $db->loadResult();
	}
	/**
	   * Store a Private tournament record.
	   * Will determine whether to insert or update based on the
	   * presence of an ID.
	   *
	   * @param array $params
	   * @return bool
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
	 * Insert a new private tournament record.
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
		 'INSERT INTO ' . $db->nameQuote('#__tournament_private') . ' (
	        tournament_id,
	        user_id,
	        tournament_prize_format_id,
	        display_identifier,
	        password
	      	) VALUES (
	        ' . $db->quote($params['tournament_id']) . ',
	        ' . $db->quote($params['user_id']) . ',
	         ' . $db->quote($params['tournament_prize_format_id']) . ',
	        ' . $db->quote($params['display_identifier']) . ',
	        ' . $db->quote($params['password']) . '
	      	)';

	    $db->setQuery($query);
	    $db->query();

	    return $db->insertId();
	  }
	/**
	 * Update an existing private tournament record.
	 *  @param array $params
	 *  @param JDatabase $db
	 *  @return bool
	 */
	private function _update($params, $db = null)
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}
		$query =
			'UPDATE
			' . $db->nameQuote('#__tournament_private') . '
			SET ';
			foreach($params as $field => $data){
				$query .= $field.' = '.$db->quote($data).', ';
			}
			$query .=
			'updated_date = NOW()
			 WHERE
			 id = ' . $db->quote($params['id']);
		$db->setQuery($query);
		return $db->query();
	}
}