<?php
/**
 * @package SourceCoast Extensions (JFBConnect, JLinked)
 * @copyright (C) 2011-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

define('CHECK_NEW_MAPPING_JLINKED', 'jLinkedCheckNewMapping');
define('CHECK_NEW_MAPPING_JFBCONNECT', 'jfbcCheckNewMapping');
define('LOGIN_TASK_JLINKED', 'loginLinkedInUser');
define('LOGIN_TASK_JFBCONNECT', 'loginFacebookUser');

class SCSocialUtilities
{
    var $version = "1.0.4"; // Same as the library XML version

    static function isJFBConnectInstalled()
    {
        return SCSocialUtilities::isComponentInstalled('com_jfbconnect', JPATH_ROOT.DS.'components'.DS.'com_jfbconnect'.DS.'libraries'.DS.'facebook.php');
    }

    static function isJLinkedInstalled()
    {
        return SCSocialUtilities::isComponentInstalled('com_jlinked', JPATH_ROOT.DS.'components'.DS.'com_jlinked'.DS.'libraries'.DS.'linkedin.php');
    }

    static function isComponentInstalled($option, $libraryFile)
    {
        $isComponentInstalled = false;
        if (JFile::exists($libraryFile))
        {
            $isComponentInstalled = JComponentHelper::isEnabled($option);
        }
        return $isComponentInstalled;
    }

    static function areJFBConnectTagsEnabled()
    {
        return JPluginHelper::isEnabled('system', 'jfbcsystem');
    }

    static function areJLinkedTagsEnabled()
    {
        return JPluginHelper::isEnabled('system', 'jlinkedsystem');
    }

    static function getJFBConnectAppId()
    {
        $libFile = JPATH_ROOT . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php';
        if (!JFile::exists($libFile))
            return '';

        require_once($libFile);
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $appId = $jfbcLibrary->facebookAppId;
        return $appId;
    }

    static function getJFBConnectRenderKeySetting()
    {
        $libFile = JPATH_ROOT . DS . 'components' . DS . 'com_jfbconnect' . DS . 'libraries' . DS . 'facebook.php';
        if (!JFile::exists($libFile))
            return '';

        require_once($libFile);
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $renderKey = $jfbcLibrary->getSocialTagRenderKey();
        return $renderKey;
    }

    static function getJLinkedRenderKeySetting()
    {
        $libFile = JPATH_ROOT.DS.'components'.DS.'com_jlinked'.DS.'libraries'.DS.'linkedin.php';
        if (!JFile::exists($libFile))
            return '';

        require_once($libFile);
        $jLinkedLibrary = JLinkedApiLibrary::getInstance();
        $renderKey = $jLinkedLibrary->getSocialTagRenderKey();
        return $renderKey;
    }

    static function getJFBConnectRenderKey()
    {
        $renderKey = SCSocialUtilities::getJFBConnectRenderKeySetting();
        if($renderKey != '')
            $renderKeyString = " key=" . $renderKey;
        else
            $renderKeyString = '';

        return $renderKeyString;
    }

    static function getJLinkedRenderKey()
    {
        $renderKey = SCSocialUtilities::getJLinkedRenderKeySetting();
        if($renderKey != '')
            $renderKeyString = " key=" . $renderKey;
        else
            $renderKeyString = '';

        return $renderKeyString;
    }

    static function getExtraShareButtons($url, $dataCount, $showFacebookLikeButton, $showFacebookSendButton, $showTwitterButton, $showGooglePlusButton, $renderKeyString, $showLinkedInButton = false)
    {
        if ($dataCount == "top" || $dataCount == "box_count")
        {
            $fbButtonStyle = "box_count";
            $li_dataCount = "top";
            $gp_size = 'tall';
            $gp_annotation = 'bubble';
            $tw_size = 'vertical';
        }
        else if ($dataCount == 'right' || $dataCount == "button_count")
        {
            $fbButtonStyle = "button_count";
            $li_dataCount = "right";
            $gp_size = 'medium';
            $gp_annotation = 'bubble';
            $tw_size = 'horizontal';
        }
        else
        {
            $fbButtonStyle = 'standard';
            $li_dataCount = 'no_count';
            $gp_size = 'standard';
            $gp_annotation = 'none';
            $tw_size = 'none';
        }

        $extraButtonText = '';

        if($url == '')
            $url = SCSocialUtilities::getStrippedUrl();

        if($showLinkedInButton)
        {
            if(SCSocialUtilities::isJLinkedInstalled() && SCSocialUtilities::areJLinkedTagsEnabled())
            {
                $renderString = SCSocialUtilities::getJLinkedRenderKey();
                $extraButtonText .= '{JLinkedShare href='. $url . ' counter=' . $li_dataCount . $renderString . '}';
            }
            else
                $extraButtonText .= '{JLinkedShare href='. $url . ' counter=' . $li_dataCount . $renderKeyString . '}';
        }
        if($showTwitterButton)
        {
            $extraButtonText .= '{SCTwitterShare href='. $url . ' data_count=' . $tw_size . $renderKeyString .'}';
        }
        if($showGooglePlusButton)
        {
            $extraButtonText .= '{SCGooglePlusOne href=' . $url . ' annotation=' . $gp_annotation . ' size=' . $gp_size . $renderKeyString . '}';
        }
        if($showFacebookLikeButton)
        {
            $sendString = $showFacebookSendButton ? "true" : "false";

            if(SCSocialUtilities::isJFBConnectInstalled() && SCSocialUtilities::areJFBConnectTagsEnabled())
            {
                $renderString = SCSocialUtilities::getJFBConnectRenderKey();
                $extraButtonText .= '{JFBCLike href='. $url . ' layout=' . $fbButtonStyle . ' show_send_button=' . $sendString . $renderString . '}';
            }
            else
                $extraButtonText .= '{JFBCLike href='. $url . ' layout=' . $fbButtonStyle . ' show_send_button=' . $sendString . $renderKeyString . '}';
        }

        return $extraButtonText;
    }

    static function getStrippedUrl()
    {
        $href = JURI::current();

        $juri = JURI::getInstance();
        // Delete some common, unwanted query params to at least try to get at the canonical URL
        $juri->delVar('fb_comment_id');
        $juri->delVar('tp');
        $juri->delVar('notif_t');
        $juri->delVar('ref');
        $query = $juri->getQuery();

        if ($query)
            $href .= '?' . $query;

        return $href;
    }

    static function stripSystemTags(&$description, $metadataTag)
    {
        $replace = false;

        //Full Match
        if (preg_match_all('/\{' . $metadataTag . '.*?\}/i', $description, $matches, PREG_SET_ORDER))
        {
            $replace = true;
            foreach ($matches as $match)
            {
                $description = str_replace($match, '', $description);
            }
        }
        //Partial Match
        if (preg_match('/\{' . $metadataTag . '+(.*?)/i', $description, $matches))
        {
            $replace = true;
            $trimPoint = strpos($description, '{' . $metadataTag);
            if ($trimPoint == 0)
                $description = '';
            else
                $description = substr($description, 0, $trimPoint);
        }

        return $replace;
    }

    static function setJFBCNewMappingEnabled()
    {
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        SCSocialUtilities::setNewMappingEnabled($jfbcLibrary, CHECK_NEW_MAPPING_JFBCONNECT);
    }

    static function setJLinkedNewMappingEnabled()
    {
        $jLinkedLibrary = JLinkedApiLibrary::getInstance();
        SCSocialUtilities::setNewMappingEnabled($jLinkedLibrary, CHECK_NEW_MAPPING_JLINKED);
    }

    static function setNewMappingEnabled($socialLibrary = null, $checkNewMappingSetting = CHECK_NEW_MAPPING_JLINKED)
    {
        $session = JFactory::getSession();
        $session->set($checkNewMappingSetting, true);

        if($socialLibrary == null) //Backwards compatibility with JLinked 1.1
            $socialLibrary = JLinkedApiLibrary::getInstance();

        $socialLibrary->checkNewMapping = true;
    }

    static function clearJFBCNewMappingEnabled()
    {
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        SCSocialUtilities::clearNewMappingEnabled($jfbcLibrary, CHECK_NEW_MAPPING_JFBCONNECT);
    }

    static function clearJLinkedNewMappingEnabled()
    {
        $jLinkedLibrary = JLinkedApiLibrary::getInstance();
        SCSocialUtilities::clearNewMappingEnabled($jLinkedLibrary, CHECK_NEW_MAPPING_JLINKED);
    }

    static function clearNewMappingEnabled($socialLibrary = null, $checkNewMappingSetting = CHECK_NEW_MAPPING_JLINKED)
    {
        $session = JFactory::getSession();
        $session->clear($checkNewMappingSetting);

        if($socialLibrary == null) //Backwards compatibility with JLinked 1.1
            $socialLibrary = JLinkedApiLibrary::getInstance();

        $socialLibrary->checkNewMapping = false;
    }

    static function getCurrentReturnParameter(&$return, &$menuItemId, $loginTaskSetting = LOGIN_TASK_JLINKED)
    {
        // setup return url in case they should be redirected back to this page
        $uri = JURI::getInstance();

        // Save the current page to the session, allowing us to redirect to it on login or logout if configured that way
        $isLoginRegister = JRequest::getCmd('view') == "loginregister";
        $isLoginReturning = JRequest::getCmd('task') == $loginTaskSetting;
        $isLogout = JRequest::getCmd('task') == "logout";

        //NOTE: Not checking option=com_blah because of system cache plugin
        if(!$isLoginRegister && !$isLoginReturning && !$isLogout)
        {
            $return = $uri->toString(array('path', 'query'));
            if ($return == "")
                $return = 'index.php';
        }

        //Save the current return parameter
        $returnParam = JRequest::getVar('return', '');
        if ($returnParam != "")
        {
            $return = urlencode($returnParam); // Required for certain SEF extensions
            $return = rawurldecode($return);
            $return = base64_decode($return);

            $returnURI = JURI::getInstance($return);
            $menuItemId = $returnURI->getVar('Itemid','');

            $filterInput = JFilterInput::getInstance();
            $menuItemId = $filterInput->clean($menuItemId, 'INT');
            //$menuItemId = JFilterInput::clean($menuItemId, 'INT');

        }
        else
            $menuItemId = JRequest::getInt('Itemid', 0);
    }

    static function getRemoteContent($url)
    {
        // Parts of this function inspired by JomSocial's implementation (c) Slashes 'n Dots azrul.com
        if (!$url)
            return false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $response = curl_exec($ch);

        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);

        if ($curl_errno != 0)
        {
            // Find a better way to show errors only when reporting is enabled
/*            $mainframe = JFactory::getApplication();
            $err = 'CURL error : ' . $curl_errno . ' ' . $curl_error;
            $mainframe->enqueueMessage($err, 'error');*/
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // TODO: Add recursion allowing for more redirects? Unsure if multi redirects would happen...
        if ($code == 301 || $code == 302)
        {
            list($headers, $body) = explode("\r\n\r\n", $response, 2);

            preg_match("/(Location:|URI:)(.*?)\n/", $headers, $matches);

            if (!empty($matches) && isset($matches[2]))
            {
                $url = JString::trim($matches[2]);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, true);
                $response = curl_exec($ch);
            }
        }

        list($headers, $body) = explode("\r\n\r\n", $response, 2);

        curl_close($ch);
        return $body;
    }
}

class SCStringUtilities
{
    static function endswith($string, $test)
    {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen)
            return false;
        return substr_compare($string, $test, -$testlen) === 0;
    }

    static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    static function trimNBSP($htmlText)
    {
        // turn some HTML with non-breaking spaces into a "normal" string
        $converted = strtr($htmlText, array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)));
        $converted = trim($converted," \t\n\r\0\x0B".chr(0xC2).chr(0xA0)); // UTF encodes it as chr(0xC2).chr(0xA0)
        return $converted;
    }

    //Recursively implode and trim an array (of strings/arrays)
    static function r_implode($glue, $pieces)
    {
        foreach($pieces as $r_pieces)
        {
            if(is_array($r_pieces))
            {
                unset($r_pieces['id']); // Remove id key which is useless to import
                $retVal[] = SCStringUtilities::r_implode($glue, $r_pieces);
            }
            else
            {
                $retVal[] = trim($r_pieces);
            }
        }
        return implode($glue, $retVal);
    }
}