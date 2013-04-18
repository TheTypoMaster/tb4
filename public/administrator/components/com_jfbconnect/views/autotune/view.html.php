<?php

/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JFBConnectViewAutoTune extends JView
{
    function display($tpl = null)
    {
        include('tmpl/step_sidebar.php');
        $configModel = $this->getModel('config');

        if ($this->getLayout() == 'fbapp')
        {
            $appConfig = $this->get('mergedRecommendations');
            $this->assignRef('appConfig', $appConfig);

            $appConfigUpdated = $configModel->getUpdatedDate('autotune_app_config');
            $fieldsUpdated = $configModel->getUpdatedDate('autotune_field_descriptors');
            $this->assignRef('appConfigUpdated', $appConfigUpdated);
            $this->assignRef('fieldsUpdated', $fieldsUpdated);

            $subscriberId = $configModel->getSetting('sc_download_id', 'No ID Set!');
            $this->assignRef('subscriberId', $subscriberId);
        }

        
        JRequest::setVar('hidemainmenu', 1);
        //SC15

        $this->assignRef('jfbcLibrary', $jfbcLibrary);

        parent::display($tpl);

        $atModel = $this->getModel('autotune');
        $subStatus = $atModel->getSubscriptionStatus();
        if ($subStatus)
        {
            $subStatus = $subStatus->messages;
            $this->assignRef('subStatus', $subStatus);

            $subStatusUpdated = $configModel->getUpdatedDate('autotune_authorization');
            $subStatusUpdated = strftime("%Y/%m/%d", strtotime($subStatusUpdated));
            $this->assignRef('subStatusUpdated', $subStatusUpdated);
            include('tmpl/subscription_status.php');
        }
    }

}
