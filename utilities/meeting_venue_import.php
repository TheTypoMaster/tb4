<?php
require_once '../common/shell-bootstrap.php';

/**
 * Imports meeting venues and states from a CSV file
 */
class MeetingVenueImport extends TopBettaCLI
{
	/**
	 * MeetingVenue pointer
	 *
	 * @var TournamentModelMeetingVenue
	 */
	private $venue;

	/**
	 * Calls parent constructor, adds tournament models and sets the venue instance
	 */
	public function __construct() {
		parent::__construct();

		$this->addComponentModels('tournament');
		$this->venue =& JModel::getInstance('MeetingVenue', 'TournamentModel');
	}

	/**
	 * Checks the data file then imports the data if everything looks right
	 */
	public function execute() {
		$file = $this->arg('file');
		if(is_null($file)) {
			$this->l("No data file specified in arguments", self::LOG_TYPE_ERROR);
			return;
		}

		if(!file_exists($file)) {
			$this->l("File could not be found: {$file}", self::LOG_TYPE_ERROR);
			return;
		}

		$handle = fopen($file, 'r');
		if(!$handle) {
			$this->l("Failed to open filestream", self::LOG_TYPE_ERROR);
			return;
		}

		$count = 0;
		while($row = fgetcsv($handle)) {
			if(strtolower($row[0]) == 'venue' || strtolower($row[1]) == 'state') {
				continue;
			}

			$data = array(
				'name' 	=> trim($row[0]),
				'state'	=> trim($row[1])
			);

			$this->l("Creating record for {$data['name']} in {$data['state']}");
			$this->venue->store($data);

			++$count;
		}

		$this->l("Added {$count} venues");
	}
}

$cron = new MeetingVenueImport();
$cron->execute();