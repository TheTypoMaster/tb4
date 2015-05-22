<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;

use TopBetta\Models\BetTypes;
use TopBetta\Repositories\RisaFormRepository;

class FrontCombinedRacingController extends Controller {

	/**
	 * @var \TopBetta\Repositories\RisaFormRepository
	 */
	public $risaFormRepository;

	function __construct(RisaFormRepository $risaFormRepository) {
		$this->risaFormRepository = $risaFormRepository;
	}

	public function indexNew($type = 'r', $race = false, $meeting = false) {
		// required input
		$typeCode = \Input::get('type', $type);
		$raceId = \Input::get('race', $race);
		$meetingId = \Input::get('meeting', $meeting);

		if (!$meetingId && !$raceId) {
			return array("success" => false, "error" => "No meeting id or race id selected");
		}

		// work out meeting id based off race id only
		if (!$meetingId && $raceId) {
			$meetingId = \TopBetta\Models\RaceEventGroupEvent::where('event_id', $raceId)->pluck('event_group_id');
		}

		$meetingsController = new FrontMeetingsController();
		$meetingAndRaces = $meetingsController->show($meetingId, true);

		if (!$meetingAndRaces['success']) {
			return array("success" => false, "error" => "No meetings and races available");
		}

		$meetingAndRaces = $meetingAndRaces['result'];

		$races = $meetingAndRaces['races'];

		if ($races) {
			foreach ($races as $key => $value) {
				$races[$key]['meeting_id'] = $meetingAndRaces['id'];

                //exclude international races
                if($meetingAndRaces['country'] != 'AU' && $meetingAndRaces['country'] != 'NZ') {
                    $races[$key]['exclude_bet_types'] = BetTypes::getExcludedBetTypesForInternationalRaces();

                } else {
                    $races[$key]['exclude_bet_types'] = array();
                }
			}
		}

		unset($meetingAndRaces['races']);

		$meeting = $meetingAndRaces;

        // get the totes being paid fow win/place/exotic
        $totesPaid = \TopBetta\Models\ProductDefaults::getTotePaidForMeeting($meeting['country'], $meeting['meeting_grade'], $meeting['type_code']);

        foreach($totesPaid as $tote){
            switch($tote['bet_type']){
                case 'W':
                    $meeting['win_tote'] = $tote['provider_product_name'];
                    break;
                case 'P':
                    $meeting['place_tote'] = $tote['provider_product_name'];
                    break;
                case 'E':
                    $meeting['exacta_tote'] = $tote['provider_product_name'];
                    break;
                case 'Q':
                    $meeting['quinella_tote'] = $tote['provider_product_name'];
                    break;
                case 'T':
                    $meeting['trifecta_tote'] = $tote['provider_product_name'];
                    break;
                case 'FF':
                    $meeting['firstfour_tote'] = $tote['provider_product_name'];
                    break;
            }
        }

		$runnersController = new FrontRunnersController();
		$runners = $runnersController->index(false, $raceId);


		if (!$runners['success']) {
			return array("success" => false, "error" => "No runners available");
		}

		$runners = $runners['result'];

		$repository = $this->risaFormRepository;

		foreach ($runners as $key => $value) {
			$runnersForm = \Cache::remember("risaform-runner-$key-race-$raceId", 240, function() use (&$repository, $runners, $key, $raceId) {
				return $repository->getFormForRunnerAndRaceId($runners[$key], (int)$raceId);
			});

			$runners[$key]['race_id'] = $raceId;

			if (isset($runnersForm['detailed_form'])) {
				$runners[$key]['detailed_form'] = $runnersForm['detailed_form'];
			}

		}

		return array('success' => true, 'result' => array('meeting' => $meeting, 'races' => $races, 'runners' => $runners));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param string $type
	 * @param bool $race
	 * @param bool $meeting
	 * @return Response
	 */
    public function index($type = 'r', $race = false, $meeting = false)
    {

        // required input
        $typeCode = \Input::get('type', $type);
        $raceId = \Input::get('race', $race);
        $meetingId = \Input::get('meeting', $meeting);

        if (!$meetingId && !$raceId) {
            return array("success" => false, "error" => "No meeting id or race id selected");
        }

        // work out meeting id based off race id only
        if (!$meetingId && $raceId) {
            $meetingId = \TopBetta\Models\RaceEventGroupEvent::where('event_id', $raceId)->pluck('event_group_id');
        }

        $meetingsController = new FrontMeetingsController();
        $meetingAndRaces = $meetingsController->show($meetingId, true);

        if (!$meetingAndRaces['success']) {
            return array("success" => false, "error" => "No meetings and races available");
        }

        $meetingAndRaces = $meetingAndRaces['result'];

        $races = $meetingAndRaces['races'];

        if ($races) {
            foreach ($races as $key => $value) {
                $races[$key]['meeting_id'] = $meetingAndRaces['id'];
            }
        }

        unset($meetingAndRaces['races']);

        $meeting = $meetingAndRaces;

        $runnersController = new FrontRunnersController();
        $runners = $runnersController->index(false, $raceId);


        if (!$runners['success']) {
            return array("success" => false, "error" => "No runners available");
        }

        $runners = $runners['result'];

        $repository = $this->risaFormRepository;

        foreach ($runners as $key => $value) {
            $runnersForm = \Cache::remember("risaform-runner-$key-race-$raceId", 240, function() use (&$repository, $runners, $key, $raceId) {
                return $repository->getFormForRunnerAndRaceId($runners[$key], (int)$raceId);
            });

            $runners[$key]['race_id'] = $raceId;

            if (isset($runnersForm['detailed_form'])) {
                $runners[$key]['detailed_form'] = $runnersForm['detailed_form'];
            }
		}

        return array('success' => true, 'result' => array('meeting' => $meeting, 'races' => $races, 'runners' => $runners));

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
        //
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

}