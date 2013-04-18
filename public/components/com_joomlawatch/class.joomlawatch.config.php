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

class JoomlaWatchConfig {

	var $database;

	function JoomlaWatchConfig() {
		global $database;
		if (!JOOMLAWATCH_JOOMLA_15) // if Joomla 1.0
		$this->database = $database;
		else
		$this->database = & JFactory :: getDBO();

	}

	/**
	 * config
	 */
	function checkPermissions() {
		$rand = $this->getRand();
		if ($rand != addslashes(strip_tags(@ $_GET['rand'])))
		die(_JW_ACCESS_DENIED);
	}


	/**
	 * config
	 *
	 * @return unknown
	 */
	function getRand() {
		$query = sprintf("select value from #__joomlawatch_config where name = 'rand' order by id desc limit 1; ");
		$this->database->setQuery($query);
		$rand = $this->database->loadResult();
		return $rand;

	}

	/**
	 * config
	 */
	function isIgnored($name, $key) {
		if (!@$key) {
			return false;
		}
		$name = strtoupper($name); 
		$query = sprintf("select value from #__joomlawatch_config where name='JOOMLAWATCH_IGNORE_".$name."' limit 1");
		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();
		$rowValue = $this->database->loadResult();
		$exploded = explode("\n", $rowValue);
		foreach ($exploded as $value) {
			if (JoomlaWatchHelper::wildcardSearch(trim($value), $key)) {
				return true;
			}
		}
		return false;
	}

	
	/**
	 * config
	 */
	function updateHelperCountByKey($key, $value) {
		$count = $this->getCountByKey($key);

		if (@ $count) {
			$query = sprintf("update #__joomlawatch_config set value = '%s' where (name = '%s' and date = '%d')", JoomlaWatchHelper::sanitize($value), JoomlaWatchHelper::sanitize($key), (int) $date);
			$this->database->setQuery($query);
			$this->database->query();
		} else {
			$query = sprintf("insert into #__joomlawatch_config values ('', '%s', '%s')", JoomlaWatchHelper::sanitize($key), JoomlaWatchHelper::sanitize($value));
			$this->database->setQuery($query);
			$this->database->query();
		}
	}

	/**
	 * config
	 */

	function getConfigValue($key) {

		$query = sprintf("select value from #__joomlawatch_config where name = '%s' limit 1", JoomlaWatchHelper::sanitize($key));
		$this->database->setQuery($query);
		$this->database->query();
		$value = $this->database->loadResult();
		// explicit off for checkboxes
		if ($value == "Off") {
			return false;
		}
		if ($value) {
			return addslashes($value);
		} 
		
		return constant($key);
	}

	/**
	 * config
	 */
	function saveConfigValue($key, $value) {
		$query = sprintf("select count(name) as count from #__joomlawatch_config where name = '%s' limit 1", JoomlaWatchHelper::sanitize($key));
		$this->database->setQuery($query);
		$this->database->query();
		$count = $this->database->loadResult();

		if ($count) { //update
			$query = sprintf("update #__joomlawatch_config set value = '%s' where name = '%s'", JoomlaWatchHelper::sanitize($value), JoomlaWatchHelper::sanitize($key));
			$this->database->setQuery($query);
			$this->database->query();
		} else { //insert
			$query = sprintf("insert into #__joomlawatch_config values ('','$key','$value')", JoomlaWatchHelper::sanitize($key), JoomlaWatchHelper::sanitize($value));
			$this->database->setQuery($query);
			$this->database->query();

		}

	}

	/**
	 * config
	 */
	function getLanguage() {
		$language = $this->getConfigValue("JOOMLAWATCH_LANGUAGE");
		return $language;
	}

	/**
	 * config
	 */
	function checkLicenseAccepted() {
		$accepted = $this->getConfigValue("JOOMLAWATCH_LICENSE_ACCEPTED");
		if (@ $accepted) {
			return true;
		}
		return false;
	}


	/**
	 * config
	 */
	function setLicenseAccepted() {
		$this->saveConfigValue("JOOMLAWATCH_LICENSE_ACCEPTED", "1");
	}

	/**
	 * config
	 */
	function getCheckboxValue($key) {
		$setting = $this->getConfigValue($key);
		if ($setting == '1' || strtolower($setting) == 'on') {
			return true;
		}
		return false;
	}


}

?>