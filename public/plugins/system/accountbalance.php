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
class  plgSystemAccountBalance extends JPlugin
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
	function plgSystemAccountBalance(& $subject, $config)
	{
		parent::__construct($subject, $config);

		//load the translation
		$this->loadLanguage( );
	}

	/**
	* Create a new instance of the account transaction model and store it into user object
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
		
		$user =& JFactory::getUser();
		include_once( 'components' . DS . "com_payment" . DS . "models" . DS . "accounttransaction.php" );
		$model = new PaymentModelAccounttransaction();
		//only initialize model when user login
		if( !$user->guest )
		{
			$user->account_balance =& $model;
			$user->account_balance->setUserId( $user->id );
		}
	}
}