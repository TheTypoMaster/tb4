<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.factory');

class BettingModelWageringApi extends SuperModel
{
	protected $_table_name = '#__wagering_api';

	protected $_member_list = array(
		'id' => array(
			'name'		=> 'ID',
			'type' 		=> self::TYPE_INTEGER,
			'primary'	=> true
		),
		'keyword' => array(
			'name' => 'Keyword',
			'type' => self::TYPE_STRING
		),
		'name' => array(
			'name' => 'Name',
			'type' => self::TYPE_STRING
		),
		'description'=> array(
			'name' => 'Description',
			'type' => self::TYPE_INTEGER
		),
		'created_date' => array(
			'name' 	=> 'Created Date',
			'type'	=> self::TYPE_DATETIME_CREATED
		),
		'updated_date' => array(
			'name' 	=> 'Updated Date',
			'type'	=> self::TYPE_DATETIME_UPDATED
		)
	);

	public function getWageringApi($id)
	{
		return $this->load($id);
	}

	public function getWageringApiByKeyword($keyword)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('keyword', $keyword)
		), 	SuperModel::FINDER_SINGLE);
	}
}
