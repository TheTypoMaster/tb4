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
if (JOOMLAWATCH_DEBUG) {
	error_reporting(E_ALL);
}

class JoomlaWatchHTML {

	var $database;
	var $mosConfig_live_site;
	var $lastDate;
	var $joomlaWatch;

	function JoomlaWatchHTML($omitDir = "") {
		global $database, $mosConfig_live_site;
		if (!JOOMLAWATCH_JOOMLA_15) { // if Joomla 1.0
			$this->database = $database;
			if (!defined('JOOMLAWATCH_LIVE_SITE')) {
				$this->mosConfig_live_site = rtrim($mosConfig_live_site, "/\\");
			} else {
				$this->mosConfig_live_site = rtrim(constant('JOOMLAWATCH_LIVE_SITE'), "/\\");
			}
		} else { // if Joomla 1.5
			$this->database = & JFactory :: getDBO();
			if (!defined('JOOMLAWATCH_LIVE_SITE')) {
				if (@$_SERVER['HTTPS'] == "on") {
					$base = "https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
				} else {
					$base = "http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
				}
				if ($omitDir != "") {
					$base = substr($base, 0, -strlen($omitDir)); //length of directory to omit
				}
				$this->mosConfig_live_site = rtrim($base, "/\\");
			} else {
				$this->mosConfig_live_site = rtrim(constant('JOOMLAWATCH_LIVE_SITE'), "/\\");
			}
		}

		$this->joomlaWatch = new JoomlaWatch();

	}

	function renderIntValuesByName($groupOriginal, $expanded = false, $total = false, $date = 0, $limit = 5, $frontend = false) {

		$group = constant('DB_KEY_'.strtoupper($groupOriginal));

		if (!$date) {
			$date = $this->joomlaWatch->helper->jwDateToday();
		}

		if ($total) {
			$rows = $this->joomlaWatch->stat->getTotalIntValuesByName($group, $expanded, $limit);
			$count = $this->joomlaWatch->stat->getTotalCountByKey($group);
		} else {
			$rows = $this->joomlaWatch->stat->getIntValuesByName($group, $date, $expanded, $limit);
			$count = $this->joomlaWatch->stat->getCountByKeyAndDate($group, $date);
		}

		$i = 0xFF;

		$output = "";

		$j = 0;
		foreach ($rows as $row) {

			$origName = $row->name;

			if (@ !$total && @!$frontend) {
				$relDiff = $this->joomlaWatch->stat->getRelDiffOfTwoDays($date -2, $date -1, $group, $row->name);
				$diffOutput = $this->renderDiff($group, $row->name, $date -1, $relDiff, 1, "$relDiff% - " . _JW_TOOLTIP_YESTERDAY_INCREASE);
			}



			$i -= 3;
			$color = sprintf("%x", $i) . sprintf("%x", $i) . sprintf("%x", $i);

			if ($count) {
				$percent = floor(($row->value / $count) * 1000) / 10;
			}
			else {
				$percent = 0;
			}

			switch (@$group) {

				case DB_KEY_REFERERS : {

					$groupTruncated = $this->joomlaWatch->helper->truncate($row->name, $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_TRUNCATE_STATS'));
					$row->name = "<a href='http://$row->name' title='$row->name' target='_blank'>$groupTruncated</a>";
					break;
				}
				case DB_KEY_URI : {

					$groupTruncated = $this->joomlaWatch->helper->truncate($row->name, $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_TRUNCATE_STATS'));
					$title = $this->joomlaWatch->visit->getTitleByUri($row->name);
					$uriEncoded = urlencode($row->name);
					$row->value = "<table><tr><td>".$row->value."</td><td><a href='".$this->mosConfig_live_site."/administrator/index2.php?option=com_joomlawatch&task=goals&action=insert&uri=$uriEncoded' title='"._JW_STATS_ADD_TO_GOALS."'><img src='".$this->mosConfig_live_site."/components/com_joomlawatch/icons/goal.gif'/ border='0'></a></td></tr></table>";
					$row->name = "<a href='$this->mosConfig_live_site$row->name' onmouseover=\"toggleDiv('uriDetailDiv$j',1);\" onmouseout=\"toggleDiv('uriDetailDiv$j',0);\">$groupTruncated</a><div id='uriDetailDiv$j' class='uriDetailDiv'><table><tr><td><b>$title</b></td></tr><tr><td><a href='$this->mosConfig_live_site$row->name' title='$row->name'>$row->name</a></td></tr></table></div>";
					break;
				}
				case ($group == DB_KEY_BROWSER or $group == DB_KEY_OS) : {

					if ($row->name) {
						$icon = "<img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/" . strtolower($row->name) . ".gif' />";
					}
					break;
				}
				case DB_KEY_COUNTRY : {
					if ($row->name) {
						$countryName = $this->joomlaWatch->helper->countryCodeToCountryName($row->name);
						if ($frontend && $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_FRONTEND_COUNTRIES_UPPERCASE')) {
							$countryName = strtoupper($countryName);
						}
						$icon = "<img src='$this->mosConfig_live_site/components/com_joomlawatch/flags/" . strtolower($row->name) . ".png' title='$countryName' alt='$countryName'/>";
					}
					break;
				}
				case DB_KEY_GOALS : {
					$goalName = $this->joomlaWatch->goal->getGoalNameById($row->name);
					$groupTruncated = $this->joomlaWatch->helper->truncate($goalName, $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_TRUNCATE_STATS'));
					if (@ $row->name) {
						$row->name = "<a href='$this->mosConfig_live_site/administrator/index2.php?option=com_joomlawatch&task=goals&action=edit&goalId=$row->name'>$groupTruncated</a>";
					}

					break;
				}
				case DB_KEY_INTERNAL : {

					$inboundRow = $this->joomlaWatch->visit->getInternalNameById(@ $row->name);
					$from = $inboundRow->from;
					$to = $inboundRow->to;
					$fromEncoded = urlencode($from);
					$toEncoded = urlencode($to);
					$fromTruncated = $this->joomlaWatch->helper->truncate($from, $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_TRUNCATE_STATS')-5);
					$toTruncated = $this->joomlaWatch->helper->truncate($to, $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_TRUNCATE_STATS')-5);
					$row->value = "<table><tr><td>".$row->value."</td><td><a href='".$this->mosConfig_live_site."/administrator/index2.php?option=com_joomlawatch&task=goals&action=insert&from=$fromEncoded&uri=$toEncoded' title='"._JW_STATS_ADD_TO_GOALS."'><img src='".$this->mosConfig_live_site."/components/com_joomlawatch/icons/goal.gif'/ border='0'></a></td></tr></table>";

					$row->name = "-><a href='".$this->mosConfig_live_site."$to' target='_blank' onmouseover=\"toggleDiv('internalDetailDiv$j',1)\" onmouseout=\"toggleDiv('internalDetailDiv$j',0)\">$toTruncated</a>
						<div id='internalDetailDiv$j' class='internalDetailDiv'><table><tr><td colspan='3'>"._JW_STATS_FROM.": </td></tr><tr><td><b>".$this->joomlaWatch->visit->getTitleByUri($from)."</b></td></tr><tr><td><a href='".$this->mosConfig_live_site."/$from'>$from</a></td></tr><tr><td colspan='3'>"._JW_STATS_TO.": </td></tr><tr><td><b>".$this->joomlaWatch->visit->getTitleByUri($to)."</b></td></tr><tr><td><a href='".$this->mosConfig_live_site."/$to'>$to</a></td></tr></table></div>";

					break;
				}
				case DB_KEY_USERS : {
					$row->name = "<a href='$this->mosConfig_live_site/administrator/index2.php?option=com_users&task=view&search=$row->name'>$row->name</a>";

					break;
				}
				case DB_KEY_KEYWORDS : {
					// to have characters like " instead of it's code
					$row->name = strip_tags(urldecode($row->name));

					break;
				}
				case DB_KEY_IP : {
					if (@ $row->name) {
						$mapsIcon = "<img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/map_icon.gif' border='0'  " . $this->joomlaWatch->helper->getTooltipOnEvent() . "=\"ajax_showTooltip('" . $this->mosConfig_live_site . "/components/com_joomlawatch/tooltip.php?rand=" . $this->joomlaWatch->config->getRand() . "&ip=" . @ $row->name . "',this);return false\"/>";

						if ($this->joomlaWatch->block->getBlockedIp($row->name)) {
							$ipStrikedOut = "<s>" . $row->name . "</s>";
						} else {
							$ipStrikedOut = $row->name;
						}
						$blocked = $this->joomlaWatch->block->getBlockedIp($row->name);
						$country = "";
						$country = $this->joomlaWatch->helper->countryByIp($row->name);
						$countryName = $this->joomlaWatch->helper->countryCodeToCountryName($country);

						if (!$country) {
							$country = "none";
						}
						$ip = $row->name;
						$icon = "<table><tr><td>".$mapsIcon . "</td><td><img src='$this->mosConfig_live_site/components/com_joomlawatch/flags/" . strtolower($country) . ".png' title='$countryName' alt='$countryName'/></td>";
						$row->name = "<td><a  id='$row->name' href='javascript:blockIpToggle(\"$row->name\");' style='color: black;'>" . $ipStrikedOut . "</a></td></tr></table>";

					}
					break;
				}
			}

			$trendsIcon = "<img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/trend_icon.gif' border='0'  " . $this->joomlaWatch->helper->getTooltipOnEvent() . "=\"ajax_showTooltip('" . $this->mosConfig_live_site . "/components/com_joomlawatch/trendtooltip.php?rand=" . $this->joomlaWatch->config->getRand() . "&group=$group&name=$origName&date=$date',this);return false\"/>";

			$progressBarIcon = "$this->mosConfig_live_site/components/com_joomlawatch/icons/progress_bar.gif";


			$color = "ffffff";
			if (@ $row->name) {

				if (!$total) {
					$output .= "<tr><td>" . @ $icon . "&nbsp;" . $row->name . "</td><td align='right'><table><tr><td align='right'>" . $row->value . "</td></tr></table></td><td> <table border='0'><tr><td>" . @ $diffOutput . "</td><td>" . @ $trendsIcon . "</td><td><img src='$progressBarIcon' width='$percent' height='10' /></td><td align='left'>$percent%</td></tr></table></td></tr>";

				} else {

					if (!@ $frontend) {
						$output .= "<tr><td align='left' style='background-color: #$color;'>" . @ $icon . "&nbsp;" . $row->name . "</td><td style='background-color: #$color;' align='right'>" . $row->value . "</td><td style='background-color: #$color;'> <table><tr><td><img src='$progressBarIcon' width='$percent' height='10' /></td><td  " . $this->joomlaWatch->helper->getTooltipOnEvent() . "=\"ajax_showTooltip('" . $this->mosConfig_live_site . "/components/com_joomlawatch/trendtooltip.php?rand=" . $this->joomlaWatch->config->getRand() . "&group=$group&name=$origName&date=$date',this);return false\">$percent%</td></tr></table></td></tr>";
					} else {
						$output .= "<tr><td valign='top' align='right' class='joomlawatch'>$percent%</td><td valign='top' align='left' class='joomlawatch'>" . @ $icon . "&nbsp;</td><td valign='top' align='left' class='joomlawatch'>" . $countryName . "</td></tr>";
					}

				}
			}
			$j++;
		}


		if ($count > $limit && !$frontend)
		$output = "<tr><td colspan='4'>" . $this->renderExpand($groupOriginal, $total) . "</td></tr>" . $output;

		if (@ $count && !@ $frontend)
		$output .= "<tr><td colspan='5'><b>" . _JW_STATS_TOTAL . ":</b> " . @ $count . " </td></tr>";

		return $output;
	}

	/**
	 * upper side visits statistics - unique/loads/hits
	 * @param unknown_type $week
	 * @return unknown
	 */
	function renderVisitsGraph($week = 0) {
		$output = "";

		$today = date("d.m.Y", $this->joomlaWatch->helper->getServerTime());

		$dateExploded = explode('.', $today);

		$dayOfWeek = $this->joomlaWatch->helper->dayOfWeek();

		$startTimestamp = ($week * 24 * 3600 * 7) - (3*24*3600);

		$i = 0xFF;

		$dateWeekStart = $this->joomlaWatch->helper->getDayByTimestamp($startTimestamp);
	
		$maxLoads = $this->joomlaWatch->stat->getMaxValueInGroupForWeek(DB_KEY_LOADS, DB_KEY_LOADS, $dateWeekStart);

		for ($sec = $startTimestamp; $sec < $startTimestamp+7*3600*24; $sec += 24 * 3600) {
			$i -= 3;
			$color = sprintf("%x", $i) . sprintf("%x", $i) . sprintf("%x", $i);

			if ($i % 2 == 0) {
				$color = "#f5f5f5";
			}
			else {
				$color = "#fafafa";
			}

			$percent = 0;
			$count = 0;
/*			$date = JoomlaWatchHelper::jwDateBySeconds($sec);
			echo("date: $date");
*/
			$date = floor($sec / 3600 / 24 + $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_DAY_OFFSET'));

			$stats['unique'] = $this->joomlaWatch->stat->getKeyValueInGroupByDate(DB_KEY_UNIQUE, DB_KEY_UNIQUE, $date);
			$stats['loads'] = $this->joomlaWatch->stat->getKeyValueInGroupByDate(DB_KEY_LOADS, DB_KEY_LOADS, $date);
			$stats['hits'] = $this->joomlaWatch->stat->getKeyValueInGroupByDate(DB_KEY_HITS, DB_KEY_HITS, $date);

			foreach ($stats as $key => $value) {

				$count = $stats['loads'];
				if ($count)
				$percent = floor(($value / $count) * 100);

				$progressBarIcon = "$this->mosConfig_live_site/components/com_joomlawatch/icons/progress_bar_$key.gif";

				$output .= "<tr><td style='background-color: $color;'>";
				$dow = date("D", $sec);
				if (@ !$once[$dow]) {
					$output .= substr(date("d.m.Y", $sec), 0, 6) . "&nbsp;" . $dow;
					$once[$dow] = 1;
				}
				$output .= "</td>";

				if ($maxLoads)
				$percentWidth = floor( ($percent * $value) / $maxLoads);
				else
				$percentWidth = $percent;

				if (@ $value) {
					switch ($key) {
						case 'hits' : {
							$fontColor = "#aaaaaa";
							$output .= "<td align='right' style='color:$fontColor; background-color: $color;'>" . $value . "</td><td style='background-color: $color;'> <table cellpadding='0' cellspacing='0' ><tr><td style='background-color: $color;'></td><td style='color:$fontColor; background-color: $color;'>&nbsp;</td></tr></table></td>";
							break;
						}
						case 'loads': {
							$fontColor = "#00C000";
							$output .= "<td align='right' style='color:$fontColor; background-color: $color;'>" . $value . "</td><td style='background-color: $color;'> <table cellpadding='0' cellspacing='0' ><tr><td style='background-color: $color;'><img src='$progressBarIcon' width='$percentWidth' height='10' /></td><td style='color:$fontColor; background-color: $color;'></td></tr></table></td>";
							break;
						}
						default: {
							$fontColor = "black";
							$output .= "<td align='right' style='color:$fontColor; background-color: $color;'>" . $value . "</td><td style='background-color: $color;'> <table cellpadding='0' cellspacing='0' ><tr><td style='background-color: $color;'><img src='$progressBarIcon' width='$percentWidth' height='10' /></td><td style='color:$fontColor; background-color: $color;'>&nbsp;$percent%</td></tr></table></td>";
						}
					}
				} else
				$output .= "<td align='right' style='background-color: $color;'></td><td align='right' style='background-color: $color;'></td>";

				$output .= "</tr>";

			}

		}
		$output .= "<tr><td colspan='3' align='right'>* <span style='color:#0000FF;'>" . _JW_STATS_UNIQUE . "</span>, <span style='color:#00C000;'>" . _JW_STATS_LOADS . "</span>, <span style='color:#aaaaaa;'>" . _JW_STATS_HITS . "</span></td></tr>";

		return $output;
	}

	function renderTable($rows, $bots = false) {

		$output = "";
		$i = 0xFF;
		foreach ($rows as $row) {

			/* reset the values from previous iteration */
			$country = "none";
			$countryName = "";
			$flag = "<img src='$this->mosConfig_live_site/components/com_joomlawatch/flags/$country.png' title='$countryName' alt='$countryName'/>";

			if ($i > 0x00)
			$i -= 2;
			else
			$i = 0xFF;

			$rows2 = $this->joomlaWatch->visit->getJoinedURIRows($row->ip);
			$row2 = $rows2[0];

			$color = sprintf("%x", $i) . sprintf("%x", $i) . sprintf("%x", $i);

			if ($bots == true)
			$color = "ffffff";

			$country = $row2->country;

			if (!$country) {
				$country = $this->joomlaWatch->helper->countryByIp($row->ip);
			}
			if (@ $country) {
				$countryName = $this->joomlaWatch->helper->countryCodeToCountryName($country);
				$flag = "<img src='$this->mosConfig_live_site/components/com_joomlawatch/flags/$country.png' title='$countryName' alt='$countryName'/>";
				$countryUpper = strtoupper($country);
			}

			$userAgent = $this->joomlaWatch->visit->getBrowserByIp($row->ip);

			$browser = "";
			$os = "";
			$browserIcon = "";
			$osIcon = "";

			if (@ $userAgent) {
				$browser = $this->joomlaWatch->visit->identifyBrowser(@ $userAgent);
				if (@ $browser)
				$browserIcon = "$this->mosConfig_live_site/components/com_joomlawatch/icons/" . strtolower($browser) . ".gif";

				if (@ $browserIcon)
				$browser = "<img src='$browserIcon' alt='$userAgent' title='$userAgent' />";

				$os = $this->joomlaWatch->visit->identifyOs(@ $userAgent);

				if (@ $os)
				$osIcon = "$this->mosConfig_live_site/components/com_joomlawatch/icons/" . strtolower($os) . ".gif";

				if (@ $osIcon)
				$os = "<img src='$osIcon' alt='$userAgent' title='$userAgent'/>";
			}

			if ($bots == true && $osIcon)
			continue; // bot icon fix
			if ($bots == true) {
				$osIcon = "$this->mosConfig_live_site/components/com_joomlawatch/icons/blank.gif";
				$browserIcon = "$this->mosConfig_live_site/components/com_joomlawatch/icons/blank.gif";
				$browser = "<img src='$browserIcon' alt='$userAgent' title='$userAgent' />";
				$os = "<img src='$osIcon' alt='$userAgent' title='$userAgent'/>";
			}

			/*			if (!$row->addr) {
			$addr = getHostByAddr($row->ip);
			$query = "update #__joomlawatch set host = '".$addr."' where ip = '".$row->ip."' ";
			$this->database->setQuery($query);
			$this->database->query();
			}
			*/
			if ($this->joomlaWatch->block->getBlockedIp($row->ip))
			$ip = "<s>" . $row->ip . "</s>";
			else
			$ip = $row->ip;

			// sometimes happens that timestamp is nothing
			if (!$rows2[0]->timestamp)
			continue;

			$username = "";
			if (@ $row->username) {
				$username = "<br/><a href='$this->mosConfig_live_site/administrator/index2.php?option=com_users&task=view&search=$row->username' style='color: black; text-decoration:none;'><i>" . @ $row->username . "</i></a>";
			}
			$ip = "<a id='$row->ip' href='javascript:blockIpToggle(\"$row->ip\");' style='color:black;'>" . $ip . "</a>";

			$date = date("d.m.Y", $rows2[0]->timestamp);
			if ($this->lastDate && $this->lastDate != $date) {
				$output .= "<tr><td colspan='5'><h3>$date</h3></td></tr>";
			}

			$mapsIcon = "<img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/map_icon.gif' border='0' " . $this->joomlaWatch->helper->getTooltipOnEvent() . "=\"ajax_showTooltip('" . $this->mosConfig_live_site . "/components/com_joomlawatch/tooltip.php?rand=" . $this->joomlaWatch->config->getRand() . "&ip=$row->ip',this);return false\"/>";
			$output .= ("<tr><td valign='top' align='left' style='background-color: #$color'>" . @ $row->id . "</td>
																		<td valign='top' align='left' style='background-color: #$color;'>" . @ $mapsIcon . "</td>
																		<td valign='top' align='left' style='background-color: #$color; color: #999999;'><a href='".$this->mosConfig_live_site."/administrator/index2.php?option=com_joomlawatch&task=goals&action=insert&country=".@$countryUpper."' style='color: #999999;' title='"._JW_VISITS_ADD_GOAL_COUNTRY."'>" . @ $countryUpper . "</a></td>
																		<td valign='top' align='left' style='background-color: #$color;'>" . @ $flag . "</td>
																		<td valign='top' align='left' style='background-color: #$color;'>$ip$username</td>
																		<td valign='top' align='left' style='background-color: #$color;'>" . @ $browser . "</td>
																		<td valign='top' align='left' style='background-color: #$color;'>" . @ $os . "</td>
																		<td valign='top' align='left' style='background-color: #$color;' width='100%'>");

			foreach ($rows2 as $row2) {

				$date = date("d.m.Y", $row2->timestamp);

				$this->lastDate = $date;

				$row2->timestamp = date("H:i:s", $row2->timestamp);
				$uriTruncated = $this->joomlaWatch->helper->truncate($row2->uri);
				$row2->title = $this->joomlaWatch->helper->truncate($row2->title);

				$output .= ("<div id='id$row2->id' style='background-color: #$color'>$row2->timestamp <a href='$row2->uri' target='_blank'>$row2->title</a> $uriTruncated</div>");

			}
			if (@ $row->referer) {
				$refererTruncated = $this->joomlaWatch->helper->truncate($row->referer);
				$output .= "<i style='color: gray;'> " . _JW_VISITS_CAME_FROM . ": <a href='$row->referer' target='_blank' style='color: gray;' title='$row->referer'>$refererTruncated</a></i>";
			}

			$output .= ("</td></tr>");
		}

		return $output;
	}

	function renderExpand($element, $total = false) {

		$elementSuffix = "";
		if (@$total) {
			$elementSuffix = "_total";
		}
		
		if (@ $_GET[$element.$elementSuffix] == "false" || !@ $_GET[$element.$elementSuffix]) {
			$operation = "expand";
		}
		else {
			$operation = "collapse";
		}

		$operationTranslated = constant("_JW_STATS_" . strtoupper($operation));
		$elementTranslated = constant("_JW_STATS_" . strtoupper($element));
		

		$output = "<a name='$element'></a><a href=\"javascript:expand('$element".$elementSuffix."')\" id='$element".$elementSuffix."'><img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/$operation.gif' border='0' alt='$operation'/>$operationTranslated&nbsp;$elementTranslated</a>";

		return $output;
	}

	function renderVisitors() {
		$rows = $this->joomlaWatch->visit->getVisitors();
		$this->lastDate = "";
		$output = JoomlaWatchHTML :: renderTable($rows);

		return $output;
	}

	function renderBots() {

		$rows = $this->joomlaWatch->visit->getBots();
		$this->lastDate = "";
		$output = JoomlaWatchHTML :: renderTable($rows, true);

		return $output;
	}

	function renderSwitched($element, $text, $value) {
		$output = "";
		if ($element != $value) {
			$output .= "<a href=\"javascript:setStatsType('$element');\" id='$element'>$text</a>";
		} else
		$output .= "$text</a>";

		return $output;
	}

	function renderTabClass($name, $value) {
		if ($name == $value) {
			return "tab_active";
		}
		else {
			return "tab_inactive";
		}
	}

	function renderInputElement($key, & $color, $addToDescription = "") {

		if (!@ $color) {
			$color = "#f7f7f7";
		}
		else {
			$color = "";
		}

		$value = "";
		$value = $this->config->getConfigValue("JOOMLAWATCH_" . $key);
		$defaultValue = @ constant("JOOMLAWATCH_" . $key);

		$type = @ constant("TYPE_JOOMLAWATCH_" . $key);
		if ((strcmp($value, $defaultValue)) && ($type != "checkbox") && ($type != "largetext")) {
			$changed = "<i>(" . _JW_SETTINGS_DEFAULT . ": <a href=\"javascript:setElementValueById('JOOMLAWATCH_$key','$defaultValue');\">$defaultValue</a>" . ")</i>&nbsp;";
		}

		$desc = "";
		if ($type == "number" && !(is_numeric($value))) {
			$desc .= " <span style='color: red; font-weight: bold;'> WARNING: The value you entered is not a number. JoomlaWatch will not work properly!</span> ";
		}
		$desc .= constant("_JW_DESC_" . $key);
		$desc .= $addToDescription;

		$keyShortened = str_replace("JOOMLAWATCH_", "", $key);
		$output = "<tr><td style='background-color: $color;' align='left'>$keyShortened</td><td style='background-color: $color;' align='center'>";

		$key = "JOOMLAWATCH_" . $key;
		switch ($type) {

			case "select": {

				if ($value && $value != "Off")
				$checked = "checked";
				else
				$checked = "";
				$output .= "<select align='center' name='$key' id='$key' style='text-align:center;'>";

				$languages = $this->helper->getAvailableLanguages();
				foreach ($languages as $language) {
					if ($value == $language) {
						$selected = "selected";
					} else {
						$selected = "";
					}
					$output .= "<option align='center' style='text-align:center;' $selected>$language</option>";
				}
				$output .= "</select>";

				break;
			}
			case "checkbox": {

				if ($value && $value != "Off")
				$checked = "checked";
				else
				$checked = "";
				$output .= "<input type='checkbox' id='$key' name='$key' $checked/>";
				break;
			}
			case "text": {

				$output .= "<textarea id='$key' cols='15' rows='3' name='$key' style='text-align:center;'>$value</textarea>";
				break;
			}
			case "largetext": {
				$output .= "<textarea id='$key' cols='40' rows='30' name='$key' style='text-align:center;'>$value</textarea>";
				break;
			}
			default: {
				$output .= "<input type='text' id='$key' name='$key' value='$value' size='20' style='text-align:center;'/>";
				break;
			}

		}

		$output .= "</td><td style='background-color: $color;' align='left'>" . @ $changed . " $desc</td></tr>";
		return $output;
	}

	function renderBlockedIPs($date = 0, $expanded = false) {

		$total = false;
		$totalString = "";
		if (!@$date ) {
			$total = true;
		}

		$output = "";
		$count = $this->joomlaWatch->block->countBlockedIPs($date);
		
		if (!$count) {
			return false;
		}
		
		$limit = $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_STATS_MAX_ROWS');

		$output .= "<tr><th></th><th></th><th>IP</th><th>".strtolower(_JW_STATS_HITS)."</th></tr>";
		
		if ($count > $limit )  {
			$output = "<tr><td colspan='4'>".$this->renderExpand('ip_blocking_title', $total)."</td></tr>";
		}
		if (@ $expanded) {
			$rows = $this->joomlaWatch->block->getBlockedIPs($date);
		} else {
			$rows = $this->joomlaWatch->block->getBlockedIPs($date, $limit);
		}

		if (@ $rows)
		foreach ($rows as $row) {
			$icon = "";
			$country = "";
			if (!strstr($row->ip, "*")) {
				$country = $this->joomlaWatch->helper->countryByIp($row->ip);
				$countryName = $this->joomlaWatch->helper->countryCodeToCountryName($country);
				if (!$country) {
					$country = "none";
				}
				$icon = "<img src='$this->mosConfig_live_site/components/com_joomlawatch/flags/" . @ strtolower($country) . ".png' title='$countryName' alt='$countryName'/>";
			}

			$mapsIcon = "<img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/map_icon.gif' border='0'  " . $this->joomlaWatch->helper->getTooltipOnEvent() . "=\"ajax_showTooltip('" . $this->mosConfig_live_site . "/components/com_joomlawatch/tooltip.php?rand=" . $this->joomlaWatch->config->getRand() . "&ip=" . @ $row->ip . "',this);return false\"/>";

			$output .= "<tr><td align='center'>".$mapsIcon."</td><td align='center' title='$row->reason'>" . $icon . "</td><td align='left' title='$row->reason'>" . $row->ip . "</td><td align='center' title='$row->reason'>" . $row->hits . "</td><td>".
			"<a  id='$row->ip' href='javascript:blockIpToggle(\"$row->ip\");' title='$row->reason'>unblock</a>";
			"</td></tr>";

		}
		return $output;

	}

	function renderDiff($group, $name, $date, $relDiff, $onlyImage = 0, $title = "") {
		$diffOutput = "";

		if (!$relDiff) {
			$color = "gray";
		} else {
			if ($relDiff < 0) {
				$color = "red";
			} else {
				$color = "green";
			}
		}

		if (!$onlyImage) {
			$diffOutput .= "<table cellpadding='0' cellspacing='0'><tr><td><a href='" . @ $this->mosConfig_live_site . "/administrator/index2.php?option=com_joomlawatch&task=trends&group=$group&name=$name&date=$date' style='color:$color;'>";
			$diffOutput .= "$relDiff%</a></td><td>";
		}
		$diffOutput .= "<img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/trend_$color.gif' border='0' title='$title'/>";
		if (!$onlyImage) {
			$diffOutput .= "</td></tr></table>";
		}

		return $diffOutput;
	}

	function renderDayDiff($group, $name, $date1, $date2, $onlyImage = 0) {
		$relDiff = $this->joomlaWatch->stat->getRelDiffOfTwoDays($date1, $date2, $group, $name);
		$output = $this->renderDiff($group, $name, $date2, $relDiff, $onlyImage);
		return $output;

	}

	function renderDayTrends($group, $name, $date) {

		$dbKeysArray = array(1 => 'BROWSER','COUNTRY','GOALS','HITS','INTERNAL','IP','KEYWORDS','LOADS','OS','REFERERS','UNIQUE','URI','USERS');

		$resultsArray = array ();
		$max = 0;
		$maxDate = 0;
		for ($i = $date -20; $i <= $date; $i++) {
			$value = $this->joomlaWatch->stat->getKeyValueInGroupByDate($group, $name, $i);
			if ($max < $value) {
				$max = $value;
				$maxDate = $i;
			}
			$resultsArray[$i - ($date -20)] = $value;
		}
		

		
		$nameTranslated = $name;
		
		if ($group == DB_KEY_INTERNAL) {
			/* special handling for inbound */
			$inboundRow = $this->joomlaWatch->visit->getInternalNameById(@$name);
			$from = $inboundRow->from;
			$to = $inboundRow->to;

			$nameTranslated = "<br/>$from -> $to";
		}

		$groupTranslated = @constant ("_JW_STATS_".strtoupper($dbKeysArray[$group]));
		
		$helpId = $this->renderOnlineHelp("trends");
		$output = "<center><h2>" . _JW_TRENDS_DAILY_WEEKLY . " $groupTranslated : $nameTranslated $helpId</h2><br/>";
		$output .= "<table cellpadding='0' cellspacing='0'>";
		$output .= "<tr>";
		$progressBarIcon = "$this->mosConfig_live_site/components/com_joomlawatch/icons/progress_bar_vertical.gif";

		for ($i = $date -20; $i <= $date; $i++) {
			$value = ($resultsArray[$i - ($date -20)]);
			$percent = 0;
			if ($max) {
				$percent = floor(($value / $max) * 1000) / 10;
			}
			$output .= "<td valign='bottom' align='center'>";
			$output .= "$value<br/><img src='$progressBarIcon' height='$percent' width='10' /><br/>";
			$output .= $this->renderDayDiff($group, $name, $i -1, $i);
			$output .= $this->joomlaWatch->helper->getDateByDay($i, "d.m") . "<br/>";
			$output .= $this->joomlaWatch->helper->getDateByDay($i, "D") . "<br/>";
			$output .= "</td>";

		}
		$output .= "</tr>";
		$output .= "</table>";
		$output .= "</center>";

		return $output;

	}
	function renderWeekTrends($group, $name, $date) {

		$resultsArray = array ();
		$max = 0;
		$maxDate = 0;
		// first day has to be monday
		$offset = - (date("N", $date * 24 * 3600) - 1) + 7;
		for ($i = $date -20 * 7 + $offset; $i <= $date + $offset; $i += 7) {
			$value = $this->joomlaWatch->stat->getSumOfTwoDays($i, $i -7, $group, $name);
			if ($max < $value) {
				$max = $value;
				$maxDate = $i;
			}
			$resultsArray[$i - ($date -20 * 7)] = $value;
		}

		$output = "<center>";
		$output .= "<table>";
		$output .= "<tr>";
		$progressBarIcon = "$this->mosConfig_live_site/components/com_joomlawatch/icons/progress_bar_vertical_wide.gif";

		for ($i = $date -20 * 7 + $offset; $i <= $date + $offset; $i += 7) {
			$value = ($resultsArray[$i - ($date -20 * 7)]);
			$percent = 0;

			if ($max) {
				$percent = floor(($value / $max) * 1000) / 10;
			}
			$output .= "<td valign='bottom' align='center'>";
			$output .= "$value<br/><img src='$progressBarIcon' height='$percent' width='20' /><br/>";
			$relDiff = $this->joomlaWatch->stat->getRelDiffOfTwoWeeks($i, $i -7, $group, $name);
			$output .= $this->renderDiff($group, $name, $i -7, $relDiff);
			$output .= date("W/y", $i * 24 * 3600) . "<br/>";
			$output .= "</td>";

		}
		$output .= "</tr>";
		$output .= "</table>";
		$output .= "</center>";

		return $output;

	}
	function renderTrends() {
		$group = @ $_GET['group'];
		$name = @ $_GET['name'];
		$date = @ $_GET['date'];

		$output = "";
		$output .= "<br/><br/>";
		$output .= "<br/><br/>";
		$output .= "<br/><br/>";
		$output .= "<br/><br/>";
		$output .= $this->renderDayTrends($group, $name, $date);
		$output .= "<br/><br/>";
		$output .= $this->renderWeekTrends($group, $name, $date);
		return $output;

	}

	function renderPrint() {
		$group = @ $_GET['group'];
		$name = @ $_GET['name'];
		$date = @ $_GET['date'];
		$task = @ $_GET['task'];
		$action = @ $_GET['action'];

		$print = @ $_GET['print'];
		if (@ $print) {
			$output = "<script language='Javascript'>window.print();</script>";
		} else {
			$output = ("<table width='100%'><tr><td align='right'><a href='$this->mosConfig_live_site/components/com_joomlawatch/trendtooltip.php?rand=" . $this->joomlaWatch->config->getRand() . "&group=$group&name=$name&date=$date&print=1' target='_blank'><img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/print.gif' border='0' title='" . _JW_TOOLTIP_PRINT . "'/></a></td></tr></table>");
		}
		return $output;
	}

	function renderFrontendStats($joomlaWatch) {


		$output = "";
		$outputFirst = "";
		$outputSecond = "";
		if (@ $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_FRONTEND_COUNTRIES')) {
			$cachedItem = $this->joomlaWatch->cache->getCachedItem("CACHE_FRONTEND_COUNTRIES");
			if (@ $cachedItem) {
				$outputFirst .= stripslashes($cachedItem);
			} else {

				$numberOfCountries = @ $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_FRONTEND_COUNTRIES_NUM');
				if (!$numberOfCountries) {
					$numberOfCountries = 5;
				}

				$ctryOutput = "<h3 class='joomlawatch'>" . _JW_FRONTEND_COUNTRIES . "</h3>";
				$ctryOutput .= "<table width='150' border='0' class='joomlawatch'>";
				$ctryOutputFetched = $this->renderIntValuesByName("country", false, true, 0, $numberOfCountries, true);
				$ctryOutput .= $ctryOutputFetched;
				$ctryOutput .= "</table>";

				if ($ctryOutputFetched) {
					$this->joomlaWatch->cache->storeCachedItem("CACHE_FRONTEND_COUNTRIES", $ctryOutput);
				}
				$outputFirst .= $ctryOutput;
			}
		}

		if (@ $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_FRONTEND_VISITORS')) {

			$cachedItem = $this->joomlaWatch->cache->getCachedItem("CACHE_FRONTEND_VISITORS");
			if (@ $cachedItem) {
				$outputSecond .= stripslashes($cachedItem);
			} else {

				$todayDate = $this->joomlaWatch->helper->jwDateToday();
				$yesterdayDate = $todayDate -1;
				$dow = $this->joomlaWatch->helper->dayOfWeek();
				$dom = $this->joomlaWatch->helper->dayOfMonth();
				$numOfDaysActualMonth = date("t", $this->joomlaWatch->helper->getServerTime());
				$numOfDaysPrevMonth = date("t", $this->joomlaWatch->helper->getServerTime() - $numOfDaysActualMonth * 24 * 3600);
				$lastMonthsDate = $todayDate - $numOfDaysActualMonth;

				$timePeriodArray = array('TODAY', 'YESTERDAY','THIS_WEEK', 'LAST_WEEK', 'THIS_MONTH','LAST_MONTH','TOTAL');

				$visOutput = "";
				$visOutput .= "<h3 class='joomlawatch'>" . _JW_FRONTEND_VISITORS . "</h3>";
				$visOutput .= "<table width='150' border='0' class='joomlawatch'>";

				foreach ($timePeriodArray as $key) {

					if (!@$this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_FRONTEND_VISITORS_'.$key)) {
						continue;	// isn't enabled
					}

					switch($key) {
						case 'TODAY': {
							$value = $this->joomlaWatch->stat->getCountByKeyAndDate(DB_KEY_UNIQUE, $todayDate);
							break;
						}
						case 'YESTERDAY': {
							$value = $this->joomlaWatch->stat->getCountByKeyAndDate(DB_KEY_UNIQUE, $yesterdayDate);
							break;
						}
						case 'THIS_WEEK': {
							$value = $this->joomlaWatch->stat->getCountByKeyBetweenDates(DB_KEY_UNIQUE, $todayDate - $dow, $todayDate);
							break;
						}
						case 'LAST_WEEK': {
							$value = $this->joomlaWatch->stat->getCountByKeyBetweenDates(DB_KEY_UNIQUE, $todayDate - $dow -7, $todayDate - $dow);
							break;
						}
						case 'THIS_MONTH': {
							$value = $this->joomlaWatch->stat->getCountByKeyBetweenDates(DB_KEY_UNIQUE, $todayDate - $dom, $todayDate);
							break;
						}
						case 'LAST_MONTH': {
							$value = $this->joomlaWatch->stat->getCountByKeyBetweenDates(DB_KEY_UNIQUE, $lastMonthsDate - $numOfDaysPrevMonth, $lastMonthsDate);
							break;
						}
						case 'TOTAL': {
							$totalFromSettings = $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_FRONTEND_VISITORS_TOTAL_INITIAL');
							$totalReal = $this->joomlaWatch->stat->getTotalCountByKey(DB_KEY_UNIQUE);
							if (@ $totalFromSettings) {
								/** use total from settings, but still append the real value as a comment */
								$value = ($totalFromSettings + $totalReal) . "<!-- $totalReal -->";
							} else {
								$value = $totalReal;
							}
							break;
						}

					}
					if (isset($value)) {
						$visOutput .= "<tr><td align='left' valign='top' class='joomlawatch'>" . @constant("_JW_FRONTEND_".$key) . ": </td><td align='left' valign='top'> " . @ $value . "</td></tr>";
					}
				}
				$visOutput .= "</table>";

				if (@ $value) {
					$this->joomlaWatch->cache->storeCachedItem("CACHE_FRONTEND_VISITORS", $visOutput);
				}
				$outputSecond .= $visOutput;
			}
		}
		// to be able to swap order of Countries/Visitors
		if (@ $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_FRONTEND_COUNTRIES_FIRST')) {
			$output .= "<br/><br/>" . $outputFirst . "<br/>" . $outputSecond . "<br/><br/>";
		} else {
			$output .= "<br/><br/>" . $outputSecond . "<br/>" . $outputFirst . "<br/><br/>";
		}

		return $output;

	}

	function renderOnlineHelp($id) {
		$output = "&nbsp;<a href='http://www.codegravity.com/projects/joomlawatch#doc-$id' target='_blank'><img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/help.gif' border='0' title='" . _JW_TOOLTIP_HELP . ": $id'/></a>";
		return $output;
	}

	function renderCloseWindow() {
		$output = "<div align='right'><a href='javascript:ajax_hideTooltip();'>X " . _JW_TOOLTIP_WINDOW_CLOSE . "</a></div>";
		return $output;
	}

	function renderFrontendUsers() {

		$output = "";
		$cachedItem = $this->joomlaWatch->cache->getCachedItem("CACHE_FRONTEND_USERS");
		if (@ $cachedItem) {
			$output .= stripslashes($cachedItem);
		} else {

			$users = $this->joomlaWatch->stat->getUsersForToday();
			$count = $this->joomlaWatch->stat->countUsersForToday();

			/** no users */
			if (@!$count) {
				return;
			}


			$output = "<h2 class='joomlaWatch'>"._JW_STATS_USERS."</h2>";

			$output .= "<table class='joomlaWatch'><tr><td><u>"._JW_FRONTEND_USERS_MOST." $count:</td></tr><tr><td>";
			$link = $this->joomlaWatch->config->getConfigValue('JOOMLAWATCH_FRONTEND_USER_LINK');
			
			$i=0;
			$userSize = sizeof($users);
			foreach ($users as $user)  {
				$i++;
				if (@$link)	{
					$linkOutput = str_replace("{user}", $user->name, $link);
					$output .= "<a href='".$linkOutput."'>";
				}
				$output .= htmlspecialchars($user->name);
				if (@$link)	{
					$output .= "</a>";
				}
				if ($i<$userSize) {
					$output .= ", ";
				}
			}
			$output .= "</td></tr></table><br/><br/>";

			$this->joomlaWatch->cache->storeCachedItem("CACHE_FRONTEND_USERS", $output);
		}

		return $output;
	}

	function renderDateControl() {
		if (@ $_GET['day']) {
			$day = @ $_GET['day'];
		} else {
			$day = $this->joomlaWatch->helper->jwDateToday();
		}
		$prev = $day -1;
		$next = $day +1;
		$today = $this->joomlaWatch->helper->jwDateToday();

?>
	<table width='100%'>
		<tr><td align='left'><?php echo("<a href='javascript:setDay($prev)' id='$prev'>&lt;".date("d.m.Y",$prev*3600*24)."<img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/calendar.gif' border='0' align='center' /></a>"); ?></td>
		<td align='center'><?php if ($day != $today)echo("<a href='javascript:setDay($today)' id='$today'>"._JW_STATS_TODAY."</a>"); ?></td>
		<td align='right'><?php if ($next <= $today) echo("<a href='javascript:setDay($next)' id='$next'><img src='$this->mosConfig_live_site/components/com_joomlawatch/icons/calendar.gif' border='0' align='center' />".date("d.m.Y",$next*3600*24)."&gt;</a>"); ?></td>
		</tr>
	</table>
<?php
	}


}
?>