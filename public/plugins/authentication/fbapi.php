<?php
/**
 * @version		$Id: fbapi.php 14401 2010-01-26 14:10:00Z mic $
 * @package		Joomla
 * @subpackage	JFramework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Example Authentication Plugin
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since 1.5
 */
class plgAuthenticationFbapi extends JPlugin {
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param	object	$subject	The object to observe
	 * @param	array	$config		An array that holds the plugin configuration
	 * @since	1.5
	 */
	function plgAuthenticationFbapi(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param	array	$credentials	Array holding the user credentials
	 * @param	array	$options		Array of extra options
	 * @param	object	$response		Authentication response object
	 * @return	boolean
	 * @since	1.5
	 */
	function onAuthenticate($credentials, $options, &$response) {
		# authentication via facebook for Joomla always uses the FB API and secret keys
		# When this is present, the user's FB uid is used to look up their Joomla uid and log that user in
		jimport('joomla.filesystem.file');
		$libraryFile = JPATH_ROOT . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php';
		if (JFile::exists($libraryFile)) {
			require_once $libraryFile;
			$jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
			$this -> configModel = $jfbcLibrary -> getConfigModel();
			# always check the secret username and password to indicate this is a JFBConnect login
			#echo "Entering JFBConnectAuth<br>";
			if (($credentials['username'] != $this -> configModel -> getSetting('facebook_app_id')) || ($credentials['password'] != $this -> configModel -> getSetting('facebook_secret_key'))) {
				$response -> status = JAUTHENTICATE_STATUS_FAILURE;
				return false;
			}

			#echo "Passed API/Secret key check, this is a FB login<br>";
			include_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models' . DS . 'usermap.php');
			$userMapModel = new JFBConnectModelUserMap();

			$fbUserId = $credentials['fb_id'];
			$app = JFactory::getApplication();

			#echo "Facebook user = ".$fbUserId;
			# test if user is logged into Facebook
			if ($fbUserId) {
				# Test if user has a Joomla mapping
				$jUserId = $userMapModel -> getJoomlaUserId($fbUserId);
				if ($jUserId) {
					$jUser = JUser::getInstance($jUserId);
					if ($jUser -> id == null)// Usermapping is wrong (likely, user was deleted)
					{
						$userMapModel -> deleteMapping($fbUserId);
						return false;
					}

					if ($jUser -> block) {
						$isAllowed = false;
						//SC16

						$app -> enqueueMessage(JText::_('E_NOLOGIN_BLOCKED'), 'error');
						//SC15
					} else {
						$isAllowed = true;
					}

					if ($isAllowed) {
						$response -> status = JAUTHENTICATE_STATUS_SUCCESS;
						$response -> username = $jUser -> username;
						$response -> user_id = $jUser -> id;

						/*
						if (!$this -> configModel -> getSetting('create_new_users'))# psuedo-users
						{
							// Update the J user's email to what it is in Facebook
							$fbProfileFields = $jfbcLibrary -> getUserProfile($fbUserId, array('email'));
							if ($fbProfileFields != null && $fbProfileFields['email']) {
								$jUser -> email = $fbProfileFields['email'];
								$jUser -> save();
							}
						}
						 */

						$response -> language = $jUser -> getParam('language');
						$response -> email = $jUser -> email;
						$response -> fullname = $jUser -> name;
						$response -> error_message = '';
						return true;
					}
				}
			}
		}

		# catch everything else as an authentication failure
		$response -> status = JAUTHENTICATE_STATUS_FAILURE;
		return false;
	}

}
