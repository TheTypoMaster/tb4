<?php
 defined('_JEXEC') OR defined('_VALID_MOS') OR die('...Direct Access to this location is not allowed...');

jimport('joomla.plugin.plugin');


/**
 * Plugin to record user's session
 */
class plgUserSessionTracking extends JPlugin {
	
	const SESSION_CLOSE_LOGOUT = 'logout';
	const SESSION_CLOSE_TIMEOUT = 'timeout';
	
	function plgUserSessionTracking( &$subject, $config) {
		parent::__construct($subject, $config);
	}//endfct


	/**
	 * Store the session info after user login
	 *
	 * @param 	array		holds the new user data
	 * @param 	array    extra options
	 * @return	boolean	True on success
	 */
	function onLoginUser($user, $option)
	{
		$loginUser =& JFactory::getUser();
		//only record the session when the user logged in
		if( !$loginUser->guest )
		{
			$db =& Jfactory::getDBO();
			$userId = $db->quote($loginUser->id);
			$currentDate = $db->quote(date( 'Y-m-d H:i:s'));
			
			$table = $db->nameQuote('#__session_tracking');
			$query = "SELECT * FROM $table
				 WHERE user_id = $userId
				 ORDER BY session_start DESC LIMIT 1";
			;
			
			$db->setQuery($query);
			$rs = $db->loadObject();
			
			$lastAccess = 'NULL';
			if( $rs )
			{
				$lastAccess = ($rs->session_start ? $db->quote($rs->session_start) : 'NULL');

				if( !$rs->session_close_code_id )
				{
					$sessionCloseCodeId = $this->getSessionCloseCodeId(self::SESSION_CLOSE_TIMEOUT);
					
					if( $sessionCloseCodeId )
					{
						//update previous session_close_code_id and session_close
						$table = $db->nameQuote('#__session_tracking');
						$updateQuery = "UPDATE $table SET session_close_code_id = $sessionCloseCodeId,
							session_close = $currentDate WHERE id=" . $rs->id;
						$db->setQuery($updateQuery);
						$db->query();
					}
				}
			}
			
			//create the new session record
			$session =& JFactory::getSession();
			$sessionId = $db->quote($session->getId());
			
			$remoteIp = $db->quote($_SERVER['REMOTE_ADDR']);
			$userAgent = $db->quote($_SERVER['HTTP_USER_AGENT']);
			
			$table = $db->nameQuote('#__session_tracking');
			$insertQuery = "INSERT INTO $table
				( session_id, user_id, remote_ip, user_agent, session_start, last_access )
				VALUES ($sessionId, $userId, $remoteIp, $userAgent, now(), $lastAccess)
			";
			
			$db->setQuery($insertQuery);
			$db->query();
				
			//register session tracking id to session
			$session =& JFactory::getSession();
			$session->set( 'sessionTrackingId', mysql_insert_id() );
			
		}
		
		return true;
	}
	
	/**
	 * Store session_close, session_close_id after user logout
	 *
	 * @param 	array		holds the new user data
	 * @return	boolean	True on success
	 */
	function onLogoutUser($user, $option)
	{
	
		/*
		 	Session was destroied, we couldn't get the session id,
		 	for now we use user id
		  
		 */	
		
		if( $user['id'])
		{
			$db =& Jfactory::getDBO();
			$userId = $db->quote($user['id']);
			
			$session =& JFactory::getSession();
			$sessionId =$session->getId();
			$registry =& JFactory::getConfig();
			$sessionId = $db->quote($registry->getValue('tracking.session'));
			
			$sessionCloseCodeId = $this->getSessionCloseCodeId(self::SESSION_CLOSE_LOGOUT);
			
			if( $sessionCloseCodeId)
			{
				$cond = array();
				$cond[] = "user_id = $userId";
				if( $sessionId )
				{
					$cond[] = "session_id = $sessionId";
				}
				$cond = join( ' AND ', $cond);
				
				//update session_close_code_id and session_close
				$table = $db->nameQuote('#__session_tracking');
				$updateQuery = "UPDATE $table SET session_close_code_id = $sessionCloseCodeId,
					session_close = now() WHERE $cond
					ORDER BY session_start DESC LIMIT 1";
				$db->setQuery($updateQuery);
				$db->query();
			}
		}
		
		return true;
	}

	/**
	 * Get stored session_close_code_id
	 *
	 * @param 	string		the keyword of session close code
	 * @return	string
	 */
	function getSessionCloseCodeId( $keyword )
	{
		$sessionCloseCodeId = NULL;
		$db =& Jfactory::getDBO();
		$table = $db->nameQuote('#__session_close_code');
		$query = "SELECT id FROM $table
			 WHERE keyword = " . $db->quote($keyword) . " LIMIT 1";
		;
		
		$db->setQuery($query);
		$rs = $db->loadObject();
		if($rs)
		{
			$sessionCloseCodeId = $rs->id;
		}
		
		return $sessionCloseCodeId;
	}
}//endclass

