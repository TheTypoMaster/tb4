<?php
/**
 * @version		$Id: user.php 10704 2008-08-21 09:38:40Z eddieajau $
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
class TopbettaUserModelTopbettaUser extends JModel
{
	private $_id = null;

	public $options = array(
	    'title' => array(
	      'Mr' => 'Mr',
	      'Mrs' => 'Mrs',
	      'Ms' => 'Ms',
	      'Miss' => 'Miss',
	      'Dr' => 'Dr',
	      'Prof' => 'Prof',
		),
	    'day' => array(),
	    'month' => array(),
	    'year' => array(),
	    'state' => array(
	      'nsw' => 'New South Wales',
	      'vic' => 'Victoria',
	      'qld' => 'Queensland',
	      'sa' => 'South Australia',
	      'wa' => 'Western Australia',
	      'nt' => 'Northern Territory',
	      'act' => 'Australian Capital Territory',
	      'tas' => 'Tasmania',
	      'other' => 'Not in Australia'
		),
	    'country' => array(
	      'AUSTRALIA' => 'AUSTRALIA',
		),
	    'heard_about' => array(
	      'Friend' => 'Friend (Please give full name #)',
	      'TV Advertisement' => 'TV Advertisement',
	      'Radio Advertisement' => 'Radio Advertisement',
	      'Word of mouth' => 'Word of Mouth',
	      'Internet' => 'Internet (Please give name of website #)',
	      'Other' => 'Other (please give details #)',
		),
	);

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setId($id);
	}

	function loadDynamicOptions()
	{
		for($i=1; $i <= 31; $i++ )
		{
			$this->options['day'][$i] = sprintf('%02s', $i);
		}

		for( $i=1; $i <= 12; $i++ )
		{
			$this->options['month'][$i] = date('F', mktime(0,0,0,$i,1));
		}

		$currentYear = date('Y');
		$current18thYear = $currentYear - 18;
		for( $i= $current18thYear; $i >= 1900; $i-- )
		{
			$this->options['year'][$i] = $i;
		}
	}

	/**
	 * Method to set the weblink identifier
	 *
	 * @access	public
	 * @param	int Weblink identifier
	 */
	function setId($id)
	{
		// Set weblink id and wipe data
		$this->_id		= $id;
	}

	/**
	 * Method to get a user
	 *
	 * @since 1.5
	 */
	function &getData()
	{
		// Load the weblink data
		if ($this->_loadData()) {
			//do nothing
		}

		return $this->_data;
	}

	/**
	 * Method to update user data
	 *
	 * @param string field name
	 * @param string value
	 * @return	boolean	True on success
	 */
	function update($field, $value, $userId = null)
	{
		if( !$userId )
		{
			$userId = $this->_id;
		}

		if( !$userId )
		{
			$user =& JFactory::getUser();
			$userId = $user->get('id');
		}

		if( !$userId )
		{
			return false;
		}

		$db =& Jfactory::getDBO();
		$table = $db->nameQuote('#__topbetta_user');
		$field = $db->nameQuote($field);
		$value = $db->quote( $value );

		$updateQuery = "UPDATE $table SET $field = $value WHERE user_id = " . $db->quote($userId);
		$db->setQuery($updateQuery);
		return $db->query();
	}

    /**
	 * Method to upgrade user from basic to topbetta
	 *
	 * @param int userid
	 * @return	boolean	True on success
	 */
	function userUpgradeTopBetta($userId)
	{
		if( !$userId )
		{
			return false;
		}

		$db =& Jfactory::getDBO();
		$table = $db->nameQuote('#__users');
		$field = $db->nameQuote('isTopBetta');
		$value = true;

		$updateQuery = "UPDATE $table SET $field = {$value}	WHERE id = " . $db->quote($userId);
		$db->setQuery($updateQuery);
		return $db->query();

	}

	/**
	 * Method to store the user data
	 *
	 * @param array data array
	 * @return	boolean	True on success
	 */
	function store($params)
	{
		$db =& Jfactory::getDBO();

		$query =
			'INSERT INTO ' . $db->nameQuote('#__topbetta_user') . ' (
				user_id,
				title,
				first_name,
				last_name,
				street,
				city,
				state,
				country,
				postcode,
				dob_day,
				dob_month,
				dob_year,
				msisdn,
				phone_number,
				promo_code,
				heard_about,
				heard_about_info,
				marketing_opt_in_flag,
				source,
				self_exclusion_date,
				bet_limit,
				requested_bet_limit,
				btag
        	) VALUES (
        		' . $db->quote($params['user_id']) . ',
        		' . $db->quote($params['title']) . ',
        		' . $db->quote($params['first_name']) . ',
        		' . $db->quote($params['last_name']) . ',
        		' . $db->quote($params['street']) . ',
        		' . $db->quote($params['city']) . ',
        		' . $db->quote($params['state']) . ',
        		' . $db->quote($params['country']) . ',
        		' . $db->quote($params['postcode']) . ',
        		' . $db->quote($params['dob_day']) . ',
        		' . $db->quote($params['dob_month']) . ',
        		' . $db->quote($params['dob_year']) . ',
        		' . $db->quote($params['msisdn']) . ',
        		' . $db->quote($params['phone_number']) . ',
        		' . $db->quote($params['promo_code']) . ',
        		' . $db->quote($params['heard_about']) . ',
        		' . $db->quote($params['heard_about_info']) . ',
        		' . $db->quote($params['marketing_opt_in_flag']) . ',
        		' . $db->quote($params['source']) . ',
        		' . (isset($params['self_exclusion_date']) ? $db->quote($params['self_exclusion_date']) : 'null') . ',
        		' . (isset($params['bet_limit']) ? $db->quote($params['bet_limit']) : -1) . ',
        		' . (isset($params['requested_bet_limit']) ? $db->quote($params['requested_bet_limit']) : 0) . ',
        		' . (isset($params['btag']) ? $db->quote($params['btag']) : 'null') . '
        	)';
		
		$db->setQuery($query);
		return $db->query();
	}
	
	/**
	 * Method to update the user data
	 *
	 * @param array data array
	 * @return	boolean	True on success
	 */
	function updateUser($params)
	{
		$db =& Jfactory::getDBO();

		$query =
			'UPDATE ' . $db->nameQuote('#__topbetta_user') . ' SET 
				title		= ' . $db->quote($params['title']) . ',
				first_name	= ' . $db->quote($params['first_name']) . ',
				last_name	= ' . $db->quote($params['last_name']) . ',
				street		= ' . $db->quote($params['street']) . ',
				city		= ' . $db->quote($params['city']) . ',
				state		= ' . $db->quote($params['state']) . ',
				country		= ' . $db->quote($params['country']) . ',
				postcode	= ' . $db->quote($params['postcode']) . ',
				dob_day		= ' . $db->quote($params['dob_day']) . ',
				dob_month	= ' . $db->quote($params['dob_month']) . ',
				dob_year	= ' . $db->quote($params['dob_year']) . ',
				msisdn		= ' . $db->quote($params['msisdn']) . ',
				phone_number	= ' . $db->quote($params['phone_number']) . ',';
		
		if(isset($params['promo_code']) && !empty($params['promo_code'])) $query .= 'promo_code	= ' .  $db->quote($params['promo_code']) . ',';
				
		$query .='heard_about		= ' . $db->quote($params['heard_about']) . ',
				heard_about_info	= ' . $db->quote($params['heard_about_info']) . ',
				marketing_opt_in_flag = ' . $db->quote($params['marketing_opt_in_flag']) . ',
				self_exclusion_date	= ' . (isset($params['self_exclusion_date']) ? $db->quote($params['self_exclusion_date']) : 'null') . ',
				bet_limit	= ' . (isset($params['bet_limit']) ? $db->quote($params['bet_limit']) : -1) . ',
				requested_bet_limit	 = ' . (isset($params['requested_bet_limit']) ? $db->quote($params['requested_bet_limit']) : 0) . '
				WHERE user_id		= ' . $db->quote($params['user_id']) . ' LIMIT 1';
		
		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Method to store the corporate user data
	 *
	 * @param array data array
	 * @return	boolean	True on success
	 */
	function storeCorporate($params)
	{
		$db =& Jfactory::getDBO();

		$query =
			'INSERT INTO ' . $db->nameQuote('#__corporate_user') . ' (
				user_id,
				corporate_name,
				url,
				logo
				) VALUES (
        		' . $db->quote($params['user_id']) . ',
        		' . $db->quote($params['corporate_name']) . ',
        		' . $db->quote($params['url']) . ',
				' . $db->quote($params['logo']) . '
        	)';
		
		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Method to load user data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$this->_data =& JFactory::getUser();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Takes a user supplied e-mail address, looks
	 * it up in the database to find the username
	 * and then e-mails the username to the e-mail
	 * address given.
	 *
	 * @since	1.5
	 * @param	string	E-mail address
	 * @return	bool	True on success/false on failure
	 */
	function remindUsername($email)
	{
		jimport('joomla.mail.helper');

		global $mainframe;
		// get the users name
		$db		=& JFactory::getDBO();
		$query 	= " SELECT name FROM #__users WHERE email = '$email' LIMIT 1 ";
		$db->setQuery($query);
		$name =  $db->loadResult();

		// Validate the e-mail address
		if (!JMailHelper::isEmailAddress($email))
		{
			$this->setError(JText::_('INVALID_EMAIL_ADDRESS'));
			return false;
		}

		$db = &JFactory::getDBO();
		$db->setQuery('SELECT username FROM #__users WHERE email = '.$db->Quote($email), 0, 1);

		// Get the username
		if (!($username = $db->loadResult()))
		{
			$this->setError(JText::_('COULD_NOT_FIND_EMAIL'));
			return false;
		}

		// Push the email address into the session
		$mainframe->setUserState('topbettauser.remind.email', $email);

		// Send the reminder email
		if (!$this->_sendReminderMail($email, $username, $name))
		{
			return false;
		}

		return true;
	}

	/**
	 * Sends a username reminder to the e-mail address
	 * specified containing the specified username.
	 *
	 * @since	1.5
	 * @param	string	A user's e-mail address
	 * @param	string	A user's username
	 * @return	bool	True on success/false on failure
	 */
	function _sendReminderMail($email, $username, $name)
	{
		$config		= &JFactory::getConfig();
		$sitename	= $config->getValue('sitename');
		$subject	= JText::sprintf('USERNAME_REMINDER_EMAIL_TITLE', $sitename);

		// Send the e-mail
		$mailer = new UserMAIL();
		$email_params	= array(
		'subject'	=> $subject,
		'mailto'	=> $email
		);
		$email_replacements = array(
		'name'				=> $name,
		'username'			=> $username,
		);

		if( $mailer->sendUserEmail('forgotUsernameEmail', $email_params, $email_replacements) !== true)
		{
			$this->setError('ERROR_SENDING_REMINDER_EMAIL');
			return false;
		}

		return true;
	}

	/**
	 * Verifies the validity of a username/e-mail address
	 * combination and creates a token to verify the request
	 * was initiated by the account owner.  The token is
	 * sent to the account owner by e-mail
	 *
	 * @since	1.5
	 * @param	string	Username string
	 * @param	string	E-mail address
	 * @return	bool	True on success/false on failure
	 */
	function requestReset($email)
	{
		jimport('joomla.mail.helper');
		jimport('joomla.user.helper');

		$db = &JFactory::getDBO();

		// Make sure the e-mail address is valid
		if (!JMailHelper::isEmailAddress($email))
		{
			$this->setError(JText::_('INVALID_EMAIL_ADDRESS'));
			return false;
		}

		// Build a query to find the user
		$query	= 'SELECT id FROM #__users'
		. ' WHERE email = '.$db->Quote($email)
		. ' AND block = 0';
		$db->setQuery($query);

		// Check the results
		if (!($id = $db->loadResult())) {
			$this->setError(JText::_('COULD_NOT_FIND_USER'));
			return false;
		}
		// Build a query to find the user
		$query	= 'SELECT name FROM #__users'
		. ' WHERE email = '.$db->Quote($email)
		. ' AND block = 0';
		$db->setQuery($query);
		$name = $db->loadResult();


		// Generate a new token
		$token = JUtility::getHash(JUserHelper::genRandomPassword());

		$query	= 'UPDATE #__users'
		. ' SET activation = '.$db->Quote($token)
		. ' WHERE id = '.(int) $id
		. ' AND block = 0';

		$db->setQuery($query);

		// Save the token
		if (!$db->query())
		{
			$this->setError(JText::_('DATABASE_ERROR'));
			return false;
		}

		// Send the token to the user via e-mail
		if (!$this->_sendConfirmationMail($email, $token, $name))
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks a user supplied token for validity
	 * If the token is valid, it pushes the token
	 * and user id into the session for security checks.
	 *
	 * @since	1.5
	 * @param	token	An md5 hashed randomly generated string
	 * @return	bool	True on success/false on failure
	 */
	function confirmReset($token)
	{
		global $mainframe;

		if(strlen($token) != 32) {
			$this->setError(JText::_('INVALID_TOKEN'));
			return false;
		}

		$db	= &JFactory::getDBO();
		$db->setQuery('SELECT id FROM #__users WHERE block = 0 AND activation = '.$db->Quote($token));

		// Verify the token
		if (!($id = $db->loadResult()))
		{
			$this->setError(JText::_('INVALID_TOKEN'));
			return false;
		}

		// Push the token and user id into the session
		$mainframe->setUserState('topbettauser.reset.token',	$token);
		$mainframe->setUserState('topbettauser.reset.id',	$id);

		return true;
	}

	/**
	 * Takes the new password and saves it to the database.
	 * It will only save the password if the user has the
	 * correct user id and token stored in her session.
	 *
	 * @since	1.5
	 * @param	string	New Password
	 * @param	string	New Password
	 * @return	bool	True on success/false on failure
	 */
	function completeReset($password1, $password2)
	{
		jimport('joomla.user.helper');

		global $mainframe;

		// Make sure that we have a pasword
		if ( ! $password1 )
		{
			$this->setError(JText::_('MUST_SUPPLY_PASSWORD'));
			return false;
		}

		// Verify that the passwords match
		if ($password1 != $password2)
		{
			$this->setError(JText::_('PASSWORDS_DO_NOT_MATCH_LOW'));
			return false;
		}

		// Get the necessary variables
		$db			= &JFactory::getDBO();
		$id			= $mainframe->getUserState('topbettauser.reset.id');
		$token		= $mainframe->getUserState('topbettauser.reset.token');
		$salt		= JUserHelper::genRandomPassword(32);
		$crypt		= JUserHelper::getCryptedPassword($password1, $salt);
		$password	= $crypt.':'.$salt;

		// Get the user object
		$user = new JUser($id);

		// Fire the onBeforeStoreUser trigger
		JPluginHelper::importPlugin('user');
		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeStoreUser', array($user->getProperties(), false));

		// Build the query
		$query 	= 'UPDATE #__users'
		. ' SET password = '.$db->Quote($password)
		. ' , activation = ""'
		. ' WHERE id = '.(int) $id
		. ' AND activation = '.$db->Quote($token)
		. ' AND block = 0';

		$db->setQuery($query);

		// Save the password
		if (!$result = $db->query())
		{
			$this->setError(JText::_('DATABASE_ERROR'));
			return false;
		}

		// Update the user object with the new values.
		$user->password			= $password;
		$user->activation		= '';
		$user->password_clear	= $password1;

		// Fire the onAfterStoreUser trigger
		$dispatcher->trigger('onAfterStoreUser', array($user->getProperties(), false, $result, $this->getError()));

		// Flush the variables from the session
		$mainframe->setUserState('topbettauser.reset.id',	null);
		$mainframe->setUserState('topbettauser.reset.token',	null);

		return true;
	}

	/**
	 * Sends a password reset request confirmation to the
	 * specified e-mail address with the specified token.
	 *
	 * @since	1.5
	 * @param	string	An e-mail address
	 * @param	string	An md5 hashed randomly generated string
	 * @return	bool	True on success/false on failure
	 */
	function _sendConfirmationMail($email, $token, $name)
	{
		$config		= &JFactory::getConfig();
		$sitename	= $config->getValue('sitename');
		$subject	= JText::sprintf('PASSWORD_RESET_CONFIRMATION_EMAIL_TITLE', $sitename);

		// Send the e-mail
		$mailer = new UserMAIL();
		$email_params	= array(
		'subject'	=> $subject,
		'mailto'	=> $email
		);
		$email_replacements = array(
		'name'		=> $name,
		'token'		=> $token
		);

		if($mailer->sendUserEmail('forgotPasswordEmail', $email_params, $email_replacements) !== true)
		{
			$this->setError('ERROR_SENDING_CONFIRMATION_EMAIL');
			return false;
		}

		return true;
	}

	/**
	 * Get User Informatiion
	 *
	 * @param int user id
	 * @return object
	 */

	function getUser( $userId = null )
	{
		// Get the database connection
		$db =& $this->getDBO();
		$topbettaUserTable = $db->nameQuote('#__topbetta_user');
		$userTable = $db->nameQuote('#__users');

		if( empty($userId) )
		{
			$userId = $this->_id;
		}

		if( empty($userId))
		{
			$user	=& JFactory::getUser();
			$userId = $user->get('id');
		}

		$query = "SELECT t.*, u.email, u.username, u.name FROM $topbettaUserTable t"
		. " LEFT JOIN $userTable u ON t.user_id = u.id"
		. " WHERE t.user_id = " . $db->quote($userId)
		;
		$db->setQuery($query);
		// Return the transaction
		return $db->loadObject();
	}

	/**
	 * Check if a field has already had the same value
	 *
	 * @param string field name
	 * @param string value
	 * @return object
	 */

	function isExisting( $field, $value )
	{
		$db =& $this->getDBO();
		$table = $db->nameQuote('#__users');
		$field = $db->nameQuote($field);
		$value = $db->quote($value);

		$query = "SELECT * FROM $table WHERE $field = $value LIMIT 1";
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Check if a logo is already exists
	 *
	 * @param string value
	 * @return object
	 */

	function isExistingLogo( $value )
	{
		$db =& $this->getDBO();
		$table = $db->nameQuote('#__corporate_user');
		$field = $db->nameQuote('logo');
		$value = $db->quote($value);

		$query = "SELECT * FROM $table WHERE $field = $value LIMIT 1";
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Block a user and delete their active session to log them out.
	 *
	 * @param integer $user_id
	 * @return boolean
	 */
	function selfExclude($user_id, $exclusion_end_timestamp)
	{
		$db =& $this->getDBO();
		
		$query =
			'UPDATE
				' . $db->nameQuote('#__users') . '
			SET
				block = 1
			WHERE
			id = ' . $db->quote($user_id);
		$db->setQuery($query);
		
		if (!$db->query()) {
			return false;
		}
		
		return ($this->update('self_exclusion_date', date('Y-m-d H:i:s'), $user_id));
	}
	
	/**
	 * Get a list of btag users
	 *
	 * @param date $from_date
	 * @param date $to_date
	 * @return array
	 */
	function getBtagUserList($from_date = null, $to_date = null)
	{
		$registry =& JFactory::getConfig();
		$timezone_offset = $registry->getValue('config.offset');
		
		$db =& $this->getDBO();
		
		$query = '
			SELECT
				u.id,
				u.username,
				(u.registerDate + INTERVAL ' . $timezone_offset . ' hour) AS registrationDate,
				t.btag,
				t.country
			FROM
				' . $db->nameQuote('#__users') . ' AS u
			INNER JOIN
				' . $db->nameQuote('#__topbetta_user') . ' AS t
			ON
				u.id = t.user_id
			WHERE
				btag IS NOT NULL
			AND
				btag != ""
		';
		
		if (!empty($from_date)) {
			$query .= '
				AND
					u.registerDate >= ' . $db->quote($from_date . ' 00:00:00') . " - INTERVAL $timezone_offset hour";
			;
		}
		
		if (!empty($to_date)) {
			$query .= '
				AND
					u.registerDate <= ' . $db->quote($to_date . ' 23:59:59') . " - INTERVAL $timezone_offset hour";
			;
		}
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Checks if the user id is mapped as facebook user.
	 *
	 * @param	int	 user id
	 * @return	bool	True on success/false on failure
	 */
	function isFacebookUser($id)
	{
		
        $db		=& JFactory::getDBO();
		$query 	= " SELECT * FROM ". $db->nameQuote('#__jfbconnect_user_map') ." WHERE ". $db->nameQuote('j_user_id') ." = ".$db->quote($id);
		
		$db->setQuery($query);
		$result = count($db->loadObjectList());
        if($result == '1'){
			return true;
		}else{
            return false;
		}
	}

	/**
	 * Checks if the user id is a full topbetta user.
	 *
	 * @param	int	 user id
	 * @return	bool	True on success/false on failure
	 */
	function isTopbettaUser($id)
	{
		
        $db		=& JFactory::getDBO();
		$query 	= " SELECT * FROM ". $db->nameQuote('#__users') ." WHERE ". $db->nameQuote('id') ." = ".$db->quote($id) . " AND ". $db->nameQuote('isTopBetta') . " = 1";
		
		$db->setQuery($query);
		$result = count($db->loadObjectList());
        if($result == '1'){
			return true;
		}else{
            return false;
		}
	}
	
	/**
	 * get the promotion code.
	 *
	 * @param	string	 $code
	 */
	function getPromotion($code='')
	{
		
        $db		=& JFactory::getDBO();
		$query 	= " SELECT * FROM ". $db->nameQuote('#__promotions') . " WHERE pro_status=1 ";
		if (!empty($code)) $query .= " AND ". $db->nameQuote('pro_code') ." = ".$db->quote($code);
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}
?>
