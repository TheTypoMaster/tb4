<?php
/**
 * @version		$Id: mod_login.php 10381 2008-06-01 03:35:53Z pasamio $
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

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

// Require wagering library
jimport( 'mobileactive.wagering.bet' );

$params->def('greeting', 1);

$type 	= modbsLoginHelper::getType();
$return	= base64_encode(JURI::current());

$user =& JFactory::getUser();

$ticket_list = array();
$funds = array();
/*

if(!$user->guest) {
	if(!class_exists('TournamentModelTournament')) {
		JLoader::import( 'tournament', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
	}

	if(!class_exists('TournamentModelTournamentTicket')) {
		JLoader::import( 'tournamentticket', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
	}

	if(!class_exists('TournamentModelTournamentRacing')) {
		JLoader::import( 'tournamentracing', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
	}

	if(!class_exists('TournamentModelTournamentSport')) {
		JLoader::import( 'tournamentsport', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
	}

	if(!class_exists('TournamentModelTournamentLeaderboard')) {
		JLoader::import( 'tournamentleaderboard', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
	}

	if(!class_exists('TournamentModelTournamentSportEvent')) {
		JLoader::import( 'tournamentsportevent', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
	}
		
	if(!class_exists('TournamentModelMeeting')) {
		JLoader::import( 'meeting', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
	}
	
	if(!class_exists('BettingModelBet')) {
		JLoader::import( 'bet', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' );
	}
	
	if(!class_exists('BettingModelBetSelection')) {
		JLoader::import( 'betselection', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' );
	}
	
	if(!class_exists('TopbettaUserModelTopbettaUser')) {
		JLoader::import( 'topbettauser', JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' );
	}

	$ticket_model = JModel::getInstance( 'TournamentTicket', 'TournamentModel' );

	$tournament_model	= JModel::getInstance('Tournament', 'TournamentModel');
	$racing_model		= JModel::getInstance('TournamentRacing', 'TournamentModel');
	$leaderboard_model	= JModel::getInstance('TournamentLeaderboard', 'TournamentModel');

	$tickets_open = array();

	$open_ticket_list = $ticket_model->getTournamentTicketActiveListByUserID($user->id);

	$tournament_sport_model = JModel::getInstance('TournamentSport', 'TournamentModel');
	$racing_sports			= $tournament_sport_model->excludeSports;

	$tournament_sport_event_model	= JModel::getInstance('TournamentSportEvent', 'TournamentModel');

	foreach($open_ticket_list as $ticket) {
		$tournament			= $tournament_model->getTournament($ticket->tournament_id);
		$tournament_sport	= $tournament_sport_model->getTournamentSport($tournament->tournament_sport_id);
		$bet_open			= strtotime($tournament->end_date) > time();
		$tournament_type	= in_array($tournament_sport->name, $racing_sports) ? 'racing' : 'sports';
		if('sports' == $tournament_type && $bet_open) {
			$sport_tournament	= $tournament_sport_event_model->getTournamentSportEventByTournamentID($ticket->tournament_id);
			$bet_open			= strtotime($sport_tournament->betting_closed_date) > time();
		}

		$icon_image = modbsLoginHelper::getTournamentIcon(preg_replace('/[^a-z0-9]/i', '', strtolower($tournament_sport->name)));

		$tickets_open[$ticket->sport_name][$ticket->id] = array(
			'ticket_id'			=> $ticket->id,
			'icon'				=> $icon_image,
			'buy_in'			=> $ticket->buy_in > 0 ? ('$' . number_format($ticket->buy_in / 100, 2)) : 'Free',
			'tournament_name'	=> $ticket->tournament_name,
			'tournament_id'		=> $ticket->tournament_id,
			'bet_open_txt'		=> $tournament->cancelled_flag ? 'Cancelled' : ($bet_open ? 'BETTING OPEN' : 'BETTING CLOSED'),
			'bet_open_class'	=> ($bet_open && !$tournament->cancelled_flag) ? 'betting-open' : 'betting-closed',
			'qualified_txt'		=> $tournament->cancelled_flag ? 'Cancelled' : 'Pending',
			'qualified_class'	=> 'ticket-pending',
			'leaderboard_rank'	=> 'N/A',
			'betta_bucks'		=> '$' . number_format($ticket_model->getAvailableTicketCurrency($ticket->tournament_id, $user->id) / 100, 2),
			'tournament_type'	=> $tournament_type,
		);

		$leaderboard = $leaderboard_model->getLeaderBoardRankByUserAndTournament($user->id, $tournament);

		if($leaderboard && !$tournament->cancelled_flag) 
			{
			$tickets_open[$ticket->sport_name][$ticket->id]['qualified_txt'] = ($leaderboard->qualified ? 'Qualified' : 'Pending');
			$tickets_open[$ticket->sport_name][$ticket->id]['qualified_class'] = ($leaderboard->qualified ? 'ticket-qualified' : 'ticket-pending');
			$tickets_open[$ticket->sport_name][$ticket->id]['leaderboard_rank'] = ($leaderboard->rank == '-' ? 'N/Q' : $leaderboard->rank);
			$tickets_open[$ticket->sport_name][$ticket->id]['betta_bucks'] = '$' . number_format($ticket_model->getAvailableTicketCurrency($tournament->id, $user->id)/100, 2);
			}
			
		}
	
	$ticket_button_class = (empty($tickets_open) ? ' class="inactive"' : '');

	$tickets_recent = array();
	$recent_ticket_list = $ticket_model->getTournamentTicketRecentListByUserID($user->id, time() - 48 * 60 * 60, time(), 1, 't.end_date DESC, t.start_date DESC');
	
	foreach($recent_ticket_list as $ticket) {
		$tournament			= $tournament_model->getTournament($ticket->tournament_id);
		$tournament_sport	= $tournament_sport_model->getTournamentSport($tournament->tournament_sport_id);
		$bet_open			= strtotime($tournament->end_date) > time();
		$tournament_type	= in_array($tournament_sport->name, $racing_sports) ? 'racing' : 'sports';

  		$icon_image = modbsLoginHelper::getTournamentIcon(preg_replace('/[^a-z0-9]/i', '', strtolower($tournament_sport->name)));

		$tickets_recent[$ticket->sport_name][$ticket->id] = array(
			'ticket_id'			=> $ticket->id,
			'icon'				=> $icon_image,
			'buy_in'			=> $ticket->buy_in > 0 ? ('$' . number_format($ticket->buy_in / 100, 2)) : 'Free',
			'tournament_name'	=> $ticket->tournament_name,
			'tournament_id'		=> $ticket->tournament_id,
			'bet_open_txt'		=> $tournament->cancelled_flag ? 'CANCELLED' : 'COMPLETED',
			'bet_open_class'	=> 'betting-completed',
			'qualified_txt'		=> 'All Paying',
			'qualified_class'	=> 'ticket-qualified',
			'leaderboard_rank'	=> 'N/A',
			'tournament_type'	=> $tournament_type,
		);

		$prize = 0;
		if(!$ticket->cancelled_flag && $ticket->result_transaction_id) {
			if($ticket->jackpot_flag) {
				$transaction_record =  $user->tournament_dollars->getTournamentTransaction($ticket->result_transaction_id);
			} else {
				$transaction_record =  $user->account_balance->getAccountTransaction($ticket->result_transaction_id);
			}

			if($transaction_record && $transaction_record->amount > 0 ) {
				$prize = $transaction_record->amount;
			}
		}
		$tickets_recent[$ticket->sport_name][$ticket->id]['prize'] = ('$' . number_format($prize / 100, 2) );
		
		$tickets_recent[$ticket->sport_name][$ticket->id]['winner_alert_flag'] = $ticket->winner_alert_flag;

		$leaderboard = $leaderboard_model->getLeaderBoardRankByUserAndTournament($user->id, $tournament);
		if($leaderboard) {
			$tickets_recent[$ticket->sport_name][$ticket->id]['leaderboard_rank'] = ($leaderboard->rank == '-' ? 'N/Q' : $leaderboard->rank);
			
			//if($leaderboard->rank == 1)
			//{
				$ticket_model->setWinnerAlertFlagByTournamentID($ticket->tournament_id);
			//}
		}
	}
	
	$meeting_model			= JModel::getInstance('Meeting', 'TournamentModel');
	$bet_model				= JModel::getInstance('Bet', 'BettingModel');
	$unresulted_bet_list	= $bet_model->getActiveBetListByUserID($user->id);
	$bets_unresulted		= modbsLoginHelper::getBetDisplayList($unresulted_bet_list);
	
	$recent_bet_list	= $bet_model->getBetRecentListByUserID($user->id, time() - 48 * 60 * 60, time(), 1, 'e.start_date DESC');
	$bets_recent		= modbsLoginHelper::getBetDisplayList($recent_bet_list, true);
	
	$bet_button_class	= (empty($bets_unresulted) ? ' class="inactive"' : '');
	
	$balances = array('account_balance', 'tournament_dollars');
	foreach($balances as $balance) {
		$amount = 0;
		if(!empty($user->$balance)) {
			$user->$balance->setUserId($user->get('id'));
			$amount = $user->$balance->getTotal();
			if(!empty($amount)) {
				$amount = $amount / 100;
			}
		}
		$funds[$balance] = '$ '.number_format($amount, 2, '.', ',');
	}
}
*/

//for user status
	$tb_status_model =& JModel::getInstance('TopbettaUser', 'TopbettaUserModel');
		if($tb_status_model->isTopbettaUser($user->id)) 
		{
			$tb_user = true;
		}
		else
		{
			$tb_user = false;
		}

require(JModuleHelper::getLayoutPath('mod_bslogin'));
