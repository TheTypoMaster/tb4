<?php
/**
 * @version		$Id: racing.php  Michael Costa $
 * @package		API
 * @subpackage
 * @copyright	Copyright (C) 2012 Michael Costa. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
jimport('joomla.application.component.controller');

class Api_Sport extends JController {

	function Api_Sport() {

	}

	/*
	 * MAPS TO: /com_tournament/controllers/tournamentsportevent.php->display
	 */
	public function getSportTournamentsByType() {

		if ($type = RequestHelper::validate('type')) {

			$component_list = array('tournament', 'topbetta_user');
			foreach ($component_list as $component) {
				$path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
				$this -> addModelPath($path);
			}

			//$jackpot = JRequest::getVar('jackpot', false);
			$jackpot = ($type == 'jackpot') ? TRUE : FALSE;

			$user = &JFactory::getUser();

			//$sport_id = JRequest::getVar('sport_id', null);
			/*
			 * sport id: 4=afl, 7=football, 5=rugby league
			 */ 
			$sport_id = RequestHelper::validate('sport_id');
			//$competition_id = JRequest::getVar('competition_id', null);
			/*
			 * competition id: 7=afl, 8=nrl, 57=premier league
			 */ 
			$competition_id = RequestHelper::validate('competition_id');
			//$jackpot = JRequest::getVar('jackpot', false);

			$sport_model = &$this -> getModel('TournamentSport', 'TournamentModel');
			//set up cookies for first visit, which will be used to display different banners when register
			if ($user -> guest) {
				$sport = $sport_model -> getTournamentSport($sport_id);
				if (!empty($sport)) {
					//setcookie("FirstVisit", $sport -> id, time() + 604800, '/');
				}
			}

			$tournament_model = &$this -> getModel('Tournament', 'TournamentModel');
			$tournament_sport_event_model = &$this -> getModel('TournamentSportEvent', 'TournamentModel');

			//get sport tournament list
			$list_params = array('sport_id' => $sport_id, 'competition_id' => $competition_id, 'jackpot' => $jackpot, 'private' => 0);
			$tournament_list = $tournament_sport_event_model -> getTournamentSportActiveList($list_params);
			//get sport list
			$sport_list = $tournament_sport_event_model -> getActiveTournamentSportList(false, 0);
			//get competition list
			$competition_model = &$this -> getModel('TournamentCompetition', 'TournamentModel');
			$competition_list = $competition_model -> getActiveTournamentCompetitionListBySportId(null, 0, 0);

			$ticket_model = &$this -> getModel('TournamentTicket', 'TournamentModel');
			if (!empty($tournament_list)) {
				foreach ($tournament_list as $tournament) {
					$tournament -> entrants = $ticket_model -> countTournamentEntrants($tournament -> id);
					$tournament -> prize_pool = $tournament_model -> calculateTournamentPrizePool($tournament -> id);
					$tournament -> place_list = $tournament_model -> calculateTournamentPlacesPaid($tournament, $tournament -> entrants, $tournament -> prize_pool);
				}
			}

			$ticket_list = array();
			if (!$user -> guest) {
				$ticket_list = $ticket_model -> getTournamentTicketActiveListByUserID($user -> id);
			}
			$data = array('tournament_list' => $tournament_list, 'ticket_list' => $ticket_list, 'jackpot' => $jackpot, 'tournament_type' => 'sports');
			/*
			$view = &$this -> getView('Tournament', 'html', 'TournamentView');

			$view -> assignRef('tournament_list', $tournament_list);
			$view -> assignRef('sport_list', $sport_list);
			$view -> assignRef('competition_list', $competition_list);
			$view -> assign('jackpot', (bool)$jackpot);
			$view -> assign('sport_id', (int)$sport_id);
			$view -> assign('competition_id', (int)$competition_id);
			$view -> assignRef('ticket_list', $ticket_list);
			$view -> assign('tournament_type', 'sports');

			$event_group_model = &$this -> getModel('EventGroup', 'TournamentModel');
			$view -> setModel($event_group_model);

			$competition_model = &$this -> getModel('TournamentCompetition', 'TournamentModel');
			$view -> setModel($competition_model);

			$view -> display();
			 */

			$result = OutputHelper::json(200, $data);
		} else {
			$result = OutputHelper::json(500, array('error_msg' => 'Not a valid type!'));
		}

		return $result;

	}

}
?>