<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2012 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$jfbcConfigModel = $fbClient->getConfigModel();

//Affiliate ID
$link = 'http://www.sourcecoast.com/jfbconnect/';
$affiliateID = $jfbcConfigModel->getSetting('affiliate_id');
if($affiliateID)
	$link .= '?amigosid='.$affiliateID;

//Powered By
$showPoweredBy = $params->get('showPoweredByLink');
$showJfbcPoweredBy = (($showPoweredBy == '2' && $jfbcConfigModel->getSetting('show_powered_by_link')) || ($showPoweredBy == '1'));

if($showJfbcPoweredBy)
{
    $lang = JFactory::getLanguage();
    $lang->load('com_jfbconnect');

    echo '<div class="powered_by">'.JText::_('COM_JFBCONNECT_POWERED_BY').' <a target="_blank" href="'.$link.'" title="Facebook for Joomla">JFBConnect</a></div>';
}