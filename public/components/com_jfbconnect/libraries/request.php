<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/*
 * Canvas class for using site in a Facebook Canvas layout or Page tab
 */
class JFBConnectRequestLibrary extends JObject
{
    var $jfbcLibrary;

    /* Constructor
     * Generally, don't call - Use getInstance. Must be public to conform to parent JObject class
     */
    public function __construct()
    {
        parent::__construct();
        $this->jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $this->configModel = $this->jfbcLibrary->getConfigModel();
    }

    /*
     * Get class instance for Canvas object.
     */
    public static function getInstance()
    {
        static $instance;
        if (!$instance) {
            $instance = new JFBConnectRequestLibrary();
        }

        return $instance;
    }

    public function checkForNotification()
    {
        $fbRequestIds = JRequest::getVar('request_ids');
        if ($fbRequestIds == "" || $fbRequestIds == null)
            return;

        $fbClient = $this->jfbcLibrary->getFbClient();
        $request = JRequest::getString('signed_request', null, 'POST');
        if ($request) {
            $request = $fbClient->parseSignedRequest($request);
            $fbUserId = $request['user_id'];
        }
        else
            return;

        // Next, find the most recent (local) request, and take action on it.
        JModel::addIncludePath(JPATH_SITE . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models');
        $notificationModel = JModel::getInstance('Notification', 'JFBConnectModel');
        $notificationModel->setFbUserId($fbUserId);
        $notificationModel->setFbRequestIds($fbRequestIds);

        // First, delete all requests on Facebook
        foreach ($notificationModel->getRequestsToDelete() as $sig)
        {
            //echo "Deleting ".$sig."<br/>";
            $this->jfbcLibrary->api('/'.$sig, null, false, 'DELETE');
        }

        // Mark all notifications as 'read'
        $notificationModel->markAsRead();

        // Get the request information from the last request sent
        $redirectInfo = $notificationModel->getRedirect();

        if ($redirectInfo && $redirectInfo->destination_url) {
            $app =JFactory::getApplication();
            if (!$redirectInfo->breakout_canvas) {
                $app->redirect($redirectInfo->destination_url);
            }
            else // Pop out and redirect
            {
                echo "<html><head></head><body><script type='text/javascript'>top.location.href='" . $redirectInfo->destination_url . "'</script></body></html>";
                $app->close();
            }
        }
    }

}
