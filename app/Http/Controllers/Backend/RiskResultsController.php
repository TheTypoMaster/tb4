<?php namespace TopBetta\Http\Controllers\Backend;

use TopBetta\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class RiskResultsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = Input::all();
        if (!$input) {
            $input = Input::json()->all();
        }

        if (!isset($input['race_id']) || !\TopBetta\Models\RaceEvent::find($input['race_id'])) {
            return array("success" => false, "error" => "Problem updating results for race " . $input['race_id']);
        }

        $errors = static::updateRaceResults($input, $input['race_id']);

        if (count($errors)) {
            return array("success" => false, "error" => "Problem updating results for race " . $input['race_id'], "messages" => $errors);
        }
        return array("success" => true, "result" => "Results updated for race " . $input['race_id']);
    }

    private static function updateRaceResults(array $raceResults, $raceId)
    {
        // delete all results records for this event
        \TopBetta\Models\RaceResult::deleteResultsForRaceId($raceId);
        \TopBetta\Models\RaceResult::deleteExoticResultsForRaceId($raceId);

        $errors = array();

        foreach ($raceResults as $key => $raceResult) {

            switch ($key) {
                case 'exotics':
                    if (!static::saveExoticResults($raceResult, $raceId)) {
                        $errors[] = "Problem saving exotic results";
                    }

                    break;

                case 'positions':
                    if (!static::savePositionResults($raceResult, $raceId)) {
                        $errors[] = "Problem saving place results";
                    }

                    break;

                case 'race_status':
                    if (!\TopBetta\Http\Backend\Controllers\RiskRaceStatusController::updateRaceStatus($raceResult, $raceId)) {
                        $errors[] = "Problem updating race status";
                    }

                    break;

                default:
                    break;
            }
        }

        return $errors;
    }

    private static function saveExoticResults($raceResult, $raceId)
    {
        $event = \TopBetta\Models\RaceEvent::find($raceId);

        if (!$event) {
            return false;
        }

        $updateData = array('quinella_dividend' => array(),
            'exacta_dividend' => array(),
            'trifecta_dividend' => array(),
            'firstfour_dividend' => array());

        // loop over each exotic type and build our result, this handles dead heats as well
        foreach ($raceResult as $result) {
            $updateData[$result['name'] . '_dividend'][$result['selections']] = $result['dividend'];
        }
		
		// we need to store the exotics results as serialized array
		array_walk($updateData, function(&$item, $key) {
			$item = serialize($item);
		});

        return $event->update($updateData);
    }

    private static function savePositionResults($raceResult, $raceId)
    {
        $success = 0;

        // loop over each position and save it separately
        foreach ($raceResult as $result) {

            $runner = \TopBetta\Models\RaceSelection::getByEventIdAndRunnerNumber($raceId, $result['number']);

            if (count($runner)) {

                $resultData = array('selection_id' => $runner[0]->id,
                    'position' => $result['position']
                );

                if ($result['position'] == 1) {
                    $resultData['win_dividend'] = $result['win_dividend'];
                    $resultData['place_dividend'] = $result['place_dividend'];
                } else {
                    $resultData['place_dividend'] = $result['place_dividend'];
                }

                if (\TopBetta\Models\RaceResult::create($resultData)) {
                    $success++;
                }
            }
        }

        // did we at least update 1 record
        return ($success > 0) ? true : false;
    }

}
