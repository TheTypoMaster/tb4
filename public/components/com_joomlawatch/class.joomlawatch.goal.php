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

class JoomlaWatchGoal {

	var $database;
	var $helper;
	var $stat;

	function JoomlaWatchGoal() {
		global $database;
		if (!JOOMLAWATCH_JOOMLA_15) // if Joomla 1.0
		$this->database = $database;
		else
		$this->database = & JFactory :: getDBO();

		$this->helper = new JoomlaWatchHelper();
		$this->stat = new JoomlaWatchStat();
	}

	
	/**
	 * goals
	 */
	function getGoalNameById($id) {
		$query = sprintf("select name from #__joomlawatch_goals where id = '%d'", (int) $id);
		$this->database->setQuery($query);
		$this->database->query();
		$rows = @ $this->database->loadObjectList();
		$row = @ $rows[0];
		return @ $row->name;
	}


	/**
	 * goals
	 */
	function checkGoals($title, $username, $ip, $came_from, $liveSite = "") {
		global $mainframe;

		$query = sprintf("select * from #__joomlawatch_goals");
		$this->database->setQuery($query);
		$this->database->query();
		$rows = $this->database->loadObjectList();
		foreach ($rows as $row) {

			$achieved = false;
			if ($row->disabled)
				continue;

			if (trim($row->uri_condition)) {
				if ($this->helper->wildcardSearch($row->uri_condition, trim($this->helper->getURI()))) {
					$achieved = true;
				} else
					continue;
			}
			if (trim($row->get_condition)) {
				if ($this->helper->wildcardSearch($row->get_condition, trim($_GET[$row->get_var]))) {
					$achieved = true;
				} else
					if ($row->get_var == "*") {
						foreach ($_GET as $get) {
							if ($this->helper->wildcardSearch($row->get_condition, trim($get))) {
								$achieved = true;
							}
						}
					} else
						continue;
			}
			if (trim($row->post_condition)) {
				if ($this->helper->wildcardSearch($row->post_condition, trim($_POST[$row->post_var]))) {
					$achieved = true;
				} else
					if ($row->post_var == "*") {
						foreach ($_POST as $post) {
							if ($this->helper->wildcardSearch($row->post_condition, trim($post))) {
								$achieved = true;
							}
						}
					} else
						continue;
			}
			if (trim($row->title_condition)) {
				if ($this->helper->wildcardSearch($row->title_condition, trim($title))) {
					$achieved = true;
				} else
					continue;
			}
			if (trim($row->username_condition)) {
				if ($this->helper->wildcardSearch($row->username_condition, trim($username))) {
					$achieved = true;
				} else
					continue;
			}
			if (trim($row->ip_condition)) {
				if ($this->helper->wildcardSearch($row->ip_condition, trim($ip))) {
					$achieved = true;
				} else
					continue;
			}
			if (trim($row->came_from_condition)) {
				if ($this->helper->wildcardSearch($row->came_from_condition, trim($came_from)) || $this->helper->wildcardSearch($liveSite.$row->came_from_condition, trim($came_from))) {
					$achieved = true;
				} else
					continue;
			}
			if (trim($row->country_condition)) {
				$country = $this->helper->countryByIp($ip);
				if ($this->helper->wildcardSearch($row->country_condition, trim($country))) {
					$achieved = true;
				} else
					continue;
			}

			if ($achieved) {
				$this->stat->increaseKeyValueInGroup(DB_KEY_GOALS, $row->id);

				if (@ $row->redirect) {
					// for 1.0 only ?
					mosRedirect(@ $row->redirect);
				}
				if (@ $row->block) {
					$this->dieWithBlockingMessage($ip);
				}
			}

		}
	}

	/**
	 * goals
	 */
	function getGoalById($id) {

		//lower case id
		$id = strtolower($id);
		$query = sprintf("select * from #__joomlawatch_goals where id = '%d' limit 1 ", (int) $id);
		$this->database->setQuery($query);
		$rows = $this->database->loadAssocList();
		$row = @ $rows[0];
		return $row;

	}

	/**
	 * goals
	 */
	function saveGoal($post) {
		$id = @ $post['id'];
		if (@ $id) {
			//update
			$query = sprintf("update #__joomlawatch_goals  set ");
			$length = sizeof($post);
			if (@ $post['option'])
				$length = $length -1;
			$i = 0;
			foreach ($post as $key => $value) {
				$i++;
				if ($key == "id")
					continue;
				$key = strtolower($key);
				$query .= sprintf("%s = '%s' ", JoomlaWatchHelper::sanitize($key), JoomlaWatchHelper::sanitize($value));
				if ($i < $length -1)
					$query .= ", ";
			}
			$query .= sprintf(" where id = '%d'", (int) $id);

			$this->database->setQuery($query);
			$result = $this->database->query();
			//			echo($query);

		} else {
			// insert
			unset($post['id']); // when it comes from new goal
			$query = sprintf("insert into #__joomlawatch_goals (id, ");
			$length = sizeof($post);
			if (@ $post['option'])
				$length = $length -1;
			$i = 0;
			foreach ($post as $key => $value) {
				if ($key == "id" || $key == "option")
					continue;
				$i++;
				$key = strtolower($key);
				$query .= sprintf("%s", JoomlaWatchHelper::sanitize($key));
				if ($i < $length)
					$query .= ", ";
			}
			$query .= ") values ('',";
			$i = 0;
			foreach ($post as $key => $value) {
				if ($key == "id" || $key == "option")
					continue;
				$i++;
				$key = strtolower($key);
				$query .= sprintf("'%s'", JoomlaWatchHelper::sanitize($value));
				if ($i < $length)
					$query .= ",";
			}
			$query .= ")";
			$this->database->setQuery($query);
			$result = $this->database->query();
		}

		return $result;
	}

	/**
	 * goals
	 */
	function deleteGoal($id) {
		$query = sprintf("delete from #__joomlawatch_goals where id = '%d' limit 1", (int) $id);
		$this->database->setQuery($query);
		//TODO delete everyting from logs as well!
		$result = $this->database->query();

		$query = sprintf("delete from #__joomlawatch_info where (`group`='".DB_KEY_GOALS."' and name='%d')", (int) $id);
		$this->database->setQuery($query);
		$this->database->query();
		return $result;
	}

	/**
	 * goals
	 */
	function enableGoal($id) {
		$query = sprintf("update #__joomlawatch_goals set disabled = NULL where id = '%d'", (int) $id);
		$this->database->setQuery($query);
		return $this->database->query();
	}

	/**
	 * goals
	 */
	function disableGoal($id) {
		$query = sprintf("update #__joomlawatch_goals set disabled = 1 where id = '%d'", (int) $id);
		$this->database->setQuery($query);
		return $this->database->query();
	}

	/**
	 * goals
	 */
	function getGoalCount($id) {
		$query = sprintf("select sum(value) as sum from #__joomlawatch_info where `group` = '".DB_KEY_GOALS."' and name = '%d'", (int) $id);
		$this->database->setQuery($query);
		$sum = $this->database->loadResult();
		return $sum;
	}
}

?>