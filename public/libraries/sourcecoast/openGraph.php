<?php
/**
 * @package SourceCoast Extensions (JFBConnect, JLinked)
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.utilities');

define('SOURCECOAST_JFBCONNECT', 'JFBConnect');
define('SOURCECOAST_JLINKED', 'JLinked');
define('CARRIAGE_RETURN', chr(13));

class OpenGraphTags
{
    var $isOpenGraphEnabled;
    var $defaultGraphFields;
    var $componentType;
    var $componentAppId;
    var $locale;

    /**
     * @param $isOpenGraphEnabled - component configuration setting
     * @param $defaultGraphFields - component configuration setting
     * @param $componentType - currently supported: 'JFBConnect' and 'JLinked'
     * @param $componentAppId - app ID. Currently only supported for JFBConnect
     * @param $locale - locale. Either component configuration setting if set or Joomla locale
     */
    function __construct($isOpenGraphEnabled, $defaultGraphFields, $componentType, $componentAppId, $locale)
    {
        $this->isOpenGraphEnabled = $isOpenGraphEnabled;
        $this->defaultGraphFields = $defaultGraphFields;
        $this->componentType = $componentType;
        $this->componentAppId = $componentAppId;
        if($locale)
            $locale = strtolower($locale);
        $this->locale = $locale;
    }

    /**
     * @param $graphEasyTags - array of SC Graph Easy Tags to replace with a metatag
     * @return string -  of open graph meta-tags to replace the SC Graph Easy Tags with
     */
    public function getOpenGraphTags($graphEasyTags)
    {
        $headerGraphString = '';

        if ($this->isOpenGraphEnabled == '1')
        {
            //Handle {SCOpenGraph} tags first. They have priority
            foreach ($graphEasyTags as $graphField)
            {
                $headerGraphString .= $this->getSCGraphProperty($graphField, $headerGraphString);
            }

            //Default list of carriage-returned fields that are each key=value format
            $fields = explode(CARRIAGE_RETURN, $this->defaultGraphFields);
            foreach ($fields as $graphField)
            {
                $headerGraphString .= $this->getSCGraphProperty($graphField, $headerGraphString);
            }

            //Check to see that description, url and title are added. If not, then
            //generate appropriate values from current page
            $doc = JFactory::getDocument();
            if (strpos($headerGraphString, 'og:url') === false)
            {
                $url = SCSocialUtilities::getStrippedUrl();
                $headerGraphString .= '<meta property="og:url" content="' . $url . '"/>' . CARRIAGE_RETURN;
            }
            if (strpos($headerGraphString, 'og:title') === false)
            {
                $title = $doc->getTitle();
                $title = str_replace('"', "'", $title);
                $headerGraphString .= '<meta property="og:title" content="' . $title . '"/>' . CARRIAGE_RETURN;
            }
            if (strpos($headerGraphString, 'og:description') === false)
            {
                $desc = $doc->getDescription();
                $desc = str_replace('"', "'", $desc);
                $headerGraphString .= '<meta property="og:description" content="' . $desc . '"/>' . CARRIAGE_RETURN;
            }
            if (strpos($headerGraphString, 'og:type') === false)
            {
                $isHomePage = $this->isHomepage();
                if ($isHomePage)
                    $type = "website";
                else
                    $type = "article";
                $headerGraphString .= '<meta property="og:type" content="' . $type . '" />' . CARRIAGE_RETURN;
            }
            if (strpos($headerGraphString, 'fb:app_id') === false && SCSocialUtilities::areJFBConnectTagsEnabled())
            {
                $appId = SCSocialUtilities::getJFBConnectAppId();
                $headerGraphString .= '<meta property="fb:app_id" content="' . $appId . '"/>' . CARRIAGE_RETURN;
            }
            if (strpos($headerGraphString, 'og:locale') === false)
            {
                $headerGraphString .= '<meta property="og:locale" content="' . $this->locale . '" />' . CARRIAGE_RETURN;
            }
            if (strpos($headerGraphString, 'og:site_name') === false)
            {
                $config = JFactory::getConfig();
                $siteName = $config->getValue("config.sitename");
                $headerGraphString .= '<meta property="og:site_name" content="' . $siteName . '" />' . CARRIAGE_RETURN;
            }
        }

        return $headerGraphString;
    }

    /*
     * Expecting a field=value string
     */


    /**
     * @param $graphField - in the form of <field>=<value>
     * @param $headerGraphString - current headerGraphString that's been generated
     * @return string - returns metatag for Open Graph tag
     */
    private function getSCGraphProperty($graphField, $headerGraphString)
    {
        $keyValue = explode('=', $graphField, 2);
        if (count($keyValue) == 2)
        {
            $graphName = strtolower(trim($keyValue[0]));
            $graphValue = trim($keyValue[1]);

            if (strpos($graphName, ':') === false)
            {
                if ($graphName == 'admins' || $graphName == 'app_id')
                    $graphName = 'fb:' . $graphName;
                else
                    $graphName = 'og:' . $graphName;
            }

            if (strpos($headerGraphString, $graphName) === false || $graphName == 'og:image')
            {
                $graphValue = str_replace('"', "'", $graphValue);
                return '<meta property="' . $graphName . '" content="' . $graphValue . '"/>' . CARRIAGE_RETURN;
            }
            else
                return '';
        }

        return '';
    }

    /**
     * @return bool
     */
    private function isHomepage()
    {
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        return ($menu->getActive() == $menu->getDefault());
    }

}