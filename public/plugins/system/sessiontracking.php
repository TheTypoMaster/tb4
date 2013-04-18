<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! Debug plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class  plgSystemSessionTracking extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemConfig(& $subject, $config)
	{
		parent::__construct($subject, $config);

		//load the translation
		$this->loadLanguage( );
	}

	/**
	* Update last access date in session tracking
	*
	* @return Boolean true on success
	*/
	function onAfterInitialise()
	{
		
		$session =& JFactory::getSession();
		$sessionId =$session->getId();
		$registry =& JFactory::getConfig();
		$registry->setValue('tracking.session', $sessionId);
		
		$user =& JFactory::getUser();
		//only record the session when the user logged in
		if( !$user->guest )
		{
			$db =& Jfactory::getDBO();
			$userId = $db->quote($user->id);
			
			$session =& JFactory::getSession();
			$sessionId = $db->quote($session->getId());
			
			//update last_access
			$updateQuery = "UPDATE #__session_tracking SET last_access = now()
				WHERE session_id = $sessionId AND user_id = $userId
			";
			
			$db->setQuery($updateQuery);
			$db->query();
		}

		return true;
	}
	
}