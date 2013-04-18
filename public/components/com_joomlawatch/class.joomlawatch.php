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

class JoomlaWatch {

	var $database;
	
	var $visit;
	var $stat;
	var $config;
	var $helper;
	var $goal;
	var $block;
	var $cache;

	function JoomlaWatch() {
		global $database;
		if (!JOOMLAWATCH_JOOMLA_15) // if Joomla 1.0
			$this->database = $database;
		else
			$this->database = & JFactory :: getDBO();
			
			
		$this->stat = new JoomlaWatchStat();
		$this->config = new JoomlaWatchConfig();
		$this->helper = new JoomlaWatchHelper();
		$this->goal = new JoomlaWatchGoal();
		$this->block = new JoomlaWatchBlock();
		$this->visit = new JoomlaWatchVisit();
		$this->cache = new JoomlaWatchCache();

	}

	



}
?>