<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JFBConnectViewJFBConnect extends JView
{

    var $versionChecker;

    function display($tpl = null)
    {
        require_once(JPATH_COMPONENT_SITE . DS . 'libraries' . DS . 'facebook.php');
        require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'assets' . DS . 'sourcecoast.php');

        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $configModel = $this->getModel();
        $usermapModel = $this->getModel('usermap');
        $autotuneModel = JModel::getInstance('AutoTune', 'JFBConnectModel');

        if ($jfbcLibrary->get('facebookAppId', ''))
        {
            $appConfig = $autotuneModel->getAppConfig();
            if (count($appConfig) == 0 || $appConfig == "")
            {
                $app = JFactory::getApplication();
                $app->enqueueMessage('Facebook Application information not found. Please run <a href="index.php?option=com_jfbconnect&view=autotune">AutoTune</a> to fetch your settings.', 'error');
            }

            $fql = "SELECT monthly_active_users, weekly_active_users, daily_active_users FROM application WHERE app_id=" . $jfbcLibrary->facebookAppId;
            $params = array(
                'method' => 'fql.query',
                'query' => $fql,
            );
            $appStats = $jfbcLibrary->rest($params, FALSE);
            $appStats = $appStats[0];
            $appStats['monthly_active_users'] = isset($appStats['monthly_active_users']) && $appStats['monthly_active_users'] != ""
                    ? $appStats['monthly_active_users'] : "0";
            $appStats['weekly_active_users'] = isset($appStats['weekly_active_users']) && $appStats['weekly_active_users'] != ""
                    ? $appStats['weekly_active_users'] : "0";
            $appStats['daily_active_users'] = isset($appStats['daily_active_users']) && $appStats['daily_active_users'] != ""
                    ? $appStats['daily_active_users'] : "0";
        }
        else
        {
            $appStats['monthly_active_users'] = "0";
            $appStats['weekly_active_users'] = "0";
            $appStats['daily_active_users'] = "0";
        }

        
            $this->versionChecker = new sourceCoastConnect('jfbconnect', 'components/com_jfbconnect/assets/images/');
         //SC15
         //SC16


        $this->assignRef('configModel', $configModel);
        $this->assignRef('autotuneModel', $autotuneModel);
        $this->assignRef('jfbcLibrary', $jfbcLibrary);
        $this->assignRef('usermapModel', $usermapModel);
        $this->assignRef('appStats', $appStats);

        parent::display($tpl);
    }

}
