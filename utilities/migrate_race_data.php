<?php
require_once '../common/shell-bootstrap.php';

jimport('mobileactive.config.reader');

class MigrateRaceData extends TopBettaCLI
{
	private $status_list = array(
		'processed' 					=> 'paying',
		'betting closed' 				=> 'interim',
		'betting open' 					=> 'selling',
		'abandoned' 					=> 'abandoned',
		'in progress ? betting closed' 	=> 'closed'
	);

	private $result_column_list = array(
		'first' 	=> 1,
		'second' 	=> 2,
		'third' 	=> 3,
		'fourth' 	=> 4
	);

	public function initialise() {
		$this->addComponentModels('tournament');

		$this->meeting 			=& JModel::getInstance('Meeting', 'TournamentModel');
		$this->meeting_type		=& JModel::getInstance('MeetingType', 'TournamentModel');

		$this->race				=& JModel::getInstance('Race', 'TournamentModel');
		$this->race_status 		=& JModel::getInstance('RaceStatus', 'TournamentModel');

		$this->runner			=& JModel::getInstance('Runner', 'TournamentModel');
		$this->runner_status 	=& JModel::getInstance('RunnerStatus', 'TournamentModel');

		$this->result			=& JModel::getInstance('Result', 'TournamentModel');
	}

	public function execute() {
		$db =& $this->getDBO();

		$meeting_query = 'SELECT * FROM ' . $db->nameQuote('racing_meeting') . ' ORDER BY id ASC';
		$db->setQuery($meeting_query);

		$meeting_list 	= $db->loadObjectList();
		$meeting_count	= count($meeting_list);

		$this->l("Found {$meeting_count} meetings to migrate");

		foreach($meeting_list as $meeting) {
			$this->l("Migrating {$meeting->name} ({$meeting->tab_meeting_id})");
			$this->d($meeting);

			$date 	= new DateTime(date('r', (int)$meeting->date)); // hey oliver - fuck you buddy
			$type	= $this->meeting_type->getMeetingTypeIDByName($meeting->type);

			list($venue, $state) 	= preg_split('/\s\(/', $meeting->name);
			$state 					= preg_replace('/[^A-Z]/i', '', $state);

			$meeting_data = array(
				'id' 				=> $meeting->id,
				'meeting_code' 		=> $meeting->tab_meeting_id,
				'name'				=> $venue,
				'state' 			=> $state,
				'events' 			=> $meeting->events,
				'meeting_type_id' 	=> $type,
				'track' 			=> $meeting->track,
				'weather' 			=> $meeting->weather,
				'meeting_date' 		=> $date->format('Y-m-d H:i:s')
			);

			$this->meeting->store($meeting_data, true);

			$race_query = 'SELECT * FROM ' . $db->nameQuote('racing_race') . ' WHERE meeting_id = ' . $db->quote($meeting->id) . ' ORDER BY id ASC';
			$db->setQuery($race_query);

			$race_list 	= $db->loadObjectList();
			$race_count	= count($race_list);

			if(empty($race_count)) {
				$this->e("Meeting {$meeting->name} ({$meeting->tab_meeting_id}) has no races");
				continue;
			}

			$this->l("Found {$race_count} races to migrate");
			foreach($race_list as $race) {
				$this->l("Migrating race number {$race->number} ({$race->tab_race_id})");
				$this->d($race);

				$status = (isset($this->status_list[$race->status])) ? $this->status_list[$race->status] : 'paying';
				$race_data = array(
					'id' 				=> $race->id,
					'meeting_id' 		=> $meeting->id,
					'number' 			=> $race->number,
					'start_date' 		=> $race->start_datetime,
					'distance' 			=> $race->distance,
					'class' 			=> $race->class,
					'paid_flag' 		=> $race->clients_paid,
					'race_status_id' 	=> $this->race_status->getRaceStatusIdByKeyword($status)
				);

				$this->race->store($race_data, true);

				$runner_query = 'SELECT * FROM ' . $db->nameQuote('racing_runner') . ' WHERE race_id = ' . $db->quote($race->id) . ' ORDER BY id ASC';
				$db->setQuery($runner_query);

				$runner_list 	= $db->loadObjectList();
				$runner_count	= count($runner_list);

				if(empty($runner_count)) {
					$this->e("Race number {$race->number} ({$race->tab_race_id}) has no runners");
					continue;
				}

				$this->l("Found {$runner_count} runners to migrate");
				foreach($runner_list as $runner) {
					$this->l("Migrating runner {$runner->name} ({$runner->number})");
					$this->d($runner);

					$runner_data = array(
						'id' 				=> $runner->id,
						'race_id' 			=> $race->id,
						'number' 			=> $runner->number,
						'name' 				=> $runner->name,
						'associate' 		=> $runner->associate,
						'runner_status_id' 	=> $this->runner_status->getRunnerStatusIdByKeyword($runner->status),
						'win_odds' 			=> $runner->win_odds,
						'place_odds' 		=> $runner->place_odds,
						'barrier' 			=> $runner->barrier,
						'handicap' 			=> $runner->handicap,
						'ident' 			=> $runner->ident
					);

					$this->runner->store($runner_data, true);
				}

				$result_db 		= $this->_getRaceDataDB();
				$result_query 	= 'SELECT * FROM ' . $db->nameQuote('results') . ' WHERE tab_race_id = ' . $db->quote($race->tab_race_id);

				$result_db->setQuery($result_query);
				$result = $result_db->loadObject();

				if(is_null($result)) {
					$this->e("Race number {$race->number} ({$race->tab_race_id}) has no results");
					continue;
				}

				$this->l("Migrating race result ({$race->tab_race_id})");
				foreach($this->result_column_list as $column => $position) {
					$position_list 	= array_filter(explode(':', $result->$column));
					$position_count = count($position_list);

					if(!empty($position_list)) {
						foreach($position_list as $number) {
							$this->l("Saving runner {$number} in position {$position}");

							$result_runner = $this->runner->getRunnerByRaceIDAndNumber($race->id, $number);
							$result_data = array(
								'race_id' 	=> $race->id,
								'runner_id' => $result_runner->id,
								'position'	=> $position
							);

							$this->result->store($result_data);
						}
					}
				}
			}
		}
	}

	private function _getRaceDataDB() {
		$server 	= ConfigReader::getInstance();
		$setting 	= $server->getDatabase('topbetta_race_data');

		$option = array(
			'host' 		=> $setting->getValue('host'),
			'user' 		=> $setting->getValue('user'),
			'password' 	=> $setting->getValue('password'),
			'database' 	=> $setting->getValue('database')
		);

		return $this->getDBO($option);
	}
}

$job = new MigrateRaceData();
$job->execute();