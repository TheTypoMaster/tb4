<?php
 defined('_JEXEC') OR defined('_VALID_MOS') OR die('...Direct Access to this location is not allowed...');

jimport('joomla.plugin.plugin');
jimport('joomla.application.module.helper');
jimport('fastpass.fastpass');
jimport('mobileactive.config.reader');

/**
 * Plugin to record user's session
 */
class plgUserGetSatisfaction extends JPlugin {
	
	function plgGetSatisfaction( &$subject, $config) {
		parent::__construct($subject, $config);
	}//endfct
	/**
	 * Set cookie after user login
	 *
	 * @param 	array		holds the new user data
	 * @param 	array    extra options
	 * @return	boolean	True on success
	 */
	function onLoginUser($user, $option)
	{
		$loginUser =& JFactory::getUser();
		
		if (!$loginUser->guest){
			$JModule = new JModuleHelper();
			$getsatisfaction_module = $JModule->getModule('getsatisfaction');
			
			$params = new JParameter($getsatisfaction_module->params);
			$key 	= $params->get('fastpass_key');
			$secret =  $params->get('fastpass_secret');;
			
			$fastpass_url = FastPass::url($key, $secret, $loginUser->email, $loginUser->username, $loginUser->id, true, array());
			setcookie("fastpass", $fastpass_url, time()+60*60*24*30,'/','.topbetta.com');
		}
		
		return true;
	}
	/**
	 * Log user out from get satisfaction
	 *
	 * @param 	array		holds the new user data
	 * @return	boolean	True on success
	 */
	function onLogoutUser($user, $option)
	{	
		global $mainframe;
		
		if ($user['id']){
			// disable logout of getsatisfaction as causes ssl exception in ie
			setcookie("fastpass", '', time()-3600, '/','.topbetta.com');
			setcookie("fastpass_logout", true, time()+3600);
		}
		
		return true;
	}
}//endclass

