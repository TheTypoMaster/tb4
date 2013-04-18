<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('mobileactive.model.super');
jimport('mobileactive.database.factory');

/**
 * tournament event group event Model
 */
class TournamentModelEventGroupEvent extends SuperModel
{
	protected $_table_name = '#__event_group_event';

	protected $_member_list = array(
		'event_group_id' => array(
			'name' 		=> 'Event Group ID',
			'type'		=> self::TYPE_INTEGER
		),
		'event_id' => array(
			'name' 		=> 'Exvent ID',
			'type' 		=> self::TYPE_INTEGER
		)
	);
	
	/**
	* Get record by event_group_id and event_id
	* @param string $name
	*/
	public function getEventGroupEventByEventGroupIDAndEventID($event_group_id, $event_id)
	{
		return $this->find(array(
			self::getFinderCriteria('event_group_id', $event_group_id),
			self::getFinderCriteria('event_id', $event_id)
		),
		self::FINDER_SINGLE);
	}
	

	public function isEventExistsByEventGroupIDAndEventId( $event_group_id, $event_id )
	{
		$db =& $this->getDBO();
		$query =
			'SELECT
				COUNT(event_id)
			FROM
				' . $db->nameQuote( '#__event_group_event' ) . '
			WHERE
				event_group_id = ' . $db->quote($event_id) . '
			AND
				event_id = ' . $db->quote($event_id);
		$db->setQuery($query);
		return (bool)$db->loadResult();
	}
	

	public function deleteByEventGroupIDAndEventID($event_group_id, $event_id )
	{
		$db =& $this->getDBO();
		$query = '
			DELETE FROM ' . $db->nameQuote('#__event_group_event') . '
			WHERE
				event_group_id = ' . $db->quote($event_group_id) . '
			AND
				event_id = ' . $db->quote($event_id);
		$db->setQuery($query);
		return $db->query();
	}
}