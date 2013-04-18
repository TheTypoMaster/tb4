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
$jBasePath = dirname(__FILE__).DS."..".DS."..".DS;
define('JPATH_BASE', $jBasePath);

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

$joomlaWatchHTML = new JoomlaWatchHTML("/components/com_joomlawatch");
$joomlaWatch->config->checkPermissions();

$t1 = ($joomlaWatch->helper->getServerTime()+ microtime());

$last = $joomlaWatch->visit->getLastVisitId();

//$params = new MosParameters("");
$thisWeek = $joomlaWatch->helper->getWeekByTimestamp($joomlaWatch->helper->getServerTime());
if (@ $_GET['day']) {
	$day = @ $_GET['day'];
} else {
	$day = $joomlaWatch->helper->jwDateToday();
}

if (@ $_GET['week']) {
	$week = @ $_GET['week'];
}
else {
	$week = $joomlaWatch->helper->getWeekByTimestamp($joomlaWatch->helper->getServerTime());
}

$prevWeek = $week -1;
$nextWeek = $week +1;
?>
<table border='0' cellpadding='1' cellspacing='0' width='100%'>

<tr><td colspan='5'>
<h3><?php echo _JW_STATS_TITLE."&nbsp;"; echo(date("W",$week*3600*24*7)); ?>/<?php echo(date("Y",$week*3600*24*7)); ?>
<?php echo $joomlaWatchHTML->renderOnlineHelp("visit-stats"); ?></h3>
</td></tr>
<tr><td colspan='5'>
	<table border='0'>
	<tr><td align='left' width='10%'><?php echo("<a href='javascript:setWeek($prevWeek)' id='visits_$prevWeek'>&lt;"._JW_STATS_WEEK."&nbsp;".date("W",$prevWeek*3600*24*7)."</a></td><td align='left'><img src='$joomlaWatchHTML->mosConfig_live_site/components/com_joomlawatch/icons/calendar.gif' border='0' align='center' />"); ?></td>
	<td align='center' width='20%'><?php if (@$week != $thisWeek)echo("<a href='javascript:setWeek($thisWeek)' id='visits_$thisWeek'>"._JW_STATS_THIS_WEEK."</a>"); ?></td>
	<td align='right' width='10%'><?php if ($nextWeek <= $thisWeek) echo("<img src='$joomlaWatchHTML->mosConfig_live_site/components/com_joomlawatch/icons/calendar.gif' border='0' align='center' /></td><td width='20%' align='right'><a href='javascript:setWeek($nextWeek)' id='visits_$nextWeek'>"._JW_STATS_WEEK."&nbsp;".date("W",$nextWeek*3600*24*7)."&gt;</a>"); ?></td>
	</tr>
	</table>
<?php echo $joomlaWatchHTML->renderVisitsGraph($week); ?>
<tr><td colspan='4'>

<table width='100%'>
	<tr>
	<td align='center' class='<?php echo $joomlaWatchHTML->renderTabClass("0", @$_GET['tab']);?>'>
	<?php echo $joomlaWatchHTML->renderSwitched("0", _JW_STATS_DAILY, @$_GET['tab']); ?>
	</td>
	<td align='center' class='<?php echo $joomlaWatchHTML->renderTabClass("1", @$_GET['tab']);?>'> 
	<?php echo $joomlaWatchHTML->renderSwitched("1", _JW_STATS_ALL_TIME, @$_GET['tab']); ?>
	</td>
	<td align='center' class='tab_none'> 
	</td>
	</tr>
</table>


<?php if (@$_GET['tab'] == "1") { ?>
<tr><td colspan='5'><h3><?php echo(_JW_STATS_ALL_TIME_TITLE); echo $joomlaWatchHTML->renderOnlineHelp("all-time-stats"); ?></h3></td>
<?php
foreach ($keysArray as $key) {
	if ($key == 'ip' && !$joomlaWatch->config->getConfigValue('JOOMLAWATCH_IP_STATS')) {
		continue;
	}
	?>
<tr><td colspan='4'><u><?php echo(_JW_STATS_ALL_TIME)."&nbsp;"; echo (@constant('_JW_STATS_'.strtoupper($key))); ?></u></td></tr>
<tr><td  valign='top'><?php echo $joomlaWatchHTML->renderIntValuesByName($key, @$_GET[$key."_total"], true); ?></td></tr>
<tr><td colspan='4'>&nbsp;</td></tr>

<?php
}
?>

<?php } else { ?>
	
<h3><?php echo(_JW_STATS_DAILY_TITLE)."&nbsp;"; echo $joomlaWatch->helper->getDateByDay($day);?><?php echo $joomlaWatchHTML->renderOnlineHelp("daily-stats"); ?></h3>
<?php echo $joomlaWatchHTML->renderDateControl(); ?>

<?php
foreach ($keysArray as $key) {
	if ($key == 'ip' && !$joomlaWatch->config->getConfigValue('JOOMLAWATCH_IP_STATS')) {
		continue;
	}
	?>
<tr><td colspan='4'><u><?php echo (@constant('_JW_STATS_'.strtoupper($key)))."&nbsp;"; echo(_JW_STATS_FOR)."&nbsp;"; echo $joomlaWatch->helper->getDateByDay($day);?></u></td></tr>
<tr><td  valign='top'><?php echo $joomlaWatchHTML->renderIntValuesByName($key, @$_GET[$key], false, $day ); ?></td></tr>
<tr><td colspan='4'>&nbsp;</td></tr>
<?php
}
?>

</table>

<?php } ?>

</td>
</tr>
</table>


<?php if (@$_GET['tab'] == "1") { ?>
	<a href='javascript:blockIpManually();'><?php echo(_JW_STATS_IP_BLOCKING_ENTER);?></a><br/>
	<table>
		<?php echo($joomlaWatchHTML->renderBlockedIPs(0, $_GET['ip_blocking_title_total'])); ?>
	</table>
<br/><hr size='1' width='100%'/>

<?php } else { ?>
		<a href='javascript:blockIpManually();'><?php echo(_JW_STATS_IP_BLOCKING_ENTER);?></a><br/>
	<table>
		<?php echo($joomlaWatchHTML->renderBlockedIPs($day, $_GET['ip_blocking_title'])); ?>
	</table>
	<br/><hr size='1' width='100%'/>
	<?php echo $joomlaWatchHTML->renderDateControl(); ?>
<?php } ?>



<br/><br/>
	<span style='color: #cccccc;'>JoomlaWatch &copy;2006-2009 by Matej Koval</span>

<!-- rendered in <?php echo((time()+microtime())-$t1); ?>s -->
