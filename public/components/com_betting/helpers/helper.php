<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Betting Helper
 *
 */
class BettingHelper
{
	public static function getPositionNumber($position)
	{
		$position_number = null;
		switch ($position) {
			case 'first':
				$position_number = 1;
			break;
			case 'second':
				$position_number = 2;
			break;
			case 'third':
				$position_number = 3;
			break;
			case 'fourth':
				$position_number = 4;
			break;
		}
		
		return $position_number;
	}
	
	public static function getBetTicketDisplay($bet_id)
	{
		if (!class_exists('BettingModelBet')) {
			JLoader::import('bet', JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'models' );
		}
		if (!class_exists('BettingModelBetSelection')) {
			JLoader::import('betselection', JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'models' );
		}
		if (!class_exists('TournamentModelMeeting')) {
			JLoader::import('meeting', JPATH_SITE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
		}
		if (!class_exists('TournamentModelRace')) {
			JLoader::import('race', JPATH_SITE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
		}
		if (!class_exists('TournamentModelRunner')) {
			JLoader::import('runner', JPATH_SITE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
		}
		
		$bet_model				= JModel::getInstance('Bet', 'BettingModel');
		$meeting_model			= JModel::getInstance('Meeting', 'TournamentModel');
		$race_model				= JModel::getInstance('Race', 'TournamentModel');
		$runner_model			= JModel::getInstance('Runner', 'TournamentModel');
		$bet_selection_model	= JModel::getInstance('BetSelection', 'BettingModel');
		
		$wagering_bet = WageringBet::newBet();
		
		$bet		= $bet_model->getBetDetails($bet_id);
		$meeting	= $meeting_model->getMeetingByRaceID($bet->event_id);
		$race		= $race_model->getRace($bet->event_id);
		
		$ticket_display  = $meeting->name . ' (' . $meeting->competition_name . ') (Race ' . $race->number . ') ';
		
		if ($wagering_bet->isStandardBetType($bet->bet_type)) {
			$ticket_display .= $bet->selection_number . '.' . $bet->selection_name;
		} else {
			$selection_list		= $bet_selection_model->getBetSelectionListByBetID($bet->id);
			$selection_group	= array();
			foreach ($selection_list as $selection) {
				$selection_group[$selection->position][] = $selection->number; 
			}
			ksort($selection_group);
			
			foreach ($selection_group as $k => $selections) {
				$selection_group[$k] = implode(',', $selections);
			}
			
			$selection_display = implode('/', $selection_group);
			
			if (count($selection_group) == 1) {
				$selection_display .= '(BOXED)';
			}
			$ticket_display .= $selection_display;
		}
		
		return $ticket_display;
	}
	
	public static function getBetTicketDisplayApi($bet_id)
	{
		if (!class_exists('BettingModelBet')) {
			JLoader::import('bet', JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'models' );
		}
		if (!class_exists('BettingModelBetSelection')) {
			JLoader::import('betselection', JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'models' );
		}
		if (!class_exists('TournamentModelMeeting')) {
			JLoader::import('meeting', JPATH_SITE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
		}
		if (!class_exists('TournamentModelRace')) {
			JLoader::import('race', JPATH_SITE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
		}
		if (!class_exists('TournamentModelRunner')) {
			JLoader::import('runner', JPATH_SITE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
		}
		
		$bet_model				= JModel::getInstance('Bet', 'BettingModel');
		$meeting_model			= JModel::getInstance('Meeting', 'TournamentModel');
		$race_model				= JModel::getInstance('Race', 'TournamentModel');
		$runner_model			= JModel::getInstance('Runner', 'TournamentModel');
		$bet_selection_model	= JModel::getInstance('BetSelection', 'BettingModel');
		
		$wagering_bet = WageringBet::newBet();
		
		$bet		= $bet_model->getBetDetails($bet_id);
		$meeting	= $meeting_model->getMeetingByRaceID($bet->event_id);
		$race		= $race_model->getRace($bet->event_id);
		
		$ticket_display = array();
		$ticket_display['bet_name'] = $meeting->name;
		$ticket_display['competition_name'] = $meeting->competition_name;
		$ticket_display['race_number'] = $race->number;
		$ticket_display['runner_name'] = '';
		$ticket_display['runner_number'] = '';
		
		if ($wagering_bet->isStandardBetType($bet->bet_type)) {
			$ticket_display['runner_name'] = $bet->selection_name;
			$ticket_display['runner_number'] = $bet->selection_number;
		} else {
			/*
			$selection_list		= $bet_selection_model->getBetSelectionListByBetID($bet->id);
			$selection_group	= array();
			foreach ($selection_list as $selection) {
				$selection_group[$selection->position][$selection->number] = array('runner_name' => $selection->name, 'number' => $selection->number); 
			}
			ksort($selection_group);
			
			$selection_groups = array();
			foreach ($selection_group as $k => $selections) {
				foreach($selections as $selection) $selection_groups[$selection['number']] = $selection;
			}
			
			//$selection_display = implode('/', $selection_group);
			
			if (count($selection_group) == 1) {
				$ticket_display['runner_selection'] = '(BOXED)';
			} else $ticket_display['runner_selection'] = '';
			
			$ticket_display['runner_list'] = $selection_groups;
			*/
		}
		
		return $ticket_display;
	}
}
