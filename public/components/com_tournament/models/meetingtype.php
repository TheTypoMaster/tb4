<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');

class TournamentModelMeetingType extends SuperModel
{
	protected $_table_name = '#__meeting_type';

	protected $_member_list = array(
		'id' => array(
			'name' 		=> 'Name',
			'type' 		=> self::TYPE_INTEGER,
			'primary' 	=> true
		),
		'code' => array(
			'name' => 'Code',
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
	 * Load a single record from the #__meeting_type table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getMeetingType($id)
	{
		return $this->load($id);
	}

	/**
	 * Get meeting type by code
	 *
	 * @param string $code
	 * @return integer
	 */
	public function getMeetingTypeIDByCode($code)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('code', $code)
		),	SuperModel::FINDER_SINGLE);
	}

	/**
	 * Get meeting type by name
	 *
	 * @param string $code
	 * @return integer
	 */
	public function getMeetingTypeIDByName($name)
	{
		return $this->find(array(
			SuperModel::getFinderCriteria('name', $name)
		), 	SuperModel::FINDER_SINGLE);
	}
}
