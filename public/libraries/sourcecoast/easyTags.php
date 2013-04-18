<?php
/**
 * @package SourceCoast Extensions (JFBConnect, JLinked)
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.utilities');

class SCEasyTags
{
    /*
     * Determines if the Easy-Tag can be rendered. If it can, then remove the render key
     */
    static function cannotRenderEasyTag(&$easyTag, $renderKey)
    {
        $key1 = 'key=' . $renderKey . ' ';
        $key2 = 'key=' . $renderKey;

        $renderKeyCheck = strtolower($easyTag);

        $containsKey = strpos($renderKeyCheck, 'key=') !== false;
        $missingKey1 = strpos($renderKeyCheck, $key1) === false;
        $missingKey2 = SCStringUtilities::endswith($renderKeyCheck, $key2) == false;

        $cannotRender = ($renderKey != '' && $missingKey1 && $missingKey2) ||
                ($renderKey == '' && $containsKey && $missingKey1 && $missingKey2);

        if(!$cannotRender && $renderKey != '')
        {
            $easyTag = str_replace($key1, '', $easyTag);
            $easyTag = str_replace($key2, '', $easyTag);
            $easyTag = SCStringUtilities::trimNBSP($easyTag);
        }

        return $cannotRender;
    }

    static function _splitIntoTagParameters($paramList)
    {
        $params = explode(' ', $paramList);

        $count = count($params);
        for($i=0; $i < $count; $i++)
        {
            $params[$i] = str_replace('"', '', $params[$i]);
            if(strpos($params[$i], '=') === false && $i>0)
            {
                $previousIndex = SCEasyTags::_findPreviousParameter($params, $i-1);
                //Combine this with previous entry and space
                $combinedParamValue = $params[$previousIndex].' '.$params[$i];
                $params[$previousIndex] = $combinedParamValue;
                unset($params[$i]);
            }
        }
        return $params;
    }

    static function _findPreviousParameter($params, $i)
    {
        for($index=$i; $index >=0; $index--)
        {
            if(isset($params[$index]))
               return $index;
        }
        return 0;
    }

    static $jLinkedLoginCSSIncluded = false;
    static function getJLinkedLogin($paramList)
    {
        $buttonHtml = '';
        $showLogoutButton = '';
        $buttonSize = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);
                    $paramValues[1] = trim($paramValues[1], ' '); //trim email address was not working

                    switch ($paramValues[0])
                    {
                        case 'logout':
                            $showLogoutButton = $paramValues[1];
                            break;
                        case 'size':
                            $buttonSize = ' '.$paramValues[1];
                            break;
                    }
                }
            }
        }

        $jLinkedLibrary = JLinkedApiLibrary::getInstance();
        $user = JFactory::getUser();
        if ($user->guest) // Only show login button if user isn't logged in (no remapping for now)
        {
            $lang = JFactory::getLanguage();
            $lang->load('com_jlinked');
            $loginText = JText::_('COM_JLINKED_LOGIN_USING_LINKEDIN');

            if (!self::$jLinkedLoginCSSIncluded)
            {
                self::$jLinkedLoginCSSIncluded = true;
                $buttonHtml .= '<link rel="stylesheet" href="'.JURI::base().'components/com_jlinked/assets/jlinked.css" type="text/css" />';
            }
            $buttonHtml .= '<div class="jLinkedLogin"><a href="'.$jLinkedLibrary->getLoginURL().'"><span class="jlinkedButton'.$buttonSize.'"></span><span class="jlinkedLoginButton'.$buttonSize.'">'.$loginText.'</span></a></div>';
        }
        else
        {
            if ($showLogoutButton == '1' || $showLogoutButton == 'true')
            {
                $buttonHtml .= $jLinkedLibrary->getLogoutButton();
            }
        }
        return $buttonHtml;
    }

    static function getJLinkedApply($paramList)
    {
        $companyId = ''; //required
        $recipientEmail = ''; //required
        $jobTitle = ''; //required
        $jobLocation = '';
        $companyLogo = '';
        $themeColor = '';
        $requirePhoneNumber = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);
                    $paramValues[1] = trim($paramValues[1], ' '); //trim email address was not working

                    switch ($paramValues[0])
                    {
                        case 'companyid':
                            $companyId = $paramValues[1];
                            break;
                        case 'email':
                            $recipientEmail = $paramValues[1];
                            break;
                        case 'jobtitle':
                            $jobTitle = $paramValues[1];
                            break;
                        case 'joblocation':
                            $jobLocation = $paramValues[1];
                            break;
                        case 'logo':
                            $companyLogo = $paramValues[1];
                            break;
                        case 'themecolor':
                            $themeColor = $paramValues[1];
                            break;
                        case 'phone':
                            $requirePhoneNumber = strtolower($paramValues[1]);
                            break;
                    }
                }
            }
        }

        $tagButtonText = '<div class="jlinkedApply">';
        $tagButtonText .= '<script type="IN/Apply"';

        if($companyId)
            $tagButtonText .= ' data-companyid="'. $companyId . '"';
        if($recipientEmail)
            $tagButtonText .= ' data-email="' . $recipientEmail . '"';
        if($jobTitle)
            $tagButtonText .= ' data-jobtitle="' . $jobTitle . '"';
        if($jobLocation)
            $tagButtonText .= ' data-joblocation="' . $jobLocation . '"';
        if($companyLogo)
            $tagButtonText .= ' data-logo="' . $companyLogo . '"';
        if($themeColor)
            $tagButtonText .= ' data-themecolor="' . $themeColor . '"';
        if($requirePhoneNumber == 'true' || $requirePhoneNumber == '1')
            $tagButtonText .= ' data-phone="required"';
        $tagButtonText .= '></script></div>';

        return $tagButtonText;
    }

    static function getJLinkedShare($paramList)
    {
        $url = '';
        $countMode = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'href':
                        case 'url': //DEPRECATED 3/6/12
                            $url = $paramValues[1];
                            break;
                        case 'counter':
                            $countMode = $paramValues[1];
                            break;
                    }
                }
            }
        }

        if (!$url)
            $url = SCSocialUtilities::getStrippedUrl();

        $tagButtonText = '<div class="jlinkedShare">';
        $tagButtonText .= '<script type="IN/Share"';

        if($url)
            $tagButtonText .= ' data-url="' . $url . '"';
        if($countMode && ($countMode == 'top' || $countMode == 'right'))
            $tagButtonText .= ' data-counter="' . $countMode . '"';

        $tagButtonText .= '></script></div>';

        return $tagButtonText;
    }

    static function getJLinkedMember($paramList)
    {
        $url = '';
        $displayMode = '';
        $displayBehavior = '';
        $displayText = '';
        $displayWidth = '';
        $showConnections = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'href':
                        case 'url': //DEPRECATED 3/6/12
                            $url = $paramValues[1];
                            break;
                        case 'display_mode':
                            $displayMode = $paramValues[1];
                            break;
                        case 'display_behavior':
                            $displayBehavior = $paramValues[1];
                            break;
                        case 'display_text':
                            $displayText = $paramValues[1];
                            break;
                        case 'display_width':
                            $displayWidth = $paramValues[1];
                            break;
                        case 'related':
                            $showConnections = strtolower(($paramValues[1]));
                            break;
                    }
                }
            }
        }


        $tagButtonText = '<div class="jlinkedMember"><style type="text/css">.IN-canvas-member iframe{left:20px !important; top:135px !important;}</style>';
        $tagButtonText .= '<span class="IN-canvas-member"><script type="IN/MemberProfile"';

        if($url)
            $tagButtonText .= ' data-id="' . $url . '"';
        if($showConnections == 'false' || $showConnections == '0')
            $tagButtonText .= ' data-related="false"';

        if($displayMode == 'inline')
            $tagButtonText .= ' data-format="inline"';
        else if($displayMode == 'icon_name')
        {
            $tagButtonText .= ' data-format="' . $displayBehavior . '"';
            $tagButtonText .= ' data-text="' . $displayText . '"';
        }
        else if($displayMode == 'icon')
        {
            $tagButtonText .= ' data-format="' . $displayBehavior . '"';
        }

        if($displayWidth)
            $tagButtonText .= ' data-width="' . $displayWidth . '"';

        $tagButtonText .= '></script></span></div>';

        return $tagButtonText;
    }

    static function getJLinkedCompanyInsider($paramList)
    {
        $companyId = '';
        $showInNetwork = '';
        $showNewHires = '';
        $showPromotions = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'companyid':
                            $companyId = $paramValues[1];
                            break;
                        case 'in_network':
                            $showInNetwork = strtolower($paramValues[1]);
                            break;
                        case 'new_hires':
                            $showNewHires = strtolower($paramValues[1]);
                            break;
                        case 'promotions_changes':
                            $showPromotions = strtolower($paramValues[1]);
                            break;
                    }
                }
            }
        }

        $tagButtonText = '<div class="jlinkedCompanyInsider">';
        $tagButtonText .= '<script type="IN/CompanyInsider"';

        if($companyId)
            $tagButtonText .= ' data-id="' . $companyId . '"';

        $modules = array();
        if($showInNetwork == '1' || $showInNetwork == 'true')
            $modules[] = 'innetwork';
        if($showNewHires == '1' || $showNewHires == 'true')
            $modules[] = 'newhires';
        if($showPromotions == '1' || $showPromotions == 'true')
            $modules[] = 'jobchanges';

        if (count($modules) > 0)
            $tagButtonText .= ' data-modules="' . implode(',', $modules) . '"';

        $tagButtonText .= '></script></div>';

        return $tagButtonText;
    }

    static function getJLinkedCompanyProfile($paramList)
    {
        $companyId = '';
        $displayMode = '';
        $displayBehavior = '';
        $displayText = '';
        $showConnections = '';
        $displayWidth = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'companyid':
                            $companyId = $paramValues[1];
                            break;
                        case 'display_mode':
                            $displayMode = $paramValues[1];
                            break;
                        case 'display_behavior':
                            $displayBehavior = $paramValues[1];
                            break;
                        case 'display_text':
                            $displayText = $paramValues[1];
                            break;
                        case 'related':
                            $showConnections = strtolower(($paramValues[1]));
                            break;
                        case 'display_width':
                            $displayWidth = $paramValues[1];
                            break;
                    }
                }
            }
        }

        $tagButtonText = '<div class="jlinkedCompanyProfile"><style type="text/css">.IN-canvas-company iframe{left:20px !important; top:135px !important;}</style>';
        $tagButtonText .= '<span class="IN-canvas-company"><script type="IN/CompanyProfile"';

        if($companyId)
            $tagButtonText .= ' data-id="' . $companyId . '"';
        if($showConnections == 'false' || $showConnections == '0')
            $tagButtonText .= ' data-related="false"';

        if($displayMode == 'inline')
            $tagButtonText .= ' data-format="inline"';
        else if($displayMode == 'icon_name')
        {
            $tagButtonText .= ' data-format="' . $displayBehavior . '"';
            $tagButtonText .= ' data-text="' . $displayText . '"';
        }
        else if($displayMode == 'icon')
        {
            $tagButtonText .= ' data-format="' . $displayBehavior . '"';
        }

        if($displayWidth)
            $tagButtonText .= ' data-width="' . $displayWidth . '"';

        $tagButtonText .= '></script></span></div>';

        return $tagButtonText;
    }

    static function getJLinkedRecommend($paramList)
    {
        $companyId = '';
        $productId = '';
        $countMode = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'companyid':
                            $companyId = $paramValues[1];
                            break;
                        case 'productid':
                            $productId = $paramValues[1];
                            break;
                        case 'counter':
                            $countMode = $paramValues[1];
                            break;
                    }
                }
            }
        }

        $tagButtonText = '<div class="jlinkedRecommend">';
        $tagButtonText .= '<script type="IN/RecommendProduct"';

        if($companyId)
            $tagButtonText .= ' data-company="' . $companyId . '"';
        if($productId)
            $tagButtonText .= ' data-product="' . $productId . '"';
        if($countMode && ($countMode == 'top' || $countMode == 'right'))
            $tagButtonText .= ' data-counter="' . $countMode . '"';

        $tagButtonText .= '></script></div>';

        return $tagButtonText;
    }

    static function getJLinkedJobs($paramList)
    {
        $companyId = ''; //optional

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);
                    $paramValues[1] = trim($paramValues[1], ' '); //trim email address was not working

                    switch ($paramValues[0])
                    {
                        case 'companyid':
                            $companyId = $paramValues[1];
                            break;
                    }
                }
            }
        }

        $tagButtonText = '<div class="jlinkedJobs">';
        $tagButtonText .= '<script type="IN/JYMBII"';

        if($companyId)
            $tagButtonText .= ' data-companyid="'. $companyId . '"';
        $tagButtonText .= ' data-format="inline"';
        $tagButtonText .= '></script></div>';

        return $tagButtonText;
    }

    static function getSCTwitterShare($paramList)
    {
        $url = '';
        $dataCount = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'href':
                        case 'url': //DEPRECATED 3/6/12
                            $url = $paramValues[1];
                            break;
                        case 'data_count':
                            $dataCount = $paramValues[1];
                            break;
                    }
                }
            }
        }

        if (!$url)
            $url = SCSocialUtilities::getStrippedUrl();

        $tagButtonText = '<div class="sc_twittershare">';
        $tagButtonText .= '<a href="http://twitter.com/share" class="twitter-share-button" ';

        if($url)
            $tagButtonText .= 'data-url="' . $url . '"';;
        if($dataCount == 'horizontal' || $dataCount == 'vertical' || $dataCount == 'none')
            $tagButtonText .= '" data-count="' . $dataCount . '"';;

        $tagButtonText .= '>Tweet</a></div>';

        return $tagButtonText;
    }

    static function getSCGPlusOne($paramList)
    {
        $url = '';
        $annotation = '';
        $size = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'href':
                        case 'url': //DEPRECATED 3/6/12
                            $url = $paramValues[1];
                            break;
                        case 'annotation':
                            $annotation = $paramValues[1];
                            break;
                        case 'size':
                            $size = $paramValues[1];
                            break;
                    }
                }
            }
        }

        if (!$url)
            $url = SCSocialUtilities::getStrippedUrl();

        $tagButtonText = '<div class="sc_gplusone"><g:plusone';
        if($size)
            $tagButtonText .= ' size="' . $size . '"';
        if($annotation)
            $tagButtonText .= ' annotation="' . $annotation . '"';
        if($url)
            $tagButtonText .= ' href="' . $url . '"';
        $tagButtonText .= '></g:plusone></div>';

        return $tagButtonText;
    }

    static function getJFBCLike($paramList)
    {
        $url = '';
        $buttonStyle = '';
        $showFaces = '';
        $showSendButton = '';
        $width = '';
        $verbToDisplay = '';
        $font = '';
        $colorScheme = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'href':
                        case 'url': //DEPRECATED 3/6/12
                            $url = $paramValues[1];
                            break;
                        case 'layout':
                            $buttonStyle = $paramValues[1];
                            break;
                        case 'show_faces':
                            $showFaces = strtolower($paramValues[1]);
                            break;
                        case 'show_send_button':
                            $showSendButton = strtolower($paramValues[1]);
                            break;
                        case 'width':
                            $width = $paramValues[1];
                            break;
                        case 'action':
                            $verbToDisplay = $paramValues[1];
                            break;
                        case 'font':
                            $font = $paramValues[1];
                            break;
                        case 'colorscheme':
                            $colorScheme = $paramValues[1];
                            break;
                    }
                }
            }
        }

        if (!$url)
            $url = SCSocialUtilities::getStrippedUrl();

        $likeButtonText = '<div class="jfbclike"><fb:like href="' . $url . '"';
        if ($showFaces == "false" || $showFaces == "0")
            $likeButtonText .= ' show_faces="false"';
        else
            $likeButtonText .= ' show_faces="true"';

        if ($showSendButton == "false" || $showSendButton == "0")
            $likeButtonText .= ' send="false"';
        else
            $likeButtonText .= ' send="true"';

        if ($buttonStyle)
            $likeButtonText .= ' layout="' . $buttonStyle . '"';
        if ($width)
            $likeButtonText .= ' width="' . $width . '"';
        if ($verbToDisplay)
            $likeButtonText .= ' action="' . $verbToDisplay . '"';
        if ($font)
            $likeButtonText .= ' font="' . $font . '"';
        if ($colorScheme)
            $likeButtonText .= ' colorscheme="' . $colorScheme . '"';
        $likeButtonText .= '></fb:like></div>';
        return $likeButtonText;
    }

    static function getJFBCLogin($paramList)
    {
        $buttonSize = 'medium';
        $showLogoutButton = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'size':
                            $buttonSize = $paramValues[1];
                            break;
                        case 'logout':
                            $showLogoutButton = $paramValues[1];
                            break;
                    }
                }
            }
        }

        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $user = JFactory::getUser();
        if ($user->guest) // Only show login button if user isn't logged in (no remapping for now)
            $fbLogin = $jfbcLibrary->getLoginButton($buttonSize);
        else
        {
            $fbLogin = ""; // return blank for registered users

            if ($showLogoutButton == '1' || $showLogoutButton == 'true')
                $fbLogin = $jfbcLibrary->getLogoutButton();
        }

        return '<div class="jfbclogin">' . $fbLogin . '</div>';
    }

    static function getJFBCSend($paramList)
    {
        $url = '';
        $font = '';
        $colorScheme = '';
        $ref = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'href':
                        case 'url': //DEPRECATED 3/6/12
                            $url = $paramValues[1];
                            break;
                        case 'font':
                            $font = $paramValues[1];
                            break;
                        case 'colorscheme':
                            $colorScheme = $paramValues[1];
                            break;
                        case 'ref' :
                            $ref = $paramValues[1];
                            break;
                    }
                }
            }
        }

        if (!$url)
            $url = SCSocialUtilities::getStrippedUrl();

        $sendButtonText = '<div class="jfbcsend"><fb:send href="' . $url . '"';

        if ($font)
            $sendButtonText .= ' font="' . $font . '"';
        if ($colorScheme)
            $sendButtonText .= ' colorscheme="' . $colorScheme . '"';
        if ($ref)
            $sendButtonText .= ' ref="' . $ref . '"';
        $sendButtonText .= '></fb:send></div>';
        return $sendButtonText;
    }

    static function getJFBCComments($paramList)
    {
        $href = '';
        $xid = ''; //DEPRECATED
        $width = '';
        $numComments = '';
        $colorscheme = '';
        $mobile = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'href':
                            $href = $paramValues[1];
                            break;
                        case 'width':
                            $width = $paramValues[1];
                            break;
                        case 'num_posts':
                            $numComments = $paramValues[1];
                            break;
                        case 'xid':
                            $xid = $paramValues[1];
                            break;
                        case 'colorscheme':
                            $colorscheme = $paramValues[1];
                            break;
                        case 'mobile':
                            $mobile = $paramValues[1];
                            break;
                    }
                }
            }
        }

        $commentString = '<div class="jfbccomments"><fb:comments';

        if ($href)
            $commentString .= ' href="' . $href . '"';
        else if ($xid) //Use deprecated xid to keep old comments: http://developers.facebook.com/blog/post/472
            $commentString .= ' xid="' . $xid . '" migrated="1"';
        else
        {
            $url = SCSocialUtilities::getStrippedUrl();
            $commentString .= ' href="' . $url . '"';
        }

        if ($width)
            $commentString .= ' width="' . $width . '"';
        if ($numComments || $numComments == "0")
            $commentString .= ' num_posts="' . $numComments . '"';
        if ($colorscheme)
            $commentString .= ' colorscheme="' . $colorscheme . '"';
        if ($mobile == "false" || $mobile == "0")
            $commentString .= ' mobile="false"';

        $commentString .= '></fb:comments></div>';
        return $commentString;
    }

    static function getJFBCCommentsCount($paramList)
    {
        $href = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'href':
                            $href = $paramValues[1];
                            break;
                    }
                }
            }
        }

        //Get the Comments Count string
        $tagString = '<fb:comments-count';
        if ($href)
            $tagString .= ' href="' . $href . '"';
        else
        {
            $url = SCSocialUtilities::getStrippedUrl();
            $tagString .= ' href="' . $url . '"';
        }
        $tagString .= '></fb:comments-count>';

        $lang = JFactory::getLanguage();
        $lang->load('com_jfbconnect');

        $commentString = '<div class="jfbccomments_count">';
        $commentString .= JText::sprintf('COM_JFBCONNECT_COMMENTS_COUNT', $tagString);
        $commentString .= '</div>';
        return $commentString;
    }

    static function getJFBCFan($paramList)
    {
        $height = '';
        $width = '';
        $colorScheme = '';
        $href = '';
        $showFaces = '';
        $stream = '';
        $header = '';
        $borderColor = '';
        $forceWall = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'height': //Not shown - http://developers.facebook.com/docs/reference/plugins/like-box/
                            $height = $paramValues[1];
                            break;
                        case 'width':
                            $width = $paramValues[1];
                            break;
                        case 'colorscheme':
                            $colorScheme = $paramValues[1];
                            break;
                        case 'href':
                            $href = $paramValues[1];
                            break;
                        case 'show_faces':
                            $showFaces = $paramValues[1];
                            break;
                        case 'stream':
                            $stream = $paramValues[1];
                            break;
                        case 'header':
                            $header = $paramValues[1];
                            break;
                        case 'border_color':
                            $borderColor = $paramValues[1];
                            break;
                        case 'force_wall':
                            $forceWall = $paramValues[1];
                            break;
                    }
                }
            }
        }

        $fanString = '<div class="jfbcfan"><fb:like-box';

        if ($showFaces == "false" || $showFaces == "0")
            $fanString .= ' show_faces="false"';
        else
            $fanString .= ' show_faces="true"';

        if ($header == "false" || $header == "0")
            $fanString .= ' header="false"';
        else
            $fanString .= ' header="true"';

        if ($stream == "false" || $stream == "0")
            $fanString .= ' stream="false"';
        else
            $fanString .= ' stream="true"';

        if ($forceWall == "false" || $forceWall == "0")
            $fanString .= ' force_wall="false"';
        else
            $fanString .= ' force_wall="true"';

        if ($width)
            $fanString .= ' width="' . $width . '"';
        if ($height)
            $fanString .= ' height="' . $height . '"';
        if ($href)
            $fanString .= ' href="' . $href . '"';
        if ($colorScheme)
            $fanString .= ' colorscheme="' . $colorScheme . '"';
        if ($borderColor)
            $fanString .= ' border_color="' . $borderColor . '"';

        $fanString .= '></fb:like-box></div>';
        return $fanString;
    }

    static function getJFBCFeed($paramList)
    {
        $site = '';
        $height = '';
        $width = '';
        $colorScheme = '';
        $font = '';
        $borderColor = '';
        $recommendations = '';
        $header = '';
        $linkTarget = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'site':
                            $site = $paramValues[1];
                            break;
                        case 'height':
                            $height = $paramValues[1];
                            break;
                        case 'width':
                            $width = $paramValues[1];
                            break;
                        case 'colorscheme':
                            $colorScheme = $paramValues[1];
                            break;
                        case 'font':
                            $font = $paramValues[1];
                            break;
                        case 'border_color':
                            $borderColor = $paramValues[1];
                            break;
                        case 'recommendations':
                            $recommendations = $paramValues[1];
                            break;
                        case 'header':
                            $header = $paramValues[1];
                            break;
                        case 'link_target':
                            $linkTarget = $paramValues[1];
                    }
                }
            }
        }

        $feedString = '<div class="jfbcfeed"><fb:activity';

        if ($recommendations == "false" || $recommendations == "0")
            $feedString .= ' recommendations="false"';
        else
            $feedString .= ' recommendations="true"';

        if ($header == "false" || $header == "0")
            $feedString .= ' header="false"';
        else
            $feedString .= ' header="true"';

        if ($width)
            $feedString .= ' width="' . $width . '"';
        if ($height)
            $feedString .= ' height="' . $height . '"';
        if ($site)
            $feedString .= ' site="' . $site . '"';
        if ($colorScheme)
            $feedString .= ' colorscheme="' . $colorScheme . '"';
        if ($font)
            $feedString .= ' font="' . $font . '"';
        if ($borderColor)
            $feedString .= ' border_color="' . $borderColor . '"';
        if ($linkTarget)
            $feedString .= ' linktarget="' . $linkTarget . '"';

        $feedString .= '></fb:activity></div>';
        return $feedString;
    }

    static function getJFBCFriends($paramList)
    {
        $href = '';
        $width = '';
        $maxRows = '';
        $colorScheme = '';
        $size = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'href':
                            $href = $paramValues[1];
                            break;
                        case 'width':
                            $width = $paramValues[1];
                            break;
                        case 'max_rows':
                            $maxRows = $paramValues[1];
                            break;
                        case 'colorscheme':
                            $colorScheme = $paramValues[1];
                            break;
                        case 'size':
                            $size = $paramValues[1];
                            break;
                    }
                }
            }
        }

        $friendsString = '<div class="jfbcfriends"><fb:facepile';

        if ($href)
            $friendsString .= ' href="' . $href . '"';
        if ($width)
            $friendsString .= ' width="' . $width . '"';
        if ($maxRows)
            $friendsString .= ' max_rows="' . $maxRows . '"';
        if ($colorScheme)
            $friendsString .= ' colorscheme="' . $colorScheme . '"';
        if ($size)
            $friendsString .= ' size="' . $size . '"';

        $friendsString .= '></fb:facepile></div>';
        return $friendsString;
    }

    static function getJFBCLiveStream($paramList)
    {
        $appId = '';
        $width = '';
        $height = '';
        $xid = '';
        $viaURL = '';
        $alwaysPostToFriends = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'event_app_id':
                            $appId = $paramValues[1];
                            break;
                        case 'width':
                            $width = $paramValues[1];
                            break;
                        case 'height':
                            $height = $paramValues[1];
                            break;
                        case 'xid' :
                            $xid = $paramValues[1];
                            break;
                        case 'via_url' :
                            $viaURL = $paramValues[1];
                            break;
                        case 'always_post_to_friends':
                            $alwaysPostToFriends = $paramValues[1];
                            break;
                    }
                }
            }
        }

        $liveStreamString = '<div class="jfbclivestream"><fb:live-stream';

        if ($alwaysPostToFriends == "true" || $alwaysPostToFriends == "1")
            $liveStreamString .= ' always_post_to_friends="true"';
        else
            $liveStreamString .= ' always_post_to_friends="false"';

        if ($appId)
            $liveStreamString .= ' event_app_id="' . $appId . '"';
        if ($width)
            $liveStreamString .= ' width="' . $width . '"';
        if ($height)
            $liveStreamString .= ' height="' . $height . '"';
        if ($xid)
            $liveStreamString .= ' xid="' . $xid . '"';
        if ($viaURL)
            $liveStreamString .= ' via_url="' . $viaURL . '"';

        $liveStreamString .= '></fb:live-stream></div>';
        return $liveStreamString;
    }

    static function getJFBCRecommendations($paramList)
    {
        $site = '';
        $width = '';
        $height = '';
        $header = '';
        $colorScheme = '';
        $font = '';
        $borderColor = '';
        $linkTarget = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'site':
                            $site = $paramValues[1];
                            break;
                        case 'width':
                            $width = $paramValues[1];
                            break;
                        case 'height':
                            $height = $paramValues[1];
                            break;
                        case 'colorscheme' :
                            $colorScheme = $paramValues[1];
                            break;
                        case 'header' :
                            $header = $paramValues[1];
                            break;
                        case 'font':
                            $font = $paramValues[1];
                            break;
                        case 'border_color':
                            $borderColor = $paramValues[1];
                            break;
                        case 'link_target':
                            $linkTarget = $paramValues[1];
                            break;
                    }
                }
            }
        }

        $recString = '<div class="jfbcrecommendations"><fb:recommendations';

        if ($header == "false" || $header == "0")
            $recString .= ' header="false"';
        else
            $recString .= ' header="true"';

        if ($site)
            $recString .= ' site="' . $site . '"';
        if ($width)
            $recString .= ' width="' . $width . '"';
        if ($height)
            $recString .= ' height="' . $height . '"';
        if ($colorScheme)
            $recString .= ' colorscheme="' . $colorScheme . '"';
        if ($font)
            $recString .= ' font="' . $font . '"';
        if ($borderColor)
            $recString .= ' border_color="' . $borderColor . '"';
        if ($linkTarget)
            $recString .= ' linktarget="' . $linkTarget . '"';

        $recString .= '></fb:recommendations></div>';
        return $recString;
    }

    static function getJFBCRequest($paramList)
    {
        $requestID = '';
        $linkText = '';
        $linkImage = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'request_id':
                            $requestID = $paramValues[1];
                            break;
                        case 'link_text':
                            $linkText = $paramValues[1];
                            break;
                        case 'link_image':
                            $linkImage = $paramValues[1];
                            break;
                    }
                }
            }
        }
        $tagString = '';
        if ($requestID != '')
        {
            JModel::addIncludePath(JPATH_SITE . DS . 'components' . DS . 'com_jfbconnect' . DS . 'models');
            $requestModel = JModel::getInstance('Request', "JFBConnectModel");
            $request = $requestModel->getData($requestID);

            if ($request && $request->published)
            {
                $message = str_replace("\r\n", " ", $request->message);
                $linkValue = $linkText;
                if ($linkImage != '')
                    $linkValue = '<img src="' . $linkImage . '" alt="' . $request->title . ' "/>';

                $tagString = '<div class="jfbcrequest">';
                $tagString .= '<a href="javascript:void(0)" onclick="jfbc.request.popup(' . $requestID . '); return false;">' . $linkValue . '</a>';
                $tagString .= '</div>';
                $tagString .=
                        <<<EOT
                        <script type="text/javascript">
    var jfbcRequests = Object.prototype.toString.call(jfbcRequests) == "[object Array]" ? jfbcRequests : [];
    var jfbcRequest = new Object;
    jfbcRequest.title = "{$request->title}";
    jfbcRequest.message = "{$message}";
    jfbcRequest.destinationUrl = "{$request->destination_url}";
    jfbcRequest.thanksUrl = "{$request->thanks_url}";
    jfbcRequests[{$requestID}] = jfbcRequest;
</script>
EOT;
            }
        }
        return $tagString;
    }

    static function getJFBCSubscribe($paramList)
    {
        $href = '';
        $layout = '';
        $showFaces = '';
        $colorScheme = '';
        $font = '';
        $width = '';

        $params = SCEasyTags::_splitIntoTagParameters($paramList);
        foreach ($params as $param)
        {
            if ($param != null)
            {
                $paramValues = explode('=', $param, 2);
                if (count($paramValues) == 2) //[0] name [1] value
                {
                    $paramValues[0] = strtolower(trim($paramValues[0]));
                    $paramValues[1] = trim($paramValues[1]);

                    switch ($paramValues[0])
                    {
                        case 'href':
                            $href = $paramValues[1];
                            break;
                        case 'layout':
                            $layout = $paramValues[1];
                            break;
                        case 'show_faces':
                            $showFaces = strtolower($paramValues[1]);
                            break;
                        case 'width':
                            $width = $paramValues[1];
                            break;
                        case 'font':
                            $font = $paramValues[1];
                            break;
                        case 'colorscheme':
                            $colorScheme = $paramValues[1];
                            break;
                    }
                }
            }
        }

        $tagText = '<div class="jfbcsubscribe"><fb:subscribe href="' . $href . '"';
        if ($showFaces == "false" || $showFaces == "0")
            $tagText .= ' show_faces="false"';
        else
            $tagText .= ' show_faces="true"';

        if ($layout)
            $tagText .= ' layout="' . $layout . '"';
        if ($width)
            $tagText .= ' width="' . $width . '"';
        if ($font)
            $tagText .= ' font="' . $font . '"';
        if ($colorScheme)
            $tagText .= ' colorscheme="' . $colorScheme . '"';
        $tagText .= '></fb:subscribe></div>';
        return $tagText;
    }

    static function extendJoomlaUserForms($loginTag)
    {
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');
        $user = JFactory::getUser();

        if(($option == 'com_user' && $view == 'login') || ($option == 'com_users' && $view == 'login'))
        {
            if($user->guest)
            {
                $document = JFactory::getDocument();
                $output = $document->getBuffer('component');
                $contents = $output . $loginTag;
                $document->setBuffer($contents, 'component');
            }
        }
    }
}

