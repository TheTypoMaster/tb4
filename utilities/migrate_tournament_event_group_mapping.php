<?php
require_once '../common/shell-bootstrap.php';

define('__DATA_MIGRATION_IN_PROGRESS__', true);

class MigrateTournamentEventGroupMapping extends TopBettaCLI
{
	public function initialise()
	{
		$this->addComponentModels('tournament');
		$this->addComponentModels('betting');

		jimport('mobileactive.database.query');
		$this->db = $this->getDBO();
	}

	public function execute()
	{
		$count = 0;
		$batch = is_null($this->arg('batch')) ? 1 : $this->arg('batch');
		$limit = 1000;
		$start_limit = ($batch - 1) * $limit;
		
		$db =& $this->db;
		$this->l('Loading racing tournament mappings');
		
		$sql = 'SELECT  * FROM  `tbdb_tournament_racing` LIMIT '. $start_limit . ', ' . $limit;
		
		$this->d($sql);
		$db->setQuery($sql);
		
		$mapping_list = $db->loadObjectList();
		$this->l(sprintf('Loaded %d records to migrate', count($mapping_list)));

		foreach($mapping_list as $mapping) {
			
			$tournament = JModel::getInstance('Tournament', 'TournamentModel')
							->load($mapping->tournament_id);

			$this->d('TOURNAMENT CURRENT');
			$this->d($tournament);

			if(!is_null($tournament)) {
				$this->l(sprintf('Migrating tournament "%s" (%s) [#%s]', $tournament->name, $tournament->start_date, $count+1));
				$event_group = $this->_migrateMeeting($mapping, $tournament);
				
				if(is_int($event_group)) {
					$event_group = JModel::getInstance('Meeting', 'TournamentModel')->load($event_group);
					
					$event_group_id = $event_group->id;
					$type_code = $event_group->type_code;
				} else {
					$event_group_id = $event_group[0];
					$type_code = $event_group[1];
				}

				if($event_group_id) {
					$tournament->event_group_id 		= (int)$event_group_id;
					$tournament->tournament_sport_id 	= $this->_getSportByMeetingType($type_code)->id;
					$tournament->betting_closed_date	= $tournament->end_date;
					$tournament->reinvest_winnings_flag = 1;
					
					if (empty($tournament->description)) {
						$tournament->description = '-';
					}
					
					$this->d($tournament);
					$error_list = $tournament->validate();
					
					//some jackpot tournaments were set up wrongly, such as the parent tournaments were free or got deleted 
					//as a result, we don't want to validate 'parent_tournament_id'.
					//when we save it we don't re-validate the data
					unset($error_list['parent_tournament_id']);
					
					if(empty($error_list)) {
						$migrated_tournament_id = $tournament->save(false, false);
						$this->l(sprintf('New tournament id: %s', $migrated_tournament_id));
					} else {
						$this->d($error_list);
					}

					$this->d('TOURNAMENT MIGRATED');
					$this->d($tournament);
				}
			}

			++$count;
		}
	}

	private function _migrateMeeting($mapping, $tournament)
	{
		$meeting = $this->_getOldMeeting($mapping->meeting_id);
		if(is_null($meeting)) {
			$this->l(sprintf('ERROR: Meeting ID %d was not found in the database', $mapping->meeting_id));
			return false;
		}

		$this->d('MEETING CURRENT');
		$this->d($meeting);

		$this->l(sprintf('Processing meeting "%s"', $meeting->name));
		$migrated = JModel::getInstance('Meeting', 'TournamentModel')
						->getMeetingByMeetingCodeAndDate($meeting->meeting_code, $tournament->start_date);

		if(!empty($migrated)) {
			$this->l(sprintf('Meeting "%s" has already been migrated', $meeting->name));
			return $migrated->id;
		}

		unset($meeting->id);

		$migrated = JModel::getInstance('Meeting', 'TournamentModel')
						->setMembers((array)$meeting);
						
		$migrated->external_event_group_id = (int)$mapping->meeting_id;

		$meeting_type_code						= $this->_getMeetingTypeByID($migrated->meeting_type_id)->code;
		$migrated->tournament_competition_id 	= (int)$this->_getCompetitionByMeetingType($meeting_type_code)->id;
		$migrated->wagering_api_id				= (strtotime($tournament->start_date) > time()) ? $this->_getFutureMeetingWageringAPI()->id : $this->_getWageringAPI()->id;
		$migrated->start_date					= $tournament->start_date;
		$migrated->type_code					= $meeting_type_code;

		$event_group_id = $migrated->save(false, false);

		$this->d('MEETING MIGRATED');
		$this->d($migrated);

		$this->l('Meeting data has been migrated');

		$this->l('Loading race list');
		$race_list = $this->_getOldRaceListByMeetingID($mapping->meeting_id);

		if(empty($race_list)) {
			$this->l(sprintf('WARNING: No races found in the database for meeting "%s"', $meeting->name));
		} else {

			$this->l(sprintf('Found %d races to migrate', count($race_list)));

			foreach($race_list as $race) {
				$this->_migrateRace($race, $migrated);
			}
		}

		return array($event_group_id, $meeting_type_code);
	}

	private function _migrateRace($race, $meeting)
	{
		$this->d('RACE CURRENT');
		$this->d($race);

		$race_id 					= $race->id;
		$race->external_event_id 	= $race_id;
		unset($race->id);

		$migrated = JModel::getInstance('Race', 'TournamentModel')
						->setMembers((array)$race, array('race_status_id' => 'event_status_id'));
		
		$migrated->wagering_api_id	= $this->_getWageringAPI()->id;

		$event_id = $migrated->save(false, false);
		$this->d('RACE MIGRATED');
		$this->d($migrated);

		$ege = JModel::getInstance('EventGroupEvent', 'TournamentModel');

		$ege->event_group_id 	= $meeting->id;
		$ege->event_id			= $event_id;

		$ege->save(false, false);

		$this->d('EVENT GROUP EVENT MIGRATED');
		$this->d($ege);

		$market_type = $this->_getMarketType();
		
		$market = Jmodel::getInstance('Market', 'TournamentModel')
						->setMembers(array(
							'event_id' 				=> $event_id,
							'market_type_id' 		=> $market_type->id,
							'external_market_id' 	=> $race->external_event_id,
							'refund_flag'			=> 0,
							'wagering_api_id'		=> $this->_getWageringAPI()->id
						));

		$market->save(false, false);

		$this->d('MARKET MIGRATED');
		$this->d($market);

		$runner_list = $this->_getOldRunnerListByRaceID($race_id);
		if(empty($runner_list)) {
			$this->l(sprintf('ERROR: No runners found for race %d', $race->number));
			return;
		}

		foreach($runner_list as $runner) {
			$this->_migrateRunner($runner, $market);
		}
	}

	private function _migrateRunner($runner, $market)
	{
		$this->d('RUNNER CURRENT');
		$this->d($runner);

		$old_runner_id = $runner->id;
		unset($runner->id);

		$runner->market_id = $market->id;
		$runner->selection_status_id = $runner->runner_status_id;
		$migrated = JModel::getInstance('Runner', 'TournamentModel')
						->setMembers((array)$runner);
		
		$migrated->wagering_api_id	= $this->_getWageringAPI()->id;
		
		$runner_id = $migrated->save(false, false);

		$this->d('RUNNER MIGRATED');
		$this->d($migrated);

		$price_data = array(
			'selection_id' 		=> $runner_id,
			'bet_product_id' 	=> 2,
			'win_odds'			=> $runner->win_odds,
			'place_odds'		=> $runner->place_odds,
			'override_odds'		=> 0 //$runner->override_odds
		);

		$price = JModel::getInstance('SelectionPrice', 'TournamentModel')
					->setMembers($price_data);

		$price->save(false, false);

		$this->d('PRICE MIGRATED');
		$this->d($price);

		$result = new DatabaseQueryTable('#__result');
		$result->addWhere('runner_id', $old_runner_id);

		$query = new DatabaseQuery($result);

		$sql = $query->getSelect();
		
		$this->d($sql);
		
		$db =& $this->db;
		$db->setQuery($sql);
		
		$existing_result = $db->loadObject();
		
		if(!is_null($existing_result)){
			$selection_result_data = array(
				'selection_id'		=> (int)$runner_id,
				'position'			=> (int)$existing_result->position,
				'payout_flag'		=> (int)$existing_result->payout_flag,
			);
			
			$selection_result = JModel::getInstance('SelectionResult', 'TournamentModel')
									->setMembers($selection_result_data);
			$selection_result->save(false, false);
		
			$this->d('RESULT MIGRATED');
			$this->d($selection_result);
		}
		
		$merged_selection_check = new DatabaseQueryTable('#__tournament_bet_selection');
		$merged_selection_check->addWhere('selection_id', $runner_id);
		$query = new DatabaseQuery($merged_selection_check);
		
		$sql = $query->getSelect();
		
		$this->d($sql);
		
		$db =& $this->db;
		$db->setQuery($sql);
		
		if (!$db->loadObject()) {
			$tournament_racing_selection = new DatabaseQueryTable('#__tournament_racing_bet_selection');
			$tournament_racing_selection->addWhere('runner_id', $old_runner_id);
			
			$query = new DatabaseQuery($tournament_racing_selection);
			
			$sql = $query->getSelect();
			
			$this->d($sql);
			
			$db =& $this->db;
			$db->setQuery($sql);
			
			$existing_selection_list = $db->loadObjectList();
			
			if(!empty($existing_selection_list)){
				
				foreach ($existing_selection_list as $existing_selection) {
					$selectiont_data = array(
						'tournament_bet_id'	=> (int)$existing_selection->tournament_racing_bet_id,
						'selection_id'		=> (int)$runner_id,
					);
					
					$selection = JModel::getInstance('TournamentBetSelection', 'TournamentModel')
											->setMembers($selectiont_data);
					$selection->save(false, false);
				
					$this->d('Bet SELECTION MIGRATED');
					$this->d($selection);
				}
			}
		} else {
			$this->d('Bet SELECTION HAS ALREADY MIGRATED');
		}
	}

	private function _getOldMeeting($id)
	{
		$db =& $this->db;

		$table = new DatabaseQueryTable('#__meeting');
		$table->addWhere('id', $id);

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $db->loadObject();
	}

	private function _getOldRaceListByMeetingID($id)
	{
		$db =& $this->db;

		$table = new DatabaseQueryTable('#__race');
		$table->addWhere('meeting_id', $id);

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $db->loadObjectList();
	}

	private function _getOldRunnerListByRaceID($id)
	{
		$db =& $this->db;

		$table = new DatabaseQueryTable('#__runner');
		$table->addWhere('race_id', $id);

		$query = new DatabaseQuery($table);
		$db->setQuery($query->getSelect());

		return $db->loadObjectList();
	}

	private function _getMarketType()
	{
		static $market_type = null;
			
		if(is_null($market_type)) {
			$market_type = JModel::getInstance('MarketType', 'TournamentModel')
								->getMarketTypeByName('Racing');
		}

		return $market_type;
	}

	private function _getWageringAPI()
	{
		static $api = null;

		if(is_null($api)) {
			$api = JModel::getInstance('WageringAPI', 'BettingModel')
						->getWageringApiByKeyword('legacy');
		}

		return $api;
	}
	
	private function _getFutureMeetingWageringAPI()
	{
		static $api = null;

		if(is_null($api)) {
			$api = JModel::getInstance('WageringAPI', 'BettingModel')
						->getWageringApiByKeyword('tastab');
		}

		return $api;
	}

	private function _getCompetitionByMeetingType($code)
	{
		static $model_list = array();

		if(!array_key_exists($code, $model_list)) {
			$name = JModel::getInstance('Meeting', 'TournamentModel')
						->getCompetitionNameFromImportType($code);
			
			$sport_id 			= $this->_getSportByMeetingType($code)->id;
			$model_list[$code] 	= JModel::getInstance('TournamentCompetition', 'TournamentModel')
									->getTournamentCompetitionByNameAndSportID($name, $sport_id);
		}

		return $model_list[$code];
	}

	private function _getSportByMeetingType($code){
		static $model_list = array();
		
		if(!array_key_exists($code, $model_list)) {
			$name = JModel::getInstance('Meeting', 'TournamentModel')
			->getCompetitionNameFromImportType($code);
		
			$model_list[$code] 	= JModel::getInstance('TournamentSport', 'TournamentModel')
			->getTournamentSportByName($name);
		}
	
		return $model_list[$code];
	}
	
	private function _getMeetingTypeByID($id)
	{
		$this->d('MEETING TYPE ID');
		$this->d($id);

		return JModel::getInstance('MeetingType', 'TournamentModel')
					->load($id);
	}
}

$job = new MigrateTournamentEventGroupMapping();
$job->debug(false);
$job->execute();