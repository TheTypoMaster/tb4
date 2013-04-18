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

class JoomlaWatchStat {

	var $database;
	
	var $config;
	var $helper;

	function JoomlaWatchStat() {
		global $database;
		if (!JOOMLAWATCH_JOOMLA_15) // if Joomla 1.0
		$this->database = $database;
		else
		$this->database = & JFactory :: getDBO();

		$this->config = new JoomlaWatchConfig();
		$this->helper = new JoomlaWatchHelper();
	}



	/**
	 * stats/info
	 */
	function increaseKeyValueInGroup($name, $key) {
		if (!@ $key)
		return;

		$date = $this->helper->jwDateToday();

		$query = sprintf("select count(id) as count from #__joomlawatch_info where (`group` = '%s' and name = '%s' and date = '%d') ", JoomlaWatchHelper::sanitize($name), JoomlaWatchHelper::sanitize($key), (int) $date);
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		$row = $rows[0];
		$count = @ $row->count;

		if (@ $count) {
			$query = sprintf("update #__joomlawatch_info set value = value+1 where (`group` = '%s' and name = '%s' and date = '%d') ", JoomlaWatchHelper::sanitize($name), JoomlaWatchHelper::sanitize($key), (int) $date);
			$this->database->setQuery($query);
			$this->database->query();
		} else {

			$query = sprintf("insert into #__joomlawatch_info (id, `group`, date, name, value) values ('', '%s', '%d', '%s', 1)", JoomlaWatchHelper::sanitize($name), (int) $date, JoomlaWatchHelper::sanitize($key));
			$this->database->setQuery($query);
			$this->database->query();

		}
	}

	/**
	 * stats/info
	 */
	function getMaxValueInGroupForWeek($name, $key, $dateWeekStart) {
		if (!@ $key)
		return;
		$dateWeekEnd = $dateWeekStart +7;

		$query = sprintf("select max(value) as value from #__joomlawatch_info where (`group` = '%s' and name = '%s' and `date` >= '%d' and `date` <= '%d') ", JoomlaWatchHelper::sanitize($name), JoomlaWatchHelper::sanitize($key), (int) $dateWeekStart, (int) $dateWeekEnd);
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		$row = @ $rows[0];
		$value = @ $row->value;

		return $value;
	}

	/**
	 * stats/info
	 */
	function getKeyValueInGroupByDate($group, $name, $date) {
		if (!@ $name)
		return;

		$query = sprintf("select id,value from #__joomlawatch_info where (`group` = '%s' and name = '%s' and date = '%d') ", JoomlaWatchHelper::sanitize($group), JoomlaWatchHelper::sanitize($name), (int) $date);
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		$row = @ $rows[0];
		$value = @ $row->value;

		return $value;
	}

	/**
	 * stats/info
	 */
	function getCountByKeyAndDate($group, $date) {
		$query = sprintf("select sum(value) as value from #__joomlawatch_info where (`group` = '%s' and date = '%d') order by id desc limit 1", JoomlaWatchHelper::sanitize($group), (int) $date);
		$this->database->setQuery($query);
		$count = $this->database->loadResult();
		return $count;
	}

	/**
	 * stats/info
	 */
	function getCountByKeyBetweenDates($group, $date1, $date2) {
		$query = sprintf("select sum(value) as value from #__joomlawatch_info where (`group` = '%s' and (`date`>'%d' and `date`<='%d') ) order by id desc limit 1", JoomlaWatchHelper::sanitize($group), (int) $date1, (int) $date2);
		$this->database->setQuery($query);
		$count = $this->database->loadResult();
		return $count;
	}

	/**
	 * stats/info
	 */
	function getTotalCountByKey($group) {
		$query = sprintf(" SELECT sum( value ) AS value FROM #__joomlawatch_info WHERE `group` = '%s' LIMIT 1 ", JoomlaWatchHelper::sanitize($group));
		$this->database->setQuery($query);
		$count = $this->database->loadResult();
		return $count;
	}

	/**
	 * stats/info
	 */
	function getTotalIntValuesByName($name, $expanded, $limit) {

		$maxLimit = $this->config->getConfigValue('JOOMLAWATCH_STATS_MAX_ROWS');
		if (@ $expanded == true) {
			$query = sprintf("select name, sum(value) as value from #__joomlawatch_info where (`group` = '%s') group by name order by value desc limit %d", JoomlaWatchHelper::sanitize($name), (int) $maxLimit);
		}
		else {
			$query = sprintf("select name, sum(value) as value from #__joomlawatch_info where (`group` = '%s') group by name order by value desc limit %d", JoomlaWatchHelper::sanitize($name), (int) $limit);
		}
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		return $rows;
	}

	/**
	 * stats/info
	 */
	function getIntValuesByName($name, $date, $expanded, $limit) {

		if ($date == "")
		$date = floor((time() + $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_DAY_OFFSET')) / 3600 / 24);

		if (@ $expanded == true)
		$query = sprintf("select name, value from #__joomlawatch_info where (`group` = '%s' and `date` = '%d') order by value desc limit 20", JoomlaWatchHelper::sanitize($name), (int) $date);
		else
		$query = sprintf("select name, value from #__joomlawatch_info where (`group` = '%s' and `date` = '%d') order by value desc limit $limit", JoomlaWatchHelper::sanitize($name), (int) $date);

		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		return $rows;
	}


	/**
	 * stats/info
	 */
	function getSumOfTwoDays($date1, $date2, $group, $name) {
		$query = sprintf("SELECT sum(value) as value FROM `#__joomlawatch_info` WHERE ( (`date`>'%d' and `date`<='%d') and `group` = '%s' and `name` = '%s')", (int) $date2, (int) $date1, JoomlaWatchHelper::sanitize($group), JoomlaWatchHelper::sanitize($name));
		$this->database->setQuery($query);
		$value = $this->database->loadResult();
		return ($value);
	}

	/**
	 * stats/info
	 */
	function getRelDiffOfTwoDays($date1, $date2, $group, $name) {

		$query = sprintf("SELECT sum(value) as value FROM `#__joomlawatch_info` WHERE (`date`='%d' and `group` = '%s' and `name` = '%s')", (int) $date2, JoomlaWatchHelper::sanitize($group), JoomlaWatchHelper::sanitize($name));
		$this->database->setQuery($query);
		$value2 = $this->database->loadResult();

		$query = sprintf("SELECT sum(value) as value FROM `#__joomlawatch_info` WHERE (`date`='%d' and `group` = '%s'  and `name` = '%s')", (int) $date1, JoomlaWatchHelper::sanitize($group), JoomlaWatchHelper::sanitize($name));
		$this->database->setQuery($query);
		$value1 = $this->database->loadResult();

		$diff = 0;
		if ($value1) {
			$diff = floor((($value2 - $value1) / $value1) * 1000) / 10;
		} else {
			$diff = $value2;
		}

		return $diff;

	}

	/**
	 * stats/info
	 */
	function getRelDiffOfTwoWeeks($week1, $week2, $group, $name) {

		$startOfWeek1 = $week1 -7;
		$endOfWeek1 = $week1;
		$startOfWeek2 = $week2 -7;
		$endOfWeek2 = $week2;

		$query = sprintf("SELECT sum(value) as value FROM `#__joomlawatch_info` WHERE ( (`date`>'%d' and `date` <= '%d') and `group` = '%s' and `name` = '%s')", (int) $startOfWeek1, (int) $endOfWeek1, JoomlaWatchHelper::sanitize($group), JoomlaWatchHelper::sanitize($name));
		$this->database->setQuery($query);
		$value2 = $this->database->loadResult();

		$query = sprintf("SELECT sum(value) as value FROM `#__joomlawatch_info` WHERE ( (`date`>'%d' and `date` <= '%d') and `group` = '%s'  and `name` = '%s')", (int) $startOfWeek2, (int) $endOfWeek2, JoomlaWatchHelper::sanitize($group), JoomlaWatchHelper::sanitize($name));
		$this->database->setQuery($query);
		$value1 = $this->database->loadResult();

		$diff = 0;
		if ($value1) {
			$diff = floor((($value2 - $value1) / $value1) * 1000) / 10;
		} else {
			$diff = $value2;
		}

		return $diff;
	}

	/**
	 * stats/info
	 */
	function getRelDiffOfDay($date, $group, $name, $diff) {
		$query = sprintf("SELECT sum(value) as value FROM `#__joomlawatch_info` WHERE (`date`='%d' and `group` = '%s' and `name` = '%s')", (int) $date, JoomlaWatchHelper::sanitize($group), JoomlaWatchHelper::sanitize($name));
		$this->database->setQuery($query);
		$value = $this->database->loadResult();
		if ($value)
		$percent = floor(($diff / $value) * 1000) / 10;

		return @ $percent;

	}

	/**
	 * stats/info
	 */
	function getRelDiffOfWeek($date, $group, $name, $diff) {

		$date1 = $date -7;
		$date2 = $date;
		$query = sprintf("SELECT sum(value) as value FROM `#__joomlawatch_info` WHERE ( (`date`<='%d'  and `date`>'%d') and `group` = '%s' and `name` = '%s')", (int) $date2, (int) $date1, JoomlaWatchHelper::sanitize($group), JoomlaWatchHelper::sanitize($name));
		$this->database->setQuery($query);
		$value = $this->database->loadResult();
		$percent = 0;
		if ($value)
		$percent = floor(($diff / $value) * 1000) / 10;

		return @ $percent;

	}
	
	/**
	 * stats/info
	 */
	function isIPUniqueForToday($ip) {
		$date = $this->helper->jwDateToday();
		$query = sprintf("select count(value) as count from #__joomlawatch_info where `group` = 'ip' and name = '%s' and `date` = '%d' ", JoomlaWatchHelper::sanitize($ip), (int) $date);
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		$count = $this->database->loadResult();
		if (isset ($count)) {
			return false;
		} else {
			return true;
		}

	}
	
	/**
	 * stats/info
	 */
	function countUsersForToday() {
		$date = $this->helper->jwDateToday();
		$query = sprintf("select count(id) as count from #__joomlawatch_info where `group` = '%d' and `date` = '%d' order by value desc", (int) DB_KEY_USERS, (int) $date);
		$this->database->setQuery($query);
		$this->database->query();
		$count = @ $this->database->loadResult();
		return $count;
	}

	
	/**
	 * stats/info
	 */
	function getUsersForToday() {
		$date = $this->helper->jwDateToday();
		$limit = $this->config->getConfigValue('JOOMLAWATCH_FRONTEND_USERS_COUNT');
		$limit = 20;
		$query = sprintf("select `name`, value from #__joomlawatch_info where `group` = '%d' and `date` = '%d' order by value desc limit $limit", (int) DB_KEY_USERS, (int) $date);
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		return $rows;
	}




}

?>