<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.factory');

class TournamentModelEventStatus extends SuperModel
{
	protected $_table_name = '#__event_status';

	protected $_member_list = array(
		'id' => array(
			'name' 			=> 'ID',
			'type' 			=> self::TYPE_INTEGER,
			'primary' 		=> true
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
			'type' => self::TYPE_STRING
		)
	);

	public function getEventStatus($id)
	{
		return $this->load($id);
	}

	public function getEventStatusApi($id)
	{
		$db =& $this->getDBO();
		$query = "SELECT id, keyword, name, description FROM `tbdb_event_status` WHERE id = '".$id."'";
		$db->setQuery($query);

		return $db->loadObject();
	}

	public function getEventStatusByKeyword($keyword)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('keyword', $keyword)
		), 	SuperModel::FINDER_SINGLE);
	}

	public function getEventStatusByKeywordList($keyword)
	{
		$db =& $this->getDBO();
		$query = "SELECT id, keyword FROM `tbdb_event_status`";
		$db->setQuery($query);
		return $db->loadAssocList();
	}
	
	
	public function getEventStatusByKeywordApi($keyword)
	{
		$db =& $this->getDBO();
		$query= "SELECT * FROM `tbdb_event_status` WHERE `keyword` ='".$keyword."'";
		$db->setQuery($query);
        return $db->loadObject();
	}
	
}
