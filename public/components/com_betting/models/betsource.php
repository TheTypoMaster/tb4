<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class BettingModelBetSource extends SuperModel
{
	protected $_table_name = 'tb_bet_source';
	
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
		'description' => array(
			'name' => 'Description',
			'type' => self::TYPE_STRING
		),
		'api_endpoint' => array(
			'name' => 'Api Endpoint',
			'type' => self::TYPE_STRING
		),
		'shared_secret' => array(
			'name' => 'Shared Secret',
			'type' => self::TYPE_STRING
		),
		'created_at' => array(
			'name' => 'Created At',
			'type' => self::TYPE_DATETIME_CREATED
		),
		'updated_at' => array(
			'name' => 'Updated At',
			'type' => self::TYPE_DATETIME_UPDATED
		)
	);


	/**
	 * Load a single record from the tb_bet_source table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getBetSource($id) {
		return $this->find(array(SuperModel::getFinderCriteria('id', $id)), SuperModel::FINDER_SINGLE);
	}
	
	/**
	 * Load a single record from the tb_bet_source table by keyword.
	 *
	 * @param integer $keyword
	 * @return object
	 */
	public function getBetSourceByKeyword($keyword) {
		return $this->find(array(SuperModel::getFinderCriteria('keyword', $keyword)), SuperModel::FINDER_SINGLE);
	}
      
	/**
	 * Load a single record from the tb_bet_source table by keyword. (API)
	 *
	 * @param integer $keyword
	 * @return object
	 */
	public function getBetSourceByKeywordApi($keyword) {
		$db =& $this->getDBO();
		$query = "SELECT * FROM `tb_bet_source` WHERE keyword ='".$keyword."'";
        $db->setQuery($query);
	    return $db->loadObject();
	}

}
