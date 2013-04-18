<?php
/**
* @version		$Id: mod_whosonline.php 9764 2007-12-30 07:48:11Z ircmaxell $
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

// Include the whosonline functions only once
jimport('mobileactive.application.utilities.format');


if (!class_exists('TournamentModelTournament')) {
	JLoader::import('tournament', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
}

if (!class_exists('TournamentModelTournamentTicket')) {
	JLoader::import('tournamentticket', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
}

if (!class_exists('TournamentModelTournamentSport')) {
	JLoader::import('tournamentsport', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
}

if (!class_exists('TournamentModelEventGroup')) {
	JLoader::import('eventgroup', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
}

$user =& JFactory::getUser();

$tournament_model		= JModel::getInstance('Tournament', 'TournamentModel');
$ticket_model			= JModel::getInstance('TournamentTicket', 'TournamentModel');
$sport_model			= JModel::getInstance('TournamentSport', 'TournamentModel');
$event_group_model		= JModel::getInstance('EventGroup', 'TournamentModel');


$active_tournament_event_group_list = $event_group_model->getActiveTournamentEventGroupList();

$list_params = array(
	'private'			=> 0,
	'event_group_id'	=> array()
);

$i = 1;
foreach ($active_tournament_event_group_list as $event_group) {
	$list_params['event_group_id'][] = $event_group->id;
	$i++;
	
	if ($i > 2) {
		break;
	}
}

$uc_tournament_list = $tournament_model->getTournamentActiveList($list_params);

foreach ($uc_tournament_list as $tournament) {
	$tournament_type = $sport_model->isRacingByTournamentId($tournament->id) ? 'racing' : 'sports';
	
	$tournament->entrants     = $ticket_model->countTournamentEntrants($tournament->id);
	$tournament->prize_pool   = Format::currency($tournament_model->calculateTournamentPrizePool($tournament->id), true);
	
	$place_list	= $tournament_model->calculateTournamentPlacesPaid($tournament, $tournament->entrants, $tournament->prize_pool);
	$tournament->places_paid  = count($place_list['place']);
	
	if ($tournament->buy_in > 0) {
		$tournament->value = Format::currency($tournament->buy_in, true) . ' + ' . Format::currency($tournament->entry_fee, true);
	} else {
		$tournament->value = 'Free';
	}
	
	// $tournament->type = $tournament->jackpot_flag ? 'Jackpot' : 'Cash';
	if ($tournament->jackpot_flag) {
		$tournament->type = 'Jackpot';
	} elseif ($tournament->free_credit_flag){
		$tournament->type = 'Free';
	}else {
		$tournament->type = 'Cash';
	}
	
	
	$tournament->info_link_href= 'tournament/details/' . $tournament->id;
	$ticket_list = $ticket_model->getTournamentTicketActiveListByUserID($user->id);

	$time = strtotime($tournament->start_date);
	$tournament->time_class = ($time > time()) ? 'time' : 'timeP';
	$tournament->time		= ($time > time()) ? Format::counterText(strtotime($tournament->start_date)) : 'In Progress';
	
	$tournament->is_light_box_link = false;
	if ($tournament_type == 'sports') {
		$tournament->image = '/components/com_tournament/images/icon-'. preg_replace('/[^a-z0-9]/i', '', strtolower($tournament->sport_name)) . '.png';
	} else { 
		$tournament->image = '/templates/topbetta/images/icn_'. preg_replace('/[^a-z0-9]/i', '', strtolower($tournament->sport_name)) . '_sml.png';
	}
	
	if (!empty($ticket_list) && isset($ticket_list[$tournament->id])) {
		$tournament->entry_link_href	= 'tournament/'. $tournament_type . '/game/' . $tournament->id;
		$tournament->entry_link_text	= 'Bet Now';
		$tournament->entry_link_class	= 'bet_link';
	} else {
		$tournament->entry_link_href	= $user->guest ? '/user/register' : 'tournament/' . $tournament_type . '/confirmticket/' . $tournament->id;
		$tournament->entry_link_text	= 'Enter';
		$tournament->entry_link_class	= $user->guest ? '' : 'register_link';
		$tournament->is_light_box_link	= $user->guest ? false : true;
	}
}


$document =& JFactory::getDocument();

$document->addScript('components/com_tournament/assets/common.js');
$document->addScript('components/com_tournament/assets/tournslist.js');

require(JModuleHelper::getLayoutPath('mod_uctournaments'));
