<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.user.helper');
jimport('sourcecoast.utilities');

class JFBConnectControllerLoginRegister extends JController {
	var $_newUserPassword = "";

	function display($cachable = false, $urlparams = false) {
		$this -> fetchProfileData();

		#JRequest::setVar('tmpl', 'component');
		parent::display();
	}

	function fetchProfileData() {
		$app = JFactory::getApplication();
		JPluginHelper::importPlugin('jfbcprofiles');
		$app -> triggerEvent('jfbcProfilesOnFetchData');
	}

	function createNewUser() {
		$redirect = '';
		$menuItemId = 0;
		SCSocialUtilities::getCurrentReturnParameter($redirect, $menuItemId, LOGIN_TASK_JFBCONNECT);
		$returnParam = '&return=' . base64_encode($redirect);

		$app = JFactory::getApplication();
		if (!JRequest::checkToken())
			$app -> redirect(JRoute::_('index.php?option=com_jfbconnect&view=loginregister' . $returnParam), 'Your session timed out. Please try again', 'error');

		$this -> _getLoginPostData();

		$jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
		$configModel = $jfbcLibrary -> getConfigModel();
		$fbUserId = $jfbcLibrary -> getFbUserId();

		require_once (JPATH_ROOT . DS . 'components' . DS . 'com_user' . DS . 'controller.php');
		jimport('joomla.user.helper');

		$fbUserProfile = $jfbcLibrary -> _getUserName($fbUserId);

		$username = JRequest::getVar('username', '', 'POST', 'username');
		$email = JRequest::getVar('email', '', 'POST', 'string');
		$password = JRequest::getVar('password', '', 'POST', 'string', JREQUEST_ALLOWRAW);
		$password2 = JRequest::getVar('password2', '', 'POST', 'string', JREQUEST_ALLOWRAW);

		if (strlen($password) < 6) {
			$app -> enqueueMessage(JText::_('COM_JFBCONNECT_PASSWORD_TOO_SHORT'), 'error');
			$app -> redirect(JRoute::_('index.php?com_jfbconnect&view=loginregister' . $returnParam, false));
		}

		$jUser = clone(JFactory::getUser());
		$ACL = &JFactory::getACL();
		$userConfig = &JComponentHelper::getParams('com_users');
		$newUserType = $userConfig -> get('new_usertype');
		if (!$newUserType)
			$newUserType = 'Registered';

		$userVals['name'] = $fbUserProfile['name'];
		$userVals['username'] = $username;
		$userVals['email'] = $email;
		$userVals['password'] = $password;
		$userVals['password2'] = $password2;

		//$user = JFactory::getUser();
		if (!$jUser -> bind($userVals)) {
			$app -> enqueueMessage(JText::_("COM_JFBCONNECT_UNABLE_TO_SAVE_USER"), 'error');
			$app -> redirect(JRoute::_('index.php?com_jfbconnect&view=loginregister' . $returnParam, false));
		}

		// Set the activation bit based on whether we're skipping it and if it's enabled in the first place
		$useractivation = $userConfig -> get('useractivation') && !$configModel -> getSetting('joomla_skip_newuser_activation');

		$userConfig -> set('useractivation', $useractivation);
		if ($useractivation) {
			$jUser -> set('activation', JUtility::getHash(JUserHelper::genRandomPassword()));
			$jUser -> set('block', '1');
		} else
			$jUser -> set('block', 0);

		$jUser -> set('id', 0);
		$jUser -> set('usertype', $newUserType);
		$jUser -> set('gid', $ACL -> get_group_id('', $newUserType, 'ARO'));

		if (!$jUser -> save()) {
			$app -> enqueueMessage(JText::_("COM_JFBCONNECT_UNABLE_TO_SAVE_USER"), 'error');
			$app -> redirect(JRoute::_('index.php?com_jfbconnect&view=loginregister' . $returnParam, false));
		}

		#Send the new user confirmation email and admin notify emails
		$password = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);
		$this -> _newUserPassword = preg_replace('/[\x00-\x1F\x7F]/', '', $password);
		//Disallow control chars in the email
		//SC15

		//SC16

		$this -> _clearLoginPostData();

		$jfbcLibrary -> setInitialRegistration();
		SCSocialUtilities::clearJFBCNewMappingEnabled();

		include_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models' . DS . 'usermap.php');
		$userMapModel = new JFBConnectModelUserMap();
		if ($userMapModel -> mapUser($fbUserId, $jUser -> id))
			$app -> enqueueMessage(JText::_('COM_JFBCONNECT_MAP_USER_SUCCESS'));
		else
			$app -> enqueueMessage(JText::_('COM_JFBCONNECT_MAP_USER_FAIL'));

		$this -> login();
	}

	function _getLoginPostData() {
		$postData = JRequest::get('post');

		if (isset($postData['password']))
			$postData['password'] = '';
		if (isset($postData['password2']))
			$postData['password2'] = '';
		//SC15

		//SC16

		$session = JFactory::getSession();
		$session -> set('postDataLoginRegister', $postData);
	}

	function _clearLoginPostData() {
		$session = JFactory::getSession();
		$session -> clear('postDataLoginRegister');
	}

	function login() {
		$app = JFactory::getApplication();
		$jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
		$configModel = $jfbcLibrary -> getConfigModel();
		$fbUserId = $jfbcLibrary -> getFbUserId();

		if (!$jfbcLibrary -> validateToken() || !$fbUserId) {
			$session = JFactory::getSession();
			$session -> clear('jfbcTokenRequestCount');
			// Let user retry again if they want
			$app -> enqueueMessage('We were unable to retrieve your Facebook account information. Please try again');
			$app -> redirect('index.php');
		}

		JPluginHelper::importPlugin('jfbcprofiles');
		$userMapModel = new JFBConnectModelUserMap();
		$jUserId = $userMapModel -> getJoomlaUserId($fbUserId);

		// Check if no mapping, and Automatic Registration is set. If so, create the new user.
		if (!$jUserId && !$configModel -> getSetting('create_new_users')) {# User is not in system, should create their account automatically
			if ($this -> createFacebookOnlyUser($fbUserId))
				$jUserId = $userMapModel -> getJoomlaUserId($fbUserId);
		}

		$doLogin = true;
		if ($jfbcLibrary -> initialRegistration) {
			if ($jUserId) {
				$jUser = JUser::getInstance($jUserId);

				$doLogin = $this -> activateUser();
				// Set to false if user has to activate
				$this -> sendNewUserEmails($jUser);

				# New user, set their new user status and trigger the OnRegister event
				$args = array($jUser -> get('id'), $fbUserId);
				$app -> triggerEvent('jfbcProfilesOnRegister', $args);
				$jfbcLibrary -> setFacebookNewUserMessage();
			} else
				$doLogin = false;
		}

		if ($doLogin && $jUserId) {
			$options = array('silent' => 1);
			// Disable other authentication messages
			$loginResult = $app -> login(array('username' => $configModel -> getSetting('facebook_app_id'), 'password' => $configModel -> getSetting('facebook_secret_key')), $options);
			// TODO: Move this to the JFBCUser plugin, shouldn't have to check result here
			if (!JError::isError($loginResult)) {
				$session =& JFactory::getSession();
                $session->set( 'LoggedInFromFb','1' );
				if (!$jfbcLibrary -> initialRegistration) {
					$args = array($jUserId, $fbUserId);
					$app -> triggerEvent('jfbcProfilesOnLogin', $args);
					$jfbcLibrary -> setFacebookLoginMessage();
				}

				// Store the access token to the database for possible offline access later
				$fbClient = $jfbcLibrary -> getFBClient();
				$token = $fbClient -> getPersistentData('access_token', null);
				if ($token && $token != $fbClient -> getApplicationAccessToken())// Should always be valid, but caution is good.
				{
					// get an extended access token
					$params['client_id'] = $jfbcLibrary -> facebookAppId;
					$params['client_secret'] = $jfbcLibrary -> facebookSecretKey;
					$params['grant_type'] = 'fb_exchange_token';
					$params['fb_exchange_token'] = $token;
					$url = 'https://graph.facebook.com/oauth/access_token';

					$response = $fbClient -> makeRequest($url, $params);

					$response_params = array();
					parse_str($response, $response_params);
					if (isset($response_params['access_token']))
						$token = $response_params['access_token'];

					// Always store a token, whether it's the short or long-lived one.
					$userMapModel -> updateUserToken($jUserId, $token);
				}
				
			}
		}

		$loginRegisterModel = $this -> getModel('LoginRegister', 'JFBConnectModel');
		$redirect = $loginRegisterModel -> getLoginRedirect();
        
		$app -> redirect($redirect);
	}

	function createFacebookOnlyUser($fbUserId) {
		$jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
		$jfbcLibrary -> setInitialRegistration();

		$configModel = $jfbcLibrary -> getConfigModel();
		$loginRegisterModel = $this -> getModel('LoginRegister', 'JFBConnectModel');

		$fbUser = $jfbcLibrary -> _getUserName($fbUserId);
		if ($fbUser == null)# no information returned from FB
			return false;

		if ($fbUser['email'] == null)
			$newEmail = $fbUser['first_name'] . "_" . $fbUserId . "@unknown.com";
		else
			$newEmail = $fbUser['email'];

		$fullname = $fbUser['name'];

		$user['fullname'] = $fullname;
		$user['email'] = $newEmail;

		// Create random password for FB User Only, but save so we can email to the user on account creation
		if ($configModel -> getSetting('generate_random_password')) {
			$this -> _newUserPassword = JUserHelper::genRandomPassword();
			$salt = JUserHelper::genRandomPassword(32);
			$crypt = JUserHelper::getCryptedPassword($this -> _newUserPassword, $salt);
			$user['password'] = $crypt . ':' . $salt;
		} else {
			$user['password_clear'] = "";
			$this -> _newUserPassword = '';
		}

		$lang = JRequest::getVar(JUtility::getHash('language'), '', 'COOKIE');
		$user['language'] = $lang;

		$usernamePrefixFormat = $configModel -> getSetting('auto_username_format');
		$username = $loginRegisterModel -> getAutoUsername($fbUser, $fbUserId, $usernamePrefixFormat);
		$user['username'] = $username;

		$useractivation = $this -> getActivationMode();
		$jUser = $loginRegisterModel -> getBlankUser($user, $useractivation);

		if ($jUser -> get('id', null))// If it's not set, there was an error
		{
			SCSocialUtilities::clearJFBCNewMappingEnabled();

			#Map the new user
			include_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models' . DS . 'usermap.php');
			$userMapModel = new JFBConnectModelUserMap();
			$app = JFactory::getApplication();
			if ($userMapModel -> mapUser($fbUserId, $jUser -> get('id'))) {
				$app -> enqueueMessage(JText::_('COM_JFBCONNECT_MAP_USER_SUCCESS'));
				return true;
			} else
				$app -> enqueueMessage(JText::_('COM_JFBCONNECT_MAP_USER_FAIL'));
		}
		return false;
		// User creation failed for some reason
	}

	function activateUser() {
		$useractivation = $this -> getActivationMode();
		$language = JFactory::getLanguage();
		$app = JFactory::getApplication();

		if ($useractivation) {
			$language -> load('com_user');
			# New user, set their new user status and trigger the OnRegister event
			$app -> enqueueMessage(JText::_('REG_COMPLETE_ACTIVATE'));
		}

		if ($useractivation == 0)
			return true;
		else
			return false;
	}

	function getActivationMode() {
		$jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
		$configModel = $jfbcLibrary -> getConfigModel();

		if ($configModel -> getSetting('joomla_skip_newuser_activation')) {
			return 0;
		} else {
			$params = JComponentHelper::getParams('com_users');
			$useractivation = $params -> get('useractivation');
			return $useractivation;
		}
	}

	public function loginMap() {
		JRequest::checkToken('post') or jexit(JText::_('JInvalid_Token'));
		$app = JFactory::getApplication();

		SCSocialUtilities::setJFBCNewMappingEnabled();
		$redirect = '';
		$menuItemId = 0;
		SCSocialUtilities::getCurrentReturnParameter($redirect, $menuItemId, LOGIN_TASK_JFBCONNECT);
		$returnParam = '&return=' . base64_encode($redirect);

		// Populate the data array:
		$data = array();
		$data['username'] = JRequest::getVar('username', '', 'method', 'username');
		$data['password'] = JRequest::getString('password', '', 'post', 'string', JREQUEST_ALLOWRAW);

		// Perform the log in.
		$error = $app -> login($data);

		// Check if the log in succeeded.
		if (JError::isError($error) || $error == false) {
			$app -> redirect(JRoute::_('index.php?option=com_jfbconnect&view=loginregister' . $returnParam, false));
		} else//Logged in successfully
		{
			/* Don't import on just a mapping update, for now. Need to investigate.
			 $jUser = JFactory::getUser();
			 $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
			 $fbUserId = $jfbcLibrary->getMappedFbUserId();
			 $args = array($jUser->get('id'), $fbUserId);

			 JPluginHelper::importPlugin('jfbcprofiles');
			 $app->triggerEvent('scProfilesImportProfile', $args);
			 $app->enqueueMessage('Profile Imported!');*/

			JModel::addIncludePath(JPATH_SITE . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models');
			$loginRegisterModel = JModel::getInstance('LoginRegister', 'JFBConnectModel');
			$redirect = $loginRegisterModel -> getLoginRedirect();
			$app -> redirect($redirect);
		}
	}

	function checkUserNameAvailable() {
		$username = JRequest::getString('username');
		$dbo = JFactory::getDBO();
		$query = "SELECT id FROM #__users WHERE username=" . $dbo -> quote($username);
		$dbo -> setQuery($query);
		$result = $dbo -> loadResult();

		if ($result)
			echo false;
		else
			echo true;
		exit ;
	}

	function checkEmailAvailable() {
		$email = JRequest::getString('email');
		$dbo = JFactory::getDBO();
		$query = "SELECT id FROM #__users WHERE email=" . $dbo -> quote($email);
		$dbo -> setQuery($query);
		$result = $dbo -> loadResult();

		if ($result)
			echo false;
		else
			echo true;
		exit ;
	}

	/*
	 * Send message to user notifying them of the new account and if they have to activate.
	 * If default Mail email and name are not set, grab it from a super admin in the DB.
	 */
	function sendNewUserEmails(&$user) {
		$app = JFactory::getApplication();
		$sendEmail = true;
		$profileEmails = $app -> triggerEvent('jfbcProfilesSendsNewUserEmails');
		foreach ($profileEmails as $pe) {
			if ($pe)
				$sendEmail = false;
		}
		if (!$sendEmail)
			return;

		$useractivation = $this -> getActivationMode();

		$newEmail = $user -> get('email');
		if (SCStringUtilities::endswith($newEmail, "@unknown.com"))
			return;

		$app = JFactory::getApplication();
		$language = JFactory::getLanguage();
		$language -> load('com_user');
		$language -> load('com_jfbconnect');

		$db = JFactory::getDBO();

		$name = $user -> get('name');
		$email = $user -> get('email');
		$username = $user -> get('username');
		$sitename = $app -> getCfg('sitename');
		$mailfrom = $app -> getCfg('mailfrom');
		$fromname = $app -> getCfg('fromname');
		$siteURL = JURI::base();

		$subject = sprintf(JText::_('Account details for'), $name, $sitename);
		$subject = html_entity_decode($subject, ENT_QUOTES);

		if ($useractivation == 1) {
			if ($this -> _newUserPassword == '')
				$message = sprintf(JText::_('COM_JFBCONNECT_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPASSWORD'), $name, $sitename, $siteURL . "index.php?option=com_user&task=activate&activation=" . $user -> get('activation'), $siteURL);
			else
				$message = sprintf(JText::_('COM_JFBCONNECT_EMAIL_REGISTERED_WITH_ACTIVATION_BODY'), $name, $sitename, $siteURL . "index.php?option=com_user&task=activate&activation=" . $user -> get('activation'), $siteURL, $username, $this -> _newUserPassword);
		} else {
			if ($this -> _newUserPassword == '')
				$message = sprintf(JText::_('COM_JFBCONNECT_EMAIL_REGISTERED_BODY_NOPASSWORD'), $name, $sitename, $siteURL);
			else
				$message = sprintf(JText::_('COM_JFBCONNECT_EMAIL_REGISTERED_BODY'), $name, $sitename, $siteURL, $username, $this -> _newUserPassword);
		}

		$message = html_entity_decode($message, ENT_QUOTES);

		//get all super administrator
		$query = 'SELECT name, email, sendEmail' . ' FROM #__users' . ' WHERE LOWER( usertype ) = "super administrator"';
		$db -> setQuery($query);
		$rows = $db -> loadObjectList();

		// Send email to user
		if (!$mailfrom || !$fromname) {
			$fromname = $rows[0] -> name;
			$mailfrom = $rows[0] -> email;
		}

		JUtility::sendMail($mailfrom, $fromname, $email, $subject, $message);

		// Send notification to all administrators
		$subject2 = sprintf(JText::_('Account details for'), $name, $sitename);
		$subject2 = html_entity_decode($subject2, ENT_QUOTES);

		// get superadministrators id
		foreach ($rows as $row) {
			if ($row -> sendEmail) {
				$message2 = sprintf(JText::_('SEND_MSG_ADMIN'), $row -> name, $sitename, $name, $email, $username);
				$message2 = html_entity_decode($message2, ENT_QUOTES);
				JUtility::sendMail($mailfrom, $fromname, $row -> email, $subject2, $message2);
			}
		}
		//SC15

		//SC16
	}

}
