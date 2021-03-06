<?php namespace TopBetta\backend;

use Config;

use TopBetta;
use TopBetta\Services\Caching\NextToJumpCacheService;
use TopBetta\Repositories\BetResultRepo;

use TopBetta\Repositories\DbEventRepository;

class RacingController extends \BaseController
{

    /**
     * Default log message type
     *
     * @var integer
     */
    const LOG_TYPE_NORMAL = 0;

    /**
     * Debug log message type
     *
     * @var integer
     */
    const LOG_TYPE_DEBUG = 1;

    /**
     * Log message type for errors
     *
     * @var integer
     */
    const LOG_TYPE_ERROR = 2;

    /**
     * Default time formatting string for log messages
     *
     * @var string
     */
    const LOG_TIME_FORMAT_DEFAULT = 'r';

    /**
     * Show time string in log messages
     *
     * @var string
     */
    const LOG_TIME_SHOWN = false;

    /**
     * Debugging mode flag
     *
     * @var boolean
     */
    private $debug = false;

    protected $nexttojump;
    protected $events;


    public function __construct(NextToJumpCacheService $nexttojump,
                                DbEventRepository $events,
                                BetResultRepo  $betrepository)
    {
        $this->nexttojump = $nexttojump;
        $this->events = $events;
        $this->betrepository = $betrepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        // return RaceMeetings::all();
        return "Racing API Index";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        // send email notification
        $emailNotification = 0;
        // Rate Limit Check
        $rateLimitMax = 5; // 1/2 second
        $rateLimitCost = 0;
        $rateLimitKey = "igas_race_schedule";
        $rateTTL = 5;
        $rateLimitReset = false;

        /*
          $newRateLimiter = new TopBetta\APIRateLimiter($rateLimitMax, $rateLimitCost, $rateLimitKey, $rateTTL, $rateLimitReset);
          $checkRateLimit = $newRateLimiter->RateLimiter();

          if ($checkRateLimit) {
          if ($emailNotification) {
          // Email on failer to result bet
          $emailSubject = "iGAS Race Schedule: Connection Rate Limited.";
          $emailDetails = array('email' => 'oliver@topbetta.com', 'first_name' => 'Oliver', 'from' => 'raceschedule@topbetta.com', 'from_name' => 'TopBetta iGAS RaceSchedule', 'subject' => "$emailSubject");
          $newEmail = \Mail::send('hello', $emailDetails, function($m) use ($emailDetails) {
          $m->from($emailDetails['from'], $emailDetails['from_name']);
          $m->to($emailDetails['email'], 'Oliver Shanahan')->subject($emailDetails['subject']);
          });
          }
          TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Connection Rate Limited: $rateLimitKey.");

          return \Response::json(array(
          'error' => true,
          'message' => 'Error: Connection rate limited.'), 400
          );
          }
         */

        // Log this
        //
        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Reciving POST");

        // get the JSON POST
        $racingJSON = \Input::json();

        if ($this->debug) {
            $racingJSONlog = \Input::json()->all();
            $timeStamp = date("YmdHis");
            \File::append('/tmp/backAPIracingJSON-' . $timeStamp, json_encode($racingJSONlog));
        }

        // make sure JSON was received
        $keyCount = count($racingJSON);
        if (!$keyCount) {
            TopBetta\Helpers\LogHelper::l("BackAPI: Racing - No Data In POST", 2);
            return \Response::json(array(
                        'error' => true,
                        'message' => 'Error: No JSON data received'), 400
            );
        }

        // Set the market Type
        $marketName = "Racing";

        //TODO: // validate the json. Create some rules and check the json validates

        //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing '$keyCount' Objects. SequenceNo:");
        $objectCount = 1;



        // loop on objects in data
        foreach ($racingJSON as $key => $racingArray) {
            // TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing SequenceNo:". $key['SequenceNo']);
            // Make sure we have some data to process in the array
            if (is_array($racingArray)) {

                // process the meeting/race/runner data
                switch ($key) {

                    // Meeting Data - the meeting/venue
                    case "MeetingList":
                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Meeting, Object:$objectCount");
                        foreach ($racingArray as $dataArray) {
                            // store data from array
                            if (isset($dataArray['Id']) && isset($dataArray['Name']) && isset($dataArray['Date'])) {
                                $meetingId = $dataArray['Id'];
                                if (isset($dataArray['RaceType'])) {
                                    switch ($dataArray['RaceType']) {
                                        case "R":
                                            $type_code = 'R';
                                            $tournament_competition_id = '31';
                                            break;
                                        case "T":
                                            $type_code = 'H';
                                            $tournament_competition_id = '32';
                                            break;
                                        case "G":
                                            $type_code = 'G';
                                            $tournament_competition_id = '33';
                                            break;
                                    }
                                }

                                $isThisRaceMeeting = TopBetta\Models\RaceMeeting::isRace($meetingId);

                                if ($isThisRaceMeeting) {
                                    // check if meeting exists in DB
                                    // $meetingExists = TopBetta\Models\RaceMeeting::meetingExists($meetingId);
                                    // Change meeting check to be based on name/type/date rather then iGAS meetingID
                                    //  to allow future race meetings to be created for tournaments

                                    $dt = new \DateTime($dataArray['Date']);
                                    $shortDate = $dt->format('Y-m-d');

                                    $meetingCode = str_replace(" ", "", strtoupper($dataArray['Name']) . "-" . $type_code . "-" . $shortDate);
                                    $meetingExists = TopBetta\Models\RaceMeeting::meetingExistsByCode($meetingCode);

                                    // if meeting exists update that record
                                    if ($meetingExists) {
                                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Meeting, In DB: $meetingExists", 1);
                                        $raceMeet = TopBetta\Models\RaceMeeting::find($meetingExists);
                                        $raceMeet->external_event_group_id = $dataArray['Id'];
                                    } else {
                                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Meeting, Added to DB: $meetingExists", 1);
                                        $raceMeet = new TopBetta\Models\RaceMeeting;
                                        if (isset($dataArray['Id'])) {
                                            $raceMeet->external_event_group_id = $dataArray['Id'];
                                        }
                                        // international meetings are not displayed by default
                                        if (isset($dataArray['Country'])) {
                                            if($dataArray['Country'] == 'INT') $raceMeet->display_flag = 0;
                                        }
                                    }

                                    // add the meeting code to the model
                                    $raceMeet->meeting_code = $meetingCode;

                                    // add the meeting name to the model
                                    $raceMeet->name = $dataArray['Name'];

                                    // 								if(isset($dataArray['Date'])){
                                    // 									$raceMeet->start_date = $dataArray['Date'];
                                    // 								}

                                    if (isset($dataArray['EventCount'])) {
                                        $raceMeet->events = $dataArray['EventCount'];
                                    }
                                    if (isset($dataArray['Weather'])) {
                                        $raceMeet->weather = $dataArray['Weather'];
                                        if (!$raceMeet->weather == "") {
                                            // change to TB default if we have a match
                                            $defaultValue = TopBetta\Models\DataValues::getDefaultValue('weather_condition', $raceMeet->weather);
                                            //$o = print_r($defaultValue,true);
                                            //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Meeting. defaultValue weather o:$o", 1);
                                            if (count($defaultValue) > 0) { // 
                                                $raceMeet->weather = $defaultValue[0]->value;
                                            } else {
                                                // Email on failer to find a weather status to map to
                                                $emailSubject = "iGAS Race Schedule: No Weather Mapping Found for MID:$meetingId, Name:" . $raceMeet->name . ", Type:" . $raceMeet->type_code . ", Weather:" . $raceMeet->weather . ".";
                                                $emailDetails = array('email' => 'oliver@topbetta.com', 'first_name' => 'Oliver', 'from' => 'raceschedule@topbetta.com', 'from_name' => 'TopBetta iGAS RaceSchedule', 'subject' => "$emailSubject");

                                                $newEmail = \Mail::send('hello', $emailDetails, function($m) use ($emailDetails) {
                                                            $m->from($emailDetails['from'], $emailDetails['from_name']);
                                                            $m->to($emailDetails['email'], 'Oliver Shanahan')->subject($emailDetails['subject']);
                                                        });
                                            }
                                        }
                                    }
                                    if (isset($dataArray['Track'])) {
                                        $raceMeet->track = $dataArray['Track'];
                                        if (!$raceMeet->track == "") {
                                            // change to TB default if we have a match
                                            $defaultValue = TopBetta\Models\DataValues::getDefaultValue('track_condition', $raceMeet->track);
                                            //$o = print_r($defaultValue,true);
                                            //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Meeting. defaultValue track o:$o", 1);
                                            if (count($defaultValue) > 0) {
                                                $raceMeet->track = $defaultValue[0]->value;
                                            } else {
                                                // Email on failer to find a track status to map to
                                                $emailSubject = "iGAS Race Schedule: No Track Mapping Found for MID:$meetingId, Name:" . $raceMeet->name . ", Type:" . $raceMeet->type_code . ", Track:" . $raceMeet->track . ".";
                                                $emailDetails = array('email' => 'oliver@topbetta.com', 'first_name' => 'Oliver', 'from' => 'raceschedule@topbetta.com', 'from_name' => 'TopBetta iGAS RaceSchedule', 'subject' => "$emailSubject");

                                                $newEmail = \Mail::send('hello', $emailDetails, function($m) use ($emailDetails) {
                                                            $m->from($emailDetails['from'], $emailDetails['from_name']);
                                                            $m->to($emailDetails['email'], 'Oliver Shanahan')->subject($emailDetails['subject']);
                                                        });
                                            }
                                        }
                                    }
                                    if (isset($dataArray['State'])) {
                                        $raceMeet->state = $dataArray['State'];
                                    }
                                    if (isset($dataArray['Country'])) {
                                        $raceMeet->country = $dataArray['Country'];

                                        // by default we don't display INT races. This should be configured in the applciation at some stage
                                        if($dataArray['Country'] == 'INT' && $raceMeet->display_flag != 1) $raceMeet->display_flag = 0;

                                    }
                                    if (isset($dataArray['MeetingType'])) {
                                        $raceMeet->meeting_grade = $dataArray['MeetingType'];
                                    }
                                    if (isset($dataArray['RailPosition'])) {
                                        $raceMeet->rail_position = $dataArray['RailPosition'];
                                    }

                                    $raceMeet->type_code = $type_code;
                                    $raceMeet->tournament_competition_id = $tournament_competition_id;
                                    // save or update the record
                                    $raceMeetSave = $raceMeet->save();
                                    $raceMeetID = $raceMeet->id;
                                    TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processed Meeting. MID:$meetingId, Date:$raceMeet->start_date, Name:$raceMeet->name, Type:$raceMeet->type_code, Events:$raceMeet->events, Weather:$raceMeet->weather, Track:$raceMeet->track");
                                }
                            } else {
                                TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Meeting. No Meeting ID, Can't process", 2);
                            }
                        }
                        break;

                    // Race data - the races in the meeting
                    case "RaceList":
                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $objectCount: Race");
                        
                        $eventList = array();
                        
                        foreach ($racingArray as $dataArray) {

                            if (isset($dataArray['MeetingId']) && $dataArray['RaceNo']) {
                                $meetingId = $dataArray['MeetingId'];
                                $raceNo = $dataArray['RaceNo'];

                                $isThisRaceMeeting = TopBetta\Models\RaceMeeting::isRace($meetingId);
                                if ($isThisRaceMeeting) {

                                    // make sure the meeting this race is in exists 1st
                                    $meetingExists = TopBetta\Models\RaceMeeting::meetingExists($meetingId);

                                    // if meeting exists update that record then continue to add/update the race record
                                    if ($meetingExists) {

                                        $meetingRecord = TopBetta\Models\RaceMeeting::find($meetingExists);

                                        //check if race exists in DB
                                        $raceExists = $this->events->getEventDetails($meetingId.'_'.$raceNo);

                                        // if race exists update that record
                                        if ($raceExists) {
                                            TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Race, In DB: ".$raceExists['EventId'], 1);
                                            $raceEvent = TopBetta\Models\RaceEvent::find($raceExists['EventId']);

                                            // build up the status check/order array
                                            $raceStatusCheckArray = array();

                                            $raceStatusCheckArray['O'] = 1;
                                            $raceStatusCheckArray['C'] = 2;
                                            $raceStatusCheckArray['I'] = 3;
                                            $raceStatusCheckArray['R'] = 4;
                                            $raceStatusCheckArray['A'] = 5;

                                            // get the races current status
                                            $currentRaceStatus = $raceEvent['event_status_id'];


                                        } else {
                                            TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Race, Added to DB", 1);
                                            $raceEvent = new TopBetta\Models\RaceEvent;
                                            $currentRaceStatus = 0;
                                            if (isset($dataArray['MeetingId'])) {
                                                $raceEvent->external_event_id = $meetingId.'_'.$dataArray['RaceNo'];
                                            }
                                            if($meetingRecord->display_flag == '0') $raceEvent->display_flag = 0;
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

                                        if (isset($dataArray['RaceNo']) && isset($dataArray['JumpTime'])) {
                                            $raceEvent->number = $dataArray['RaceNo'];
                                            // update meeting start time if it's race 1
                                            if ($meetingRecord->start_date == '0000-00-00 00:00:00' || $dataArray['JumpTime'] < $meetingRecord->start_date) {
                                                $meetingRecord->start_date = $dataArray['JumpTime'];
                                                $meetingRecord->save();
                                            }
                                        }

                                        if (isset($dataArray['JumpTime'])) {
                                            $raceEvent->start_date = $dataArray['JumpTime'];
                                        }

                                        // update tournament start end times
                                        if (isset($dataArray ['JumpTime']) && isset($dataArray['RaceNo'])) {
                                            $tournamentsOnMeeting = TopBetta\Models\Tournament::getTournamentWithEventGroup($meetingExists);
                                            // loop on each tournament
                                            foreach ($tournamentsOnMeeting as $tournament) {
                                                // if it's race 1 store the jump time as tourn start date.
                                                $tournamentModel = TopBetta\Models\Tournament::find($tournament->id);
                                                if ($meetingRecord->start_date == '0000-00-00 00:00:00' || $dataArray['JumpTime'] < $meetingRecord->start_date) {
                                                    $tournamentModel->start_date = $dataArray['JumpTime'];
                                                } else {
                                                    if ($dataArray['JumpTime'] > $tournamentModel->end_date) {
                                                        $tournamentModel->end_date = $dataArray['JumpTime'];
                                                    }
                                                }
                                                $tournamentModel->save();
                                            }
                                        }

                                          //TODO: Code Table lookup on different race status from provider
                                        //TODO: Triggers for tournament processing on race status of R (final divs) and A (abandoned) 
                                        if (isset($dataArray['RaceStatus'])) {
                                            TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Status:".$currentRaceStatus, 1);
                                            //example true || paying(4) < selling(1)
                                            if(!$raceExists || $raceStatusCheck[$currentRaceStatus] < $raceStatusCheckArray[$dataArray['RaceStatus']]){
                                                 switch ($dataArray['RaceStatus']) {
                                                    case "O":
                                                        $raceEvent->event_status_id = '1'; // selling
                                                        break;
                                                    case "C":
                                                        $raceEvent->event_status_id = '5'; // closed
                                                        break;
                                                    case "S":
                                                        $raceEvent->event_status_id = '5'; // no suspended status in code table
                                                        break;
                                                    case "I":
                                                        $raceEvent->event_status_id = '6'; // interim
                                                        break;
                                                    case "R":
                                                        $raceEvent->event_status_id = '2'; // paying
                                                        break;
                                                    case "A":
                                                        $raceEvent->event_status_id = '3'; // abandoned
                                                        break;
                                                    case "D":
                                                        $raceEvent->event_status_id = '7'; // deleted
                                                        break;

                                                    default:
                                                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Race. No valid race status found. Can't process. ", 2);
                                                }
                                            }
                                        }

                                        // TODO: Not stored or needed?
                                        /* if(isset($dataArray['RunnerCount'])){
                                          $raceEvent->type_code = $dataArray['RunnerCount'];
                                          } */

                                        if (isset($dataArray['RaceName'])) {
                                            $raceEvent->name = $dataArray['RaceName'];
                                        }
                                        if (isset($dataArray['Distance'])) {
                                            $raceEvent->distance = $dataArray['Distance'];
                                        }
                                        if (isset($dataArray['RaceClass'])) {
                                            $raceEvent->class = $dataArray['RaceClass'];
                                        }

                                        // save or update the record
                                        $raceEvent->weather = $meetingRecord->weather;
                                        $raceEvent->track_condition = $meetingRecord->track;
                                        $raceEventSave = $raceEvent->save();
                                        $raceEventID = $raceEvent->id;

                                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processed Race. MID:$meetingId, RaceNo:$raceNo, Name: $raceEvent->name, JumpTime:$raceEvent->start_date, Status:$raceEvent->event_status_id");

                                        // Add the event_group_event record if adding race
                                        // TODO: maybe through eloquent check if the race already exists in DB also need to check what event_id field stores
                                        $egeExists = \DB::table('tbdb_event_group_event')->where('event_id', $raceEventID)->where('event_group_id', $meetingExists)->value('event_id');

                                        if (!$egeExists) {
                                            $eventGroupEvent = new TopBetta\Models\RaceEventGroupEvent;
                                            $eventGroupEvent->event_id = $raceEventID;
                                            $eventGroupEvent->event_group_id = $meetingExists;
                                            $eventGroupEvent->save();
                                            TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Race, Added to DB", 1);
                                            // Add event_group event record
                                        } else {
                                            TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Race, EGE in DB", 1);
                                        }

                                        // if this event was abandoned - add to list for bet resulting
                                        if ($raceEvent->event_status_id == 3) {
                                            if (!array_key_exists($raceEventID, array_flip($eventList))) {
                                                array_push($eventList, $raceEventID);
                                            }
                                        }

                                        // N2J cache object check
                                        $this->nexttojump->manageCache($raceExists, $raceEvent);

                                    } else {
                                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Race. Meeting for race does not exist. Can't process. MeetingID: $meetingId, RaceNumber: $raceNo", 2);
                                    }


                                }
                            } else {
                                TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Race. MeetingID or RaceNo not set. Can't process", 2);
                            }
                        }

                        // ALL RACES PROCESSED - RESULT ALL BETS FOR THE EVENT LIST (ABANDONED ONLY)
                        foreach ($eventList as $eventId) {
                            \Log::info('ABANDONED: refund all bets for event id: ' . $eventId);
							// $betResultRepo = new TopBetta\Repositories\BetResultRepo();
                            $this->betrepository->resultAllBetsForEvent($eventId);
                        }
                        break;

                    // Selection/Runner Data - The runners in the race
                    case "RunnerList":
                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $objectCount: Runner");
                        
						$scratchList = array();
						
						foreach ($racingArray as $dataArray) {
                            $raceExists = $selectionsExists = 0;
                            // Check all required data is available in the JSON for the runner
                            if (isset($dataArray['MeetingId']) && isset($dataArray['RaceNo']) && isset($dataArray['RunnerNo'])) {
                                $meetingId = $dataArray['MeetingId'];
                                $raceNo = $dataArray['RaceNo'];
                                $runnerNo = $dataArray['RunnerNo'];

                                //check if race exists in DB
                                $raceExists = $this->events->getEventDetails($meetingId.'_'.$raceNo);

                                //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Runner, Race Exists: ". print_r($raceExists,true), 1);

                                //TODO: add error output to a log
                                if ($raceExists) {

                                    // check if selection exists in the DB
                                    $selectionsExists = TopBetta\Models\RaceSelection::selectionExists($meetingId, $raceNo, $runnerNo);

                                    // if runner exists update that record
                                    if ($selectionsExists) {
                                        //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Runner, In DB: $selectionsExists", 1);
                                        $raceRunner = TopBetta\Models\RaceSelection::find($selectionsExists);
                                    } else {
                                        //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Runner, Added to DB: $selectionsExists", 1);
                                        $raceRunner = new TopBetta\Models\RaceSelection;

                                        // get market ID
                                        $marketTypeID = TopBetta\Models\RaceMarketType::where('name', '=', $marketName)->value('id');

                                        // check if market for event exists
                                        $marketID = TopBetta\Models\RaceMarket::marketExists($raceExists['EventId'], $marketTypeID);

                                        if (!$marketID) {
                                            // add market record
                                            $runnerMarket = new TopBetta\Models\RaceMarket;
                                            $runnerMarket->event_id = $raceExists['EventId'];
                                            $runnerMarket->market_type_id = 110; //TODO: this needs to come from db
                                            $runnerMarket->save();
                                            $marketID = $runnerMarket->id;

                                            //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Runner. Add market record for event: $raceExists");
                                        }
                                        $raceRunner->market_id = $marketID;
                                    }

                                    if (isset($dataArray['BarrierNo'])) {
                                        $raceRunner->barrier = $dataArray['BarrierNo'];
                                    }
                                    if (isset($dataArray['Name'])) {
                                        $raceRunner->name = $dataArray['Name'];
                                    }
                                    if (isset($dataArray['RunnerNo'])) {
                                        $raceRunner->number = $dataArray['RunnerNo'];
                                        $raceRunner->external_selection_id = $raceExists['ExternalEventId'].'_'.$dataArray['RunnerNo'];

                                    }

                                    //TODO: Code Table Lookup/Provider matching table							
                                    if (isset($dataArray['Scratched'])) {
                                        if ($dataArray['Scratched'] == '1') {
                                            $raceRunner->selection_status_id = '2';
                                        } else {
                                            $raceRunner->selection_status_id = '1';
                                        }
                                    }
                                    if (isset($dataArray['Weight'])) {
                                        $raceRunner->weight = $dataArray['Weight'] / 10;
                                    }

                                    // get the meeting record ID
                                    $meetingExists = TopBetta\Models\RaceMeeting::meetingExists($meetingId);

                                    // Get meeting type
                                    $meetingRecord = TopBetta\Models\RaceMeeting::find($meetingExists);
                                    $meetingType = $meetingRecord->type_code;

                                    // Get silkID and Last Starts for runner from RISA table
                                    if ($meetingType == "R") {

                                        // check if meeting exists in DB
                                        $meetingExists = TopBetta\Models\RaceMeeting::meetingExists($meetingId);
                                        //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Runner. Looking up silk and LastStarts");
                                        if ($meetingExists) {
                                            // if meeting exists get the record
                                            $raceMeet = TopBetta\Models\RaceMeeting::find($meetingExists);
                                            // Grab the date from the date/time field
                                            $meetDate = substr($raceMeet->start_date, 0, 10);
                                            // default to Racing atm
                                            $codeType = 'R';
                                            // get the venue name
                                            $venueName = $raceMeet->name;
                                            // make sure the numbers are 2 digits
                                            ($raceNo < 10) ? $raceNumber = '0' . $raceNo : $raceNumber = $raceNo;
                                            ($runnerNo < 10) ? $runnerNumber = '0' . $runnerNo : $runnerNumber = $runnerNo;

                                            // Build the runner code
                                            $runnerCodeSelection = str_replace(" ", "", $meetDate . "-" . $codeType . "-" . $venueName . "-" . $raceNumber . "-" . $runnerNumber);

                                            if (isset($dataArray['SilkCode'])) {
                                                $raceRunner->silk_id = $dataArray['SilkCode'];
                                            } else {
                                                $raceRunner->silk_id = "";
                                            }

                                            TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Runner. Runner Code: $runnerCodeSelection, Silk: $raceRunner->silk_id, Last Starts: $raceRunner->last_starts.");

                                            //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Runner. Runner Silk: $raceRunner->silk_id");

                                            // add the runner code
                                            $raceRunner->runner_code = $runnerCodeSelection;
                                        }
                                    }

                                    if (isset($dataArray['LastStarts'])){
                                        $raceRunner->last_starts = $dataArray['LastStarts'];
                                    }else{
                                        $raceRunner->last_starts = "";
                                    }

                                    if (isset($dataArray['Jockey'])) {
                                        $raceRunner->associate = $dataArray['Jockey'];
                                    }
                                    if (isset($dataArray['Trainer'])) {
                                        $raceRunner->trainer = $dataArray['Trainer'];
                                    }
                                    // save or update the record
                                    $raceRunnerSave = $raceRunner->save();
                                    $raceRunnerID = $raceRunner->id;
                                    $raceRunner->wager_id = $raceRunner->id;
                                    $raceRunnerSave = $raceRunner->save();

                                    TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processed Runner. MID:$meetingId, RaceNo:$raceNo, RunnerNo:$runnerNo, Barrier:$raceRunner->barrier, Name:$raceRunner->name, Jockey:$raceRunner->associate, Scratched:$raceRunner->selection_status_id, Weight:$raceRunner->weight ");
                                } else {
                                    TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Runner. No race found for this runner. MID:$meetingId, Race:$raceNo, Runner:$runnerNo Can't process", 2);
                                }
                            } else {
                                TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Race. Missing Runner data. Can't process", 2);
                            }
							
                            // if this event was abandoned - add to list for bet resulting
							if (isset($raceRunner) && $raceRunner->selection_status_id == '2') {
								if (!array_key_exists($raceRunner->id, array_flip($scratchList))) {
									array_push($scratchList, $raceRunner->id);
								}
							}
						}
						
                        // ALL RUNNERS PROCESSED - REFUND ANY BETS FOR SCRATCHED RUNNERS
                        foreach ($scratchList as $scratchedId) {
                            \Log::info('SCRATCHED: refunding bets for runner id: ' . $scratchedId);
							\TopBetta\Facades\BetRepo::refundBetsForRunnerId($scratchedId);
                        }						
						
                        break;

                    // Result Data - the actual results of the race
                    case "ResultList" :
                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $objectCount: Result");
                        
                        $eventList = array();

                        foreach ($racingArray as $dataArray) {
                            $selectionsExists = $resultExists = 0;
							$firstProcess = false;

                            // Check required data to update a Result is in the JSON
                            if (isset($dataArray ['MeetingId']) && isset($dataArray ['RaceNo']) && isset($dataArray ['Selection']) && isset($dataArray ['BetType']) && isset($dataArray ['PriceType']) && isset($dataArray ['PlaceNo']) && isset($dataArray ['Payout'])) {

                                // TODO: mapping between provider and TB should be added to constants or DB table
                                if ($dataArray['BetType'] == "F")
                                    $dataArray['BetType'] = "FF";

                                $meetingId = $dataArray ['MeetingId'];
                                $raceNo = $dataArray ['RaceNo'];
                                $betType = $dataArray ['BetType'];
                                $priceType = $dataArray ['PriceType'];
                                $selection = $dataArray ['Selection'];
                                $placeNo = $dataArray ['PlaceNo'];
                                $payout = $dataArray ['Payout'];
                                $providerName = "igas";
                                $log_msg_prefix = "BackAPI: Racing - Processing Result. MID:$meetingId, RN:$raceNo";

                                /*
                                 * Check if this is a product we need to store in the DB
                                 */
                                $saveThisProduct = $this->canProductBeProcessed($dataArray, $providerName, $raceNo, "Result");

                                // We want this product
                                if ($saveThisProduct) {
                                    TopBetta\Helpers\LogHelper::l($log_msg_prefix . " PriceType:$priceType. BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout", 1);

                                    $eventID = TopBetta\Models\RaceEvent::eventExists($meetingId, $raceNo);
                                    if ($eventID && !array_key_exists($eventID, array_flip($eventList))) {
										\Log::info("EVENTID First Process: " . $eventID);
                                        array_push($eventList, $eventID);
                                        $firstProcess = true;
                                    }
                                    // if this is the 1st time through for this event clear all previous results
                                    if ($firstProcess == true) {

                                        // update the flag so this only happens once
                                        $firstProcess = false;

                                        //
                                        // delete all existing results data for this race
                                        //

                                        // Get ID of event record
                                        // $eventID = TopBetta\Models\RaceEvent::eventExists($meetingId, $raceNo);
                                        // if there is an event found
                                        if ($eventID) {
                                            // grab the event
                                            $raceEvent = TopBetta\Models\RaceEvent::find($eventID);

                                            // reset all exotic results to NULL
                                            $raceEvent->quinella_dividend = $raceEvent->exacta_dividend = $raceEvent->trifecta_dividend = $raceEvent->firstfour_dividend = NULL;

                                            // save the update
                                            $raceEvent->save();

                                            // delete all results records for this event
                                            $deleteRaceID = TopBetta\Models\RaceResult::deleteResultsForRaceId($eventID);

                                            TopBetta\Helpers\LogHelper::l($log_msg_prefix . " Existing Results for EventID: $eventID deleted. Response: $deleteRaceID.", 1);
                                        }
                                    }

                                    // For win and place bets results are stored with the selection record
                                    if ($betType == 'W' || $betType == 'P') {
                                        // check if selection exists in the DB
                                        $selectionsExists = TopBetta\Models\RaceSelection::selectionExists($meetingId, $raceNo, $selection);
                                        // if it exists
                                        if ($selectionsExists) {
                                            // Check if we have results already
                                            $resultExists = \DB::table('tbdb_selection_result')->where('selection_id', $selectionsExists)->value('id');
                                            if ($resultExists) {
                                                TopBetta\Helpers\LogHelper::l($log_msg_prefix . "  PriceType:$priceType Already in DB", 1);
                                                $raceResult = TopBetta\Models\RaceResult::find($resultExists);
                                            } else {
                                                TopBetta\Helpers\LogHelper::l($log_msg_prefix . "  PriceType:$priceType Added to DB", 1);
                                                $raceResult = new TopBetta\Models\RaceResult ();

                                                $raceResult->selection_id = $selectionsExists;
                                            }

                                            // grab position and correct dividend
                                            $raceResult->position = $placeNo;
                                            ($betType == 'W') ? $raceResult->position = 1 : $raceResult->position = $placeNo;
                                            ($betType == 'W') ? $raceResult->win_dividend = $payout / 100 : $raceResult->place_dividend = $payout / 100;

                                            // save win or place odds to DB
                                            $raceResultSave = $raceResult->save();
                                            $raceResultID = $raceResult->id;

                                            TopBetta\Helpers\LogHelper::l($log_msg_prefix . "  BetType:$betType, PriceType:$priceType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout");
                                        } else {
                                            TopBetta\Helpers\LogHelper::l($log_msg_prefix . "  Not Processed! Selection not found. PriceType:$priceType.  BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout", 2);
                                        }
                                        // Exotic results are stored with the event record
                                    } else {

                                        // Get ID of event record - used to store exotic results/divs if required
                                        $eventID = TopBetta\Models\RaceEvent::eventExists($meetingId, $raceNo);


                                        if ($eventID) {
                                            // grab the event
                                            $raceEvent = TopBetta\Models\RaceEvent::find($eventID);

                                            // build the serialised result data for this result
                                            $arrayKey = str_replace('-', '/', $selection);
                                            $arrayValue = $payout / 100;
                                            $exoticArray = array(
                                                $arrayKey => $arrayValue
                                            );

                                            $previousDivArray = array();

                                            TopBetta\Helpers\LogHelper::l($log_msg_prefix . "  Exotic Type:$betType. Positions:$arrayKey, Dividend:$arrayValue.", 1);

                                            // process each exotic type
                                            switch ($betType) {
                                                case "Q" : // Quinella
                                                    // if we already have a dividend stored
                                                    if ($raceEvent->quinella_dividend != NULL) {
                                                        // if the new exotic results are the same as what we already have in the database
                                                        if ($raceEvent->quinella_dividend != serialize($exoticArray)) {
                                                            // unserialise the existing dividend from the database
                                                            $previousDivArray = unserialize($raceEvent->quinella_dividend);
                                                            // update or add selection dividends
                                                            $previousDivArray[$arrayKey] = $arrayValue;
                                                            // add the new dividends
                                                            $raceEvent->quinella_dividend = serialize($previousDivArray);
                                                        }
                                                        // if we didn't have a result stored already then store it	
                                                    } else {
                                                        $raceEvent->quinella_dividend = serialize($exoticArray);
                                                    }
                                                    TopBetta\Helpers\LogHelper::l($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$raceEvent->quinella_dividend.", 1);
                                                    break;

                                                case "E" : // Exacta
                                                    // if we already have a dividend stored
                                                    if ($raceEvent->exacta_dividend != NULL) {
                                                        // if the new exotic results are the same as what we already have in the database
                                                        if ($raceEvent->exacta_dividend != serialize($exoticArray)) {
                                                            // unserialise the existing dividend from the database
                                                            $previousDivArray = unserialize($raceEvent->exacta_dividend);
                                                            // update or add selection dividends
                                                            $previousDivArray[$arrayKey] = $arrayValue;
                                                            // add the new dividends
                                                            $raceEvent->exacta_dividend = serialize($previousDivArray);
                                                        }
                                                        // if we didn't have a result stored already then store it
                                                    } else {
                                                        $raceEvent->exacta_dividend = serialize($exoticArray);
                                                    }
                                                    TopBetta\Helpers\LogHelper::l($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$raceEvent->exacta_dividend.", 1);
                                                    break;

                                                case "T" : // Trifecta
                                                    // if we already have a dividend stored
                                                    if ($raceEvent->trifecta_dividend != NULL) {
                                                        // if the new exotic results are the same as what we already have in the database
                                                        if ($raceEvent->trifecta_dividend != serialize($exoticArray)) {
                                                            // unserialise the existing dividend from the database
                                                            $previousDivArray = unserialize($raceEvent->trifecta_dividend);
                                                            // update or add selection dividends
                                                            $previousDivArray[$arrayKey] = $arrayValue;
                                                            // add the new dividends
                                                            $raceEvent->trifecta_dividend = serialize($previousDivArray);
                                                        }
                                                        // if we didn't have a result stored already then store it
                                                    } else {
                                                        $raceEvent->trifecta_dividend = serialize($exoticArray);
                                                    }
                                                    TopBetta\Helpers\LogHelper::l($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$raceEvent->trifecta_dividend.", 1);
                                                    break;

                                                case "FF" : // First Four
                                                    // if we already have a dividend stored
                                                    if ($raceEvent->firstfour_dividend != NULL) {
                                                        // if the new exotic results are the same as what we already have in the database
                                                        if ($raceEvent->firstfour_dividend != serialize($exoticArray)) {
                                                            // unserialise the existing dividend from the database
                                                            $previousDivArray = unserialize($raceEvent->firstfour_dividend);
                                                            // update or add selection dividends
                                                            $previousDivArray[$arrayKey] = $arrayValue;
                                                            // add the new dividends
                                                            $raceEvent->firstfour_dividend = serialize($previousDivArray);
                                                        }
                                                        // if we didn't have a result stored already then store it
                                                    } else {
                                                        $raceEvent->firstfour_dividend = serialize($exoticArray);
                                                    }
                                                    TopBetta\Helpers\LogHelper::l($log_msg_prefix . "  Exotics Result Div: Type:$betType. Added Dividends:$raceEvent->firstfour_dividend.", 1);
                                                    break;

                                                default :
                                                    TopBetta\Helpers\LogHelper::l($log_msg_prefix . " No valid betType found:$betType. Can't process", 2);
                                            }

                                            // save the exotic dividend
                                            $raceEvent->save();
                                        } else {
                                            TopBetta\Helpers\LogHelper::l($log_msg_prefix . "  Missing Event Record in DB", 2);
                                        }
                                    }
                                } else { // not all required data available
                                    TopBetta\Helpers\LogHelper::l($log_msg_prefix . " Not Processed! PriceType:$priceType. MeetID: $meetingId, RaceCode:, RaceNo:$raceNo, BetType:$betType, Selection:$selection, PlaceNo:$placeNo, Payout:$payout", 2);
                                }
                            } else {
                                TopBetta\Helpers\LogHelper::l($log_msg_prefix . " Missing Results data. Can't process", 2);
                            }
                        }

                        list($partMsec, $partSec) = explode(" ", microtime());
                        $currentTimeMs = $partSec.$partMsec;
                        $racingJSONlog = \Input::json()->all();
                        \File::append('/tmp/backAPIracingResultJSON-EventList-C' .count($eventList).'-'. $currentTimeMs, print_r($eventList,true));
                        // ALL RESULTS PROCESSED - RESULT ALL BETS FOR THE EVENT LIST
                        foreach ($eventList as $eventId) {
                            \Log::info('RESULTING: all bets for event id: ' . $eventId);


                            // get current micro time
                            list($partMsec, $partSec) = explode(" ", microtime());
                            $currentTimeMs = $partSec.$partMsec;
                            $racingJSONlog = \Input::json()->all();
                            \File::append('/tmp/backAPIracingResultJSON-E' .$eventId.'-'. $currentTimeMs, json_encode($racingJSONlog));

                            //$betResultRepo = new TopBetta\Repositories\BetResultRepo();
                            $this->betrepository->resultAllBetsForEvent($eventId);
                        }

                        break;

                    // Price Data
                    case "PriceList":
                        //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $objectCount: Odds");
                        foreach ($racingArray as $dataArray) {
                            //echo"Price Object: ";
                            //print_r($dataArray);
                            //echo "\n";
                            // reset counters
                            $selectionExists = $resultExists = 0;

                            if (isset($dataArray['MeetingId']) && isset($dataArray['RaceNo']) && isset($dataArray['BetType']) && isset($dataArray['PriceType']) && isset($dataArray['PoolAmount']) && isset($dataArray['OddString'])) {
                                $meetingId = $dataArray['MeetingId'];
                                $raceNo = $dataArray['RaceNo'];
                                $betType = $dataArray['BetType'];
                                $priceType = $dataArray['PriceType'];
                                $poolAmount = $dataArray['PoolAmount'];
                                $oddsString = $dataArray['OddString'];
                                $oddsArray = explode(';', $oddsString);
                                $providerName = "igas";

                                //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds. MID: $meetingId, Race: $raceNo, BT: $betType, PT: $priceType, PA: $poolAmount", 1);

                                // TODO: Check JSON data is valid

                                /*
                                 * Check if this is a product we need to store in the DB
                                 * NOTE: Moving forward, we should store the odds for ALL tote types
                                 */

                                $saveThisProduct = $this->canProductBeProcessed($dataArray, $providerName, $raceNo, "Odds");

                                // We want this product
                                if ($saveThisProduct) {
                                    // check if race exists in DB
                                    $raceExists = TopBetta\Models\RaceEvent::eventExists($meetingId, $raceNo);

                                    // grab the race type code
                                    $raceTypeCode = TopBetta\Models\RaceMeeting::where('external_event_group_id', '=', $meetingId)->value('type_code');

                                    // if race exists update that record
                                    if ($raceExists) {
                                        // check for odds
                                        if (is_array($oddsArray)) {
                                            //loop on odds array
                                            $runnerCount = 1;

                                            foreach ($oddsArray as $runnerOdds) {
                                                // TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds for Runner: $runnerCount", 1);

                                                if($runnerOdds != '0'){
                                                    // check if selection exists in the DB
                                                    $selectionExists = TopBetta\Models\RaceSelection::selectionExists($meetingId, $raceNo, $runnerCount);

                                                    if ($selectionExists) {
                                                        //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds. In DB", 1);
                                                        $priceExists = \DB::table('tbdb_selection_price')->where('selection_id', $selectionExists)->value('id');

                                                        // if result exists update that record otherwise create a new one
                                                        if ($priceExists) {
                                                            //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds, In DB: $priceExists", 1);
                                                            //echo "Price in DB, ODDS:$runnerOdds, ";
                                                            $runnerPrice = TopBetta\Models\RaceSelectionPrice::find($priceExists);
                                                        } else {
                                                            //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds, Added to DB: $priceExists", 1);
                                                            $runnerPrice = new TopBetta\Models\RaceSelectionPrice;
                                                            $runnerPrice->selection_id = $selectionExists;
                                                        }
                                                        $oddsSet = 0;
                                                        // update the correct field

                                                        switch ($betType) {
                                                            case "W":
                                                                $runnerPrice->win_odds = $runnerOdds / 100;
                                                                break;
                                                            case "P":
                                                                $runnerPrice->place_odds = $runnerOdds / 100;
                                                                break;
                                                            default:
                                                                TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds. 'Bet Type' not valid: $betType. Can't process", 2);
                                                        }

                                                        // save/update the price record
                                                        $runnerPrice->save();
                                                    } else {
                                                        //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds. No selction for Odds in DB. Can't process", 2);
                                                    }

                                                }
                                                $runnerCount++;
                                            }


                                        } else {
                                            TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds. No odds array found. Can't process", 2);
                                        }
                                    } else {
                                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds. No race found. Can't store results", 2);
                                    }
                                } else {
                                    //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds: Price Type not used for this meeting/code: $priceType.", 2);
                                }
                            } else {
                                TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing Odds. Missing Odds Data. Can't process", 2);
                            }
                        }

                        break;


                    // Outcome Data
                    case "OutcomeList":
                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $objectCount: Outcome");
                        //foreach ($racingArray as $dataArray){
                        //	echo"Outcome Object: ";
                        //	print_r($dataArray);
                        //	echo "\n";
                        //}

                        break;

                    default :
                        TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $objectCount: $key", 2);
                        return \Response::json(array(
                                    'error' => true,
                                    'message' => 'Error: Data format not recognised: ' . $key), 400
                        );
                }
            } else {
                // This is the sequence are for the moment. Might move it if it becomes a permanent addition	
               // TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $objectCount: $key. SequenceNo: $racingArray.", 2);

                // output timestamp and sequence 
                if ($this->debug) {
                    $timeStamp = date("Ymd");
                    $ttimeStamp = date("Y/m/d H:i:s");
                    $contents = $ttimeStamp . ": $racingArray\n";
                    \File::append('/tmp/igas_sequence-' . $timeStamp, $contents);
                }

                /* return Response::json(array(
                  'error' => true,
                  'message' => 'Error: No Data found'),
                  400
                  ); */
            }
            $objectCount++;
        }

        return \Response::json(array(
                    'error' => false,
                    'message' => 'OK: Processed Successfully'), 200
        );
        //return RaceMeetings::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    private function canProductBeProcessed($dataArray, $providerName, $raceNo, $type = null)
    {

        $productUsed = false;
        $meetingId = $dataArray['MeetingId'];
        $betType = $dataArray['BetType'];
        $priceType = $dataArray['PriceType'];

        // grab the meeting details we need
        $meetingTypeCodeResult = TopBetta\Models\RaceMeeting::getMeetingDetails($meetingId);

        if (is_array($meetingTypeCodeResult)) {
            if (isset($meetingTypeCodeResult[0])) {
                $meetingTypeCode = $meetingTypeCodeResult[0]['type_code'];
                $meetingCountry = $meetingTypeCodeResult[0]['country'];
                $meetingGrade = $meetingTypeCodeResult[0]['meeting_grade'];

                // check if product is used
                $productUsed = TopBetta\Models\BetProduct::isProductUsed($priceType, $betType, $meetingCountry, $meetingGrade, $meetingTypeCode, $providerName);

                if (!$productUsed) {
                    //TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $type. IGNORED: MeetID:$meetingId, RaceNo:$raceNo, BetType:$betType, PriceType:$priceType, TypeCode:$meetingTypeCode, Country:$meetingCountry, Grade:$meetingGrade", 1);
                    return false;
                }
                TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $type. USED: MeetID:$meetingId, RaceNo:$raceNo, BetType:$betType, PriceType:$priceType, TypeCode:$meetingTypeCode, Country:$meetingCountry, Grade:$meetingGrade", 0);
            } else {
                TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $type: Meeting ID not found???? - " . print_r($meetingTypeCodeResult, true), 2);
            }
        } else {
            TopBetta\Helpers\LogHelper::l("BackAPI: Racing - Processing $type: Meeting ID not found???? - " . print_r($meetingTypeCodeResult, true), 2);
        }
        return true;
    }

}
