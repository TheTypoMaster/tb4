<?php namespace TopBetta\Http\Controllers\Backend;

use TopBetta\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class RiskRaceStatusController extends Controller
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

        $success = false;
        switch ($input['action']) {
            case 'override_start':
                $success = self::updateOverrideStart($input['race_id'], $input['enabled']);
                break;

            case 'status_change':
                $success = self::updateRaceStatus($input['status'], $input['race_id']);
                break;

            default:
                break;
        }

        if (!$success) {
            return array("success" => false, "error" => "Problem updating status for race " . $input['race_id']);
        }

        return array("success" => true, "result" => "Status updated for race " . $input['race_id']);
    }

    private static function updateOverrideStart($raceId, $enabled = false)
    {
        return \TopBetta\Models\RaceEvent::where('id', $raceId)->update(array('override_start' => $enabled));
    }

    public static function updateRaceStatus($status, $raceId)
    {
        $eventStatus = \RaceEventStatus::where('keyword', $status)->pluck('id');
        $event = \TopBetta\Models\RaceEvent::find($raceId);
        if ($eventStatus && $event) {
            $event->event_status_id = $eventStatus;
            $event->save();

            if ($eventStatus == 6 || $eventStatus == 2 || $eventStatus == 3) {
                // result bets for race status of interim, paying or abandoned
                \TopBetta\Facades\BetResultRepo::resultAllBetsForEvent($raceId);
            }			

            return true;
        }

        return false;
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

}
