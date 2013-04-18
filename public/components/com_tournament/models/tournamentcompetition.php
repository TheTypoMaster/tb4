<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');

class TournamentModelTournamentCompetition extends SuperModel
{
	protected $_table_name = '#__tournament_competition';

	protected $_member_list = array(
		'id' => array(
			'name' 		=> 'ID',
			'type' 		=> self::TYPE_INTEGER,
			'primary' 	=> true
		),
		'tournament_sport_id' => array(
			'name' => 'Sport ID',
			'type' => self::TYPE_INTEGER
		),
		'external_competition_id' => array(
			'name' => 'External Competition ID',
			'type' => self::TYPE_INTEGER
		),
		'name' => array(
			'name' => 'Name',
			'type' => self::TYPE_STRING
		),
		'status_flag' => array(
			'name' => 'Status-flag',
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

	/**
	 * Load a single competition record by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentCompetition($id)
	{
		return $this->load($id);
	}

    /**
	 * Load a single competition record by ID. (API)
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getTournamentCompetitionApi($id)
	{
		$db =& $this->getDBO();
		$query = "SELECT id, tournament_sport_id, external_competition_id, name, status_flag, created_date, updated_date FROM `tbdb_tournament_competition` WHERE id = '".$id."'";
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Load a list of competition by status. Defaults to active ones only.
	 *
	 * @param integer $status
	 * @return object
	 */
	public function getTournamentCompetitionByExternalID($id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('external_competition_id', $id)
		), 	SuperModel::FINDER_SINGLE);
	}

	/**
	 * Load a competition by name
	 *
	 * @param integer $tournament_id
	 * @return object
	 */
	public function getTournamentCompetitionByNameAndSportID($name, $sport_id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('name', $name),
			SuperModel::getFinderCriteria('tournament_sport_id', $sport_id)
		), 	SuperModel::FINDER_SINGLE);
	}

	/**
	 * Load all the competition from the Table
	 *
	 * @return array
	 */
	public function getTournamentCompetitionList()
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('status_flag', 1)
		), 	SuperModel::FINDER_LIST);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param string $name
	 * @return int
	 */
	public function getTournamentCompetitionIdByName($name){
		static $competition_list = array();
		
		$competition = $this->getCompetitionByName($name);
		
		if(!array_key_exists($name, $competition_list)){
			$competition_list[$name] = $competition->id;
		}
		
		return (int) $competition_list[$name];
	}

	/**
	 * Get a complete list of competitions with arbitrary ordering etc
	 *
	 * @return array
	 */
	public function getTournamentCompetitionImportedList()
	{
		return $this->find();
	}

	/**
	 * Get the full list of competitions for the admin system
	 *
	 * @return array
	 */
	public function getTournamentCompetitionAdminList($order, $direction, $limit, $offset)
	{
		$sport = new DatabaseQueryTable('#__tournament_sport');
		$sport->addColumn(new DatabaseQueryTableColumn('name', 'sport_name'));

		$dir = ($direction == 'asc') ? DatabaseQueryTableOrder::ASCENDING : DatabaseQueryTableOrder::DESCENDING;
		$table = $this->_getTable()
					->addJoin($sport, 'tournament_sport_id', 'id')
					->addOrder($order, $dir);

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table, $limit, $offset);

		$db->setQuery($query->getSelect());
		return $db->loadObjectList();
	}

	/**
	 * Load all the competition from the Table based on sports ID.
	 * This method will be used to generate the Dynamic competition droup down
	 * @return array
	 */
	public function getTournamentCompetitionListBySportID($sport_id)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('tournament_sport_id', $sport_id)
		), 	SuperModel::FINDER_LIST);
	}
	/**
	 * Load the competitions which have tournament associated with based on sports ID.
	 * @return array
	 */
	public function getActiveTournamentCompetitionListBySportID($sport_id, $private = false, $racing_flag = null)
	{
//		$table = $this->_getTable()
//					->addWhere('status_flag', 1)
//					->addJoin('#__event_group', 'id', 'tournament_competition_id');
//
//		$db =& $this->getDBO();
//		$query = new DatabaseQuery($table);
//
//		$db->setQuery($query->getSelect());
//		return $this->_loadModelList($db->loadObjectList());

        $db =& $this->getDBO();
        $query =
          'SELECT
            c.id,
            c.name
          FROM
            ' . $db->nameQuote('#__tournament_competition') . ' AS c
          INNER JOIN
            ' . $db->nameQuote('#__tournament_sport') . ' AS s
            ON
            	s.id = c.tournament_sport_id
          INNER JOIN
          	' . $db->nameQuote('#__event_group') . ' AS eg
          	ON
          		eg.tournament_competition_id = c.id
          INNER JOIN
          	' . $db->nameQuote('#__tournament') . ' AS t
          	ON
          		t.event_group_id = eg.id
          WHERE
			t.end_date > NOW()';
        if (!empty($sport_id)) {
        	$query .= '
		    AND c.tournament_sport_id = ' . $db->quote($sport_id);
        }
        if ($private !== false) {
        	$query .= '
        	AND t.private_flag = ' . $db->quote($private);
        }
        if (!is_null($racing_flag)) {
        	$query .= '
        	AND s.racing_flag = ' . $db->quote($racing_flag);
        }
        $query .='
		  GROUP BY
			c.id
         	ORDER BY c.name ASC';
        $db->setQuery($query);
        return $db->loadObjectList();
	}

	/**
	 * Get the total number of competitions in the database
	 *
	 * @return integer
	 */
	public function getTournamentCompetitionCount()
	{
		$table = new DatabaseQueryTable($this->_table_name);
		$table->addColumn(new DatabaseQueryTableColumnFunction('*'));

		$db =& $this->getDBO();
		$query = new DatabaseQuery($table);

		$db->setQuery($query->getSelect());
		return $db->loadResult();
	}

	/**
	 * Get # of competitions having live tournaments
	 */
	public function checkEventExistsByCompetitionID($competition_id)
	{
		if($competition_id){
			$db =& $this->getDBO();
			$query =
	          'SELECT
	            count(id) AS events
	          FROM
	            ' . $db->nameQuote('#__tournament_event') . '
			  WHERE tournament_competition_id= ' . $db->quote($competition_id);

			$db->setQuery($query);
			return $db->loadResult();
		}
	}
    
    public function getCompetitionByName($name)
    {
        $db =& $this->getDBO();
        $query =
          'SELECT *
          FROM
          	' . $db->nameQuote('#__tournament_competition') . '
          WHERE lower(' . $db->nameQuote('name') . ') = ' . $db->quote(strtolower($name));
          
		$db->setQuery($query);
		return $db->loadObject();
    }
}
