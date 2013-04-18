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

class JoomlaWatchBlock {

	var $database;
	var $config;
	var $helper;

	function JoomlaWatchBlock() {
		global $database;
		if (!JOOMLAWATCH_JOOMLA_15) // if Joomla 1.0
		$this->database = $database;
		else
		$this->database = & JFactory :: getDBO();

		$this->config = new JoomlaWatchConfig();
		$this->helper = new JoomlaWatchHelper();
	}

	/**
	 * block
	 */
	function blockIp($ip, $reason = "", $date = 0) {
		$query = sprintf("insert into #__joomlawatch_blocked values ('','%s','','%d', '%s')", JoomlaWatchHelper::sanitize($ip), (int) $date, JoomlaWatchHelper::sanitize($reason));
		$this->database->setQuery($query);
		$this->database->query();
	}

	/**
	 * block
	 */
	function unblockIp($ip) {
		$query = sprintf("delete from #__joomlawatch_blocked where ip = '%s'", JoomlaWatchHelper::sanitize($ip));
		$this->database->setQuery($query);
		$this->database->query();
	}

	/**
	 * block
	 */
	function blockIpToggle($ip) {

		$count = $this->getBlockedIp($ip);
		$today = $this->helper->jwDateToday();

		if (!$count) {
			$this->blockIp($ip, "", $today);
		} else {
			$this->unblockIp($ip);
		}

	}

	/**
	 * block
	 */
	function searchBlockedIp($ip) {
		$query = sprintf("select count(ip) as count from #__joomlawatch_blocked where ip = '%s' limit 1", JoomlaWatchHelper::sanitize($ip)); //starting % ommited
		$this->database->setQuery($query);
		$this->database->query();
		$count = $this->database->loadResult();
		return $count;
	}

	/**
	 * block
	 */
	function searchBlockedIpWildcard($term) {
		$query = sprintf("select count(ip) as count from #__joomlawatch_blocked where ip like '%s%%' limit 1", JoomlaWatchHelper::sanitize($term)); //starting % ommited
		$this->database->setQuery($query);
		$this->database->query();
		$count = $this->database->loadResult();
		return $count;
	}

	/**
	 * block
	 */
	function getBlockedIp($ip) {

		$ipExploded = explode('.', $ip);

		if ($this->searchBlockedIp($ip)) {
			return $ip;
		} else {
			$ip = $ipExploded[0] . "." . $ipExploded[1] . "." . $ipExploded[2] . ".*";
			if ($this->searchBlockedIpWildcard($ip)) {
				return $ip;
			} else {
				$ip = $ipExploded[0] . "." . $ipExploded[1] . ".*";
				if ($this->searchBlockedIpWildcard($ip)) {
					return $ip;
				} else {
					$ip = $ipExploded[0] . ".*";
					if ($this->searchBlockedIpWildcard($ip))
					return $ip;
				}

			}

		}

		return "";

	}


	/**
	 * block
	 */
	function increaseHitsForBlockedIp($ip) {

		$ip = $this->getBlockedIp($ip);
		$query = sprintf("select hits from #__joomlawatch_blocked where ip = '$ip' ", JoomlaWatchHelper::sanitize($ip));
		$this->database->setQuery($query);
		$this->database->query();
		$hits = $this->database->loadResult();

		$hits++;
		if ($hits) { //update
			$query = sprintf("update #__joomlawatch_blocked set hits = '%d' where ip = '%s'", (int) $hits, JoomlaWatchHelper::sanitize($ip));
			$this->database->setQuery($query);
			$this->database->query();
		}
	}


	/**
	 * block
	 */
	function getBlockedIPs($date = 0, $limitCount = 0) {

		if (@$limitCount) {
			$limit = " limit $limitCount ";
		} else {
			$limit = "";
		}

		if (@$date != 0) {
			$query = sprintf("select ip,hits,reason from #__joomlawatch_blocked where `date` = '%d' order by hits desc %s", (int) $date, JoomlaWatchHelper::sanitize($limit));
		} else {
			$query = sprintf("select * from #__joomlawatch_blocked order by hits desc %s", JoomlaWatchHelper::sanitize($limit));
		}
		$this->database->setQuery($query);
		return $rows = $this->database->loadObjectList();
	}

	/**
	 * block
	 */
	function countBlockedIPs($date = 0) {
		if (@$date != 0) {
			$query = sprintf("select count(id) as count from #__joomlawatch_blocked where `date` = '%d' order by hits desc ", (int) $date);
			$this->database->setQuery($query);
			return $this->database->loadResult();
		} else {
			$query = sprintf("select count(id) as count from #__joomlawatch_blocked order by hits desc ", (int) $date);
			$this->database->setQuery($query);
			return $this->database->loadResult();
		}
	}

	/**
	 * blocking
	 *
	 * @return unknown
	 */
	function dieWithBlockingMessage($ip) {
		$this->increaseHitsForBlockedIp($ip);
		die($this->config->getConfigValue('JOOMLAWATCH_BLOCKING_MESSAGE'));
	}


	function checkPostRequestForSpam($post) {

		/** if nothing is there in the post request */
		if (@!$post) {
			return true;
		}
		$ip = $_SERVER['REMOTE_ADDR'];

		if (@$this->searchBlockedIp($ip)) {
			$this->dieWithBlockingMessage($ip);
		}
		$today = $this->helper->jwDateToday();

		if (@ $this->config->getCheckboxValue('JOOMLAWATCH_SPAMWORD_BANS_ENABLED')) {

			$spamList = explode( "\n", $this->config->getConfigValue('JOOMLAWATCH_SPAMWORD_LIST'));
			foreach ($post as $key => $value) {

				foreach($spamList as $spamWord) {
					$spamWord = trim($spamWord);
					$value = trim($value);
					if (@ $spamWord && @$value && JoomlaWatchHelper :: wildcardSearch("*".$spamWord."*", $value)) {
						$this->blockIp($ip, htmlspecialchars($value), $today);
						$this->dieWithBlockingMessage($ip);
					}
				}
			}

		}

	}
}

?>