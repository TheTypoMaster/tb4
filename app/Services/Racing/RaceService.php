<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 11:16 AM
 */

namespace TopBetta\Services\Racing;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Repositories\Contracts\EventModelRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;

class RaceService extends RacingResourceService {

    /**
     * @var SelectionService
     */
    private $selectionService;
    /**
     * @var EventModelRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var RaceResultService
     */
    private $resultService;

    public function __construct(EventModelRepositoryInterface $eventRepository, SelectionService $selectionService, RaceResultService $resultService)
    {
        $this->selectionService = $selectionService;
        $this->eventRepository = $eventRepository;
        $this->resultService = $resultService;
    }

    public function getRaceWithSelections($raceId)
    {
        $race = $this->eventRepository->getEvent($raceId, true);

        if( ! $race ) {
            throw new ModelNotFoundException;
        }

        return $race;
    }

    public function raceHasResults($race)
    {
        return $race->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_INTERIM ||
            $race->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_PAYING ||
            $race->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_PAID;
    }

    public function isOpen($race)
    {
        return $race->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_SELLING;
    }

    public function loadResultsForRaces($races)
    {
        foreach($races as $race) {
            $this->loadResultForRace($race);
        }

        return $races;
    }

    public function loadResultForRace($race)
    {
        if( $this->raceHasResults($race) ) {
            $results = $this->resultService->formatForResponse($race);

            $race->setResultString($results['result_string']);
            $race->setResults($results['results']);
            $race->setExoticResults($results['exotic_results']);
        }

        return $race;
    }


    public function formatForResponse($race)
    {
        $response = array(
            "id" => $race->id,
            "name" => $race->name,
            "start_date" => $race->start_date,
            "number" => $race->number,
            "description" => $race->description,
            "class" => $race->class,
            "distance" => $race->distance,
            "status" => $race->eventstatus->name,
            "weather" => $race->weather,
            "track_condition" => $race->track_condition
        );

        if( $this->raceHasResults($race) ) {
            $response = array_merge($response, $this->resultService->formatForResponse($race));
        }

        if( isset($race->markets) && isset($race->markets->first()->selections) ) {
            $response['selections'] = $this->selectionService->formatCollectionsForResponse($race->markets->first()->selections);
        }

        return $response;
    }
}