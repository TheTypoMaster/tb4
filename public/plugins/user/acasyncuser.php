<?php
 defined('_JEXEC') OR defined('_VALID_MOS') OR die('...Direct Access to this location is not allowed...');
### Copyright (C) 2006-2009 Acajoom Services. All rights reserved.

jimport('joomla.plugin.plugin');


/**
 * Plugin to sync user with Acajoom
 */
class plgUserAcasyncuser extends JPlugin {

	function plgUserAcasyncuser( &$subject, $config) {
		parent::__construct($subject, $config);
	}//endfct


	/**
	 * Example store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param 	array		holds the new user data
	 * @param 	boolean		true if a new user is stored
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 */
	function onAfterStoreUser($user, $isnew, $success, $msg)
	{
		global $mainframe;

		if ( strtolower( substr( JPATH_ROOT, strlen(JPATH_ROOT)-13 ) ) =='administrator' ) {	// joomla 15
			$adminPath = strtolower( substr( JPATH_ROOT, strlen(JPATH_ROOT)-13 ) );
		} else {
			$adminPath = JPATH_ROOT;
		}//endif

		if ( !@include_once( $adminPath . '/components/com_acajoom/defines.php') ) return;
		include_once( WPATH_CLASS . 'class.acajoom.php');

		// convert the user parameters passed to the event
		// to a format the external application

		if ($isnew) {
			// Call a function in the external app to create the user
			// ThirdPartyApp::createUser($user['id'], $args);
			$subscriber = null;
			$subscriberId = 0;
			$subscriber->name = $user['name'];
			$subscriber->email = $user['email'];
			$subscriber->receive_html = 1;
			$subscriber->confirmed = 1;
            $subscriber->subscribe_date = date("Y-m-d H:m:s");
            $subscriber->language_iso = 'eng';
            $subscriber->timezone = '00:00:00';
            $subscriber->blacklist = 0;
            $subscriber->params = '';

			subscribers::insertSubscriber($subscriber, $subscriberId);

		} else {
			// Call a function in the external app to update the user
			// ThirdPartyApp::updateUser($user['id'], $args);
		}//endif

	}//endif


}//endclass

