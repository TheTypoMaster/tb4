<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.utilities');

include_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models' . DS . 'usermap.php');

class JFBConnectFacebookLibrary extends JObject
{

    // Override model's getInstance to really only get the instance
    var $facebookAppId;
    var $facebookSecretKey;
    protected $configModel;
    protected $fbUserId;
    protected $mappedFbUserId;
    private static $libraryInstance;

    public static function getInstance()
    {
        if (!isset(self::$libraryInstance))
        {
            self::$libraryInstance = new JFBConnectFacebookLibrary();
            // After the instance is grabbed, get the Javascript code to insert
            // Don't do this in the constructor as inititing the Javascript calls functions which need
            //   this very instance
            $app = JFactory::getApplication();
            if (!$app->isAdmin())
            {
                // Set whether, on this page load, the user should be checked for a new mapping (i.e. they were just on the login/register page)
                $session = JFactory::getSession();
                $checkNewMapping = $session->get('jfbcCheckNewMapping', false);
                self::$libraryInstance->checkNewMapping = $checkNewMapping;
            }
        }

        return self::$libraryInstance;
    }

    function __construct()
    {
        $this->getConfigModel();
        $this->facebookAppId = $this->configModel->getSetting('facebook_app_id');
        $this->facebookSecretKey = $this->configModel->getSetting('facebook_secret_key');
    }

    function getConfigModel()
    {
        if (!$this->configModel)
        {
            require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models' . DS . 'config.php');
            $this->configModel = new JFBConnectModelConfig();
        }
        return $this->configModel;
    }

    var $initialRegistration = false;

    function setInitialRegistration()
    {
        $this->initialRegistration = true;
    }

    /**
     * Perform initialization of JFBConnect variables into the document. Currently adds:
     * ** The (dynamic) login/logout redirects, used by jfbconnect.js
     * ** The {jfbcgraphplaceholder} tag to be replaced/removed by the system plugin (DEPRECATED 2/28)
     * ** The {scopengraphplaceholder} tag to be replaced/removed by the system plugin
     * @return none
     */
    function initDocument()
    {
        //$fbClient = $this->getFbClient();
        $doc = JFactory::getDocument();
        if ($doc->getType() != 'html')
            return; // Only insert javascript on HTML pages, not AJAX, RSS, etc

        $return = '';
        $menuItemId = 0;
        SCSocialUtilities::getCurrentReturnParameter($return, $menuItemId, LOGIN_TASK_JFBCONNECT);

        $requiredPerms = $this->configModel->getRequiredPermissions();

        $fbUserId = $this->getMappedFbUserId();
        $logoutJoomlaOnly = $this->configModel->getSetting('logout_joomla_only');

        if ($fbUserId && !$logoutJoomlaOnly)
            $logoutFacebookJavascript = "jfbc.login.logout_facebook = true;";
        else
            $logoutFacebookJavascript = "jfbc.login.logout_facebook = false;";

        $user = JFactory::getUser();
        $showLoginModal = $this->configModel->getSetting('facebook_login_show_modal');
        if ($showLoginModal && $user->guest)
            JHTML::_('behavior.modal');

        $doc->addScript(JURI::base(true) . '/components/com_jfbconnect/includes/jfbconnect.js?v=424'); //4.2.4
        $doc->addCustomTag('<script type="text/javascript">' .
                $logoutFacebookJavascript . "\n" .
                "jfbc.base = '" . JURI::base() . "';\n" .
                "jfbc.return_url = '" . base64_encode($return) . "';\n" .
                "jfbc.login.scope = '" . $requiredPerms . "';\n" .
                "jfbc.login.show_modal = '" . $showLoginModal . "';\n" .
                "</script>");

        $doc->addCustomTag('<JFBCGraphPlaceholder />');
        $doc->addCustomTag('<SCOpenGraphPlaceholder />');
    }

    function getFbClient()
    {
        static $_facebook;
        if (!isset($_facebook))
        {
            include_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jfbconnect' . DS . 'assets' . DS . 'facebook-api' . DS . 'facebook.php');
            $_facebook = new JFBCFacebook(array(
                'appId' => $this->facebookAppId,
                'secret' => $this->facebookSecretKey,
                'cookie' => true,
            ));
        }

        return $_facebook;
    }

    function validateToken()
    {
        $fbUserId = $this->getFbUserId();
        $fbUser = $this->_getUserName($fbUserId);
        if ($fbUser == null)
        {
            $session = JFactory::getSession();
            $validateCount = $session->get('jfbcTokenRequestCount', 0);
            if ($validateCount >= 1)
                return false;

            $session->set('jfbcTokenRequestCount', 1);

            $redirect = '';
            $menuItemId = 0;
            SCSocialUtilities::getCurrentReturnParameter($redirect, $menuItemId, LOGIN_TASK_JFBCONNECT);
            $returnParam = '&return=' . base64_encode($redirect);

            $uri = JURI::getInstance();
            $loginLink = $uri->toString(array('scheme', 'host')) . JRoute::_('index.php?option=com_jfbconnect&task=loginFacebookUser' . $returnParam, false);
            $requiredPerms = $this->configModel->getRequiredPermissions();
            $fbClient = $this->getFbClient();
            $url = $fbClient->getLoginUrl(
                array(
                    'scope' => $requiredPerms,
                    'redirect_uri' => $loginLink,
                    'cancel_uri' => $loginLink
                )
            );
            $app = JFactory::getApplication();
            $app->redirect($url);
        }
        return true;
    }

    function _getUserName($fbUserId)
    {
        $fields = array(0 => 'first_name', 1 => 'last_name', 2 => 'status', 3 => 'email', 4 => 'name');
        return $this->getUserProfile($fbUserId, $fields);
    }

    function getUserProfile($fbUserId, $fields)
    {
        $colFields = implode(",", $fields);
        $fql = "SELECT " . $colFields . " FROM user WHERE uid=" . $fbUserId;
        $params = array(
            'method' => 'fql.query',
            'query' => $fql,
        );
        $profile = $this->rest($params, TRUE);
        return $profile[0];
    }

    function getUserData($fbUserId, $path)
    {
        $userProfile = array();
        try
        {
            $userProfile[] = $this->api($path);
        } catch (JFBCFacebookApiException $e)
        {
            $userProfile[0] = null;
        }

        if (!isset($userProfile[0]))
            $userProfile[0] = null;

        return $userProfile[0];
    }

    function api($api, $params = null, $callAsUser = true, $method = null, $suppressErrors = false)
    {
        $fbClient = $this->getFbClient();
        if (!$method)
        {
            if ($params)
                $method = "POST";
            else
                $method = "GET";
        }

        if (!$callAsUser)
            $params['access_token'] = $this->facebookAppId . "|" . $this->facebookSecretKey;
        try
        {
            if ($params != null) // Graph API call with paramters (either App call or POST call)
                $apiData = $fbClient->api($api, $method, $params);
            else // Graph API call to only get data
                $apiData = $fbClient->api($api);
        } catch (JFBCFacebookApiException $e)
        {
            // Only display errors on the front-end if the config is set to do so
            $app = JFactory::getApplication();
            if (!$suppressErrors && ($app->isAdmin() || $this->configModel->getSetting('facebook_display_errors')))
            {
                $app->enqueueMessage("Facebook API Error: " . $e->getMessage(), 'error');
            }
            $apiData = null;
        }

        return $apiData;
    }

    function rest($params, $callAsUser = true)
    {
        $fbClient = $this->getFbClient();
        if (!$callAsUser)
            $params['access_token'] = $this->facebookAppId . "|" . $this->facebookSecretKey;

        try
        {
            $result = $fbClient->api($params);
        } catch (JFBCFacebookApiException $e)
        {
            // Only display errors on the front-end if the config is set to do so
            $app = JFactory::getApplication();
            if ($app->isAdmin() || $this->configModel->getSetting('facebook_display_errors'))
            {
                $app->enqueueMessage("Facebook API Error: " . $e->getMessage(), 'error');
            }
            $result = null;
        }

        // This should be decoded by the Facebook api, but for some reason, it returns not perfect
        // JSON encoding (difference between admin.getAppProperties and a FQL query
        // So, check if we're just getting a string and try a 2nd JSON decode, which seems to work.
        // .. ugh.
        if (is_string($result))
            $result = json_decode($result, true);

        return $result;
    }

    function getLoginButton($buttonSize = "medium")
    {
        $perms = $this->configModel->getRequiredPermissions();
        if ($perms != "")
            $perms = 'scope="' . $perms . '"'; // OAuth2 calls them 'scope'

        $lang = JFactory::getLanguage();
        $lang->load('com_jfbconnect');
        return '<fb:login-button size="' . $buttonSize . '" ' . $perms . ' onlogin="javascript:jfbc.login.on_login();">' . JText::_('COM_JFBCONNECT_LOGIN_USING_FACEBOOK') . '</fb:login-button>';
    }

    function getLogoutButton()
    {
        $lang = JFactory::getLanguage();
        $lang->load('com_jfbconnect');
        $logoutStr = JText::_('COM_JFBCONNECT_LOGOUT');

        return '<input type="submit" name="Submit" id="jfbcLogoutButton" class="button" value="' . $logoutStr . '" onclick="javascript:jfbc.login.logout_button_click()" />';
    }

    // Deprecated. Should call getFbUserId or getMappedFbUserId
    function getUserId($validateWithJoomla = TRUE)
    {
        if ($validateWithJoomla)
            return $this->getMappedFbUserId();
        else
            return $this->getFbUserId();
    }

    /* getFbUserId
    * Gets the FB user id of user logged into Facebook. This is regardless of whether they are mapped to an
    *  existing Joomla account.
    * Use getMappedFbUserId if you want the FB ID for a user who is mapped.
    */
    function getFbUserId()
    {
        if ($this->get('fbUserId', null) == null)
        {
            $fbClient = $this->getFbClient();
            $fbId = $fbClient->getUser();
            if ($fbId != 0 && $fbId != null)
                $this->set('fbUserId', $fbId);
            else
                $this->set('fbUserId', null);
        }
        return $this->get('fbUserId');

    }

    /* getMappedFbUserId
    * Gets the FB user id of user logged into Facebook if they have a usermapping to a Joomla user
    * Returns null if user is not mapped (or not logged into Facebook).
    */
    function getMappedFbUserId()
    {
        $mappedFbUserId = $this->getFbUserId();
        $userMapModel = new JFBConnectModelUserMap();
        $jUser = JFactory::getUser();

        if ($userMapModel->getJoomlaUserId($mappedFbUserId) != $jUser->get('id') || $jUser->guest)
            $mappedFbUserId = null;

        $this->set('mappedFbUserId', $mappedFbUserId);
        return $this->get('mappedFbUserId');
    }

    //TODO: Get rid of this function - Only used in JFBCLogin and replaced by getMappedFbUserId above
    function getFacebookMappedId()
    {
        $userMapModel = new JFBConnectModelUserMap();

        jimport('joomla.user.helper');
        $jUser = JFactory::getUser();

        return $userMapModel->getFacebookUserId($jUser->id);
    }

    function setFacebookMessage($message)
    {
        if ($message)
        {
            try
            {
                $currentMessage = '';

                $response = $this->api('/me/feed');
                if (isset($response['data'][0]))
                    $currentMessage = $response['data'][0]['message'];

                if ($currentMessage != $message['message'])
                {
                    if (is_array($message))
                        $response = $this->api('/me/feed', $message, false);
                    else
                        $response = $this->api('/me/feed', array('message' => $message), false);
                }
            } catch (JFBCFacebookApiException $e)
            {
                /*
                 Fatal error: Uncaught exception 'FacebookRestClientException' with message
                 'Updating status requires the extended permission status_update' in
                 .../com_jfbconnect/assets/facebook-api/facebookapi_php5_restlib.php:3007
                */
            }
        }
    }

	function setFacebookMessageWall($message, $jUserId = null)
    {
		
        $response = false;
        if($message){

            try{

				if($jUserId){
                    
					$userMapModel = new JFBConnectModelUserMap();
                    $userInfo = $userMapModel->getData($jUserId);
                    $fbUserId = $userInfo->fb_user_id;
                    if(!$fbUserId)
                       //return $response;
                    $message['access_token'] = $userInfo->access_token;
					
					if (is_array($message)) 
							$response = $this->api('/'.$fbUserId.'/feed', $message, false);
						else
							$response = $this->api('/'.$fbUserId.'/feed', array('message' => $message), false);

				}else

					$fbUserId = 'me';



					$currentMessage = '';
                    $response = $this->api('/' . $fbUserId . '/feed');

                    if (isset($response['data'][0]))

						$currentMessage = $response['data'][0]['message'];



					if ($currentMessage != $message['message'])
                    {
                        
						if (is_array($message))

							$response = $this->api('/' . $fbUserId . '/feed', $message);

						else

							$response = $this->api('/' . $fbUserId . '/feed', array('message' => $message));
                            
					 }

            }catch(JFBCFacebookApiException $e)
            {

                /*

                 Fatal error: Uncaught exception 'FacebookRestClientException' with message

                 'Updating status requires the extended permission status_update' in

                 .../com_jfbconnect/assets/facebook-api/facebookapi_php5_restlib.php:3007

                */

            }

        }
       return $response; 
    }

    function setFacebookNewUserMessage()
    {
        $message = $this->configModel->getSetting('facebook_new_user_status_msg');
        $link = $this->configModel->getSetting('facebook_new_user_status_link');
        $picture = $this->configModel->getSetting('facebook_new_user_status_picture');

        if ($message == "")
            return;

        $post = array();
        $post['message'] = $message;
        if ($link != '')
            $post['link'] = $link;
        if ($picture != '')
            $post['picture'] = $picture;

        $this->setFacebookMessage($post);
    }

    function setFacebookLoginMessage()
    {
        $message = $this->configModel->getSetting('facebook_login_status_msg');
        $link = $this->configModel->getSetting('facebook_login_status_link');
        $picture = $this->configModel->getSetting('facebook_login_status_picture');

        if ($message == "")
            return;

        $post = array();
        $post['message'] = $message;
        if ($link != '')
            $post['link'] = $link;
        if ($picture != '')
            $post['picture'] = $picture;

        $this->setFacebookMessage($post);
    }

    function getFacebookOverrideLocale()
    {
        return $this->configModel->getSetting('facebook_language_locale');
    }

    function getSocialTagRenderKey()
    {
        return $this->configModel->getSetting('social_tag_admin_key');
    }

}
