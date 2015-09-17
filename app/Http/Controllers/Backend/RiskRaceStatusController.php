<?php namespace TopBetta\Http\Controllers\Backend;

use TopBetta\Http\Controllers\Controller;
use Queue;
use Config;
use Illuminate\Support\Facades\Input;
use TopBetta\Repositories\Cache\RaceRepository;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Services\Betting\BetResults\BetResultService;

class RiskRaceStatusController extends Controller
{

    /**
     * @var BetResultService
     */
    private $betResultService;
    /**
     * @var RaceRepository
     */
    private $raceRepository;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    public function __construct(BetResultService $betResultService, RaceRepository $raceRepository, EventRepositoryInterface $eventRepository)
    {
        $this->betResultService = $betResultService;
        $this->raceRepository = $raceRepository;
        $this->eventRepository = $eventRepository;
    }

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
                $success = $this->updateRaceStatus($input['status'], $input['race_id']);
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
        return \TopBetta\Models\RaceEvent::where('external_event_id', $raceId)->update(array('override_start' => $enabled));
    }

    public function updateRaceStatus($status, $raceId)
    {
        $eventStatus = \TopBetta\Models\RaceEventStatus::where('keyword', $status)->value('id');
        $event = \TopBetta\Models\RaceEvent::where('external_event_id', $raceId)->first();
        if ($eventStatus && $event) {

            $this->raceRepository->updateWithId($raceId, array("event_status_id" => $eventStatus), 'external_event_id');

            if ($eventStatus == 6 || $eventStatus == 2 || $eventStatus == 3) {
                // result bets for race status of interim, paying or abandoned
                //\TopBetta\Facades\BetResultRepo::resultAllBetsForEvent($raceId);
                Queue::push('TopBetta\Services\Betting\EventBetResultingQueueService', array('event_id' => $event->id), Config::get('betresulting.queue'));
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
