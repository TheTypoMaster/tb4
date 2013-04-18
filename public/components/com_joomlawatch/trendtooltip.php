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

sleep(1); // it's a tooltip. Let's sleep for a while

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
$jBasePath = dirname(__FILE__).DS."..".DS."..";
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
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "admin.joomlawatch.html.php");
require_once (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "class.ip2country.php");

$joomlaWatch = new JoomlaWatch();

$joomlaWatchHTML = new JoomlaWatchHTML("/components/com_joomlawatch");
$adminJoomlaWatchHTML = new AdminJoomlaWatchHTML("/administrator");
$joomlaWatch->config->checkPermissions();

require (JPATH_BASE . DS . "components" . DS . "com_joomlawatch" . DS . "lang" . DS . $joomlaWatch->config->getLanguage().".php");

echo $adminJoomlaWatchHTML->renderAdminStyles();
echo $joomlaWatchHTML->renderPrint(); 
echo $joomlaWatchHTML->renderCloseWindow();
echo $joomlaWatchHTML->renderTrends(); 
?>


