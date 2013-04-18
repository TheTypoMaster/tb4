<?php

/**
* JoomlaWatch - A real-time ajax joomla monitor and live stats
* @version 1.2.0
* @package JoomlaWatch
* @license http://www.gnu.org/licenses/gpl-3.0.txt 	GNU General Public License v3
* @copyright (C) 2007 by Matej Koval - All rights reserved! 
* @website http://www.codegravity.com
**/

/** ensure this file is being included by a parent file */
if (!defined('_JEXEC') && !defined('_VALID_MOS'))
die('Restricted access');

error_reporting(0);
if (JOOMLAWATCH_DEBUG) {
	error_reporting(E_ALL);
}

class JoomlaWatchCache {

	var $database;
	var $helper;
	var $config;

	function JoomlaWatchCache() {
		global $database;
		if (!JOOMLAWATCH_JOOMLA_15) // if Joomla 1.0
		$this->database = $database;
		else
		$this->database = & JFactory :: getDBO();
		$this->helper = new JoomlaWatchHelper();
		$this->config = new JoomlaWatchConfig();
	}




	/**
	 * cache
	 */
	function getCachedItem($key) {
		$time = $this->helper->getServerTime();
		$query = sprintf("select cache, lastUpdate from #__joomlawatch_cache where `key` = '%s' limit 1", JoomlaWatchHelper::sanitize($key));
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		$row = @ $rows[0];
		$cacheInterval = @ $this->config->getConfigValue('JOOMLAWATCH_'.$key);
		if ($time - @ $cacheInterval < @ $row->lastUpdate) {
			return @ $row->cache;
		} else {
			return false;
		}

	}

	/**
	 * cache
	 */
	function storeCachedItem($key, $cache) {
		$query = sprintf("select cache, lastUpdate from #__joomlawatch_cache where `key` = '%s' limit 1", JoomlaWatchHelper::sanitize($key));
		$this->database->setQuery($query);
		$this->database->query();
		$rows = @ $this->database->loadObjectList();
		$row = @ $rows[0];
		$time = $this->helper->getServerTime();
		if (!@ $row->cache) {
			// insert
			$cache = addslashes($cache);
			$query = sprintf("insert into #__joomlawatch_cache (id, `key`, lastUpdate, cache) values ('','%s', '%d', '%s' )", JoomlaWatchHelper::sanitize($key), (int) $time, JoomlaWatchHelper::sanitize($cache));
			$this->database->setQuery($query);
			$this->database->query();
		} else {

			$cache = addslashes($cache);
			$query = sprintf("update #__joomlawatch_cache set lastUpdate = '%d', cache = '%s' where `key` = '%s' limit 1", (int) $time, JoomlaWatchHelper::sanitize($cache), JoomlaWatchHelper::sanitize($key));
			$this->database->setQuery($query);
			$this->database->query();
		}

	}



	/**
	 * cache
	 */
	function clearCache() {
		$query = sprintf("delete from #__joomlawatch_cache");
		$this->database->setQuery($query);
		$result1 = $this->database->query();
	}
}

?>