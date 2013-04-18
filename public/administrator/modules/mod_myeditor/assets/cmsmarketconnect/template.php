<?php
defined('_JEXEC') or die();
/****************************************************
* CMS Market Connect
* Quickly add updatable version information, support page, and product links to your extension
* Copyright: CMS Market, LLC.  All rights reserved
* License: You may freely distribute or modify the contents of this file as long as:
*  1) This section remains intact
*  2) Any output derived from these functions to an end user must have one of the following attributions contained within a link to "http://www.cmsmarket.com/":
*   a) The text "CMS Market" or similar, ex: "Information provided by CMS Market" etc.
*   b) The CMS Market connect logo, available at "http://www.cmsmarket.com/images/cmsmconnect_logo.jpg"
*
* VERSION: 1.1
****************************************************/

echo '<style>
.rating_star_user {
background:transparent url(http://www.cmsmarket.com/components/com_jreviews/jreviews/views/themes/default/theme_css/images/rating_star_empty.png) repeat-x scroll 0 0 !important;
width:60px;
margin:3px;
}
.rating_star_user div {
background:transparent url(http://www.cmsmarket.com/components/com_jreviews/jreviews/views/themes/default/theme_css/images/rating_star_green.png) repeat-x scroll 0 0 !important;
font-size: 1px;
height: 12px;
}
</style>';
if ($this->dataReturned)
	$height = "150";
else
	$height = "85";

echo '<div id="cmsmarket_connect" style="height: '.$height.'px; margin:0 10px 0 0; border:1px solid #F6F6F6; padding:10px; text-align: left;">';
// echo '<div id="cmsmarket_connect" style="height: '.$height.'px; margin:0 10px 0 0; padding:10px; text-align: left;">';
echo '<div class="cmsmarket_intro">Current information from</div>';
// get logo location
$imgUrl = JRoute::_(dirname(__FILE__));
$imgUrl = str_replace(JPATH_ROOT.DS, "", $imgUrl);
$imgUrl = str_replace(DS, "/", $imgUrl);
$imgUrl = JURI::Root().$imgUrl;

echo '<div class="cmsmarket_title"><p style="margin:0 0 5px 0;"><a href="http://www.cmsmarket.com/" target="_blank"><img alt="Information Provided by CMS Market" src="'.$imgUrl.'/cmsmconnect_logo.png" /></a></p></div>';

if ($this->dataReturned)
{
	echo '<div class="cmsmarket_product"><h3 style="margin:0 0 5px 0;"><a href="'.$this->url.'" target="_blank">'.$this->name.'</a></h3></div>';
	echo '<div class="cmsmarket_currentversion uptodate">Current Version: '.$this->currentVersion.'</div>';
	if ($this->currentVersion != $this->installedVersion)
		echo '<div class="cmsmarket_installedversion outofdate" style="color:#DD0000">Your Version: '.$this->installedVersion.'</div>';
	else
		echo '<div class="cmsmarket_installedversion" style="color:#186D18">Your Version: '.$this->installedVersion.'</div>';
	echo '<div class="cmsmarket_rating"><a href="'.$this->url.'#review" target="_BLANK">'.$this->rating.'</a></div>';
	echo '<div class="cmsmarket_support"><a href="'.$this->supportPage.'" target="_BLANK">Support Page</a></div>';
}
else
{
	echo '<div class="cmsmarket_installedversion" style="color:#186D18">Your Version: '.$this->installedVersion.'</div>';
	echo '<div class="cmsmarket_product"><center>Online product information not available</center></div>';
}

echo '<div style="clear: both;"></div>';
echo '</div>';
?>
