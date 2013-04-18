<?php
/**
 * @version		$Id: userpreregistration.php 10704 2008-08-21 09:38:40Z eddieajau $
 * @package		Joomla
 * @subpackage	User
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * User Component User Model
 *
 * @package		Joomla
 * @subpackage	User
 * @since 1.5
 */
class TopbettaUserModelUserPreRegistration extends JModel
{
	/**
	 * Load a single record from the tbdb_user_pre_registration table by ID.
	 *
	 * @param integer $id
	 * @return object
	 */

	public function getUserPreRegistration($id) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				email,
				msisdn,
				username,
				registered_flag,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__user_pre_registration') . '
			WHERE
				id = ' . $db->quote($id);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Load a single record from the tbdb_user_pre_registration table by email.
	 *
	 * @param string $email
	 * @return object
	 */
	public function getUserPreRegistrationByEmail($email) {
		$db =& $this->getDBO();
		$query =
			'SELECT
				id,
				email,
				msisdn,
				username,
				registered_flag,
				created_date,
				updated_date
			FROM
				' . $db->nameQuote('#__user_pre_registration') . '
			WHERE
				lower(email) = ' . $db->quote(strtolower($email));

		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 * Update records by email
	 *
	 * @param string $email
	 * @param array $params
	 * @return bool
	 */
	public function updateByEmail($email, $params) {
		$db =& $this->getDBO();
		$query =
			'UPDATE
				' . $db->nameQuote('#__user_pre_registration') . '
			SET 
				username = ' . $db->quote($params['username']) . ',
				email = ' . $db->quote($params['email']) . ',
				msisdn = ' . $db->quote($params['msisdn']) . ',
				registered_flag = ' . $db->quote($params['registered_flag']) . ',
				updated_date = NOW()
			WHERE
				lower(email) = ' . $db->quote(strtolower($email));

		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Store a user pre registration record. Will determine whether to insert or update based
	 * on the presence of an ID.
	 *
	 * @param array $params
	 * @return bool
	 */
	public function store($params) {
		$db =& $this->getDBO();
		if(empty($params['id'])) {
			$result = $this->_insert($params, $db);
		} else {
			$result = $this->_update($params, $db);
		}

		return $result;
	}

	/**
	 * Insert a new user pre registration record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _insert($params, $db = null) {
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'INSERT INTO ' . $db->nameQuote('#__user_pre_registration') . ' (
				username,
				email,
				msisdn,
				registered_flag,
				created_date,
				updated_date
			) VALUES (
				' . $db->quote($params['username']) . ',
				' . $db->quote($params['email']) . ',
				' . $db->quote($params['msisdn']) . ',
				' . $db->quote($params['registered_flag']) . ',
				NOW(),
				NOW()
			)';

		$db->setQuery($query);
		$db->query();

		return $db->insertId();
	}

	/**
	 * Update an existing user pre registration record.
	 *
	 * @param array $params
	 * @param JDatabase $db
	 * @return bool
	 */
	private function _update($params, $db = null) {
		if(is_null($db)) {
			$db =& $this->getDBO();
		}

		$query =
			'UPDATE
				' . $db->nameQuote('#__user_pre_registration') . '
			SET
				id = ' . $db->quote($params['id']) . ',
				username = ' . $db->quote($params['username']) . ',
				email = ' . $db->quote($params['email']) . ',
				msisdn = ' . $db->quote($params['msisdn']) . ',
				registered_flag = ' . $db->quote($params['registered_flag']) . ',
				updated_date = NOW()
			WHERE
				id = ' . $db->quote($params['id']);

		$db->setQuery($query);
		return $db->query();
	}
}
?>
