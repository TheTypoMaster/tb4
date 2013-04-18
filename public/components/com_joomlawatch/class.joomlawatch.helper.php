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

class JoomlaWatchHelper {

	var $database;
	var $config;

	function JoomlaWatchHelper() {
		global $database;
		if (!JOOMLAWATCH_JOOMLA_15) // if Joomla 1.0
		$this->database = $database;
		else
		$this->database = & JFactory :: getDBO();

		$this->config = new JoomlaWatchConfig();
	}

	/**
	 * helper
	 */
	function getActualDateTime() {
		$date = date("d.m.Y", $this->getServerTime());
		$time = date("H:i:s", $this->getServerTime());
		return "$date $time";
	}

	/**
	 * helper
	 */
	function dayOfWeek() {
		return date("w", $this->getServerTime());
	}

	/**
	 * helper
	 */
	function dayOfMonth() {
		return date("d", $this->getServerTime());
	}

	/**
	 * helper
	 */
	function getIP2LocationURL($ip) {
		$url = $this->config->getConfigValue('JOOMLAWATCH_TOOLTIP_URL');
		$result = str_replace("{ip}", $ip, $url);
		//TODO : appears to be a bug in str_ireplace - doesn't work!
		return $result;
	}

	/**
	 * helper
	 */
	function resetData() {

		$query = sprintf("delete from #__joomlawatch");
		$this->database->setQuery($query);
		$result1 = $this->database->query();

		$query = sprintf("delete from #__joomlawatch_uri");
		$this->database->setQuery($query);
		$result2 = $this->database->query();

		$query = sprintf("delete from #__joomlawatch_info");
		$this->database->setQuery($query);
		$result3 = $this->database->query();

		$query = sprintf("delete from #__joomlawatch_blocked");
		$this->database->setQuery($query);
		$result4 = $this->database->query();

		$query = sprintf("delete from #__joomlawatch_goals");
		$this->database->setQuery($query);
		$result4 = $this->database->query();

		$query = sprintf("delete from #__joomlawatch_cache");
		$this->database->setQuery($query);
		$result4 = $this->database->query();

		$query = sprintf("delete from #__joomlawatch_uri2title");
		$this->database->setQuery($query);
		$result5 = $this->database->query();

		return ($result1 & $result2 & $result3 & $result4 & $result5);

	}


	/**
	 * helper
	 *
	 * @return unknown
	 */
	function isModulePublished() {
		$query = sprintf("select published from #__modules where module = 'mod_joomlawatch_agent' order by id desc limit 1; ");
		$this->database->setQuery($query);
		$published = $this->database->loadResult();
		return $published;
	}
	/**
	 * helper
	 *
	 * @return unknown
	 */
	function jwDateToday() {
		$today = floor($this->getServerTime() / 3600 / 24 + $this->config->getConfigValue('JOOMLAWATCH_DAY_OFFSET'));
		return $today;
	}

	/**
	 * helper
	 *
	 * @return unknown
	 */
	function getURI() {
		$redirURI = addslashes(strip_tags(@ $_SERVER[$this->config->getConfigValue('JOOMLAWATCH_SERVER_URI_KEY')]));
		$uri = addslashes(strip_tags(@ $_SERVER['REQUEST_URI']));

		if (@ $redirURI && @ substr($redirURI, -9, 9) != "index.php")
		$uri = $redirURI;

		return $uri;
	}

	/**
	 * helper
	 */
	function truncate($str, $len = "") {
		if (@ !$len)
		$len = $this->config->getConfigValue('JOOMLAWATCH_TRUNCATE_VISITS');

		if (strlen($str) < $len)
		return $str;
		else
		return substr($str, 0, $len) . "...";
	}
	/**
	 * helper
	 */
	function saveSettings($post) {

		$settingsArray = array (
		'JOOMLAWATCH_FRONTEND_LINK',
		'JOOMLAWATCH_FRONTEND_HIDE_LOGO',
		'JOOMLAWATCH_IP_STATS',
		'JOOMLAWATCH_FRONTEND_LINK',
		'JOOMLAWATCH_FRONTEND_COUNTRIES',
		'JOOMLAWATCH_FRONTEND_COUNTRIES_UPPERCASE',
		'JOOMLAWATCH_FRONTEND_COUNTRIES_FIRST',
		'JOOMLAWATCH_FRONTEND_VISITORS',
		'JOOMLAWATCH_FRONTEND_VISITORS_TODAY',
		'JOOMLAWATCH_FRONTEND_VISITORS_YESTERDAY',
		'JOOMLAWATCH_FRONTEND_VISITORS_THIS_WEEK',
		'JOOMLAWATCH_FRONTEND_VISITORS_LAST_WEEK',
		'JOOMLAWATCH_FRONTEND_VISITORS_THIS_MONTH',
		'JOOMLAWATCH_FRONTEND_VISITORS_LAST_MONTH',
		'JOOMLAWATCH_FRONTEND_VISITORS_TOTAL',
		'JOOMLAWATCH_TOOLTIP_ONCLICK',
		'JOOMLAWATCH_SPAMWORDS_BANS_ENABLED'
		);


		foreach ($post as $key => $value) {
			if (strstr($key, "JOOMLAWATCH")) {
				$this->config->saveConfigValue($key, $value);
			}
		}
		//hack :( explicitly save checkbox values
		foreach ($settingsArray as $key => $value) {
			if (@ !$post[$value]) {
				$this->config->saveConfigValue($value, "Off");
			}
		}
		// explicitly reset chache because of frontend settings
		JoomlaWatchCache :: clearCache();

		return true;
	}

	/**
	 * helper
	 */
	function getDateByDay($day, $format = "d.m.Y") {
		$date = date($format, $day * 3600 * 24);
		$output = $date;

		if ($format == "d.m.Y" && ($date == date($format, $this->getServerTime())))
		$output .= " (" . _JW_STATS_TODAY . ")";

		return $output;
	}

	/**
	 * helper
	 */
	function getServerTime() {
		return time() + $this->config->getConfigValue('JOOMLAWATCH_TIMEZONE_OFFSET') * 3600;
	}

	/**
	 * helper
	 */
	// fnmatch PHP function only on UNIX :(, this replaces the wildcard search
	function wildcardSearch($pattern, $string) {
		return preg_match("#^" . strtr(preg_quote($pattern, '#'), array (
		'\*' => '.*',
		'\?' => '.'
		)) . "$#i", $string);
	}

	/**
	 * helper
	 *
	 * @return unknown
	 */
	function jwDateBySeconds($sec) {
		$date = floor($sec / 3600 / 24 + $this->config->getConfigValue('JOOMLAWATCH_DAY_OFFSET'));
		return $date;
	}

	/**
	 * helper
	 */
	function getDayByTimestamp($timestamp) {
		return floor( ($timestamp + $this->config->getConfigValue('JOOMLAWATCH_DAY_OFFSET')) / 24 / 3600 + $this->config->getConfigValue('JOOMLAWATCH_WEEK_OFFSET'));
	}

	/**
	 * helper
	 */
	function getWeekByTimestamp($timestamp) {
		return ceil(($timestamp +$this->config->getConfigValue('JOOMLAWATCH_DAY_OFFSET')) / 7 / 24 / 3600 + $this->config->getConfigValue('JOOMLAWATCH_WEEK_OFFSET'));
	}

	/**
	 * helper
	 */
	function getTooltipOnClick() {
		return $this->config->getCheckboxValue("JOOMLAWATCH_TOOLTIP_ONCLICK");
	}

	/**
	 * helper
	 * @return unknown
	 */
	function getTooltipOnEvent() {
		if ($this->getTooltipOnClick()) {
			return "title='" . _JW_TOOLTIP_CLICK . "' onclick";
		} else {
			return "title='" . _JW_TOOLTIP_MOUSE_OVER . "' onmouseover";
		}
	}

	/**
	 * helper
	 */
	function getTooltipOnEventHide() {
		if (!$this->getTooltipOnClick()) {
			return "onClick='ajax_hideTooltip();'";
		}
		return;
	}

	/**
	 * helper
	 * @return unknown
	 */
	function getAvailableLanguages() {
		$langDirPath = JPATH_BASE2 . DS . "components" . DS . "com_joomlawatch" . DS . "lang";

		if ($handle = @ opendir($langDirPath)) {
			while (false !== ($file = readdir($handle))) {
				if (strstr($file, ".php")) {
					$file = str_replace(".php", "", $file);
					$langArray[] = $file;
				}
			}
			closedir($handle);
		}
		sort($langArray);
		return @ $langArray;
	}

	/**
	 * helper
	 * 
	 * functions taken from
	 * Open Web Application Security Project
	 * (http://www.owasp.org)
	 */
	// sanitize a string for SQL input (simple slash out quotes and slashes)
	function sanitize($string, $min = '', $max = '') {
		$string = addslashes($string); //gz
		$pattern = "//"; // jp
		$replacement = "";
		$len = strlen($string);
		if ((($min != '') && ($len < $min)) || (($max != '') && ($len > $max))) {
			return false;
		}
		return preg_replace($pattern, $replacement, $string);
	}


	/**
	 * helper
	 */
	function countryByIp($ip) {
		if ($ip == '127.0.0.1') {
			/* ignore localhost */
			return;
		}

		$query3 = sprintf("select ip, country from #__joomlawatch where (ip = '%s' and country is not NULL) limit 1", JoomlaWatchHelper::sanitize($ip));
		$this->database->setQuery($query3);
		$this->database->query();
		$row3 = $this->database->loadResult();

		if (@ !$row3->country) {

			$iplook = new ip2country($ip);
			$iplook->UseDB = true;
			$iplook->db_tablename = "#__joomlawatch_ip2c";

			if (($iplook->LookUp())) {
				$country = strtolower($iplook->Country);
				$query3 = sprintf("update #__joomlawatch set country = '%s' where ip = '%s'", JoomlaWatchHelper::sanitize($country), JoomlaWatchHelper::sanitize($ip));
				$this->database->setQuery($query3);
				$this->database->query();
			}

		} else {
			$country = $row3->country;
		}

		return @ $country;
	}

	/**
	 * helper
	 */
	function countryCodeToCountryName($code) {
		$query = sprintf("select country from #__joomlawatch_cc2c where cc = '%s' limit 1", JoomlaWatchHelper::sanitize($code));
		$this->database->setQuery($query);
		$this->database->query();
		$countryName = $this->database->loadResult();
		return $countryName;
	}


}


?>