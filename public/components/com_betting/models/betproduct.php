<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class BettingModelBetProduct extends SuperModel
{
	protected $_table_name = '#__bet_product';
	
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
	 * Load a list of bet products.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getBetProductKeywordList()
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				keyword
			FROM
				' . $db->nameQuote('#__bet_product');

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Load a list of bet products with ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getBetProductKeywordListWithID()
	{
		$db =& $this->getDBO();
		$query =
		'SELECT
				id , keyword
			FROM
				' . $db->nameQuote('#__bet_product');
	
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
	
	
	/**
	 * Load a single record from the tbdb_bet_origin table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getBetProduct($id) {
		return $this->find(array(SuperModel::getFinderCriteria('id', $id)), SuperModel::FINDER_SINGLE);
	}
	
	/**
	 * Load a single record from the tbdb_bet_origin table by keyword.
	 *
	 * @param integer $keyword
	 * @return object
	 */
	public function getBetProductByKeyword($keyword) {
		return $this->find(array(SuperModel::getFinderCriteria('keyword', $keyword)), SuperModel::FINDER_SINGLE);
	}

	/**
	 * Load a single record from the tbdb_bet_origin table by keyword. (API)
	 *
	 * @param integer $keyword
	 * @return object
	 */
	public function getBetProductByKeywordApi($keyword) {
		$db =& $this->getDBO();
		$query = "SELECT * FROM `tbdb_bet_product` WHERE keyword = '".$keyword."'";
        $db->setQuery($query);
	    return $db->loadObject();
	}
		
	/**
	 * Load a single record from the tbdb_bet_origin table by Id. (API)
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getBetProductByIdApi($id) {
		$db =& $this->getDBO();
		$query = "SELECT * FROM `tbdb_bet_product` WHERE id = '".(int)$id."'";
        $db->setQuery($query);
	    return $db->loadObject();
	}
	
}
