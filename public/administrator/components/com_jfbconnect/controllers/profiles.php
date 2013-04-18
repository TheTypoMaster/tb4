<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JFBConnectControllerProfiles extends JFBConnectController
{

    function __construct()
    {
        JRequest::setVar('view', 'Profiles');
        parent::__construct();
    }

    function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('layout', 'default');
        parent::display();

    }

    function apply()
    {
        $app =JFactory::getApplication();
        $configs = JRequest::get('POST');
        $model = $this->getModel('config');
        $model->saveSettings($configs);
        $app->enqueueMessage("Settings updated!");
        $this->display();
    }

}