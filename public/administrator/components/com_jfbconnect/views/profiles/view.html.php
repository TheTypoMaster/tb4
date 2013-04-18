<?php

/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JFBConnectViewProfiles extends JView
{
	function display($tpl = null)
	{
            $app = JFactory::getApplication();
            JPluginHelper::importPlugin('jfbcprofiles');
            $profilePlugins = $app->triggerEvent('jfbcProfilesGetPlugins');
            $this->assignRef('profilePlugins', $profilePlugins);

            parent::display($tpl);
	}
}
