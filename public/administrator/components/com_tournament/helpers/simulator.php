<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * SimulatorHelper Class
 * @author Geoff
 * @version     $Id$
 */
class SimulatorHelper
{
	/**
	 * suffix to append to meeting code
	 * @var constant
	 */
	const SIMULATION_SUFFIX = 'SIMULATION';
	const SQL_DATETIME = 'Y-m-d H:i:s';
	/*
	 * Template types
	 * @var string
	 */
	const
		TEMPLATE_RACE_LIST_A = 1,
		TEMPLATE_RACE_LIST_B = 2,
		TEMPLATE_ABANDONED_MEETING = 3,
		TEMPLATE_ABANDONED_UNDER50 = 4,
		TEMPLATE_ABANDONED_OVER50 = 5;
	/*
	 * List of available templates
	 * @var array
	 * @static
	 */
	static private $_template_list = array(
		self::TEMPLATE_RACE_LIST_A 			=> 'Race Scenarios Template',
		self::TEMPLATE_ABANDONED_MEETING 	=> 'Abandoned Meeting Template',
		self::TEMPLATE_ABANDONED_UNDER50 	=> 'Under 50% Races Abandoned Template',
		self::TEMPLATE_ABANDONED_OVER50 	=> 'Over 50% Races Abandoned Template'
	);
	/**
	 * Race scenarios
	 * @var string
	 */
	const
		RACE_SCENARIO_DEAD_HEAT_FIRST = 'dead heat first',
		RACE_SCENARIO_DEAD_HEAT_LAST = 'dead heat last',
		RACE_SCENARIO_4_RUNNER = '4 runner race',
		RACE_SCENARIO_2_RUNNER = '2 runner race',
		RACE_SCENARIO_16_RUNNER = '16 runner race',
		RACE_SCENARIO_LATE_SCRATCHING = 'late scratching',
		RACE_SCENARIO_DISQUALIFIED = 'disqualified';
	/**
	 * Where to apply
	 * @var string
	 */
	const
		APPLY_TO_MEETING = 'meeting',
		APPLY_TO_OVER50 = 'under50',
		APPLY_TO_UNDER50 = 'over50',
		APPLY_TO_SINGLE_RACE = 'race';

	const
		MODEL_RACE = 'race',
		MODEL_RUNNER = 'runner',
		MODEL_MEETING = 'meeting',
		MODEL_RESULT = 'result',
		MODEL_TOURNAMENT = 'tournamentracing';

	static private $_model_list = array(
		self::MODEL_MEETING => 'Meeting',
		self::MODEL_RACE 	=> 'Race',
		self::MODEL_RUNNER	=> 'Runner',
		self::MODEL_RESULT  => 'Result',
		self::MODEL_TOURNAMENT => 'TournamentRacing'
	);
	/**
	 * Template matrix
	 * @access private
	 * @var intger
	 */
	private $_template_matrix = null;
	private $_result_matrix = null;
	private $_runner_matrix = array();
	private $_model = array();
	private $_meeting = null;
	private $_race_list = null;
	private $_time_shift = 0;
	private $_tournament = null;
	/**
	 * Object Pointer
	 * @var object
	 * @access private
	 * @static
	 */
	private static $_instance = null;
	public static $race_count = 0;
	/**
	 * Check for running simulation in meeting list
	 * @param object $active_meeting_list
	 * @static
	 */
	public static function checkForRunningSimulation($active_meeting_list)
	{

		foreach ($active_meeting_list as $meeting){

			if (preg_match('/(.+)-' . self::SIMULATION_SUFFIX . '/',$meeting->meeting_code)){

				$race_model = &JModel::getInstance(self::$_model_list[self::MODEL_RACE],'TournamentModel');
				$race_list = $race_model->getRaceListByMeetingID($meeting->id);

				$race = $race_list[count($race_list)-1];

				$end_date = new DateTime($race->start_date);
				$now_date = new DateTime();

				if ($end_date < $now_date && ($race->race_status_id != self::getRaceStatus(TournamentModelRace::STATUS_PAYING)->id && $race->race_status_id != self::getRaceStatus(TournamentModelRace::STATUS_ABANDONED)->id)){
					return $meeting;
				}
			}
		}

		return false;
	}
	public static function removeExistingSimulationFromMeetingList(&$meeting_list)
	{
		foreach ($meeting_list as $key => $meeting){
			if (preg_match('/(.+)-' . self::SIMULATION_SUFFIX . '/',$meeting->meeting_code)){
				unset($meeting_list[$key]);
			}
		}
	}
	/**
	 * get list of available templates
	 *
	 * @return multitype:string
	 */
	public static function getTemplateList()
	{
		return self::$_template_list;
	}
	/**
	 * Get list of available race statuses
	 *
	 * @return array
	 */
	public static function getRaceStatusList()
	{
		static $race_status_list = null;

		if (is_null($race_status_list)){
			$race_status_model =&JModel::getInstance('RaceStatus','TournamentModel');
			$race_status_list = $race_status_model->getRaceStatusList();
		}

		return $race_status_list;
	}
	/**
	 * Get race status based on keyword
	 *
	 * @param string $keyword
	 * @return object
	 */
	public static function getRaceStatus($keyword)
	{
		$list = self::getRaceStatusList();
		return $list[$keyword];
	}
	/**
	 * Get list of available runner statuses
	 *
	 * @return array
	 */
	public static function getRunnerStatusList()
	{
		static $runner_status_list = null;

		if (is_null($runner_status_list)){
			$runner_status_model =&JModel::getInstance('RunnerStatus','TournamentModel');
			$runner_status_list = $runner_status_model->getRunnerStatusList();
		}

		return $runner_status_list;
	}
	/**
	 * Get runner status by keyword
	 *
	 * @param string $keyword
	 * @return object
	 */
	public static function getRunnerStatus($keyword)
	{
		$list = self::getRunnerStatusList();
		return $list[$keyword];
	}
	/**
	 * Get object instance
	 *
	 * @return object
	 */
	public static function getInstance()
	{
		if (self::$_instance !== null){
			return self::$_instance;
		}

		self::$_instance = new SimulatorHelper();

		return self::$_instance;
	}
	/**
	 * Get template layout
	 *
	 * @param string $simulator_template
	 * @return void
	 */
	public static function getTemplateApplicatorList($simulator_template)
	{
		switch($simulator_template){
			case self::TEMPLATE_RACE_LIST_A:
				return array(
					self::APPLY_TO_SINGLE_RACE => array(
		 				'1' => array(
							self::RACE_SCENARIO_DEAD_HEAT_FIRST,
							self::RACE_SCENARIO_LATE_SCRATCHING,
							self::RACE_SCENARIO_DISQUALIFIED
						),
		 				'2' => array('race_status' => TournamentModelRace::STATUS_ABANDONED),
		 				'3' => array(
								'race_status' => TournamentModelRace::STATUS_PHOTO,
								self::RACE_SCENARIO_DEAD_HEAT_LAST
						),
		 				'4' => array('race_status' => TournamentModelRace::STATUS_PROTEST),
		 				'5'	=> self::RACE_SCENARIO_16_RUNNER,
		 				'6' => self::RACE_SCENARIO_4_RUNNER,
		 				'7' => self::RACE_SCENARIO_2_RUNNER,
					),
				);
				break;
			case self::TEMPLATE_ABANDONED_MEETING:
				return array(
				self::APPLY_TO_MEETING => array('race_status' => TournamentModelRace::STATUS_ABANDONED)
				);
				break;
			case self::TEMPLATE_ABANDONED_UNDER50:
				return array(
				self::APPLY_TO_UNDER50 => array('race_status' => TournamentModelRace::STATUS_ABANDONED),
				self::APPLY_TO_SINGLE_RACE => array(
						'7' => self::RACE_SCENARIO_DEAD_HEAT_LAST,
						'8' => self::RACE_SCENARIO_DISQUALIFIED
					)
				);
				break;
			case self::TEMPLATE_ABANDONED_OVER50:
				return array(
				self::APPLY_TO_OVER50 => array('race_status' => TournamentModelRace::STATUS_ABANDONED)
				);
				break;
			default:
		}
	}
	/**
	 * Generate information needed to modify meeting for simulation
	 *
	 * @param array $applicator_list
	 * @param array $race_list
	 * @return void
	 */
	public static function generateTemplateMatrix($applicator_list, $race_list)
	{
		$template_matrix = array();
		foreach($race_list as $race){
			foreach($applicator_list as $applicator_name => $scenario_race_list){
				$current_race =&$template_matrix[$race->number];
				if ($applicator_name === self::APPLY_TO_SINGLE_RACE){
					self::generateScenarioList($scenario_race_list[$race->number], $current_race);
					continue;
				}
				if ($applicator_name === self::APPLY_TO_OVER50){
					if (self::isOver50Race($race_list, $race)){
						self::generateScenarioList($scenario_race_list, $current_race);
					}
					continue;
				}
				if ($applicator_name === self::APPLY_TO_UNDER50){
					if (self::isUnder50Race($race_list, $race)){
						self::generateScenarioList($scenario_race_list, $current_race);
					}
					continue;
				}
				self::generateScenarioList($scenario_race_list, $current_race);
			}
		}
		return $template_matrix;
	}
	/**
	 * Generate scenario list for each race
	 *
	 * @param array $scenario_list
	 * @param object $current_race
	 * @return void
	 */
	public static function generateScenarioList($scenario_list, &$current_race)
	{
		if (is_array($scenario_list)){
			foreach ($scenario_list as $key => $scenario){
				if(is_array($scenario)){
					$key = key($scenario);
					$scenario = $scenario[$key];
				}
				if (is_int($key)){
					if(!in_array($scenario_list, $current_race)){
						$current_race[] = $scenario;
					}
				} else {
					$current_race[$key] = $scenario;
				}
			}
		} else {
			if (!is_null($scenario_list)){
				if (!in_array($scenario_list, $current_race)){
					$current_race[] = $scenario_list;
				}
			}
		}
	}
	/**
	 * check if race is part of just over 50%
	 *
	 * @param array $race_list
	 * @param object $race
	 * @return boolean
	 */
	public static function isOver50Race($race_list, $race)
	{
		return ceil(count($race_list)/2) > $race->number-1;
	}
	/**
	 * check if race is part of just under 50%
	 *
	 * @param array $race_list
	 * @param object $race
	 * @return boolean
	 */
	public static function isUnder50Race($race_list, $race)
	{
		return ceil(count($race_list)/2) > $race->number;
	}
	/**
	 * set configuration options via __set
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function __set($name, $value)
	{
		switch ($name){
			case 'use_template':
				$this->_loadTemplate($value);
				break;
			case 'meeting_id':
				$this->_loadMeeting($value);
				$this->_modifyMeeting();
				$this->_modifyRelatedTournaments();
				break;
			case 'start_in':
			case 'compress_meeting':
				$this->$name = $value;
		}
	}
	/**
	 * constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->_initialiseModelList();
	}
	/**
	 * initialise all models required
	 *
	 * @return void
	 */
	private function _initialiseModelList()
	{
		foreach (self::$_model_list as $key => $model){
			$this->_model[$key] = &JModel::getInstance($model,'TournamentModel');
		}
	}
	/**
	 * Load the meeting information
	 *
	 * @param integer $meeting_id
	 */
	private function _loadMeeting($meeting_id)
	{
		$this->_meeting 	= $this->_model[self::MODEL_MEETING]->getMeeting($meeting_id);
		$this->_race_list 	= $this->_model[self::MODEL_RACE]->getRaceListByMeetingID($meeting_id);
	}
	/**
	 * Modify meeting code
	 *
	 * @return void
	 */
	private function _modifyMeeting()
	{
		$this->_meeting->meeting_code = $this->_meeting->meeting_code . '-' . self::SIMULATION_SUFFIX;
	}
	/**
	 * Modify start and end time of tournaments using meeting
	 *
	 * @return void
	 */
	private function _modifyRelatedTournaments()
	{
		$tournament_list = $this->_model[self::MODEL_TOURNAMENT]->getTournamentRacingListByMeetingID($this->_meeting->id);
			
		$new_end_date = new DateTime('now');
		$start_date = new DateTime($tournament_list[0]->start_date);
		$end_date = new DateTime($tournament_list[0]->end_date);

		$this->_tournament = array('start_date' => $start_date, 'end_date' => $end_date);
		$this->_time_shift = (strtotime($new_end_date->format(self::SQL_DATETIME))- strtotime($end_date->format(self::SQL_DATETIME))). ' seconds';

		$end_date->modify($this->_time_shift);
		$start_date->modify($this->_time_shift);
	}
	/**
	 * get the template matrix
	 *
	 * @return array
	 */
	public function getTemplateMatrix()
	{
		return $this->_template_matrix;
	}
	/**
	 * Set template and prepare template matrix
	 *
	 * @param string $template
	 * @return void
	 */
	private function _loadTemplate($template)
	{
		if (!array_key_exists($template, self::$_template_list)){
			throw new Exception('Template does not exist');
		}
		$template_applicator_list = self::getTemplateApplicatorList($template);
		if (is_null($this->_race_list)){
			throw new Exception('$this->_race_list must be set to build the template');
		}
		$this->_template_matrix = self::generateTemplateMatrix($template_applicator_list, $this->_race_list);
	}
	/**
	 * Apply the template matrix to the meeting/race/runners and generate results
	 *
	 * @return void
	 */
	private function _applyTemplateMatrix()
	{
		foreach ($this->_race_list as $race){
			$this->_applyScenarioListToRace($race);
			$this->_buildResult($race);
			$this->_applyPostResultScenarioListToRace($race);
			$this->_adjustRaceTime($race);
		}
	}
	/**
	 * Adjust the race time of a race
	 *
	 * @param object $race
	 * @throws Exception
	 */
	private function _adjustRaceTime($race)
	{
		if (!is_null($this->_time_shift)){
			$race_start = new DateTime($race->start_date);
			$race_start->modify($this->_time_shift);
			$race->start_date = $race_start->format(self::SQL_DATETIME);
		} else {
			throw new Exception('Trying to adjust race start but _time_shift not set');
		}
	}
	/**
	 * Apply scenario list to race
	 *
	 * @param object $race
	 */
	private function _applyScenarioListToRace($race)
	{
		$scenario_list = $this->_getScenarioListByRaceNumber($race->number);
		foreach ($scenario_list as $scenario){
			$this->_applyScenarioToRace($race, $scenario);
		}
	}
	/**
	 * Apply post result generation scenarios to race (e.g dead heat odds)
	 *
	 * @param object $race
	 */
	private function _applyPostResultScenarioListToRace($race)
	{
		$this->_applyScenarioListToRace($race);
	}
	/**
	 * Get scenario list for race by race number
	 *
	 * @param integer $race_number
	 * @return array
	 */
	private function _getScenarioListByRaceNumber($race_number)
	{
		return $this->_template_matrix[$race_number];
	}
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $race
	 * @param unknown_type $scenario
	 */
	private function _applyScenarioToRace($race, $scenario)
	{
		if ($this->_isPostResult($race)){
			if (self::RACE_SCENARIO_DEAD_HEAT_FIRST == $scenario || self::RACE_SCENARIO_DEAD_HEAT_LAST == $scenario ){
				$this->_applyDeadHeatScenario($race, $scenario);
			}
			return;
		}

		if (array_key_exists($scenario, self::getRaceStatusList())){
			$this->_setRaceStatusId($race, $scenario);
			return;
		}

		switch ($scenario){
			case self::RACE_SCENARIO_4_RUNNER:
				$this->_applyRunnerScenario($race, 4);
				break;
			case self::RACE_SCENARIO_16_RUNNER:
				$this->_applyRunnerScenario($race, 16);
				break;
			case self::RACE_SCENARIO_2_RUNNER:
				$this->_applyRunnerScenario($race, 2);
				break;
			case self::RACE_SCENARIO_LATE_SCRATCHING:
				$this->_applyLateScratchingScenario($race);
				break;
			case self::RACE_SCENARIO_DISQUALIFIED:
				$this->_applyDisqualifiedScenario($race);
				break;
		}
	}
	/**
	 * Apply dead heat scenario to a race
	 *
	 * @param object $race
	 * @param string $scenario
	 */
	private function _applyDeadHeatScenario($race, $scenario)
	{
		$result_list = $this->_result_matrix[$race->number];

		if (self::RACE_SCENARIO_DEAD_HEAT_FIRST == $scenario)
		{
			foreach ($result_list as $result){
				if ($result['position'] == 1){
					$runner = $this->_model[self::MODEL_RUNNER]->getRunner($result['runner_id']);
					$runner->win_odds = $runner->win_odds/2 < 1 ? '1' : number_format($runner->win_odds/2,2);
					$this->_updateRunner($runner);
				}
			}
			return;
		}
		if (self::RACE_SCENARIO_DEAD_HEAT_LAST == $scenario)
		{
			foreach ($result_list as $key => $result){
				if ($result['payout_flag'] == 0){

					$result = $result_list[$key--];
					$runner = $this->_model[self::MODEL_RUNNER]->getRunner($result['runner_id']);
					$runner->place_odds = $runner->place_odds/2 < 1 ? '1' : number_format($runner->place_odds/2,2);
					$this->_updateRunner($runner);

					$result = $result_list[$key--];
					$runner = $this->_model[self::MODEL_RUNNER]->getRunner($result['runner_id']);
					$runner->place_odds = $runner->place_odds/2 < 1 ? '1' : number_format($runner->place_odds/2,2);
					$this->_updateRunner($runner);
					return;
				}
			}
		}
	}
	/**
	 * Modify runner count of race
	 * 
	 * @param object $race
	 * @param integer $runner_count_required
	 */
	private function _applyRunnerScenario($race, $runner_count_required)
	{
		$runner_list = $this->_getRunnerList($race);
		$runner_count = count($runner_list);
		$scratched_count = $this->_getScratchedCount($runner_list);
		$actual_runner_count = $runner_count - $scratched_count;

		if ($actual_runner_count === $runner_count_required){
			return;
		}

		if ($actual_runner_count > $runner_count_required){
			$scratch_count = $actual_runner_count - $runner_count_required;
			foreach ($runner_list as $runner){
				if ($scratch_count == 0){
					continue;
				}
				if ($runner->runner_status_id == self::getRunnerStatus(TournamentModelRunner::STATUS_NOT_SCRATCHED)->id){
					$runner->runner_status_id = self::getRunnerStatus(TournamentModelRunner::STATUS_SCRATCHED)->id;
					$this->_updateRunner($runner);
					$scratch_count--;
				}
			}
		}

		if ($actual_runner_count < $runner_count_required){
			$add_runner_count = null;
			$unscratch_count = 0;
			if ($runner_count >= $runner_count_required){
				$unscratch_count = $runner_count_required - $actual_runner_count;
			} else {
				$unscratch_count = $runner_count - $actual_runner_count;
				$add_runner_count = $runner_count_required - $runner_count;
			}
			if ($unscratch_count){
				foreach ($runner_list as $runner){
					if ($unscratch_count == 0){
						continue;
					}
					if ($runner->runner_status_id == self::getRunnerStatus(TournamentModelRunner::STATUS_SCRATCHED)->id){
						$runner->runner_status_id = self::getRunnerStatus(TournamentModelRunner::STATUS_NOT_SCRATCHED)->id;
						$this->_updateRunner($runner);
						$unscratch_count--;
					}

				}
			}
			if (!is_null($add_runner_count)){
				for ($i=1; $i<=$add_runner_count; $i++){
					$this->_addRunner($runner_list, $i+$runner_count);
				}
			}
		}
	}
	/**
	 * Apply late scratching scenario to race
	 * 
	 * @param object $race
	 */
	private function _applyLateScratchingScenario($race)
	{
		$runner_list = $this->_getRunnerList($race);

		$runner_count = count($runner_list);
		$runner = $runner_list[$runner_count];
		$runner->runner_status_id = self::getRunnerStatus(TournamentModelRunner::STATUS_LATE_SCRATCHING)->id;

		$this->_updateRunner($runner);
	}
	/**
	 * Apply disqualified scenario to race
	 * 
	 * @param object $race
	 * @deprecated
	 */
	private function _applyDisqualifiedScenario($race)
	{
	}
	/**
	 * Create runner based on existing runner
	 * 
	 * @param array $runner_list
	 * @param integer $number
	 */
	private function _addRunner($runner_list, $number)
	{
		$runner_key = array_rand($runner_list);
		$runner = clone $runner_list[$runner_key];
		$runner->number = $number;
		$runner->barrier = $number;
		$runner->name = $runner->name . ' ' . $number;
		$runner->ident = $runner->ident . 'n' . $number;
		$runner->id = null;
		$runner->runner_status_id = self::getRunnerStatus(TournamentModelRunner::STATUS_NOT_SCRATCHED)->id;
		$this->_runner_matrix['new'][] = $runner;
	}
	/**
	 * Update runner - store in matrix
	 * 
	 * @param array $runner
	 */
	private function _updateRunner($runner)
	{
		$this->_runner_matrix[$runner->id] = $runner;
	}
	/**
	 * Get runner list for a race
	 * 
	 * @param object $race
	 */
	private function _getRunnerList($race)
	{
		static $race_id = null;
		static $indexed_runner_list = null;

		if (is_null($indexed_runner_list) || $race_id != $race->id ){
			$runner_list = $this->_model[self::MODEL_RUNNER]->getRunnerListByRaceId($race->id);
			foreach ($runner_list as $runner){
				$indexed_runner_list[$runner->number] = isset($this->_runner_matrix[$runner->id]) ? $this->_runner_matrix[$runner->id] : $runner;
			}
		}

		return $indexed_runner_list;
	}
	/**
	 * Set race status id on a race by keyword
	 * 
	 * @param object $race
	 * @param string $keyword
	 */
	private function _setRaceStatusId($race, $keyword)
	{
		$race->race_status_id = self::getRaceStatus($keyword)->id;
	}
	/**
	 * Check to see if the result has been generated for a race
	 * 
	 * @param object $race
	 */
	private function _isPostResult($race)
	{
		return isset($this->_result_matrix[$race->number]);
	}
	/**
	 * Generate the result for a race
	 * 
	 * @param object $race
	 */
	private function _buildResult($race)
	{
		$model =& $this->_model[self::MODEL_RUNNER];
		$scenario_list = $this->_getScenarioListByRaceNumber($race->number);

		$runner_list = $this->_getRunnerList($race);

		$runner_count = count($runner_list);
		$scratched_count = $this->_getScratchedCount($runner_list);
		$late_scratched_count = $this->_getLateScratchedCount($runner_list);

		$runner_count = $runner_count - $scratched_count;
		$dividend_count = $this->_getDividendPaidCount($runner_count, $late_scratched_count);
		$position_count = $runner_count > 4 ? 4 : $runner_count;
		$position = 1;

		for($i=1; $i<=$position_count; $i++){
			$runner = $runner_list[$i];
			if (self::getRunnerStatus(TournamentModelRunner::STATUS_SCRATCHED)->id == $runner->runner_status_id || self::getRunnerStatus(TournamentModelRunner::STATUS_LATE_SCRATCHING)->id == $runner->runner_status_id){
				$position_count++;
				continue;
			}
			$result = array(
				'race_id' => $race->id,
				'runner_id'	=> $runner->id,
				'position'	=> $position,
				'payout_flag' => $position <= $dividend_count ? 1 : 0
			);

			if (in_array(self::RACE_SCENARIO_DEAD_HEAT_FIRST, $scenario_list) && $position == 2){
				$result['position'] = 1;
			}
			if (in_array(self::RACE_SCENARIO_DEAD_HEAT_LAST, $scenario_list) && $position == $dividend_count){
				$result['position'] = $position-1;
			}
			$this->_result_matrix[$race->number][] = $result;
			$position++;
		}
	}
	/**
	 * Get number of places paid out based on runner and late scratching count
	 * 
	 * @param integer $runner_count
	 * @param integer $late_scratched_count
	 */
	private function _getDividendPaidCount($runner_count, $late_scratched_count)
	{
		if ($runner_count < 8){
			return 2;
		}
		if ($runner_count >= 16){
			return 4;
		}
		if ($runner_count - $late_scratched_count < 5){
			return 2;
		}
		return 3;
	}
	
	private function _getScratchedCount(array $runner_list)
	{
		$scratched_count = 0;
		foreach ($runner_list as $runner){
			if (self::getRunnerStatus(TournamentModelRunner::STATUS_SCRATCHED)->id == $runner->runner_status_id){
				$scratched_count++;
			}
		}
		return $scratched_count;
	}

	private function _getLateScratchedCount(array $runner_list)
	{
		$late_scratched_count = 0;
		foreach ($runner_list as $runner){
			if (self::getRunnerStatus(TournamentModelRunner::STATUS_LATE_SCRATCHING)->id == $runner->runner_status_id){
				$late_scratched_count++;
			}
		}
		return $late_scratched_count;
	}
	/**
	 * Save simulation to the database
	 * 
	 * @return void
	 */
	public function save()
	{
		if (is_null($this->_meeting) || is_null($this->_race_list)){
			throw new Exception('Not all configuration options set');
		}
		if (is_null($this->_result_matrix) || is_null($this->_runner_matrix)){
			$this->_applyTemplateMatrix();
		}

		foreach (self::$_model_list as $key => $model_name){
			$model =& $this->_model[$key];
			switch ($key){
				case self::MODEL_MEETING:
					$model->store((array) $this->_meeting);
					break;
				case self::MODEL_RACE:
					foreach ($this->_race_list as $race){
						$model->store((array) $race);
					}
					break;
				case self::MODEL_RUNNER:
					if (array_key_exists('new', $this->_runner_matrix)){
						foreach ($this->_runner_matrix['new'] as $runner){
							$model->store((array) $runner);
						}
						unset($this->_runner_matrix['new']);
					}
					foreach ($this->_runner_matrix as $runner){
						$model->store((array) $runner);
					}
					break;
				case self::MODEL_RESULT:
					foreach ($this->_result_matrix as $race){
						foreach ($race as $race_result){
							$model->store($race_result);
						}
					}
					break;
				case self::MODEL_TOURNAMENT:
					$start_date = $this->_tournament['start_date']->format(self::SQL_DATETIME);
					$end_date = $this->_tournament['end_date']->format(self::SQL_DATETIME);
					$model->updateTournamentTimeByMeetingID($this->_meeting->id, $start_date, $end_date);
					break;
			}
		}
	}
}
