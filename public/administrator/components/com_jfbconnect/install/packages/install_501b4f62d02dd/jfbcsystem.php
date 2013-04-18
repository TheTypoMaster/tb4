<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');
jimport('sourcecoast.openGraph');
jimport('sourcecoast.utilities');
jimport('sourcecoast.easyTags');

class plgSystemJFBCSystem extends JPlugin
{
    var $configModel;
    var $jfbcLibrary;
    var $jfbcCanvas;

    var $tagsToReplace = array(
        'jfbclogin' => 'getJFBCLogin',
        'jfbclike' => 'getJFBCLike',
        'jfbcsend' => 'getJFBCSend',
        'jfbccomments' => 'getJFBCComments',
        'jfbccommentscount' => 'getJFBCCommentsCount',
        'jfbcfan' => 'getJFBCFan',
        'jfbcfeed' => 'getJFBCFeed',
        'jfbcfriends' => 'getJFBCFriends',
        'jfbclivestream' => 'getJFBCLiveStream',
        'jfbcrecommendations' => 'getJFBCRecommendations',
        'jfbcrequest' => 'getJFBCRequest',
        'jfbcsubscribe' => 'getJFBCSubscribe',
        "sctwittershare" => 'getSCTwitterShare',
        "scgoogleplusone" => 'getSCGPlusOne',
        "jlinkedshare" => 'getJLinkedShare'
    );

    var $metadataTagsToStrip = array('JFBC', 'JLinked', 'SCTwitterShare', 'SCGooglePlusOne');

    static $cssIncluded = false;

    function __construct(& $subject, $config)
    {
        jimport('joomla.filesystem.file');
        $libFile = JPATH_ROOT . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php';
        if (!JFile::exists($libFile))
            JError::raiseError(0, "File missing: " . $libFile . "<br/>Please re-install JFBConnect or disable the JFBCSystem Plugin");
        require_once($libFile);
        $this->jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $this->configModel = $this->jfbcLibrary->getConfigModel();

        $canvasFile = JPATH_ROOT . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'canvas.php';
        if (!JFile::exists($canvasFile))
            JError::raiseError(0, "File missing: " . $canvasFile . "<br/>Please re-install JFBConnect or disable the JFBCSystem Plugin");
        require_once($canvasFile);
        $this->jfbcCanvas = JFBConnectCanvasLibrary::getInstance();

        parent::__construct($subject, $config);
    }

    function onAfterInitialise()
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin())
        {
            $this->jfbcCanvas->setupCanvas();

            // Need to disable Page caching so that values fetched from Facebook are not saved for the next user!
            // Do this by setting the request type to POST. In the Cache plugin, it's checked for "GET". can't be that.
            $option = JRequest::getCmd("option");
            $view = JRequest::getCmd("view");
            if ($option == 'com_jfbconnect' && $view == 'loginregister')
                $_SERVER['REQUEST_METHOD'] = 'POST';
        }
    }

    function onAfterDispatch()
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin())
        {
            $this->jfbcLibrary->initDocument();

            foreach ($this->metadataTagsToStrip as $metadataTag)
            {
                $this->_replaceTagInMetadata($metadataTag);
            }

            $doc = JFactory::getDocument();
            if ($doc->getType() == 'html')
            {
                $doc->addCustomTag('<JFBConnectSCTwitterJSPlaceholder />');
                $doc->addCustomTag('<JFBConnectSCGooglePlusOneJSPlaceholder />');
                $doc->addCustomTag('<JLinkedJfbcJSPlaceholder />');
            }

            //Add Login with FB button to com_users login view and mod_login
            if ($this->configModel->getSetting('show_login_with_joomla_reg'))
            {
                $renderKey = SCSocialUtilities::getJFBConnectRenderKey();
                $loginTag = '{JFBCLogin logout=false' . $renderKey . '}';
                SCEasyTags::extendJoomlaUserForms($loginTag);
            }
        }
    }

    function onAfterRender()
    {
        $app = JFactory::getApplication();
        if (!$app->isAdmin())
        {
            $openGraphEnabled = $this->configModel->getSetting('social_graph_enabled');
            if ($openGraphEnabled)
                $openGraphNamespace = 'xmlns:og="http://ogp.me/ns#" ';
            else
                $openGraphNamespace = '';


            $body = JResponse::getBody();
            $body = str_replace("<html ", '<html xmlns:fb="http://ogp.me/ns/fb#" ' . $openGraphNamespace, $body);

            $fbApiJs = $this->_getJavascript($this->jfbcLibrary->facebookAppId);

            if (preg_match('/\<body[\s\S]*?\>/i', $body, $matches))
            {
                $newBody = str_replace($matches[0], $matches[0] . $fbApiJs, $body);
                JResponse::setBody($newBody);
            }

            $this->_doTagReplacements();
        }
        return true;
    }

    private function _replaceTagInMetadata($metadataTag)
    {
        $doc = JFactory::getDocument();
        $description = $doc->getDescription();
        $replace = SCSocialUtilities::stripSystemTags($description, $metadataTag);

        if ($replace)
        {
            $description = SCStringUtilities::trimNBSP($description);
            $doc->setDescription($description);
        }
    }

    private function _getLocale()
    {
        $fbLocale = $this->jfbcLibrary->getFacebookOverrideLocale();

        // Get the language to use
        if ($fbLocale == '')
        {
            $lang = JFactory::getLanguage();
            $locale = $lang->getTag();
        }
        else
        {
            $locale = $fbLocale;
        }

        $locale = str_replace("-", "_", $locale);
        return $locale;
    }

    private function _getJavascript($appId)
    {
        $locale = $this->_getLocale();
        // get Event Notification subscriptions
        $subs = "\nFB.Event.subscribe('comment.create', jfbc.social.comment.create);";
        $subs .= "\nFB.Event.subscribe('edge.create', jfbc.social.like.create);";
        if ($this->configModel->getSetting('social_notification_google_analytics'))
            $subs .= "\njfbc.social.googleAnalytics.trackFacebook();";

        $fbsiteurl = JURI::root();
        $channelurl = $fbsiteurl . 'components/com_jfbconnect/assets/jfbcchannel.php';
        if ($this->jfbcCanvas->get('resizeEnabled', false))
            $resizeCode = "window.setTimeout(function() {\n" .
                    "  FB.Canvas.setAutoGrow();\n" .
                    "}, 250);";
        else
            $resizeCode = "";

        if ($this->jfbcCanvas->get('canvasEnabled', false))
            $canvasCode = "jfbc.canvas.checkFrame();";
        else
            $canvasCode = "";

        // Figure out if status:true should be set. When false, makes page load faster
        $user = JFactory::getUser();
        $guest = $user->guest;
        // Check cookie to make sure autologin hasn't already occurred once. If so, and we try again, there will be loops.
        $autoLoginPerformed = JRequest::getInt('jfbconnect_autologin_disable', 0, 'COOKIE');
        if ($this->configModel->getSetting('facebook_auto_login') && $guest && !$autoLoginPerformed)
        {
            $status = 'status: true,';
            // get Event Notification subscriptions
            $subs = "\nFB.Event.subscribe('auth.authResponseChange', function(response) {jfbc.login.on_login();});";
        }
        else
            $status = 'status: false,';

        // Should the modal popup be displayed?
        $showLoginModal = $this->configModel->getSetting('facebook_login_show_modal');
        if ($showLoginModal)
        {
            $lang = JFactory::getLanguage();
            $lang->load('com_jfbconnect');
            $loginModalDiv = '<div style="display:none;position:absolute"><div id="jfbcLoginModal" style="width:500px;text-align:center;position:absolute;height:50px;top:50%;margin-top:-13px;">' . JText::_('COM_JFBCONNECT_LOGIN_POPUP') . '</div></div>';
        }
        else
            $loginModalDiv = "";

        if ($appId)
            $appIdCode = "appId: '" . $appId . "', ";
        else
            $appIdCode = "";
        $javascript =
                <<<EOT
<div id="fb-root"></div>
<script type="text/javascript">
{$canvasCode}\n
window.fbAsyncInit = function() {
FB.init({{$appIdCode}{$status} cookie: true, xfbml: true, oauth: true, channelUrl: '{$channelurl}'});{$subs}{$resizeCode}
};
(function(d){
     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/{$locale}/all.js";
     d.getElementsByTagName('head')[0].appendChild(js);
   }(document));
</script>
{$loginModalDiv}
EOT;

        return $javascript;
    }

    private function _doTagReplacements()
    {
        $twitterTagFound = false;
        $googlePlusTagFound = false;
        $jLinkedTagFound = false;

        $tagsFound = false;
        $tagKeys = array_keys($this->tagsToReplace);
        foreach ($tagKeys as $tag)
        {
            $lowercaseTag = strtolower($tag);

            //Tag has passed in values
            $regex = '/\{' . $tag . '\s+(.*?)\}/i';
            $currentTag1Found = $this->_replaceTag($lowercaseTag, $regex);
            $tagsFound = $currentTag1Found || $tagsFound;

            //Tag with no values
            $regex = '/\{' . $tag . '}/i';
            $currentTag2Found = $this->_replaceTag($lowercaseTag, $regex);
            $tagsFound = $currentTag2Found || $tagsFound;

            if ($currentTag1Found || $currentTag2Found)
            {
                if ($lowercaseTag == 'sctwittershare')
                    $twitterTagFound = true;
                else if ($lowercaseTag == 'scgoogleplusone')
                    $googlePlusTagFound = true;
                else if ($lowercaseTag == 'jlinkedshare')
                    $jLinkedTagFound = true;
            }
        }
        $this->_replaceGraphTags();
        $this->_replaceJSPlaceholders($twitterTagFound, $googlePlusTagFound, $jLinkedTagFound);
    }

    private function _replaceJSPlaceholders($twitterTagFound, $googlePlusTagFound, $jLinkedTagFound)
    {
        $uri = JURI::getInstance();
        $scheme = $uri->getScheme();

        //Twitter
        $twitterPlaceholder = '<JFBConnectSCTwitterJSPlaceholder />';
        if ($twitterTagFound)
            $twitterJavascript = '<script src="' . $scheme . '://platform.twitter.com/widgets.js"></script>';
        else
            $twitterJavascript = '';

        //GooglePlus
        $googlePlaceholder = '<JFBConnectSCGooglePlusOneJSPlaceholder />';
        if ($googlePlusTagFound)
        {
            $googleJavascript = "<script type=\"text/javascript\">
              (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = '" . $scheme . "://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
              })();
            </script>";
        }
        else
            $googleJavascript = '';

        //JLinked
        $jLinkedPlaceholder = '<JLinkedJfbcJSPlaceholder />';
        $jLinkedEnabled = SCSocialUtilities::isJLinkedInstalled() && SCSocialUtilities::areJLinkedTagsEnabled();
        if ($jLinkedTagFound && !$jLinkedEnabled)
        {
            $jLinkedJavascript = '<script src="' . $scheme . '://platform.linkedin.com/in.js"></script>';
            if ($scheme == 'https')
            {
                $jLinkedJavascript .= '<script type="text/javascript">' .
                        "IN.Event.on(IN,'frameworkLoaded',function(){if(/^https:\/\//i.test(location.href)){IN.ENV.images.sprite='https://www.linkedin.com/scds/common/u/img/sprite/'+IN.ENV.images.sprite.split('/').pop()}});
                    </script>";
            }
        }
        else
            $jLinkedJavascript = '';

        //Replace placeholder with Javascript if needed
        $contents = JResponse::getBody();
        $contents = str_replace($twitterPlaceholder, $twitterJavascript, $contents);
        $contents = str_replace($googlePlaceholder, $googleJavascript, $contents);
        $contents = str_replace($jLinkedPlaceholder, $jLinkedJavascript, $contents);
        JResponse::setBody($contents);
    }

    private function _replaceTag($method, $regex)
    {
        $replace = FALSE;
        $contents = JResponse::getBody();
        if (preg_match_all($regex, $contents, $matches, PREG_SET_ORDER))
        {
            $count = count($matches[0]);
            if ($count == 0)
                return true;

            $jfbcRenderKey = SCSocialUtilities::getJFBConnectRenderKeySetting();

            foreach ($matches as $match)
            {
                if (isset($match[1]))
                    $val = $match[1];
                else
                    $val = '';

                $cannotRender = SCEasyTags::cannotRenderEasyTag($val, $jfbcRenderKey);
                if ($cannotRender)
                    continue;

                if (array_key_exists($method, $this->tagsToReplace))
                {
                    $methodName = $this->tagsToReplace[$method];
                    $newText = call_user_func(array('SCEasyTags', $methodName), $val);
                    $replace = TRUE;

                    if (!self::$cssIncluded)
                    {
                        self::$cssIncluded = true;
                        $newText = '<link rel="stylesheet" href="' . JURI::base() . 'components/com_jfbconnect/assets/jfbconnect.css" type="text/css" />' . $newText;
                    }
                }
                else
                {
                    $newText = '';
                    $replace = FALSE;
                }

                $search = '/'.preg_quote($match[0], '/').'/';
                $contents = preg_replace($search, $newText, $contents, 1);
            }
            if ($replace)
                JResponse::setBody($contents);
        }

        return $replace;
    }

    private function getGraphContents($regex, &$contents, &$newGraphTags)
    {
        if (preg_match_all($regex, $contents, $matches, PREG_SET_ORDER))
        {
            $count = count($matches[0]);
            if ($count == 0)
                return true;

            $jfbcRenderKey = SCSocialUtilities::getJFBConnectRenderKeySetting();
            $jLinkedRenderKey = SCSocialUtilities::getJLinkedRenderKeySetting();

            foreach ($matches as $match)
            {
                if (isset($match[1]))
                    $val = $match[1];
                else
                    $val = '';

                $cannotRenderJFBC = SCEasyTags::cannotRenderEasyTag($val, $jfbcRenderKey);
                $cannotRenderJLinked = SCEasyTags::cannotRenderEasyTag($val, $jLinkedRenderKey);

                if ($cannotRenderJFBC && $cannotRenderJLinked)
                    continue;

                $newGraphTags[] = $val;
                $contents = str_replace($match[0], '', $contents);
            }
        }
    }

    private function _replaceGraphTags()
    {
        // TODO - deprecate JFBCGraph tags
        $placeholder1 = '<JFBCGraphPlaceholder />';
        $placeholder2 = '<SCOpenGraphPlaceholder />';
        $regex1 = '/\{JFBCGraph\s+(.*?)\}/i';
        $regex2 = '/\{SCOpenGraph\s+(.*?)\}/i';

        $newGraphTags1 = array();
        $newGraphTags2 = array();

        $contents = JResponse::getBody();
        $this->getGraphContents($regex1, $contents, $newGraphTags1);
        $this->getGraphContents($regex2, $contents, $newGraphTags2);

        $newGraphTags = array_merge($newGraphTags1, $newGraphTags2);

        //Replace Placeholder with new Head tags
        $isOpenGraphEnabled = $this->configModel->getSetting('social_graph_enabled');
        $defaultGraphFields = $this->configModel->getSetting('social_graph_fields');
        $locale = $this->_getLocale();

        $openGraphLib = new OpenGraphTags($isOpenGraphEnabled, $defaultGraphFields, SOURCECOAST_JFBCONNECT, $this->jfbcLibrary->facebookAppId, $locale);
        $graphTags = $openGraphLib->getOpenGraphTags($newGraphTags);
        $contents = str_replace($placeholder1, $graphTags, $contents);
        $contents = str_replace($placeholder2, '', $contents);
        JResponse::setBody($contents);
    }
}