<?php
/**
 * @package        JFBConnect/JLinked
 * @copyright (C) 2011-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

class modSCLoginHelper
{
    var $isJLinkedInstalled = false;
    var $isJFBConnectInstalled = false;
    var $jlinkedLibrary = null;
    var $jfbcLibrary = null;
    var $params;

    function __construct($params)
    {
        $this->params = $params;
        //Check to see if JLinked is installed
        if (class_exists("JLinkedApiLibrary"))
        {
            $this->isJLinkedInstalled = true;
            $this->jlinkedLibrary = JLinkedApiLibrary::getInstance();
            $renderKey = $this->jlinkedLibrary->getSocialTagRenderKey();
            $this->jlinkedRenderKey = $renderKey != "" ? " key=" . $renderKey : "";
        }

        //Check to see if JFBConnect is installed
        if (class_exists("JFBConnectFacebookLibrary"))
        {
            $this->isJFBConnectInstalled = true;
            $this->jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        }
    }

    function getPoweredByLink()
    {
        $showPoweredBy = $this->params->get('showPoweredByLink');
        if ($showPoweredBy == 0)
            return;

        if ($this->isJLinkedInstalled)
        {
            $jlConfigModel = $this->jlinkedLibrary->getConfigModel();
            $jlinkedAffiliateID = $jlConfigModel->getSetting('affiliate_id');
            $showJLinkedPoweredBy = (($showPoweredBy == '2' && $jlConfigModel->getSetting('show_powered_by_link')) || ($showPoweredBy == '1'));
        }
        else
            $showJLinkedPoweredBy = false;

        if ($this->isJFBConnectInstalled)
        {
            $jfbcConfigModel = $this->jfbcLibrary->getConfigModel();
            $jfbcAffiliateID = $jfbcConfigModel->getSetting('affiliate_id');
            $showJFBCPoweredBy = (($showPoweredBy == '2' && $jfbcConfigModel->getSetting('show_powered_by_link')) || ($showPoweredBy == '1'));
        }
        else
            $showJFBCPoweredBy = false;


        if ($showJFBCPoweredBy && $showJLinkedPoweredBy)
        {
            $link = 'http://www.sourcecoast.com/';
            $title = 'Facebook and LinkedIn for Joomla';
            $poweredByLabel = 'SourceCoast';
            if ($jfbcAffiliateID)
                $affiliate_id = $jfbcAffiliateID;
            else
                $affiliate_id = $jlinkedAffiliateID;
        }
        else if ($showJFBCPoweredBy)
        {
            $link = 'http://www.sourcecoast.com/jfbconnect/';
            $title = 'Facebook for Joomla';
            $poweredByLabel = 'JFBConnect';
            $affiliate_id = $jfbcAffiliateID;
        }
        else if ($showJLinkedPoweredBy)
        {
            $link = 'http://www.sourcecoast.com/jlinked/';
            $title = 'LinkedIn for Joomla';
            $poweredByLabel = 'JLinked';
            $affiliate_id = $jlinkedAffiliateID;
        }

        if (isset($link))
        {
            if ($affiliate_id)
                $link .= '?amigosid=' . $affiliate_id;

            return '<div class="powered_by">'.JText::_('MOD_SCLOGIN_POWERED_BY').' <a target="_blank" href="' . $link . '" title="' . $title . '">' . $poweredByLabel . '</a></div>';
        }
        return "";
    }

    function getType()
    {
        $user = JFactory::getUser();
        return (!$user->get('guest')) ? 'logout' : 'login';
    }

    function getLoginRedirect($loginType)
    {
        if (JRequest::getString('return'))
            return JRequest::getString('return');

        $url = '';
        if (($loginType == 'jlogin' && $this->params->get('jlogin_redirect')))
        {
            $itemId = $this->params->get($loginType);
            if ($itemId)
            {
                $app = JFactory::getApplication();
                $router = $app->getRouter();

                $db = JFactory::getDbo();
                //Derived from mod_login Joomla 1.6, but modified to be backwards compatible
                $query = "SELECT link FROM #__menu WHERE published=1 AND id=" . $db->quote($itemId);
                $db->setQuery($query);
                if ($link = $db->loadResult())
                {
                    // Use this default for Joomla 1.5 or Non-SEF Joomla 2.5
                    $url = $link . '&Itemid=' . $itemId;

                     //SC16
                }
            }
        }

        if (!$url)
        {
            $uri = JURI::getInstance();
            $url = $uri->toString(array('path', 'query'));
        }

        return base64_encode($url);
    }

    function getAvatarDimensions(&$width, &$height)
    {
        $picHeightParam = $this->params->get("profileHeight");
        $picWidthParam = $this->params->get("profileWidth");
        $height = $picHeightParam != "" ? 'height="' . $picHeightParam . 'px"' : "";
        $width = $picWidthParam != "" ? 'width="' . $picWidthParam . 'px"' : "";
    }

    function getSocialAvatarImage($avatarURL, $profileURL)
    {
        $html = '';
        if($avatarURL)
        {
            $picWidth = "";
            $picHeight = "";
            $this->getAvatarDimensions($picWidth, $picHeight);

            $html = '<img src="' . $avatarURL . '" ' . $picWidth . " " . $picHeight . ' />';

            $linked = ($this->params->get("linkProfile") == 1);
            if ($linked && $profileURL != '')
                $html = '<a target="_BLANK" href="' . $profileURL . '">' . $html . '</a>';
        }
        return $html;
    }

    function showSecurely()
    {
        $uri = JURI::getInstance();
        $scheme = $uri->getScheme();
        return $scheme == 'https';
    }

    function getFacebookAvatar()
    {
        $html = "";
        if ($this->isJFBConnectInstalled)
        {
            $fbUserId = $this->jfbcLibrary->getMappedFbUserId();

            # Show their FB avatar (if desired), or give them the option to link accounts
            if ($fbUserId)
            {
                $fbProfileURL = 'https://www.facebook.com/profile.php?id='.$fbUserId;
                $fbAvatarSize = $this->getJFBConnectAvatarSize();
                $fbAvatarURL = 'https://graph.facebook.com/'.$fbUserId.'/picture?type='.$fbAvatarSize;

                if($this->showSecurely())
                    $fbAvatarURL .= '&return_ssl_resources=1';

                $html = $this->getSocialAvatarImage($fbAvatarURL, $fbProfileURL);
            }
        }
        return $html;
    }

    function getLinkedInAvatar()
    {
        $html = "";
        if ($this->isJLinkedInstalled && $this->jlinkedLibrary->getMappedLinkedInUserId())
        {
            $app = JFactory::getApplication();

            $liAvatarUrl = $app->getUserState('modScLoginLiAvatar', null);
            $liProfileURL = $app->getUserState('modScLoginLiProfileURL', null);

            if ($liAvatarUrl == null || $liProfileURL == null)
            {
                $data = $this->jlinkedLibrary->api('profile', '~:(picture-url,public-profile-url)');
                //Avatar URL
                $liAvatarUrl = $data->get('picture-url');
                $app->setUserState('modScLoginLiAvatar', $liAvatarUrl);
                //Profile URL
                $liProfileURL = $data->get('public-profile-url');
                $app->setUserState('modScLoginLiProfileURL', $liProfileURL);
            }

            $html = $this->getSocialAvatarImage($liAvatarUrl, $liProfileURL);
        }
        return $html;
    }

    function getJoomlaAvatar($registerType, $profileLink, $user)
    {
        $html = '';
        if($registerType == 'jomsocial')
        {
            $jsUser = & CFactory::getUser($user->id);
            $avatarURL = $jsUser->get('_avatar');
            $html = $this->getSocialAvatarImage($avatarURL, $profileLink);
        }
        else if($registerType == 'communitybuilder')
        {
            $db = JFactory::getDbo();
            $query = "SELECT avatar FROM #__comprofiler WHERE id = " . $user->id;
            $db->setQuery($query);
            $avatarURL = $db->loadResult();
            if($avatarURL)
                $avatarURL =  JRoute::_('images/comprofiler/' . $avatarURL, false);
            $html = $this->getSocialAvatarImage($avatarURL, $profileLink);
        }
        return $html;
    }

    function getSocialAvatar($registerType, $profileLink, $user)
    {
        $html = "";
        $enableProfilePic = $this->params->get('enableProfilePic');
        if ($enableProfilePic == 'facebook')
        {
            $html = $this->getFacebookAvatar();
            if ($html == "")
                $html = $this->getLinkedInAvatar();
        }
        else if ($enableProfilePic == 'linkedin')
        {
            $html = $this->getLinkedInAvatar();
            if ($html == "")
                $html = $this->getFacebookAvatar();
        }
        else if ($enableProfilePic == 'joomla')
        {
            $html = $this->getJoomlaAvatar($registerType, $profileLink, $user);
        }

        if ($html != "")
            $html = '<div id="scprofile-pic">' . $html . '</div>';

        return $html;
    }

    function getJFBConnectAvatarSize()
    {
        $picHeightParam = $this->params->get("profileHeight");
        $picWidthParam = $this->params->get("profileWidth");

        $picHeight = intval($picHeightParam);
        $picWidth = intval($picWidthParam);

        if($picWidth > 100)
            return "large";
        else if($picWidth > 50)
            return "normal";
        else if($picWidth <= 50 & $picHeight == $picWidth)
            return "square";
        else
            return "small";
    }

    function getJFBConnectLoginButton()
    {
        if ($this->isJFBConnectInstalled)
        {
            $buttonSize = $this->params->get("loginButtonSize");
            $loginButton = $this->jfbcLibrary->getLoginButton($buttonSize);
            $loginButton = '<div class="jfbcLogin">'.$loginButton.'</div>';
            return $loginButton;
        }
        return "";
    }

    function getJLinkedLoginButton()
    {
        //Show JLinked Login Button
        if ($this->isJLinkedInstalled)
        {
            $buttonSize = $this->params->get("loginButtonSize");
            return '{JLinkedLogin size=' . $buttonSize . $this->jlinkedRenderKey . '}';
        }
        return "";
    }

    function getLogoutButton()
    {
        if ($this->isJFBConnectInstalled)
            return $this->jfbcLibrary->getLogoutButton();
        else if ($this->isJLinkedInstalled)
            return $this->jlinkedLibrary->getLogoutButton();
    }

    function getJFBCConnectButton()
    {
        $buttonHtml = "";
        if (!$this->jfbcLibrary->getMappedFbUserId())
        {
            $buttonHtml = '<div class="fb_connect_user">';
            $buttonHtml .= '<fb:login-button v="2" onlogin="javascript:jfbc.login.on_login();">' . JText::_('MOD_SCLOGIN_CONNECT_BUTTON') .'</fb:login-button>';
            $buttonHtml .= '</div>';
        }
        return $buttonHtml;
    }

    function getJLinkedConnectButton()
    {
        $buttonHtml = "";
        if (!$this->jlinkedLibrary->getMappedLinkedInUserId())
        {
            $buttonHtml = '<link rel="stylesheet" href="components/com_jlinked/assets/jlinked.css" type="text/css" />';
            $buttonHtml .= '<div class="li_connect_user">';
            $buttonHtml .= '<div class="jLinkedLogin"><a href="' . $this->jlinkedLibrary->getLoginURL() . '"><span class="jlinkedButton"></span><span class="jlinkedLoginButton">'.JText::_('MOD_SCLOGIN_CONNECT_BUTTON').'</span></a></div>';
            $buttonHtml .= '</div>';
        }
        return $buttonHtml;
    }

    function getReconnectButtons()
    {
        $buttonHtml = '';
        if ($this->isJFBConnectInstalled)
            $buttonHtml .= $this->getJFBCConnectButton();
        if ($this->isJLinkedInstalled)
            $buttonHtml .= $this->getJLinkedConnectButton();

        if($buttonHtml)
            $buttonHtml = '<div class="sc_connect_user">'. JText::_('MOD_SCLOGIN_CONNECT_USER') . '</div>' . $buttonHtml;

        return $buttonHtml;
    }
}