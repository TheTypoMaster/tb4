<?php namespace TopBetta\Http\Controllers\Backend;

use TopBetta\Http\Controllers\Controller;
use TopBetta\Http\Controllers\Backend\RiskRaceStatusController;

use Illuminate\Support\Facades\Input;
use TopBetta\Repositories\Cache\RaceRepository;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Services\Betting\BetResults\BetResultService;
use TopBetta\Services\Racing\RaceResultService;


class RiskResultsController extends Controller
{
    /**
     * @var BetResultService
     */
    private $betResultService;
    /**
     * @var RiskRaceStatusController
     */
    private $riskRaceStatusController;
    /**
     * @var RaceRepository
     */
    private $raceRepository;
    /**
     * @var RaceResultService
     */
    private $resultService;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;

    public function __construct(BetResultService $betResultService,
                                RiskRaceStatusController $riskRaceStatusController,
                                RaceRepository $raceRepository,
                                RaceResultService $resultService,
                                EventRepositoryInterface $eventRepository, SelectionRepositoryInterface $selectionRepository )
    {
        $this->betResultService = $betResultService;
        $this->riskRaceStatusController = $riskRaceStatusController;
        $this->raceRepository = $raceRepository;
        $this->resultService = $resultService;
        $this->eventRepository = $eventRepository;
        $this->selectionRepository = $selectionRepository;
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

        if (!isset($input['race_id']) || !($race = $this->eventRepository->getEventModelFromExternalId($input['race_id']))) {
            return array("success" => false, "error" => "Problem updating results for race " . $input['race_id']);
        }

        $errors = $this->updateRaceResults($input, $race);

        //update results in cache
        $this->raceRepository->makeCacheResource($race);
        $race = $this->raceRepository->getRace($race->id);
        $this->resultService->loadResultForRace($race, true);
        $this->raceRepository->save($race);

        if (count($errors)) {
            return array("success" => false, "error" => "Problem updating results for race " . $input['race_id'], "messages" => $errors);
        }
        return array("success" => true, "result" => "Results updated for race " . $input['race_id']);
    }

    private function updateRaceResults(array $raceResults, $race)
    {
        // delete all results records for this event
        $errors = array();

        foreach ($raceResults as $key => $raceResult) {

            switch ($key) {
                case 'exotics':
                    $this->saveExoticResults($raceResult, $race);
                    break;

                case 'positions':
                    $this->savePositionResults($raceResult, $race);
                    break;

                case 'race_status':
                    if (!$this->riskRaceStatusController->updateRaceStatus($raceResult, $race)) {
                        $errors[] = "Problem updating race status";
                    }
                    break;

                default:
                    break;
            }
        }

        return $errors;
    }

    private function saveExoticResults($raceResult, $race)
    {
        $normalizedResults = array();

        foreach ($raceResult as $result) {
            $normalizedResults[] = array(
                'bet_type' => $result['name'],
                'result_string' => $result['selections'],
                'dividend' => $result['dividend'],
            );
        }
        return $this->resultService->storeDefaultExoticResults($race, $normalizedResults);
    }

    private function savePositionResults($raceResult, $race)
    {
        $normalizedResults = array();

        // loop over each position and format for saving
        foreach ($raceResult as $result) {
            $resultData = array(
                'selection' => $this->selectionRepository->getSelectionByExternalId($result['selection_id']),
                'position' => $result['position']
            );

            if ($result['position'] == 1) {
                $normalizedWinResult = array(
                    'bet_type' => BetTypeRepositoryInterface::TYPE_WIN,
                    'dividend' => $result['win_dividend'],
                );

                $normalizedResults[] = array_merge($resultData, $normalizedWinResult);
            }

            $normalizedPlaceResult = array(
                'bet_type' => BetTypeRepositoryInterface::TYPE_PLACE,
                'dividend' => $result['place_dividend'],
            );

            $normalizedResults[] = array_merge($resultData, $normalizedPlaceResult);

        }

        return $this->resultService->storeDefaultPositionResults($race, $normalizedResults);
    }

}
