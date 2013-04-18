<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * @package Joomla
 * @subpackage Config
 */
class TOOLBAR_JFBConnect
{
    static function _DEFAULT()
    {
        JToolBarHelper::title('JFBConnect', 'jfbconnect.png');

        $viewName = JRequest::getVar('view');

        if ($viewName == "Config" || $viewName == "Social" || $viewName == "Profiles" || $viewName == "Canvas")
            JToolBarHelper::apply('apply', 'Apply Changes');

        // Check if AutoTune is up-to-date
        if ($viewName != 'AutoTune')
        {
            JToolBarHelper::divider();
            $autotuneModel = JModel::getInstance('AutoTune', 'JFBConnectModel');
            $upToDate = $autotuneModel->isUpToDate();
            if ($upToDate)
                JToolBarHelper::custom('autotune', 'config', 'config', 'AutoTune', false);
            else
                JToolBarHelper::custom('autotune', 'config', 'config', 'AutoTune<br/>Recommended', false);
        }
        else
            JToolBarHelper::title('JFBConnect - AutoTune', 'jfbconnect.png');

    }

}