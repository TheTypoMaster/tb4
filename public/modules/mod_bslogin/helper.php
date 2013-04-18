<?php
/**
* @version		$Id: helper.php 11668 2009-03-08 20:33:38Z willebil $
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
require_once JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'helpers' . DS . 'helper.php';
jimport( 'mobileactive.application.utilities.format' );

class modbsLoginHelper {
	public static function getReturnURL($params, $type) {
		if($itemid =  $params->get($type)) {
			$menu =& JSite::getMenu();
			$item = $menu->getItem($itemid);
			$url = JRoute::_($item->link.'&Itemid='.$itemid, false);
		} else {
			// stay on the same page
			$uri = JFactory::getURI();
			$url = $uri->toString(array('path', 'query', 'fragment'));
		}
		return base64_encode($url);
	}

	public static function getType() {
		$user = & JFactory::getUser();
		return (!$user->get('guest')) ? 'logout' : 'login';
	}

	public static function getSitename() {
		$user = & JFactory::getUser();
		$sitename = $user->get('name');
		return $sitename;
	}
	
	public static function getTournamentIcon($icon)
	{
		$image_extention_list = array('gif','png','jpg');
	
		foreach($image_extention_list as $extension){
			if(file_exists(JPATH_ROOT.'/templates/topbetta/images/icn_'.$icon.'.'.$extension)){
				return 'icn_'.$icon.'.'.$extension;
			}
		}
	
		return false;
	}
	
	public static function getBetDisplayList($bet_list, $include_results = false)
	{
		$bet_display_list = array();
		
		$meeting_model			= JModel::getInstance('Meeting', 'TournamentModel');
		$bet_selection_model	= JModel::getInstance('BetSelection', 'BettingModel');
		$wagering_bet			= WageringBet::newBet();
		
		foreach ($bet_list as $bet) {
			$meeting	= $meeting_model->getMeetingByRaceID($bet->event_id);
			
			if (is_null($meeting)) {
				//should not get to here
				continue;
			}
			
			$icon_image	= self::getTournamentIcon(strtolower($meeting->competition_name));
			
			$label = BettingHelper::getBetTicketDisplay($bet->id);
			
			$label .= ' - ' . $wagering_bet->getBetTypeDisplayName($bet->bet_type) . ' ';
			if ($bet->bet_freebet_amount > 0) {
				if($bet->bet_amount > $bet->bet_freebet_amount) 
					$label .= Format::currency(abs($bet->bet_amount), true) . ' (' . Format::currency(abs($bet->bet_freebet_amount), true) . ' Free credit)';
				else
					$label .= Format::currency(abs($bet->bet_freebet_amount), true) . ' (Free credit)';
			} 
			else {
				$label .= Format::currency(abs($bet->bet_total), true);
			}

			$bet_display_list[$bet->id] = array(
				'meeting_id'		=> $meeting->id,
				'race_number'		=> $bet->event_number,
				'competition_name'	=> $meeting->competition_name,
				'icon'				=> $icon_image,
				'bet_time'			=> date('d/m/y H:i:s', strtotime($bet->created_date)),
				'label'				=> $label,
			);
			
			if ($include_results) {
				if ('fully-refunded' == $bet->bet_result_status || ('partially-refunded' == $bet->bet_result_status && !$bet->win_amount)) {
					$bet_display_list[$bet->id]['result']		= 'refund';
					$bet_display_list[$bet->id]['win_amount']	= null;
				} else if (('paid' == $bet->bet_result_status || 'partially-refunded' == $bet->bet_result_status) && $bet->win_amount) {
					$bet_display_list[$bet->id]['result']		= 'win';
					$bet_display_list[$bet->id]['win_amount']	= '+' . FORMAT::currency($bet->win_amount, true);  
				} else if ('paid' == $bet->bet_result_status) {
					$bet_display_list[$bet->id]['result']		= 'loss';
					$bet_display_list[$bet->id]['win_amount']	= '-' . FORMAT::currency(abs($bet->bet_total), true);  	
				}
			}
		}
		
		return $bet_display_list;
	}
	
	//For API
	public static function getBetDisplayListApi($bet_list, $include_results = false)
	{
		$bet_display_list = array();
		
		$meeting_model			= JModel::getInstance('Meeting', 'TournamentModel');
		$bet_selection_model	= JModel::getInstance('BetSelection', 'BettingModel');
		$wagering_bet			= WageringBet::newBet();
		
		foreach ($bet_list as $bet) {
			$meeting	= $meeting_model->getMeetingByRaceID($bet->event_id);
			
			if (is_null($meeting)) {
				//should not get to here
				continue;
			}
			
			$icon_image	= self::getTournamentIcon(strtolower($meeting->competition_name));
			
			$label = BettingHelper::getBetTicketDisplay($bet->id);
			
			$label .= ' - ' . $wagering_bet->getBetTypeDisplayName($bet->bet_type);
			
			//Set the bet amount
			$bet_total = '';
			if ($bet->bet_freebet_amount > 0) {
				if($bet->bet_amount > $bet->bet_freebet_amount) 
					$bet_total = Format::currency(abs($bet->bet_amount), true) . ' (FC)';
				else
					$bet_total = Format::currency(abs($bet->bet_freebet_amount), true) . ' (FC)';
			} 
			else {
				$bet_total = Format::currency(abs($bet->bet_total), true);
			}
			
			$ticket_display	= BettingHelper::getBetTicketDisplayApi($bet->id);
			
			if(strtolower($bet->bet_type) == 'win' || strtolower($bet->bet_type) == 'place' || strtolower($bet->bet_type)=='eachway') // skip exodict bets for now
			{
				$bet_display_list[$bet->id] = array(
				'meeting_id'		=> $meeting->id,
				'race_number'		=> $bet->event_number,
				'competition_name'	=> $meeting->competition_name,
				'icon'				=> $icon_image,
				'bet_time'			=> date('d/m/y H:i:s', strtotime($bet->created_date)),
				'label'				=> $label,
				'bet_type'			=> $bet->bet_type,
				'bet_name'			=> $ticket_display['bet_name'],
				'bet_total'			=> $bet_total, //Format::currency(abs($bet->bet_total), true),
				//'runner_list'		=> $ticket_display['runner_list'],
				'runner_name' 		=> $ticket_display['runner_name'],
				'runner_number' 	=> $ticket_display['runner_number']
				);
				
				if ($include_results) {
					if ('fully-refunded' == $bet->bet_result_status || ('partially-refunded' == $bet->bet_result_status && !$bet->win_amount)) {
						$bet_display_list[$bet->id]['result']		= 'refund';
						$bet_display_list[$bet->id]['win_amount']	= Format::currency(abs($bet->bet_amount), true); //null;
					} else if (('paid' == $bet->bet_result_status || 'partially-refunded' == $bet->bet_result_status) && $bet->win_amount) {
						$bet_display_list[$bet->id]['result']		= 'win';
						$bet_display_list[$bet->id]['win_amount']	= '+' . FORMAT::currency($bet->win_amount, true);  
					} else if ('paid' == $bet->bet_result_status) {
						$bet_display_list[$bet->id]['result']		= 'loss';
						$bet_display_list[$bet->id]['win_amount']	= '-' . FORMAT::currency(abs($bet->bet_total), true);  	
					}
				}
			}
		}
		
		return $bet_display_list;
	}
}
