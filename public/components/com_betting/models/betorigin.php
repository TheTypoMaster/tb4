<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class BettingModelBetOrigin extends SuperModel
{
	protected $_table_name = '#__bet_origin';
	
	protected $_member_list = array(
		'id' => array(
			'name' => 'ID',
			'type' => self::TYPE_INTEGER,
			'primary' => true
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
	 * Load a single record from the tbdb_bet_origin table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getBetOrigin($id) {
		return $this->find(array(SuperModel::getFinderCriteria('id', $id)), SuperModel::FINDER_SINGLE);
	}
	
	/**
	 * Load a single record from the tbdb_bet_origin table by keyword.
	 *
	 * @param integer $keyword
	 * @return object
	 */
	public function getBetOriginByKeyword($keyword) {
		return $this->find(array(SuperModel::getFinderCriteria('keyword', $keyword)), SuperModel::FINDER_SINGLE);
	}
      
	/**
	 * Load a single record from the tbdb_bet_origin table by keyword. (API)
	 *
	 * @param integer $keyword
	 * @return object
	 */
	public function getBetOriginByKeywordApi($keyword) {
		$db =& $this->getDBO();
		$query = "SELECT * FROM `tbdb_bet_origin` WHERE keyword ='".$keyword."'";
        $db->setQuery($query);
	    return $db->loadObject();
	}

}
