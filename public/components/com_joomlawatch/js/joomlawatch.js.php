<?php
/**
* JoomlaWatch - A real-time ajax joomla monitor and live stats
* @version 1.2.x
* @package JoomlaWatch
* @license http://www.gnu.org/licenses/gpl-3.0.txt 	GNU General Public License v3
* @copyright (C) 2008 by Matej Koval - All rights reserved! 
* @website http://www.codegravity.com
**/

error_reporting(E_ALL);

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
$jBasePath = dirname(__FILE__).DS."..".DS."..".DS."..".DS;
define('JPATH_BASE', $jBasePath);
define('JPATH_BASE2', $jBasePath);

if (@ file_exists(JPATH_BASE . DIRECTORY_SEPARATOR . "globals.php"))
@ define('JOOMLAWATCH_JOOMLA_15', 0);
else
@ define('JOOMLAWATCH_JOOMLA_15', 1);

if (JOOMLAWATCH_JOOMLA_15) {
	if (!defined('JPATH_ROOT'))
	require_once (JPATH_BASE . DS . 'includes' . DS . 'defines.php');
	if (!defined('JDEBUG'))
	require_once (JPATH_BASE . DS . 'includes' . DS . 'framework.php');
	require_once (JPATH_BASE . DS . 'libraries' . DS . 'joomla' . DS . 'application' . DS . 'module' . DS . 'helper.php');
	$mainframe = & JFactory :: getApplication('site');
	$mainframe->initialise();
} else {
	define('_VALID_MOS', 1);
	require_once (JPATH_BASE . DS . 'globals.php');
	require_once (JPATH_BASE . DS . 'configuration.php');
	require_once (JPATH_BASE . DS . 'includes'.DS.'joomla.php');
}

require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "config.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.joomlawatch.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.joomlawatch.block.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.joomlawatch.cache.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.joomlawatch.config.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.joomlawatch.goal.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.joomlawatch.helper.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.joomlawatch.stat.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.joomlawatch.visit.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.joomlawatch.html.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.ip2country.php");

$joomlaWatch = new JoomlaWatch();
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "lang" . DS . $joomlaWatch->config->getLanguage().".php");

$joomlaWatchHTML = new JoomlaWatchHTML("/components/com_joomlawatch/js");
$joomlaWatch->config->checkPermissions();
?>

var rand='<?php echo $joomlaWatch->config->getRand(); ?>';

var lastTimeoutId = null;
var statsTimeoutId = null;
var visitsTimeoutId = null;
var refreshStopped = false;

var last=null;
var lastId=null;
var http=null;
var day = 0;
var week = 0;
var expanded = new Array();
var statsType = "0";

var traffic = 0;

function setDay(_day) {
 day = _day;
 document.getElementById(_day).innerHTML = "<?php echo(_JW_STATS_LOADING_WAIT);?>";	
 sendStatsReq();
}
function setStatsType(_statsType) {
 statsType = _statsType;
 document.getElementById(_statsType).innerHTML = "<?php echo(_JW_STATS_LOADING);?>";	
 sendStatsReq();
}
function setWeek(_week) {
 week = _week;
 document.getElementById("visits_" + _week).innerHTML = "<?php echo(_JW_STATS_LOADING_WAIT);?>";	
 sendStatsReq();
}

function createRequestObject() {
    var ro;
    if(window.ActiveXObject){
        ro = new ActiveXObject("Microsoft.XMLHTTP");
    }else{
        ro = new XMLHttpRequest();
    }
    return ro;
}

function sendLastIdReq() {
try {
    http4 = createRequestObject();
    http4.onreadystatechange = needLastIdRefresh;
    var newdate = new Date();	
    var url = "<?php echo($joomlaWatchHTML->mosConfig_live_site);?>/components/com_joomlawatch/last.php?rand=" + rand + "&timeID="+newdate.getTime() + "&traffic="+traffic;
    http4.open("GET", url, true);
    http4.send(null);
}
catch (err) {
try {
if ((window.ActiveXObject && err.message.substring(0,17) == "Permission denied") || (!window.ActiveXObject  && err.substring(0,17) == "Permission denied"))
alert("<?php echo(_JW_AJAX_PERMISSION_DENIED_1);?>&nbsp;<?php echo($joomlaWatchHTML->mosConfig_live_site);?>&nbsp;<?php echo(_JW_AJAX_PERMISSION_DENIED_2);?>&nbsp;<?php echo($joomlaWatchHTML->mosConfig_live_site);?>&nbsp;<?php echo(_JW_AJAX_PERMISSION_DENIED_3);?>&nbsp;<?php echo(str_replace("www.","",$joomlaWatchHTML->mosConfig_live_site));?><?php echo(_JW_AJAX_PERMISSION_DENIED_4);?>");
} catch(err2) {
}
}
}

function sendVisitsReq() {
try {
    http = createRequestObject();
    http.onreadystatechange = needVisitsRefresh;
    var newdate = new Date();	
    var url = "<?php echo($joomlaWatchHTML->mosConfig_live_site);?>/components/com_joomlawatch/visits.php?rand=" + rand + "&timeID="+newdate.getTime() + "&traffic="+traffic;
    http.open("GET", url, true);
    http.send(null);
}
catch (err) { 
try {
if ((window.ActiveXObject && err.message.substring(0,17) == "Permission denied") || (!window.ActiveXObject  && err.substring(0,17) == "Permission denied"))
alert("<?php echo(_JW_AJAX_PERMISSION_DENIED_1);?>&nbsp;<?php echo($joomlaWatchHTML->mosConfig_live_site);?>&nbsp;<?php echo(_JW_AJAX_PERMISSION_DENIED_2);?>&nbsp;<?php echo($joomlaWatchHTML->mosConfig_live_site);?>&nbsp;<?php echo(_JW_AJAX_PERMISSION_DENIED_3);?>&nbsp;<?php echo(str_replace("www.","",$joomlaWatchHTML->mosConfig_live_site));?><?php echo(_JW_AJAX_PERMISSION_DENIED_4);?>");
} catch(err2) {
}
}

}

function sendStatsReq() {
try {
    http2 = createRequestObject();
    http2.onreadystatechange = needStatsRefresh;
    var newdate = new Date();	
    var url = "<?php echo($joomlaWatchHTML->mosConfig_live_site);?>/components/com_joomlawatch/stats.php?rand=" + rand + "&timeID="+newdate.getTime() + "&traffic="+traffic;
    if (day != 0) url += "&day="+day;
    if (week != 0) url += "&week="+week;
    
    <?php

    foreach($keysArray as $key) {
    	echo("if (expand['".$key."']) url += '&".$key."=true';");
    }
    foreach($keysArray as $key) {
    	echo("if (expand['".$key."_total']) url += '&".$key."_total=true';");
    }
    ?>
    
    url += "&tab="+statsType;
    http2.open("GET", url, true);
    http2.send(null);
}
catch (err) {
}
}


function blockIpToggle(_ip) {
try {
	if (confirm("<?php echo(_JW_STATS_IP_BLOCKING_TOGGLE);?> " + _ip + " ?")) {	
    http3 = createRequestObject();
    http3.onreadystatechange = needStatsRefresh;
    var newdate = new Date();	
    var url3 = "<?php echo($joomlaWatchHTML->mosConfig_live_site);?>/components/com_joomlawatch/block.php?ip="+ _ip +"&rand=" + rand + "&timeID="+newdate.getTime();
    http3.open("GET", url3, true);
    http3.send(null);
	sendStatsReq();
	sendVisitsReq();
	}
}
catch (err) {
}
}

function blockIpManually() {
try {
	var ipManual = prompt("<?php echo(_JW_STATS_IP_BLOCKING_MANUALLY);?>","");
	if (ipManual) blockIpToggle(ipManual);
	}
catch (err) {
}
}

function expand(_element) {
	if (!expand[_element]) expand[_element] = true;
		else expand[_element] = false;
	document.getElementById(_element).innerHTML = "<?php echo(_JW_STATS_LOADING_WAIT);?>";	
	sendStatsReq();
}

function needLastIdRefresh() {
try {
if (http4.readyState == 4) 
  {
     if(http4.status == 200)
     {
         if (http4.responseText != lastId) {
         	sendVisitsReq();
         	sendStatsReq();
            lastId = http4.responseText;
         }

	      lastTimeoutId = window.setTimeout("sendLastIdReq()",<?php echo($joomlaWatch->config->getConfigValue('JOOMLAWATCH_UPDATE_TIME_STATS'));?>);
      }
   }
} catch (err) {
}
}

function needVisitsRefresh()
{
try {
  if (http.readyState == 4) 
  {
     if(http.status == 200)
     {
         document.getElementById("visits").innerHTML = http.responseText;
         
         
         number = "";
         for (i=0 ; i<11; i++ ) {		
         if (http.responseText.charAt(i) == '\n') break;
         if (http.responseText.charAt(i) == '\r') break;
         if (http.responseText.charAt(i) == ' ') break;
         
         number = number + http.responseText.charAt(i);
         }
         
         number = number.replace(/(<([^>]+)>)/ig,"");	
         parsedNumber = parseInt(number);
         
         if (last != parsedNumber) {
         	   last = parsedNumber;
         	fade("id" + last);
         }
         
     }
       traffic += http.responseText.length;
       
	   
  }
} catch (err) {
}
}

function needStatsRefresh()
{
try {
  if (http2.readyState == 4) 
  {
     if(http2.status == 200)
     {
         document.getElementById("stats").innerHTML = http2.responseText;
         traffic += http2.responseText.length;
     }
  }
} catch (err) {
}
}
/* Custom variables */

/* Offset position of tooltip */
var x_offset_tooltip = 5;
var y_offset_tooltip = 0;

/* Don't change anything below here */


var ajax_tooltipObj = false;
var ajax_tooltipObj_iframe = false;

var ajax_tooltip_MSIE = false;
if(navigator.userAgent.indexOf('MSIE')>=0)ajax_tooltip_MSIE=true;


function ajax_showTooltip(externalFile,inputObj)
{
	window.clearInterval(visitsTimeoutId);
	window.clearInterval(statsTimeoutId);
	refreshStopped = true;

	if(!ajax_tooltipObj)	/* Tooltip div not created yet ? */
	{
		ajax_tooltipObj = document.createElement('DIV');
		ajax_tooltipObj.style.position = 'absolute';
		ajax_tooltipObj.id = 'ajax_tooltipObj';		
		document.body.appendChild(ajax_tooltipObj);

		
		var leftDiv = document.createElement('DIV');	/* Create arrow div */
		leftDiv.className='ajax_tooltip_arrow';
		leftDiv.id = 'ajax_tooltip_arrow';
		ajax_tooltipObj.appendChild(leftDiv);
		
		var contentDiv = document.createElement('DIV'); /* Create tooltip content div */
		contentDiv.className = 'ajax_tooltip_content';
		ajax_tooltipObj.appendChild(contentDiv);
		contentDiv.id = 'ajax_tooltip_content';
		
		if(ajax_tooltip_MSIE){	/* Create iframe object for MSIE in order to make the tooltip cover select boxes */
			ajax_tooltipObj_iframe = document.createElement('<IFRAME frameborder="0">');
			ajax_tooltipObj_iframe.style.position = 'absolute';
			ajax_tooltipObj_iframe.border='0';
			ajax_tooltipObj_iframe.frameborder=0;
			ajax_tooltipObj_iframe.style.backgroundColor='#FFF';
			ajax_tooltipObj_iframe.src = 'about:blank';
			contentDiv.appendChild(ajax_tooltipObj_iframe);
			ajax_tooltipObj_iframe.style.left = '0px';
			ajax_tooltipObj_iframe.style.top = '0px';
		}

			
	}
	// Find position of tooltip
	ajax_tooltipObj.style.display='block';
	ajax_loadContent('ajax_tooltip_content',externalFile);
	if(ajax_tooltip_MSIE){
		ajax_tooltipObj_iframe.style.width = ajax_tooltipObj.clientWidth + 'px';
		ajax_tooltipObj_iframe.style.height = ajax_tooltipObj.clientHeight + 'px';
	}

	ajax_positionTooltip(inputObj);
}

function ajax_positionTooltip(inputObj)
{
	var leftPos = (ajaxTooltip_getLeftPos(inputObj) + inputObj.offsetWidth);
	var topPos = ajaxTooltip_getTopPos(inputObj);
	
	/*
	var rightedge=ajax_tooltip_MSIE? document.body.clientWidth-leftPos : window.innerWidth-leftPos
	var bottomedge=ajax_tooltip_MSIE? document.body.clientHeight-topPos : window.innerHeight-topPos
	*/
	var tooltipWidth = document.getElementById('ajax_tooltip_content').offsetWidth +  document.getElementById('ajax_tooltip_arrow').offsetWidth; 
	// Dropping this reposition for now because of flickering
	//var offset = tooltipWidth - rightedge; 
	//if(offset>0)leftPos = Math.max(0,leftPos - offset - 5);
	
	ajax_tooltipObj.style.left = leftPos + 'px';
	ajax_tooltipObj.style.top = topPos + 'px';	
	
	
}


function ajax_hideTooltip()
{
	ajax_tooltipObj.style.display='none';
	if (refreshStopped) {
		visitsTimeoutId = window.setTimeout("sendVisitsReq()",<?php echo($joomlaWatch->config->getConfigValue('JOOMLAWATCH_UPDATE_TIME_VISITS'));?>);
		statsTimeoutId = window.setTimeout("sendStatsReq()",<?php echo($joomlaWatch->config->getConfigValue('JOOMLAWATCH_UPDATE_TIME_STATS'));?>);
		refreshStopped = false;	
	}
}

function ajaxTooltip_getTopPos(inputObj)
{		
  var returnValue = inputObj.offsetTop;
  while((inputObj = inputObj.offsetParent) != null){
  	if(inputObj.tagName!='HTML')returnValue += inputObj.offsetTop;
  }
  return returnValue;
}

function ajaxTooltip_getLeftPos(inputObj)
{
  var returnValue = inputObj.offsetLeft;
  while((inputObj = inputObj.offsetParent) != null){
  	if(inputObj.tagName!='HTML')returnValue += inputObj.offsetLeft;
  }
  return returnValue;
}
function setElementValueById(_id, _value) {
  document.getElementById(_id).value = _value;	
}
function addElementValueById(_id, _value) {
  value = document.getElementById(_id).value;
  if (value) document.getElementById(_id).value +="\n"; 
  document.getElementById(_id).value += _value;	
}
	

function toggleDiv(id,flagit) {
	
	if (flagit=="1"){
			document.getElementById(id).style.display = "block";
	}
	else
	if (flagit=="0"){
			document.getElementById(id).style.display = "none";
	}
	
}

function makeLoadingDisappear() {
	try {	
		document.getElementById('loading').style.display='';
	} catch (err) {
	}
}

