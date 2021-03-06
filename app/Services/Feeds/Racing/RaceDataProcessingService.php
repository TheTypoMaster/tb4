<?php namespace TopBetta\Services\Feeds\Racing;
/**
 * Coded by Oliver Shanahan
 * File creation date: 03/04/15
 * File creation time: 14:26
 * Project: tb4
 */

use Illuminate\Support\Facades\Validator;
use Log;
use File;
use Carbon;
use Queue;

use TopBetta\Jobs\Pusher\Racing\PriceSocketUpdate;
use TopBetta\Jobs\Pusher\Racing\SelectionSocketUpdate;
use TopBetta\Repositories\Cache\MeetingRepository;
use TopBetta\Repositories\Cache\RaceRepository;
use TopBetta\Repositories\Cache\RacingSelectionPriceRepository;
use TopBetta\Repositories\Cache\RacingSelectionRepository;
use TopBetta\Repositories\Contracts\ProductProviderMatchRepositoryInterface;
use TopBetta\Repositories\Contracts\RunnerRepositoryInterface;
use TopBetta\Services\Tournaments\TournamentBetService;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionPriceRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\DataValueRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Repositories\Contracts\LastStartRepositoryInterface;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentEventGroupRepositoryInterface;


use TopBetta\Services\Caching\NextToJumpCacheService;
use TopBetta\Repositories\BetRepo;
use TopBetta\Repositories\BetResultRepo;
use TopBetta\Repositories\RisaFormRepository;

use TopBetta\Helpers\RiskManagerAPI;

class RaceDataProcessingService {

    protected $events;
    protected $selections;
    protected $results;
    protected $competitions;
	protected $datavalues;
	protected $tournaments;
	protected $nexttojump;
	protected $betrepository;
	protected $markets;
	protected $risaform;
	protected $laststarts;
	protected $prices;
	protected $betproduct;
    /**
     * @var TournamentBetService
     */
    private $tournamentBetService;
    /**
     * @var RunnerRepositoryInterface
     */
    private $runnerRepository;
    /**
     * @var ProductProviderMatchRepositoryInterface
     */
    private $productProviderMatchRepository;
    /**
     * @var BetTypeMapper
     */
    private $betTypeMapper;

    private $riskhelper;

    private $tournamenteventgroups;

    public function __construct(RaceRepository $events,
                                RacingSelectionRepository $selections,
								RacingSelectionPriceRepository $prices,
                                SelectionResultRepositoryInterface $results,
                                MeetingRepository $competitions,
								DataValueRepositoryInterface $datavalues,
								TournamentRepositoryInterface $tournaments,
								NextToJumpCacheService $nexttojump,
								BetProductRepositoryInterface $betproduct,
								BetRepo $betrepository,
								BetResultRepo $betresultrepository,
								BetResultRepo $betresultrepository,
								MarketRepositoryInterface $markets,
								RisaFormRepository $risaform,
								LastStartRepositoryInterface $laststarts,
                                TournamentBetService $tournamentBetService,
                                RunnerRepositoryInterface $runnerRepository,
                                ProductProviderMatchRepositoryInterface $productProviderMatchRepository,
                                BetTypeMapper $betTypeMapper,
                                RiskManagerAPI $riskhelper,
                                TournamentEventGroupRepositoryInterface $tournamenteventgroups){
        $this->events = $events;
        $this->selections = $selections;
        $this->results = $results;
        $this->competitions = $competitions;
        $this->datavalues = $datavalues;
        $this->tournaments = $tournaments;
		$this->nexttojump = $nexttojump;
		$this->betrepository = $betrepository;
		$this->betresultrepository = $betresultrepository;
		$this->markets = $markets;
		$this->risaform = $risaform;
		$this->laststarts = $laststarts;
		$this->prices = $prices;
		$this->betproduct = $betproduct;
		$this->tournamenteventgroups = $tournamenteventgroups;

		$this->logprefix = 'RaceDataProcessingService: ';
        $this->tournamentBetService = $tournamentBetService;
        $this->runnerRepository = $runnerRepository;
        $this->productProviderMatchRepository = $productProviderMatchRepository;
        $this->betTypeMapper = $betTypeMapper;
        $this->riskhelper = $riskhelper;
    }


	/**
	 * Pass payload to correct method for processing
	 *
	 * @param $data
	 * @return string|void
	 */
	public function processRacingData($data){
        //\Log::info(print_r($data,true));
		//Log::debug('Processing Payload');
		foreach ($data as $key => $racingData) {

			switch ($key) {
				case 'MeetingList':
					return $this->_processMeetingData(($racingData));
					break;
				case 'RaceList':
					return $this->_processRaceData(($racingData));
					break;
				case 'RunnerList':
					return $this->_processRunnerData(($racingData));
					break;
				case 'PriceList':
					return $this->_processPriceData(($racingData));
					break;
			}

		}
	}

	/**
	 * Process meeting payload
	 *
	 * @param $meetings
	 * @return string
	 */
	private function _processMeetingData($meetings){

		foreach ($meetings as $meeting) {

			/*
			 * validate meeting payload
			 */
			$rules = array('Id' => 'required',
				'Name' => 'required',
				'Date' => 'required',
				'RaceType' => 'required');
			$validator = Validator::make($meeting, $rules);
			if ($validator->fails()) {
				Log::debug($this->logprefix . 'Meeting data incomplete ', $meeting);
				continue;
			}

			$meetingDetails = array();
			$meetingDetails['name'] = $meeting['Name'];
			$meetingDetails['external_event_group_id'] = $meeting['Id'];
			$meetingDetails['type_code'] = $meeting['RaceType'];
			switch ($meeting['RaceType']) {
				case "R":
					$meetingDetails['tournament_competition_id'] = '31';
					break;
				case "T":
					$meetingDetails['type_code'] = 'H';
					$meetingDetails['tournament_competition_id'] = '32';
					break;
				case "G":
					$meetingDetails['tournament_competition_id'] = '33';
					break;
			}
			$meetingDetails['meeting_code'] = str_replace(" ", "", strtoupper($meeting['Name']) . "-" . $meetingDetails['type_code'] . "-" . substr($meeting['Date'], 0, 10));
			$existingMeeting = $this->competitions->getMeetingFromCode($meetingDetails['meeting_code']);

			// make sure there is no overlap with an existing sports competition
			if ($existingMeeting && $existingMeeting['type_code'] == 'NULL'){
				Log::debug($this->logprefix . 'Meeting overlaps sports ' . $meeting['Id']);
				continue;
			}

			// international races are now displayed by default
			if (isset($meeting['Country'])) {
				$meetingDetails['country'] = $meeting['Country'];
			}

			if (isset($meeting['EventCount'])) $meetingDetails['events'] = $meeting['EventCount'];
			if (isset($meeting['State'])) $meetingDetails['state'] = $meeting['State'];
			if (isset($meeting['MeetingType'])) $meetingDetails['meeting_grade'] = $meeting['MeetingType'];
			if (isset($meeting['RailPosition'])) $meetingDetails['rail_position'] = $meeting['RailPosition'];

			/*
			 * weather and track values are normalised if needed
			 */
			if (isset($meeting['Weather']) && !$meeting['Weather'] == "") {
				$meetingDetails['weather'] = $meeting['Weather'];
				$defaultWeather = $this->datavalues->getDefaultValueForType('weather_condition', $meeting['Weather']);
				if ($defaultWeather) $meetingDetails['weather'] = $defaultWeather;
			}
			if (isset($meeting['Track']) && !$meeting['Track'] == "") {
				$meetingDetails['track'] = $meeting['Track'];
				$defaultTrack = $this->datavalues->getDefaultValueForType('track_condition', $meeting['Track']);
				if ($defaultTrack) $meetingDetails['track'] = $defaultTrack;
			}

			$competition = $this->competitions->updateOrCreateLaravel(array('meeting_code' => $meetingDetails['meeting_code']), $meetingDetails);

            $this->attachDefaultProducts($competition);

            Log::info($this->logprefix. 'Meeting Saved - '.$meetingDetails['external_event_group_id']);

			/*
			 * Add tournament event group record
			 */
            $tournamentGroupDetails = array('name' => $meeting['Name'],
                                            'type' => 'race',
                                            'event_group_id' => $competition['id']
                                            );

            $tournamentEventGroup = $this->tournamenteventgroups->updateOrCreateLaravel(array('event_group_id' => $tournamentGroupDetails['event_group_id']), $tournamentGroupDetails);



		}

		return "Meeting(s) Processed";
	}

	/**
	 * Process the racing data payload
	 *
	 * @param $races
	 * @return string
	 */
	private function _processRaceData($races){

		$eventList = array();

		foreach ($races as $race) {

			/*
			 * validate race payload
			 */
			$rules = array('MeetingId' => 'required',
				'RaceNo' => 'required');
			$validator = Validator::make($race, $rules);
			if ($validator->fails()) {
				Log::debug($this->logprefix . 'Race data incomplete ', $race);
				continue;
			}

			$existingMeetingDetails = $this->competitions->getMeetingFromExternalId($race['MeetingId']);
			if(!$existingMeetingDetails) {
				Log::debug($this->logprefix . '(_processRaceData): Race meeting not found ' . $race['MeetingId']);
				continue;
			}

			// make sure there is no overlap with an existing sports competition
			if ($existingMeetingDetails['type_code'] == 'NULL'){
				Log::debug($this->logprefix . '(_processRaceData): Race meeting overlaps sports ' . $race['MeetingId']);
				continue;
			}

			// check if race exists in DB
			$existingRaceDetails = $this->events->getEventDetailByExternalId($race['MeetingId'].'_'.$race['RaceNo']);

			$raceDetails = array();

			if($existingRaceDetails) {
				Log::debug($this->logprefix . '(_processRaceData): Processing existing race - ' . $existingRaceDetails['external_event_id']);
				// build up the status check/order array
				$raceStatusCheckArray = array('O' => 1, 'C' => 2, 'I' => 3, 'R' => 4, 'A' => 5);
				$currentRaceStatus = $existingRaceDetails['event_status_id'];
				$raceDetails['event_status_id'] = $existingRaceDetails['event_status_id'];
			} else {
				$currentRaceStatus = 0;
				$raceDetails['event_status_id'] = '1';
				if($existingMeetingDetails['display_flag'] == '0') $raceDetails['display_flag'] = 0;
			}

			// race status path array
			$raceStatusCheck = array();
			$raceStatusCheck[0] = 0;
			$raceStatusCheck[1] = 1;
			$raceStatusCheck[5] = 2;
			$raceStatusCheck[6] = 3;
			$raceStatusCheck[2] = 4;
			$raceStatusCheck[4] = 5;
			$raceStatusCheck[3] = 6;

			$raceDetails['external_event_id'] = $race['MeetingId'].'_'.$race['RaceNo'];
			$raceDetails['number'] = $race['RaceNo'];
			$raceDetails['weather'] = $existingMeetingDetails['weather'];
			$raceDetails['track_condition'] = $existingMeetingDetails['track'];
			if (isset($race['JumpTime'])) $raceDetails['start_date'] = $race['JumpTime'];
			if (isset($race['RaceName'])) $raceDetails['name'] = $race['RaceName'];
			if (isset($race['Distance'])) $raceDetails['distance'] = $race['Distance'];
			if (isset($race['RaceClass'])) $raceDetails['class'] = $race['RaceClass'];

			// set start date for meeting if it's not set or this race is earlier
			if ($existingMeetingDetails['start_date'] == '0000-00-00 00:00:00' || $race['JumpTime'] < $existingMeetingDetails['start_date']) {
				$existingMeetingDetails['start_date'] = $race['JumpTime'];
                unset($existingMeetingDetails['created_at'], $existingMeetingDetails['updated_at']);
				$this->competitions->updateOrCreate($existingMeetingDetails, 'meeting_code');
			}

			// update tournament start times if required
			$tournamentsOnMeeting = $this->tournaments->getTournamentEventGroupWithEventGroup($existingMeetingDetails['id']);
			foreach ($tournamentsOnMeeting as $tournament) {
				// if it's race 1 store the jump time as tourn start date.
				if ($existingMeetingDetails['start_date'] == '0000-00-00 00:00:00' || $race['JumpTime'] < $existingMeetingDetails['start_date']) {
					$tournament['start_date'] = $race['JumpTime'];
				} else {
					if ($race['JumpTime'] > $tournament['end_date']) {
						$tournament['end_date'] = $race['JumpTime'];
					}
				}
				// Log::debug('RaceDataProcessingService: Tournament Update - ', $tournament);
				unset($tournament['created_at'], $tournament['updated_at']);

                Log::debug($this->logprefix . '(_processRaceData):Updateing Tournament Start and End Times: ', $tournament);
			//	$this->tournaments->updateOrCreate($tournament, 'id');
			}

            //set available products
            $loadedProducts = $this->productProviderMatchRepository->findAll()->keyBy('provider_product_name');
            $availableProducts = array();
            if ($products = array_get($race, 'Products')) {
                foreach ($products as $type => $productList) {
                    $betType = $this->betTypeMapper->getBetTypeName($type);

                    $availableProducts[$betType] = array_map(function ($v) use ($loadedProducts) {
                        return $loadedProducts->get($v)->tb_product_id;
                    }, $productList);
                }
            }

            $raceDetails['available_products'] = json_encode($availableProducts);

			if (isset($race['RaceStatus'])) {
				//example true || paying(4) < selling(1)
				if(!$existingRaceDetails || $raceStatusCheck[$currentRaceStatus] < $raceStatusCheckArray[$race['RaceStatus']]){
					switch ($race['RaceStatus']) {
						case "O":
							$raceDetails['event_status_id'] = '1'; // selling
							break;
						case "C":
							$raceDetails['event_status_id'] = '5'; // closed
							break;
						case "S":
							$raceDetails['event_status_id'] = '5'; // no suspended status in code table
							break;
						case "I":
							$raceDetails['event_status_id'] = '6'; // interim
							break;
						case "R":
							$raceDetails['event_status_id'] = '2'; // paying
							break;
						case "A":
							$raceDetails['event_status_id'] = '3'; // abandoned
							break;
						case "D":
							$raceDetails['event_status_id'] = '7'; // deleted
							break;
						default:
							Log::info($this->logprefix. 'Race status invalid - '.$race['RaceStatus']);
					}
				}
			}

			$raceDetails['weather'] = $existingMeetingDetails['weather'];
			$raceDetails['track_condition'] = $existingMeetingDetails['track'];

			$this->events->updateOrCreate($raceDetails, 'external_event_id');

			Log::info($this->logprefix. '(_processRaceData): Race Saved - '.$raceDetails['external_event_id'] .', Status - '.$raceDetails['event_status_id']);

            // push race status update to risk manager only if the race already exists and the status changes
            if($existingRaceDetails && $raceStatusCheck[$currentRaceStatus] < $raceStatusCheckArray[$race['RaceStatus']])
            {
                Log::info($this->logprefix. '(_processRaceData): Pushing race status update to Risk', $raceDetails);
                $race['status_id'] = $raceDetails['event_status_id'];
                // TODO: add notification
                //Queue::push('TopBetta\Services\Feeds\Queues\RiskManagerPushAPIQueueService', array('RaceStatusUpdate' => $race), 'risk-results-queue');
                $this->riskhelper->sendRaceStatus(array('RaceStatusUpdate' => $race));
            }

			// $eventId = $this->events->getEventIdFromExternalId($raceDetails['external_event_id']);

			$event = $this->events->getEventModelFromExternalId($raceDetails['external_event_id']);
            $eventId = $event->id;

			// add pivot table record if this is a newly added race
            Log::debug($this->logprefix.'(_processRaceData): Pivot Table Created for eventID - '. $eventId);
            $competitionModel = $this->competitions->getMeeting($existingMeetingDetails['id']);
            $this->events->addModelToCompetition($event, $competitionModel);

            /*
             * Add pivot tables records to link races to tournament event group
             */
            $tournamentEventGroup = $this->tournamenteventgroups->getTournamentEventGroupByEventGroupId($existingMeetingDetails['id']);
            if($tournamentEventGroup){
                $tournamentEventGroup->events()->attach($eventId);

                // if it's race 1 store the jump time as tourn start date.
                if ($tournamentEventGroup->start_date == '0000-00-00 00:00:00' || $race['JumpTime'] < $existingMeetingDetails['start_date']) {
                    $tournamentEventGroup->start_date = $race['JumpTime'];
                } else {
                    if ($race['JumpTime'] > $tournamentEventGroup->end_date) {
                        $tournamentEventGroup->end_date = $race['JumpTime'];
                    }
                }

                $tournamentEventGroup->save();
            }

			// if this event was abandoned - result bets
			if ($raceDetails['event_status_id'] == 3) {
                $this->betresultrepository->resultAllBetsForEvent($eventId);
                \Queue::push('TopBetta\Services\Tournaments\Queue\TournamentBetRefundQueueService', array("event_id" => $eventId), \Config::get('betresulting.queue'));
            }

			// N2J cache object check
			$this->nexttojump->manageCache($existingRaceDetails, $raceDetails);
		}

		return "Race(s) Processed";
	}

	/**
	 * Process runner data
	 *
	 * @param $runners
	 * @return string
	 */
	private function _processRunnerData($runners){

		$scratchList = array();
        $pusherUpdate = array();

		foreach ($runners as $runner) {
			$raceExists = $selectionsExists = 0;

			/*
			 * validate runner payload
			 */
			$rules = array('MeetingId' => 'required',
							'RaceNo' => 'required|integer',
							'RunnerNo' => 'required|integer');
			$validator = Validator::make($runner, $rules);
			if ($validator->fails()) {
				Log::debug($this->logprefix . 'Runner data incomplete ', $runner);
				continue;
			}

			// check if race exists in DB
			$existingRaceDetails = $this->events->getEventDetailByExternalId($runner['MeetingId'].'_'.$runner['RaceNo']);
			if(!$existingRaceDetails) {
				Log::debug($this->logprefix . 'Race for runner not found ' . $runner['MeetingId'].'_'.$runner['RaceNo']);
				continue;
			}

            if (!array_get($pusherUpdate, $existingRaceDetails['id'])) {
                $pusherUpdate[$existingRaceDetails['id']] = array();
            }

			$runnerDetails = array();
			$runnerDetails['number'] = $runner['RunnerNo'];
			$runnerDetails['external_selection_id'] = $existingRaceDetails['external_event_id'].'_'.$runner['RunnerNo'];
			$runnerDetails['name'] = array_get($runner, 'Name');
			$runnerDetails['barrier'] = array_get($runner, 'BarrierNo');
			$runnerDetails['associate'] = array_get($runner, 'Jockey');
			$runnerDetails['weight'] = array_get($runner, 'Weight') / 10;
			$runnerDetails['trainer'] = array_get($runner, 'Trainer', '');
			$runnerDetails['last_starts'] = array_get($runner, 'LastStarts', '');
			$runnerDetails['silk_id'] = array_get($runner, 'SilkCode', '');
            $runnerDetails['win_deductions'] = array_get($runner, 'WinDeductions', 0);
            $runnerDetails['place_deductions'] = array_get($runner, 'PlaceDeductions', 0);

            if(isset($runner['apn_fluctuations'])) $runnerDetails['apn_fluctuations'] = $runner['apn_fluctuations'];
            if(isset($runner['topbetta_fluctuations'])) $runnerDetails['topbetta_fluctuations'] = $runner['topbetta_fluctuations'];
            if(isset($runner['selection_scratched_time'])) $runnerDetails['scratching_time'] = $runner['selection_scratched_time'];

            if (isset($runner['Scratched'])) {

                if($runner['Scratched'] == '1'){
                    $runnerDetails['selection_status_id'] = '2';

                    // disable fixed odds betting on race
                    $existingRaceDetails['fixed_odds_enabled'] = 0;
                    $this->events->updateOrCreate($existingRaceDetails, 'external_event_id');

                    // notify risk
                    $this->riskhelper->sendRunnerScratchedStatus(array('RunnerScratched' => $runnerDetails['external_selection_id']));

                }else{
                    $runnerDetails['selection_status_id'] = '1';
                }

            }else{
                $runnerDetails['selection_status_id'] = '1';
            }


			// check if market exists and create a market for this selection/event if not
			$existingMarketDetails = $this->markets->getMarketDetailByEventIdAndMarket($existingRaceDetails['id'], 110);
			if(!$existingMarketDetails){
				$marketDetails = array('event_id' => $existingRaceDetails['id'], 'market_type_id' => '110');
				$marketDetails = $this->markets->create($marketDetails);
				$runnerDetails['market_id'] = $marketDetails['id'];
			}else{
				$runnerDetails['market_id'] = $existingMarketDetails['id'];
			}

            //get the runner
            if( $baseRunner = array_get($runner, 'external_runner_id') ) {
                $baseRunner = $this->runnerRepository->getByExternalId($baseRunner);
                $runnerDetails['runner_id'] = $baseRunner ? $baseRunner->id : 0;
            }

			// meeting details
			$existingMeetingDetails = $this->competitions->getMeetingFromExternalId($runner['MeetingId']);
			$meetingDate = substr($existingMeetingDetails['start_date'], 0, 10);
			$meetingCode = $existingMeetingDetails['type_code'];
			$meetingVenue = $existingMeetingDetails['name'];

			// pad runner and race numbers if single digit
			$raceNumber = str_pad($runner['RaceNo'], 2, '0', STR_PAD_LEFT);
			$runnerNumber = str_pad($runnerDetails['number'], 2, '0', STR_PAD_LEFT);

			// Build the runner code - use external_selection_id
			$runnerDetails['runner_code'] = $runnerDetails['external_selection_id'];

			$runnerSaved = $this->selections->updateOrCreate($runnerDetails, 'external_selection_id');
			// get the runner id and update the wager_id...?
			$runnerId = $this->selections->getSeletcionIdByExternalId($existingRaceDetails['external_event_id'].'_'.$runner['RunnerNo']);
			$selectionUpdate = array('id' => $runnerId, 'wager_id' => $runnerId);

			// form
			if(array_get($runner, 'Results') != '0(0-0-0)' && array_get($runner, 'Results') != NULL){
				$formDetails = array();
				$formDetails['race_code'] = $existingRaceDetails['external_event_id'];
				$formDetails['horse_code'] = $runnerDetails['external_selection_id'];
				$formDetails['runner_code'] = $runnerDetails['external_selection_id'];

				$formDetails['career_results'] = array_get($runner, 'Results');
				$formDetails['distance_results'] = array_get($runner, 'ThisDist');
				$formDetails['track_results'] = array_get($runner, 'ThisTrack');
				$formDetails['track_distance_results'] = array_get($runner, 'TrackDist');
				$formDetails['first_up_results'] = array_get($runner, 'FirstUp');
				$formDetails['second_up_results'] = array_get($runner, 'SecondUp');
				$formDetails['good_results'] = array_get($runner, 'Good');
				$formDetails['firm_results'] = array_get($runner, 'Firm');
				$formDetails['soft_results'] = array_get($runner, 'Soft');
				$formDetails['synthetic_results'] = array_get($runner, 'Synthetic');
				$formDetails['wet_results'] = array_get($runner, 'Wet');
				$formDetails['nonwet_results'] = array_get($runner, 'NonWet');
				$formDetails['night_results'] = array_get($runner, 'Night');
				$formDetails['jumps_results'] = array_get($runner, 'Jumps');
				$formDetails['season_results'] = array_get($runner, 'Season');
				$formDetails['heavy_results'] = array_get($runner, 'Heavy');
				//$formDetails['comment'] = array_get($runner, '');

				$this->risaform->updateOrCreate($formDetails, 'runner_code');

				$formId = $this->risaform->getFormIdByRunnerCode($formDetails['runner_code']);

				Log::info($this->logprefix. 'Runner Form Saved - '.$runnerDetails['external_selection_id']);
			}




			// if this runner is scratched add it to the scratching array
			if (isset($runnerId) && $runnerDetails['selection_status_id'] == '2') {
				if (!array_key_exists($runnerId, array_flip($scratchList))) {
					array_push($scratchList, $runnerId);
				}
			}

			if(isset($runner['LastStartsLong']) && (isset($formId))){
				// store last starts data
				foreach($runner['LastStartsLong'] as $lastStartLong){

					$runnerLastStarts = array();

					$runnerLastStarts['runner_form_id'] = $formId;
					$runnerLastStarts['race_code'] = $lastStartLong['AbrVenue'].'_'.$lastStartLong['Date'];
					$runnerLastStarts['horse_code'] = $runnerDetails['external_selection_id'];
					$runnerLastStarts['runner_code'] = $runnerDetails['external_selection_id'];
					$runnerLastStarts['mgt_date'] = array_get($lastStartLong, 'Date');
					$runnerLastStarts['race_distance'] = array_get($lastStartLong, 'Distance');
					$runnerLastStarts['race_starters'] = array_get($lastStartLong, 'Starters');
					$runnerLastStarts['finish_position'] = array_get($lastStartLong, 'FinishPositon');
					$runnerLastStarts['jockey_initials'] = array_get($lastStartLong, 'JockeyInitials');
					$runnerLastStarts['jockey_surname'] = array_get($lastStartLong, 'JockeySurname');
					$runnerLastStarts['barrier'] = array_get($lastStartLong, 'Barrier');
					$runnerLastStarts['abr_venue'] = array_get($lastStartLong, 'AbrVenue');
					$runnerLastStarts['handicap'] = array_get($lastStartLong, 'Handicap');
					$runnerLastStarts['starting_win_price'] = array_get($lastStartLong, 'WinStartPrice');
					$runnerLastStarts['track_condition'] = array_get($lastStartLong, 'TrackCondition');
					$runnerLastStarts['in_running_400'] = array_get($lastStartLong, 'InRunning400', '');
					$runnerLastStarts['in_running_800'] = array_get($lastStartLong, 'InRunning800', '');
					$runnerLastStarts['name_race_form'] = array_get($lastStartLong, 'Class');
					$runnerLastStarts['other_runner_name'] = array_get($lastStartLong, 'OtherRunnerName');
					$runnerLastStarts['other_runner_barrier'] = array_get($lastStartLong, 'OtherRunnerBarrier');
					$runnerLastStarts['other_runner_time'] = array_get($lastStartLong, 'OtherRunnerTime');
					$runnerLastStarts['margin_decimal'] = array_get($lastStartLong, 'MarginDecimal');
					$runnerLastStarts['numeric_rating'] = array_get($lastStartLong, 'Rating');

//				$runnerLastStarts['WeightCarried'] = array_get($lastStartLong, 'r_base_ls_weight_carried');
//				$runnerLastStarts['Comments'] = array_get($lastLong, 'r_base_ls_comments');
//				$runnerLastStarts['Class'] = array_get($lastStartLong, 'r_base_ls_form');
//				$runnerLastStarts['Time'] = array_get($lastStartLong, 'r_base_ls_time');
//				$runnerLastStarts['Prize'] = array_get($lastStartLong, 'r_base_ls_prize');
//				$runnerLastStarts['Bonus'] = array_get($lastStartLong, 'r_base_ls_bonus');


					$existingLastStart = $this->laststarts->getLastStartIdByRaceAndHorseCode($runnerLastStarts['race_code'], $runnerLastStarts['horse_code']);

					Log::debug($this->logprefix.' Last Starts for Form ID - '.$runnerLastStarts['runner_form_id'].': '.$existingLastStart. '. '.$runner['MeetingId'].'_'.$runner['RaceNo'].' '.$runnerDetails['external_selection_id']);

					if($existingLastStart) {
						$runnerLastStarts['id'] = $existingLastStart;
						$this->laststarts->updateOrCreate($runnerLastStarts, 'id');
						Log::debug($this->logprefix.' Last Start for Form ID - '.$runnerLastStarts['runner_form_id'].', Updating Existing');
					}else{
						$this->laststarts->create($runnerLastStarts);
						Log::debug($this->logprefix.' Last Start for Form ID - '.$runnerLastStarts['runner_form_id'].', Adding New');
					}

					//$this->laststarts->updateOrCreate($runner['MeetingId'].'_'.$runner['RaceNo']);

				}
			}

            $selection = $this->selections->find($runnerId);

            if ($selection && $selection->fill($selectionUpdate)->isDirty()) {
                $pusherUpdate[$existingRaceDetails['id']][] = $this->selections->update($selection, $selectionUpdate);
            } else if (!$selection) {
                $pusherUpdate[$existingRaceDetails['id']][] = $this->selections->create($selectionUpdate);
            }


            Log::info($this->logprefix. 'Runner Saved - '.$runnerDetails['external_selection_id']);

		}

		// refund bets on scratched runners
		foreach ($scratchList as $scratchedId) {
			Log::info($this->logprefix.'Scratching - Refunding bets for runner id: ' . $scratchedId);
			$this->betrepository->refundBetsForRunnerId($scratchedId);
            $this->tournamentBetService->refundBetsForSelection($scratchedId);
		}

        foreach ($pusherUpdate as $race => $selections) {
            if (count($selections)) {
                \Bus::dispatch(new SelectionSocketUpdate(array("id" => $race, "selections" => $selections)));
            }
        }

		return "Runenr(s) Processed";
	}

	/**
	 * Process price data
	 *
	 * @param $prices
	 * @return string
	 */
	private function _processPriceData($prices){

        $updates = array();

		foreach ($prices as $price) {

			/*
			 * validate runner payload
			 */
			$rules = array('MeetingId' => 'required',
				'RaceNo' => 'required|integer',
				'BetType' => 'required',
				'PriceType' => 'required',
				//'PoolAmount' => 'required',
				'OddString' => 'required');
			$validator = Validator::make($price, $rules);
			if ($validator->fails()) {
				Log::debug($this->logprefix . 'Price data incomplete - ' . $validator->messages());
				continue;
			}

			$providerName = "igas";

			// explode the odds string
			$oddsArray = explode(';', $price['OddString']);

			if (!is_array($oddsArray)) {
				Log::debug($this->logprefix . 'Price data odds incomplete ', $price);
				continue;
			}

			// check if race exists in DB
			$existingRaceDetails = $this->events->getEventDetailByExternalId($price['MeetingId'] . '_' . $price['RaceNo']);
			if (!$existingRaceDetails) {
				Log::debug($this->logprefix . 'Race for price not found ' . $price['MeetingId'] . '_' . $price['RaceNo']);
				continue;
			}

            if (!array_get($updates, $existingRaceDetails['id'])) {
                $updates[$existingRaceDetails['id']] = array();
            }


			$runnerCount = 1;

            $betProduct = $this->betproduct->getProductByCode($price['PriceType']);
            if (!$betProduct) {
                Log::debug($this->logprefix . 'PriceType not found ' . $price['PriceType']);
                continue;
            }

            Log::info($this->logprefix ."Processing Odds. USED: MeetID:{$price['MeetingId']}, RaceNo:{$price['RaceNo']}, BetType:{$price['BetType']}, PriceType:{$price['PriceType']}, Odds:" . $price['OddString']);

			// loop on each runners odds
			foreach ($oddsArray as $runnerOdds) {

				// ignore odds of 0
				if($runnerOdds == '0'){
                    $runnerCount++;
					continue;
				}

				// check if selection exists
				$existingSelection = $this->selections->getSelectionByExternalId($price['MeetingId'] . '_' . $price['RaceNo'].'_'.$runnerCount);

				if(!$existingSelection) {
					Log::debug($this->logprefix . 'Selection for price missing', $price);
					continue;

				}

				$priceDetails = array("bet_product_id" => $betProduct->id);
				$priceDetails['selection_id'] = $existingSelection->id;
				switch ($price['BetType']) {
					case "W":
						$priceDetails['win_odds'] = $runnerOdds / 100;
						break;
					case "P":
						$priceDetails['place_odds'] = $runnerOdds / 100;
						break;
					default:
						Log::debug($this->logprefix . 'Price BetType is invalid ', $price);
						continue;
				}

                $priceModel = $this->prices->getPriceForSelectionByProduct($existingSelection->id, $betProduct->id);

                if ($priceModel && $priceModel->fill($priceDetails)->isDirty()) {
                    $priceModel = $this->prices->update($priceModel, $priceDetails);
                    $this->selections->updatePricesForSelectionInRace($existingSelection->id, $existingRaceDetails, $priceModel);
                    $updates[$existingRaceDetails['id']][] = $priceModel;
                } else if (!$priceModel) {
                    $priceModel = $this->prices->create($priceDetails);
                    $this->selections->updatePricesForSelectionInRace($existingSelection->id, $existingRaceDetails, $priceModel);
                    $updates[$existingRaceDetails['id']][] = $priceModel;
                }


				$runnerCount++;
			}



		}

        foreach($updates as $race=>$selections) {
            if (count($selections)) {
                \Bus::dispatch(new PriceSocketUpdate(array("id" => $race, "selections" => $selections)));
            }
        }

		return "Price(s) Processed";
	}


	/**
	 * Check if we want to process this one
	 *
	 * @param $dataArray
	 * @param $providerName
	 * @param $raceNo
	 * @param null $type
	 * @return bool
	 */
	private function _canProductBeProcessed($dataArray, $providerName, $raceNo, $type = null)
    {
        $productUsed = false;
        $meetingId = $dataArray['MeetingId'];
        $betType = $dataArray['BetType'];
        $priceType = $dataArray['PriceType'];

        // get meeting details
        $meetingTypeCodeResult = $this->competitions->getMeetingDetails($meetingId);

        if(!$meetingTypeCodeResult) return false;

        $meetingTypeCode = $meetingTypeCodeResult['type_code'];
        $meetingCountry = $meetingTypeCodeResult['country'];
        $meetingGrade = $meetingTypeCodeResult['meeting_grade'];

        // check if product is used
        $productUsed = $this->betproduct->isProductUsed($priceType, $betType, $meetingCountry, $meetingGrade, $meetingTypeCode, $providerName);

        if (!$productUsed) {
            Log::debug($this->logprefix ."Processing $type. IGNORED: MeetID:$meetingId, RaceNo:$raceNo, BetType:$betType, PriceType:$priceType, TypeCode:$meetingTypeCode, Country:$meetingCountry, Grade:$meetingGrade");
            return false;
        }
        Log::info($this->logprefix ."Processing $type. USED: MeetID:$meetingId, RaceNo:$raceNo, BetType:$betType, PriceType:$priceType, TypeCode:$meetingTypeCode, Country:$meetingCountry, Grade:$meetingGrade");
        return true;
    }

    private function attachDefaultProducts($competition)
    {
        \Log::debug('DEBUG_COMPETITION: ' . $competition->id);
        //only attach if none exist
        if ($competition->products->count()) {
            return $competition;
        }

        $defaultProducts = $this->productProviderMatchRepository->getProductAndBetTypeByCompetition($competition);

        foreach ($defaultProducts as $product) {
            $competition->products()->attach(array($product->product_id => array('bet_type_id' => $product->bet_type_id)));
        }

        return $competition;
    }
}