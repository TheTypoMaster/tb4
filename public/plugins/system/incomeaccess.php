<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! Income Access plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class  plgSystemIncomeAccess extends JPlugin
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
	function plgSystemIncomeAccess(& $subject, $config)
	{
		parent::__construct($subject, $config);

		//load the translation
		$this->loadLanguage( );
	}

	/**
	* Check and store btag to a cookie if it appears in url
	*
	* @return void
	*/
	function onAfterInitialise()
	{
		global $mainframe;
		
		if($mainframe->isAdmin() )
		{
			return;
		}
		
		$existing_btag_cookie = JRequest::getVar('btag', null, 'cookie');
		if (empty($existing_btag_cookie)) {
			$btag_cookie = JRequest::getVar('btag', null);
			
			if (!empty($btag_cookie)) {
				//set registrationTracking expires after 30 days
				setcookie("btag", $btag_cookie, time()+2592000, '/');
			}
		}
	}
}