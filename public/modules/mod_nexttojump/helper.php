<?php
/**
* @version		$Id: helper.php 9764 2007-12-30 07:48:11Z ircmaxell $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('mobileactive.application.utilities.format');

class modNextToJumpHelper {

	public static function getNextToJump($meeting_type = null, $limit=null) {
		
		$meeting_type_id = null;
		if (!empty($meeting_type)) {
			if (!class_exists('TournamentModelTournamentCompetition')) {
				JLoader::import('tournamentcompetition', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
			}	
			$competition_model	= JModel::getInstance('TournamentCompetition', 'TournamentModel');
			$competition		= $competition_model->getCompetitionByName($meeting_type);
			$meeting_type_id	= $competition->id;
		}
		
		if (!class_exists('TournamentModelRace')) {
			JLoader::import( 'race', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
		}

		$race_model = JModel::getInstance('Race', 'TournamentModel');
		
		return $race_model->getTodayNextRaceListByMeetingTypeID($meeting_type_id, $limit);
	}
	
	public static function getHomepageNextToJump($meeting_type = null, $limit = null) {
		$race_list = self::getNextToJump($meeting_type, $limit);
		
		$ret = array();
		$counter_formatting = array(
			'day'		=> 'd',
			'hour'		=> 'h',
			'minute'	=> 'm',
			'second'	=> 's',
		);
		foreach ($race_list as $race) {
			
			$ret[] = array(
				'meeting_name'	=> $race->meeting_name,
				'number'		=> $race->number,
				'counter'		=> intval((strtotime($race->start_date) - time()) / 60) . 'm',
				'link'			=> '/betting/racing/meeting/' . $race->meeting_id . '/' . $race->number,
			);
		}
		
		return $ret;
	}
}


