<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.query');

class TournamentModelMarketType extends SuperModel
{
	protected $_table_name = '#__market_type';

	protected $_member_list = array(
		'id' => array(
			'name' 		=> 'ID',
			'type' 		=> self::TYPE_INTEGER,
			'primary'	=> true
		),
		'name' => array(
			'name' => 'Name',
			'type' => self::TYPE_STRING
		),
		'description' => array(
			'name' => 'Description',
			'type' => self::TYPE_STRING
		),
		'status_flag' => array(
			'name' => 'Status',
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

	public $racing_type_list = array(
		'win',
		'place',
		'each way'
	);

	public function getMarketType($id, $active_only = false)
	{
		if(!$active_only) {
			return $this->load($id);
		}

		return $this->find(array(
			SuperModel::getFinderCriteria('id', $id),
			SuperModel::getFinderCriteria('status_flag', (int)$active_only)
		), 	SuperModel::FINDER_SINGLE);
	}

	public function getMarketTypeByName($name)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('name', $name)
		), SuperModel::FINDER_SINGLE);
	}

	public function addMarketTypeIfNotExist($name, $description = '')
	{
		$type = $this->getMarketTypeByName($name);
		if(!$type) {
			$param_list = array(
				'name' 			=> $name,
				'description' 	=> $description,
				'status_flag' 	=> 1
			);

			return $this->store($param_list);
		}

		return $type->id;
	}

	public function getMarketTypeListByStatus($status)
	{
		return $this->find(array(SuperModel::getFinderCriteria('status_flag', $status)), SuperModel::FINDER_LIST);
	}
	
}