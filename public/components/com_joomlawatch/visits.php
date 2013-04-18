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
	require_once (JPATH_BASE . DS . 'includes' . DS . 'joomla.php');
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

//for joomla 1.0 only if (file_exists($mosConfig_absolute_path."/language/english.php")) require_once ("../../language/english.php");

$joomlaWatch = new JoomlaWatch();
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "lang" . DS . $joomlaWatch->config->getLanguage().".php");

$joomlaWatchHTML = new JoomlaWatchHTML("/components/com_joomlawatch");
$joomlaWatch->config->checkPermissions();

$t1 = ($joomlaWatch->helper->getServerTime()+ microtime());

$last = $joomlaWatch->visit->getLastVisitId();

echo ("$last\n\n");

if (JOOMLAWATCH_JOOMLA_15) {
	require_once (JPATH_BASE . DS . 'administrator' . DS . 'includes' . DS . 'helper.php');
	$params = new JParameter('');
	include (JPATH_BASE . DS . "modules" . DS . "mod_whosonline" . DS . "mod_whosonline.php");
} else {
	if (@ file_exists(JPATH_BASE . DS . "language" . DS . "english.php"))
		require_once (JPATH_BASE . DS . "language" . DS . "english.php");
	$params = new MosParameters('');
	echo("<span style='color: gray;'>");
	include (JPATH_BASE . DS . "modules" . DS . "mod_whosonline.php");
	echo("</span>");
}
?>
<span style='color: black;'>&nbsp;<?php echo ($joomlaWatch->helper->getActualDateTime()); ?></span>

<?php
if (!$joomlaWatch->helper->isModulePublished()) echo("<h4 style='color: red;'>"._JW_VISITS_MODULE_NOT_PUBLISHED."</h4>");


echo ("<br/>");
$today = floor($joomlaWatch->helper->getServerTime()/ 24 / 3600);
$thisWeek = floor($joomlaWatch->helper->getServerTime()/ 24 / 3600 / 7);
if (@ $_GET['day'])
	$day = @ $_GET['day'];
else
	$day = floor($joomlaWatch->helper->getServerTime()/ 24 / 3600);

if (@ $_GET['week'])
	$week = @ $_GET['week'];
else
	$week = floor($joomlaWatch->helper->getServerTime()/ 24 / 3600 / 7);

$prev = $day -1;
$next = $day +1;
$prevWeek = $week -1;
$nextWeek = $week +1;
?>

<table cellpadding='2' cellspacing='0' width='100%' border='0'>
<tr><td colspan='8'><h3><?php echo _JW_VISITS_VISITORS; echo $joomlaWatchHTML->renderOnlineHelp("visits"); ?></h3></td></tr>
<?php echo ($joomlaWatchHTML->renderVisitors()); ?>
<tr><td>&nbsp;</td></tr>
<tr><td colspan='8'><h3><?php echo _JW_VISITS_BOTS; echo $joomlaWatchHTML->renderOnlineHelp("visits-bots"); ?></h3></td></tr>
<?php echo ($joomlaWatchHTML->renderBots()); ?>
</table>



<!-- rendered in <?php echo((time()+microtime())-$t1); ?>s -->

