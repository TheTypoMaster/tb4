<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');

class TournamentModelSportMap extends SuperModel
{
	protected $_table_name = '#__sport_map';

	protected $_member_list =  array(
		'tournament_sport_id' => array(
			'name' 		=> 'Tournament Sport ID',
			'type' 		=> self::TYPE_INTEGER,
			'primary' 	=> true,
			'validate'	=> '_validateTournamentSportID',
			'required'	=> true
		),
		'external_sport_id' => array(
			'name' 		=> 'External Sport ID',
			'type' 		=> self::TYPE_INTEGER,
			'primary' 	=> true,
			'validate'	=> '_validateExternalSportID',
			'required'	=> true
		)
	);

	/**
     * Load a sport by External Sport ID
     *
     * @param integer $tournament_id
     * @return object
     */
    public function getTournamentSportByExternalID($id)
    {
    	return $this->find(array(
    		SuperModel::getFinderCriteria('external_sport_id', $id)
    	),	SuperModel::FINDER_SINGLE);
    }
	/**
     * Load a sport by Tournament Sport ID
     *
     * @param integer $tournament_id
     * @return object
     */
    public function getTournamentSportByTournamentSportID($id)
    {
    	return $this->find(array(
    		SuperModel::getFinderCriteria('tournament_sport_id', $id)
    	),	SuperModel::FINDER_SINGLE);
    }

    public function getExternalIDByTournamentSportID($sport_id)
    {
    	$table = new DatabaseQueryTable($this->_table_name);
    	$table	->addColumn('external_sport_id')
    			->addWhere('tournament_sport_id', $sport_id);

    	$db 	=& $this->getDBO();
    	$query 	= new DatabaseQuery($table);

    	$db->setQuery($query->getSelect());
    	return $db->loadResult();
    }

    protected function _validateTournamentSportID()
    {
    	if(empty($this->tournament_sport_id) || $this->tournament_sport_id <= 0) {
			$this->_addError('Invalid sport ID selected', 'tournament_sport_id');
			return;
    	}

    	$sport = JModel::getInstance('TournamentSport', 'TournamentModel')
    				->getTournamentSport($this->tournament_sport_id);

    	if(is_null($sport)) {
    		$this->_addError('Selected sport ID was not found', 'tournament_sport_id');
    		return;
    	}
    }

    protected function _validateExternalSportID()
    {
    	if(empty($this->external_sport_id) || $this->external_sport_id <= 0) {
    		$this->_addError('Invalid external ID selected', 'external_sport_id');
    		return;
    	}

    	$sport_list = JModel::getInstance('ImportSport', 'TournamentModel')
    						->getImportSportList(true);

    	if(!array_key_exists($this->external_sport_id, $sport_list)) {
    		$this->_addError('Selected external ID was not found');
    		return;
    	}
    }
    
	/**
	 * Insert a new Sport map data.
	 * @param array $params
	 * @param JDatabase $db
	 * @return integer [insert ID]
	 */
    public function insertSportMap($params)
    {
		$db =& $this->getDBO();
		
    	$query =
      	'INSERT INTO ' . $db->nameQuote('#__sport_map') . ' (
        		tournament_sport_id,
        		external_sport_id
      		) VALUES (
        		' . $db->quote($params['tournament_sport_id']) . ',
        		' . $db->quote($params['external_sport_id']) . '
      		)';

    	$db->setQuery($query);
    	$db->query();
		
    	return $db->insertId();	
    }
}