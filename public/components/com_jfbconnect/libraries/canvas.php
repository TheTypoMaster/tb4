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

JLoader::register('JFBConnectRequestLibrary', JPATH_SITE . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'request.php');

class JFBConnectCanvasLibrary extends JObject
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
        if (!$instance)
        {
            $instance = new JFBConnectCanvasLibrary();
        }

        return $instance;
    }

    public function setupCanvas()
    {
        $this->checkForBreakout();

        $jSession =JFactory::getSession();

        if ($this->parseSignedRequest())
        {
            $this->set('canvasEnabled', $jSession->get('jfbcCanvasEnabled', false));
            $this->set('inCanvas', $jSession->get('jfbcCanvasInCanvas', false));
            $this->set('inTab', $jSession->get('jfbcCanvasInTab', false));

            // Set the template and reveal page up, we're inside Facebook
            if ($this->get('canvasEnabled'))
            {
                if ($this->get('inTab'))
                {
                    $this->setupPageTab();
                }
                else if ($this->get('inCanvas'))
                {
                    $this->setupAppCanvas();
                }

                $this->setupResizing();
            }
        }
        else
            $this->set('resizeEnabled', false); // Default to disabled

    }

    // check if URL has ?jfbcCanvasBreakout set. If so, break us out of the canvas settings (restricted size, template, etc)
    private function checkForBreakout()
    {
        // Now, check the session and see how the page should be rendered
        $breakout = JRequest::getInt('jfbcCanvasBreakout', 0);
        // Check if we just came from Facebook and should be broken out of the canvas
        if ($breakout)
        {
            $app =JFactory::getApplication();
            $jSession =JFactory::getSession();

            $jSession->clear('jfbcCanvasEnabled');
            $jSession->clear('jfbcCanvasInTab');
            $jSession->clear('jfbcCanvasInCanvas');
            $jSession->clear('jfbcCanvasPageInfo');
            $this->set('canvasEnabled', false);
            // If we were just redirected to (and from) the Reveal Page, go back to the original location
            $origDestination = $jSession->get('jfbcCanvasOrigDestination', null);
            if ($origDestination)
            {
                $jSession->clear('jfbcCanvasOrigDestination');
                $app->redirect($origDestination);
            }
            else // Get rid of the jfbcCanvasBreakout query string
            {
                $uri = JURI::getInstance();
                $uri->delVar('jfbcCanvasBreakout');
                $uri->setVar('ref', 'fb');
                $app->redirect($uri->toString());
            }
        }
    }

    // Setup all the session variables, and return true if inside FB or false if not
    private function parseSignedRequest()
    {
        $fbClient = $this->jfbcLibrary->getFbClient();
        $jSession =JFactory::getSession();

        // For our detection of canvas or tab integration, we only look at the query string (not the cookie!)
        $request = JRequest::getString('signed_request', null, 'POST');
        if ($request)
        {
            $request = $fbClient->parseSignedRequest($request);

            // Check if this is the first load from FB, and set up our session vars
            if (is_array($request))
            {
                $jSession->set('jfbcCanvasEnabled', true);
                if (isset($request['page']))
                {
                    $jSession->set('jfbcCanvasPageInfo', $request['page']);
                    $jSession->set('jfbcCanvasInTab', true);
                    $jSession->set('jfbcCanvasInCanvas', false);
                }
                else
                {
                    $jSession->set('jfbcCanvasInCanvas', true);
                    $jSession->set('jfbcCanvasInTab', false);
                    $jSession->clear('jfbcCanvasPageInfo');
                }

                return true; // We are definitely inside FB
            }
        }
        return $jSession->get('jfbcCanvasEnabled', false); // Return session state for if in canvas or not
    }

    private function setupPageTab()
    {
        $app =JFactory::getApplication();
        $jSession =JFactory::getSession();

        // First, check if reveal page is setup
        $pageInfo = $jSession->get('jfbcCanvasPageInfo');
        $revealPage = $this->configModel->getSetting('canvas_tab_reveal_article_id', '');
        if ($revealPage && !$pageInfo['liked'] && ((JRequest::getCmd('option') != 'com_content') || (JRequest::getCmd('view') != 'article') ||
                (JRequest::getInt('id') != $revealPage))
        )
        {
            $uri = JURI::getInstance();
            $uri->delVar('jfbcCanvasBreakout');
            $jSession->set('jfbcCanvasOrigDestination', $uri->toString());
            $app->redirect('index.php?option=com_content&view=article&tmpl=component&id=' . $revealPage);
        }
        else
        {
            $tabTemplate = $this->configModel->getSetting('canvas_tab_template');
            $this->setTemplate($tabTemplate);
        }
    }

    private function setupAppCanvas()
    {
        $jSession =JFactory::getSession();
        $jSession->clear('jfbcCanvasOrigDestination');

        // Check if this was a Notification/Request, and redirect the user appropriately
        // If so, a redirect WILL occur here!
        $jfbcRequestLibrary = JFBConnectRequestLibrary::getInstance();
        $jfbcRequestLibrary->checkForNotification();

        // If staying on this page, set it up with the right template
        $canvasTemplate = $this->configModel->getSetting('canvas_canvas_template');
        $this->setTemplate($canvasTemplate);
    }

    private function setupResizing()
    {
        $doc =JFactory::getDocument();
        if ($doc->getType() == 'html')
        {
            // Note: Frame check occurs in system plugin
            // Add CSS styles to make sure we're the proper width for the canvas area
            if ($this->get('inCanvas') && $this->configModel->getSetting('canvas_canvas_resize_enabled', 0))
            {
                $this->set('resizeEnabled', true);
                // Removing the forced width as Facebook now has a new "Fluid" width setting that we recommend using.
                //$doc->addStyleDeclaration("body {width:760px !important; margin:0 !important; padding: 0 !important}");
            }
            else if ($this->get('inTab') && $this->configModel->getSetting('canvas_tab_resize_enabled', 0))
            {
                $this->set('resizeEnabled', true);
                $doc->addStyleDeclaration("body {width:520px !important; margin:0 !important; padding: 0 !important}");
            }
        }
    }

    // Overrides the current template. In 1.5, just by template name
    // In 1.6+, sets the current template by name and with configured styles
    function setTemplate($styleId)
    {
        // Check for No Template Override setting
        if ($styleId == -1)
            return;

        $app =JFactory::getApplication();
        
            $app->setTemplate($styleId);
         // SC15
         // SC16
    }

}