<?php


/**
* JoomlaWatch - A real-time ajax joomla monitor and live stats
* @version 1.2.x
* @package JoomlaWatch
* @license http://www.gnu.org/licenses/gpl-3.0.txt 	GNU General Public License v3
* @copyright (C) 2007 by Matej Koval - All rights reserved! 
* @website http://www.codegravity.com
**/

/** ensure this file is being included by a parent file */
if( !defined( '_JEXEC' ) && !defined( '_VALID_MOS' ) ) die( 'Restricted access' ); 

error_reporting(0);

function com_uninstall() {
	global $database;

	if (@ !defined('JPATH_BASE')) {
		define('_JEXEC', 1);
		$dirname = dirname(__FILE__);
		$dirnameExploded = explode(DIRECTORY_SEPARATOR, $dirname);
		$jBasePath = "";
		$omitLast = 3;
		for ($i = 0; $i < sizeof($dirnameExploded) - $omitLast; $i++) {
			$jBasePath .= $dirnameExploded[$i];
			if ($i < (sizeof($dirnameExploded) - ($omitLast +1)))
				$jBasePath .= DIRECTORY_SEPARATOR;
		}
		define('JPATH_BASE', $jBasePath);
		define('DS', DIRECTORY_SEPARATOR);
	}

	if (@ file_exists(JPATH_BASE . DIRECTORY_SEPARATOR . "globals.php"))
		@ define('JOOMLAWATCH_JOOMLA_15', 0);
	else
		@ define('JOOMLAWATCH_JOOMLA_15', 1);

	if (JOOMLAWATCH_JOOMLA_15) {
		if (!defined('JPATH_ROOT'))
			@require_once (JPATH_BASE . DS . 'includes' . DS . 'defines.php');
		if (!defined('JDEBUG')) 
			@require_once (JPATH_BASE . DS . 'includes' . DS . 'framework.php');
		$mainframe = & JFactory :: getApplication('site');
		$mainframe->initialise();
		$database = & JFactory :: getDBO();
	} else {
		// defines for Joomla 1.0
	}

	$query = "DROP TABLE #__joomlawatch";
	$database->setQuery(trim($query));
	$database->query();

	$query = "DROP TABLE #__joomlawatch_info";
	$database->setQuery(trim($query));
	$database->query();

	$query = "DROP TABLE #__joomlawatch_config";
	$database->setQuery(trim($query));
	$database->query();

	$query = "DROP TABLE #__joomlawatch_blocked";
	$database->setQuery(trim($query));
	$database->query();

	$query = "DROP TABLE #__joomlawatch_ip2c";
	$database->setQuery(trim($query));
	$database->query();

	$query = "DROP TABLE #__joomlawatch_cc2c";
	$database->setQuery(trim($query));
	$database->query();

	$query = "DROP TABLE #__joomlawatch_uri";
	$database->setQuery(trim($query));
	$database->query();

	$query = "DROP TABLE #__joomlawatch_cache";
	$database->setQuery(trim($query));
	$database->query();

	$query = "DROP TABLE #__joomlawatch_goals";
	$database->setQuery(trim($query));
	$database->query();

	$query = "DROP TABLE #__joomlawatch_internal";
	$database->setQuery(trim($query));
	$database->query();

	$query = "DROP TABLE #__joomlawatch_uri2title";
	$database->setQuery(trim($query));
	$database->query();

	echo "JoomlaWatch component successfully uninstalled.";
}
?>