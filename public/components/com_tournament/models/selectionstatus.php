<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.factory');

class TournamentModelSelectionStatus extends SuperModel
{
	protected $_table_name = '#__selection_status';

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
		)
	);

	public function getSelectionStatus($id)
	{
		return $this->load($id);
	}

	public function getSelectionStatusIdByKeyword($keyword)
	{
		return $this->find(array(
			self::getFinderCriteria('keyword', $keyword)
		),
		self::FINDER_SINGLE);
	}

	public function getSelectionStatusList()
	{
		$query = new DatabaseQuery($this->_getTable());

		$db =& $this->getDBO();
		$db->setQuery($query->getSelect());

		return $db->loadObjectList('keyword');
	}
}
?>