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

class JoomlaWatchVisit {

	var $database;
	var $config;
	var $helper;
	var $stat;
	var $block;
	var $goal;

	function JoomlaWatchVisit() {
		global $database;
		if (!JOOMLAWATCH_JOOMLA_15) // if Joomla 1.0
		$this->database = $database;
		else
		$this->database = & JFactory :: getDBO();
		
		$this->config = new JoomlaWatchConfig();
		$this->helper = new JoomlaWatchHelper();
		$this->stat = new JoomlaWatchStat();
		$this->block = new JoomlaWatchBlock();
		$this->goal = new JoomlaWatchGoal();

	}



	/**
	 * visitor
	 *
	 * @return unknown
	 */
	function getLastVisitId() {
		$query = sprintf("select #__joomlawatch_uri.id as last from #__joomlawatch left join #__joomlawatch_uri on #__joomlawatch.id = #__joomlawatch_uri.fk where #__joomlawatch.browser is not NULL order by #__joomlawatch_uri.id desc limit 1");
		$this->database->setQuery($query);
		$last = $this->database->loadResult();
		return $last;
	}

	/**
	 * visitor
	 *
	 * @return unknown
	 */
	function deleteOldVisits() {

		$maxRows = $this->config->getConfigValue('JOOMLAWATCH_STATS_MAX_ROWS');
		$today = $this->helper->jwDateToday();
		
		/** get oldest visitor id in database */		
		$query = sprintf("select (max(id)-min(id)) as difference from #__joomlawatch where browser is not null ");
		$this->database->setQuery($query);
		$this->database->query();
		$difference = $this->database->loadResult();

		/** if the difference between the oldest id-s is less than our max visitors, we do nothing */
		if ($difference < $this->config->getConfigValue('JOOMLAWATCH_LIMIT_VISITORS')) {
			return false;
		}
		
		for ($i = 0; $i<20; $i++) {
			/** delete records from previous day, which are not in top 20 (or value in maxRows */
			$query = sprintf("SELECT id FROM `#__joomlawatch_info` where `group` = '$i' and date = '%d' order by `value` desc limit %d,99999", (int) ($today -1), (int) $maxRows) ;
			$this->database->setQuery($query);
			$rows = @ $this->database->loadObjectList();
			foreach ($rows as $row) {
				$query = sprintf("delete from `#__joomlawatch_info` where id = '%d' ", (int) $row->id);
				$this->database->setQuery($query);
				$this->database->query();

				$query = sprintf("delete from `#__joomlawatch_uri` where fk = '%d' ", (int) $row->id);
				$this->database->setQuery($query);
				$this->database->query();
			}
		}

		$query = sprintf("select id as maxid from #__joomlawatch where browser is not NULL order by id desc limit 1");
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		$row = @ $rows[0];
		$maxidvisitors = @ $row->maxid - $this->config->getConfigValue('JOOMLAWATCH_MAXID_VISITORS');

		$query = sprintf("delete from #__joomlawatch where (browser is not NULL and id < '%d') ", (int) $maxidvisitors);
		$this->database->setQuery($query);
		$this->database->query();

		$query = sprintf("delete from #__joomlawatch_uri where fk < '%d' ", (int) $maxidvisitors);
		$this->database->setQuery($query);
		$this->database->query();

		$maxidbots = @ $row->maxid - $this->config->getConfigValue('JOOMLAWATCH_MAXID_BOTS');

		$query = sprintf("select id from #__joomlawatch where (id < '%d' and browser is NULL) order by id desc", (int) $maxidbots);
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();

		foreach ($rows as $row) {

			$query = sprintf("delete from #__joomlawatch where id = '%d' ", (int) $row->id);
			$this->database->setQuery($query);
			$this->database->query();

			$query = sprintf("delete from #__joomlawatch_uri where fk = '%d' ", (int) $row->id);
			$this->database->setQuery($query);
			$this->database->query();

		}
		

		if ($this->config->getConfigValue('JOOMLAWATCH_STATS_KEEP_DAYS') != 0) { // 0 = infinite
			$today = $this->helper->jwDateToday();
			$daysToKeep = $today -$this->config->getConfigValue('JOOMLAWATCH_STATS_KEEP_DAYS');

			$query = sprintf("delete from #__joomlawatch_info where date < '%d' ", (int) $daysToKeep);
			$this->database->setQuery($query);
			$this->database->query();
		}
		
/*		//delete all IP records that are less than 1%
		$value = $this->config->getConfigValue('JOOMLAWATCH_STATS_IP_HITS');
		$query = sprintf("DELETE FROM `#__joomlawatch_info` where (`group` = '".DB_KEY_IP."' and date < '%d')", (int) ($today-1));
		$this->database->setQuery($query);
		$this->database->query();


		//delete all IP records that are less than 1%
		$value = $this->config->getConfigValue('JOOMLAWATCH_STATS_IP_HITS');
		$query = sprintf("DELETE FROM `#__joomlawatch_info` where (`group` = '".DB_KEY_IP."' and date < '%d' and value < '%s')", (int) $today, JoomlaWatchHelper::sanitize($value));
		$this->database->setQuery($query);
		$this->database->query();
*/
		//delete from uri2title record older than JOOMLAWATCH_STATS_KEEP_DAYS days
		$count = (JoomlaWatchHelper ::getServerTime() - ( 7 *3600*24)); // 7-day unaccessed records should be deleted
		$query = sprintf("DELETE FROM `#__joomlawatch_uri2title` where (timestamp < '%d') ", (int) JoomlaWatchHelper::sanitize($count));
		$this->database->setQuery($query);
		$this->database->query();

		//delete from uri2title record older than JOOMLAWATCH_STATS_KEEP_DAYS days
		$count = (JoomlaWatchHelper ::getServerTime() - ( 7 *3600*24));	// 7-day unaccessed records should be deleted
		$query = sprintf("DELETE FROM `#__joomlawatch_internal` where (timestamp < '%d') ", (int) JoomlaWatchHelper::sanitize($count));
		$this->database->setQuery($query);
		$this->database->query();

	}



	/**
	 * visitor
	 */
	function insertVisit($liveSite) {
		global $mainframe;

		$ip = addslashes(strip_tags(@ $_SERVER['REMOTE_ADDR']));
		$uri = $this->helper->getURI();
		$userObject = @ $mainframe->getUser();
		$newUsername = @ $userObject->username;


		if ($this->config->isIgnored('IP', $ip) || $this->config->isIgnored('URI', $uri) || $this->config->isIgnored('USER', $newUsername)) {
			return true;
		}

		$referer = addslashes(strip_tags(@ $_SERVER['HTTP_REFERER']));
		$referer = strip_tags($referer);
		$sameSite = strstr($referer, $liveSite);

		if (@ !$sameSite) {
			preg_match('@^(?:http://)?([^/]+)@i', $referer, $matches);
			$host = @ $matches[1];
			$this->stat->increaseKeyValueInGroup(DB_KEY_REFERERS, $host);
			// keywords from google
			preg_match('!q=([^&.]*)!', $referer, $matches);
			$query = @ $matches[1];
			$query = str_replace("%2B", "+", $query);
			$keywords = explode('+', $query);
			foreach ($keywords as $keyword) {

				$keyword = @ strtolower($keyword);
				if ($keyword) {
					$this->stat->increaseKeyValueInGroup(DB_KEY_KEYWORDS, $keyword);
				}
			}
		} else if (@strpos($referer, $liveSite) == 0) {	/* starts with the live site */
			$from = str_replace($liveSite, "", $referer);

			$query = sprintf("select id from #__joomlawatch_internal where (`from` = '%s' and `to` = '%s') ", JoomlaWatchHelper::sanitize($from), JoomlaWatchHelper::sanitize($uri));
			$this->database->setQuery($query);
			$this->database->query();
			$id = $this->database->loadResult();

			if (! @$id) {
				$query = sprintf("insert into #__joomlawatch_internal (id, `from`,`to`,`timestamp`) values ('', '%s', '%s', '%d') ", JoomlaWatchHelper::sanitize($from), JoomlaWatchHelper::sanitize($uri), (int) JoomlaWatchHelper::getServerTime());
				$this->database->setQuery($query);
				$this->database->query();

				$query = sprintf("select id from #__joomlawatch_internal where (`from` = '%s' and `to` = '%s') ", JoomlaWatchHelper::sanitize($from), JoomlaWatchHelper::sanitize($uri));
				$this->database->setQuery($query);
				$this->database->query();
				$id = $this->database->loadResult();
			}

			$query = sprintf("update from #__joomlawatch_internal set `timestamp` = '%d' where (id = '%d') ", (int) $id);
			$this->database->setQuery($query);
			$this->database->query();

			$this->stat->increaseKeyValueInGroup(DB_KEY_INTERNAL, $id);

		}

		if ($this->helper->getServerTime() % 10 == 0)  {
			$this->deleteOldVisits();
		}

		$time = $this->helper->getServerTime();

		$count = $this->block->getBlockedIp($ip);
		if (@ $count) {
			$this->block->dieWithBlockingMessage($ip);
		}

		$query = sprintf("select id, username from #__joomlawatch where ip = '%s' limit 1", JoomlaWatchHelper::sanitize($ip));
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		$row = @ $rows[0];
		$id = @ $row->id;
		$username = @ $row->username;

		$title = @ $mainframe->getPageTitle();
		$this->addUri2Title($uri, $title);
		if (!@ $id) {

			$referer = strip_tags($referer);
			$ip = strip_tags($ip);
			$query = sprintf("insert into #__joomlawatch (id, ip, country, browser, referer) values ('', '%s',  NULL, NULL, '%s') ", JoomlaWatchHelper::sanitize($ip), JoomlaWatchHelper::sanitize($referer));
			$this->database->setQuery($query);
			$this->database->query();

			$query = sprintf("select id from #__joomlawatch where ip = '%s' limit 1", JoomlaWatchHelper::sanitize($ip));
			$this->database->setQuery($query);
			$rows = @ $this->database->loadObjectList();
			$row = @ $rows[0];
			$id = @ $row->id;

			$query = sprintf("insert into #__joomlawatch_uri (id, fk, timestamp, uri, title) values ('', '%d', '%d', '%s', '%s') ", (int) $id, (int) $time, JoomlaWatchHelper::sanitize($uri), JoomlaWatchHelper::sanitize($title));
			$this->database->setQuery($query);
			$this->database->query();
		} else {
			$query = sprintf("insert into #__joomlawatch_uri (id, fk, timestamp, uri, title) values ('', '%d', '%d', '%s', '%s') ", (int) $id, (int) $time, JoomlaWatchHelper::sanitize($uri), JoomlaWatchHelper::sanitize($title));
			$this->database->setQuery($query);
			$this->database->query();
		}

		if (($username != $newUsername) && ($newUsername)) {
			$query = sprintf("update #__joomlawatch set username = '%s' where ip = '%s'", JoomlaWatchHelper::sanitize($newUsername), JoomlaWatchHelper::sanitize($ip));
			$this->database->setQuery($query);
			$this->database->query();
		}
		
		if (@ $newUsername) {
			$this->stat->increaseKeyValueInGroup(DB_KEY_USERS, $newUsername);
		}

		if ($this->config->getConfigValue('JOOMLAWATCH_IP_STATS')) {
			$this->stat->increaseKeyValueInGroup(DB_KEY_IP, $ip); //add ip watching
		}

		$this->stat->increaseKeyValueInGroup(DB_KEY_HITS, DB_KEY_HITS);

		$this->goal->checkGoals($title, $newUsername, $ip, $referer, $liveSite);


	}

	/**
	 * visitor
	 */
	function updateVisitByBrowser($uri) {
		$ip = addslashes(strip_tags($_SERVER['REMOTE_ADDR']));
		$userAgent = addslashes(strip_tags(@ $_SERVER['HTTP_USER_AGENT']));

		if ($this->config->isIgnored('IP', $ip) || $this->config->isIgnored('URI', $uri)) {
			return true;
		}

		$this->updateBrowserStats($ip, $userAgent);

		$query = sprintf("select #__joomlawatch_uri.uri from #__joomlawatch left join #__joomlawatch_uri on #__joomlawatch.id = #__joomlawatch_uri.fk  where (#__joomlawatch.ip = '%s' and #__joomlawatch.browser is not null) order by #__joomlawatch_uri.timestamp desc limit 1", JoomlaWatchHelper::sanitize($ip));
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		$row = @ $rows[0];
		$uri = @ $row->uri;

		$this->stat->increaseKeyValueInGroup(DB_KEY_URI, $uri);
		$this->stat->increaseKeyValueInGroup(DB_KEY_LOADS, DB_KEY_LOADS);

	}


	/**
	 * visitor
	 */
	function updateBrowserStats($ip, $userAgent) {
		$query = sprintf("select id,browser from #__joomlawatch where ip = '%s' order by id asc limit 1", JoomlaWatchHelper::sanitize($ip));
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		$row = @ $rows[0];
		if (@ $row->browser == '')
		$firstTime = true;

		$country = $this->helper->countryByIp($ip);

		$query = sprintf("select browser,country from #__joomlawatch where ip = '%s' order by browser desc limit 1", JoomlaWatchHelper::sanitize($ip));
		$this->database->setQuery($query);
		$rows = @ $this->database->loadObjectList();
		$row = @ $rows[0];

		//check if first time visit
		if (@ !$row->browser) {

			$userAgent = strip_tags($userAgent);
			if (@ $userAgent) {
				// to make sure it's really unique for today
				$this->stat->increaseKeyValueInGroup(DB_KEY_UNIQUE, DB_KEY_UNIQUE);
			}

			/* Googlebot patch identifier: Googlebot-Image/1.0 */
			if (!strstr($userAgent, "Googlebot")) {
				$query = sprintf("update #__joomlawatch set browser = '%s' where ip = '%s'", JoomlaWatchHelper::sanitize($userAgent), JoomlaWatchHelper::sanitize($ip));
				$this->database->setQuery($query);
				$this->database->query();
			}

			$browser = $this->identifyBrowser(@ $userAgent);
			$this->stat->increaseKeyValueInGroup(DB_KEY_BROWSER, $browser);

			$os = $this->identifyOs(@ $userAgent);
			$this->stat->increaseKeyValueInGroup(DB_KEY_OS, $os);

			$this->stat->increaseKeyValueInGroup(DB_KEY_COUNTRY, $country);

		}

	}

	/**
	 * visitor
	 */
	function identifyOs($userAgent) {
		if (stristr($userAgent, "Mac"))
		$os = "Mac";
		else
		if (stristr($userAgent, "Linux"))
		$os = "Linux";
		else
		if (stristr($userAgent, "Windows 95"))
		$os = "Windows98";
		else
		if (stristr($userAgent, "Windows 98"))
		$os = "Windows98";
		else
		if (stristr($userAgent, "Windows ME"))
		$os = "Windows98";
		else
		if (stristr($userAgent, "Windows NT 4.0"))
		$os = "WindowsNT";
		else
		if (stristr($userAgent, "Windows NT 6.0"))
		$os = "WindowsVista";
		else
		if (stristr($userAgent, "Windows NT 5.1"))
		$os = "WindowsXP";
		else
		if (stristr($userAgent, "Windows"))
		$os = "Windows";

		return @ $os;
	}

	/**
	 * visitor
	 */
	function identifyBrowser($userAgent) {
		if (stristr($userAgent, "Safari"))
		$browser = "Safari";
		else
		if (stristr($userAgent, "MSIE"))
		$browser = "Explorer";
		else
		if (stristr($userAgent, "Firefox"))
		$browser = "Firefox";
		else
		if (stristr($userAgent, "Opera"))
		$browser = "Opera";
		else
		if (stristr($userAgent, "Mozilla"))
		$browser = "Mozilla";

		return @ $browser;
	}

	/**
	 * visitor
	 */
	function getBrowserByIp($ip) {
		$query = sprintf("select browser from #__joomlawatch where (ip = '%s' and browser is not NULL) order by browser desc limit 1", JoomlaWatchHelper::sanitize($ip));
		$this->database->setQuery($query);
		$this->database->query();
		$browser = $this->database->loadResult();
		return $browser;
	}

	/**
	 * visitor
	 */
	function getBots() {
		$limit = 0;
		$limit = $this->config->getConfigValue('JOOMLAWATCH_LIMIT_BOTS');
		$query = sprintf("select ip, referer, username from #__joomlawatch where (browser is NULL) order by id desc limit %d", (int) $limit);
		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();
		return $rows;

	}

	/**
	 * visitor
	 */
	function getVisitors() {
		$limit = 0;
		$limit = $this->config->getConfigValue('JOOMLAWATCH_LIMIT_VISITORS');
		$query = sprintf("select ip, referer, username from #__joomlawatch where (browser is not NULL and browser != '') order by id desc limit %d", (int) $limit);
		$this->database->setQuery($query);
		$rows = $this->database->loadObjectList();
		return $rows;
	}

	/**
	 * visitor
	 */
	function getJoinedURIRows($ip) {
		$query2 = "SELECT * FROM #__joomlawatch LEFT JOIN #__joomlawatch_uri ON #__joomlawatch.id = #__joomlawatch_uri.fk where ip = '$ip' ORDER BY #__joomlawatch_uri.timestamp desc";
		$this->database->setQuery($query2);
		$rows = $this->database->loadObjectList();
		return $rows;
	}

	/**
	 * visitor
	 */
	function getInternalNameById($id) {
		$query = sprintf("select `from`,`to` from #__joomlawatch_internal where id = '%d'", (int) $id);
		$this->database->setQuery($query);
		$this->database->query();
		$rows = @ $this->database->loadObjectList();
		$row = @$rows[0];
		return $row;
	}

	/**
	 * visitor
	 */
	function addUri2Title($uri, $title) {
		$title = htmlspecialchars($title);
		$uri = htmlspecialchars($uri);
		$this->increaseUri2TitleCount($uri);
		
		$query = sprintf("select id from #__joomlawatch_uri2title where (`uri` = '%s') limit 1 ", JoomlaWatchHelper::sanitize($uri), JoomlaWatchHelper::sanitize($title));
		$this->database->setQuery($query);
		$this->database->query();
		$id = $this->database->loadResult();

		if (!@$id) {
			$query = sprintf("insert into #__joomlawatch_uri2title (id, uri, title, count, timestamp) values ('','%s','%s',1,'%d') ", JoomlaWatchHelper::sanitize($uri), JoomlaWatchHelper::sanitize($title), (int) JoomlaWatchHelper::getServerTime());
			$this->database->setQuery($query);
			$this->database->query();
		}
	}

	/**
	 * visitor
	 */
	function getTitleByUri($uri) {
		$query = sprintf("select title from #__joomlawatch_uri2title where (`uri` = '%s') limit 1 ", JoomlaWatchHelper::sanitize($uri));
		$this->database->setQuery($query);
		$this->database->query();
		$title = $this->database->loadResult();
		return $title;
	}

	/**
	 * visitor
	 */
	function increaseUri2TitleCount($uri) {
		$query = sprintf("update #__joomlawatch_uri2title set count = count+1, timestamp = '%d' where (`uri` = '%s')", (int) JoomlaWatchHelper::getServerTime(), JoomlaWatchHelper::sanitize($uri));
		$this->database->setQuery($query);
		$this->database->query();
	}


}

?>