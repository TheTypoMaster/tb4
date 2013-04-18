<?php

/**
 * @package		JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JFBConnectViewConfig extends JView
{
	function display($tpl = null)
	{
		$model = $this->getModel('config');
		$this->assignRef('model', $model);

                $isLocaleSupported = true;
                $lang = JFactory::getLanguage();
                $locale = $lang->getDefault();
                if($locale == 'sr-YU')
                    $isLocaleSupported = false;

                $this->assignRef('isLocaleSupported', $isLocaleSupported);

		parent::display($tpl);
	}
}
