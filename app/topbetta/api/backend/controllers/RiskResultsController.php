<?php

namespace TopBetta\backend;

use Illuminate\Support\Facades\Input;

class RiskResultsController extends \BaseController
{

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = Input::json()->all();
        $errors = static::updateRaceResults($input, $input['race_id']);

        if (count($errors)) {
            return array("success" => false, "error" => "Problem updating results for race " . $input['race_id'], "messages" => $errors);
        }
        return array("success" => true, "result" => "Results updated for race " . $input['race_id']);
    }

    private static function updateRaceResults(array $raceResults, $raceId)
    {
        // delete all results records for this event
        \TopBetta\RaceResult::deleteResultsForRaceId($raceId);

        $errors = [];

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
                    if (!static::updateRaceStatus($raceResult, $raceId)) {
                        $errors[] = "Problem updating race status";
                    }

                    break;

                default:
                    break;
            }
        }

        return $errors;
    }

    private static function updateRaceStatus($raceResult, $raceId)
    {
        $eventStatus = \RaceEventStatus::where('keyword', $raceResult)->pluck('id');
        $event = \TopBetta\RaceEvent::find($raceId);
        if ($eventStatus && $event) {
            $event->event_status_id = $eventStatus;
            $event->save();

            return true;
        }

        return false;
    }

    private static function saveExoticResults($raceResult, $raceId)
    {
        $event = \TopBetta\RaceEvent::find($raceId);
        if (!$event) {
            return false;
        }

        // default all exotic dividend's to empty to wipe out previous results
        $updateData = array('quinella_dividend' => null,
            'exacta_dividend' => null,
            'trifecta_dividend' => null,
            'firstfour_dividend' => null);

        // loop over each exotic type and build our result
        foreach ($raceResult as $result) {
            $updateData[$result['name'] . '_dividend'] = serialize(
                    array($result['selections'] => $result['dividend'])
            );
        }

        return $event->update($updateData);
    }

    private static function savePositionResults($raceResult, $raceId)
    {
        $success = 0;

        // loop over each position and save it separately
        foreach ($raceResult as $result) {

            $runner = \TopBetta\RaceSelection::getByEventIdAndRunnerNumber($raceId, $result['number']);

            $resultData = array('selection_id' => $runner[0]->id,
                'position' => $result['position']
            );

            if ($result['position'] == 1) {
                $resultData['win_dividend'] = $result['win_dividend'];
                $resultData['place_dividend'] = $result['place_dividend'];
            } else {
                $resultData['place_dividend'] = $result['place_dividend'];
            }

            if (\TopBetta\RaceResult::create($resultData)) {
                $success++;
            }
        }

        // did we at least update 1 record
        return ($success > 0) ? true : false;
    }

}
