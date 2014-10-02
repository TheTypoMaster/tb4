<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');

/**
 * Private Tournament Model
 */
class TournamentModelTournamentPrizeFormat extends SuperModel
{
	protected $_table_name = '#__tournament_prize_format';

	protected $_member_list = array(
		'id' => array(
			'name'		=> 'ID',
			'type'		=> self::TYPE_INTEGER,
			'primary' 	=> true
		),
		'keyword' => array(
			'name' => 'Keyword',
			'type' => self::TYPE_STRING
		),
		'name' => array(
			'name' => 'Name',
			'type' => self::TYPE_STRING
		),
		'description' => array(
			'name' => 'Description',
			'type' => self::TYPE_STRING
		)
	);

	/**
   	* Get a single tournament prize format record by ID.
   	*
   	* @param integer $id
   	* @return object
   	*/
	public function getTournamentPrizeFormat($id)
	{
		return $this->load($id);
  	}
	/**
   	* Get a single tournament prize format record by keyword.
   	*
   	* @param string keyword
   	* @return object
   	*/
	public function getTournamentPrizeFormatByKeyword($keyword)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('keyword', $keyword)
		), 	SuperModel::FINDER_SINGLE);
  	}
	/**
   	* Get all tournament prize formats
   	*
   	* @param string keyword
   	* @return object
   	*/
	public function getTournamentPrizeFormats()
	{
		return $this->find();
  	}

	/**
   	* Get all tournament prize formats for Api
   	*
   	* @param string keyword
   	* @return object
   	*/
	public function getTournamentPrizeFormatsApi()
	{
		$db =& $this->getDBO();
        $query = 'SELECT * FROM '.$db->nameQuote('#__tournament_prize_format');
		$db->setQuery($query);
		return $db->loadObjectList();
		
  	}

    /**
     * Load all possible buy-in values. Defaults to only active records.
     *
     * @return array
     */
    public function getTournamentPrizeFormatList()
    {
        $table = $this->_getTable();

        $query = new DatabaseQuery($table);
        $db =& $this->getDBO();

        $db->setQuery($query->getSelect());
        return $this->_loadModelList($db->loadObjectList());
    }
}
