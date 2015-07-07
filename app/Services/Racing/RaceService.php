<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/07/2015
 * Time: 11:16 AM
 */

namespace TopBetta\Services\Racing;


use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionResultRepositoryInterface;

class RaceService extends RacingResourceService {

    /**
     * @var SelectionService
     */
    private $selectionService;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;
    /**
     * @var RaceResultService
     */
    private $resultService;

    public function __construct(EventRepositoryInterface $eventRepository, SelectionService $selectionService, RaceResultService $resultService)
    {
        $this->selectionService = $selectionService;
        $this->eventRepository = $eventRepository;
        $this->resultService = $resultService;
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