<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class TopbettaUserModelUserAudit extends JModel
{
	/**
	 * Get list of user audit records by user id and field name
	 *
	 * @param int $user_id
	 * @param mix $field_name
	 * @return array
	 */
	public function getUserAuditListByUserIDAndFieldName($user_id, $field_name)
	{
		$db =& $this->getDBO();
		
		if(!is_array($field_name)) {
			$fielname = array($field);
		}

		$clean = array();
		foreach($field_name as $f) {
			$clean[] = $db->quote($f);
		}
		$query =
			'SELECT
				a.id,
				a.user_id,
				a.admin_id,
				a.field_name,
				a.old_value,
				a.new_value,
				a.update_date,
				admin.username AS admin,
				u.username AS username
			FROM
				' . $db->nameQuote('#__user_audit') . ' AS a
			LEFT JOIN
				' . $db->nameQuote('#__users') . ' AS admin
				ON admin.id = a.admin_id
			LEFT JOIN
				' . $db->nameQuote('#__users') . ' AS u
				ON u.id = a.user_id
			WHERE
				user_id = ' . $db->quote($user_id) . '
			AND
				field_name IN(' . implode(', ', $clean) . ')
			ORDER BY
				a.update_date DESC
		';
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * Get most recent audit record by user id and field name
	 *
	 * @param int $user_id
	 * @param string $field_name
	 * @return object
	 */
	public function getRecentUserAuditByUserIDAndFieldName($user_id, $field_name)
	{
		$db =& $this->getDBO();
		
		if(!is_array($field_name)) {
			$fielname = array($field);
		}

		$clean = array();
		foreach($field_name as $f) {
			$clean[] = $db->quote($f);
		}
		
		$query =
			'SELECT
				id,
				user_id,
				admin_id,
				field_name,
				old_value,
				new_value,
				update_date
			FROM ' . $db->nameQuote( '#__user_audit' ) . '
			WHERE
				user_id = ' . $db->quote($user_id) . '
			AND
				field_name IN(' . implode(', ', $clean) . ')
			ORDER BY
				update_date DESC
			LIMIT 1';
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 * Store an audit record of a change made to one of the user fields
	 *
	 * @param array $params
	 * @return bool
	 */
	public function store($params)
	{
		$db =& $this->getDBO();
		$result = $this->_insert($params, $db);

		return $result;
	}

	/**
	 * Insert a new audit record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _insert($params, $db = null)
	{
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
      'INSERT INTO ' . $db->nameQuote('#__user_audit') . ' (
        user_id,
        admin_id,
        field_name,
        old_value,
        new_value,
        update_date
      ) VALUES (
        ' . $db->quote($params['user_id']) . ',
        ' . $db->quote($params['admin_id']) . ',
        ' . $db->quote($params['field_name']) . ',
        ' . ($params['old_value'] === null ? 'null' : $db->quote($params['old_value'])) . ',
        ' . ($params['new_value'] === null ? 'null' : $db->quote($params['new_value'])) . ',
        NOW()
      )';

		$db->setQuery($query);
		return $db->query();
	}

}