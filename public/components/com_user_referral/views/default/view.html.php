<?php
/**
 * Joomla! 1.5 component uc_betman
 *
 * @version $Id: view.html.php 2009-08-07 04:40:27 svn $
 * @author uc-joomla.net
 * @package Joomla
 * @subpackage uc_betman
 * @license Copyright (c) 2009 - All Rights Reserved
 *
 * sports tournament betting component
 *

 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the uc_betman component
 */
class userReferralViewDefault extends JView {

	function display($tpl = null)
	{
		global $mainframe;
		//get userinfo
		$user =& JFactory::getUser();

		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TopBetta - Refer a Friend') );

		$model =& $this->getModel('topbettaUser');

		$document->addStyleSheet('components/com_user_referral/assets/default.css');
		$this->assign('is_registered', !$user->guest && strtolower($user->usertype) == 'registered');
		$this->assign('userid', $user->id);

		parent::display($tpl);
	}
}
?>