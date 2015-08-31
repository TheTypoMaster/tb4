<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/05/2015
 * Time: 1:54 PM
 */

namespace TopBetta\Services\Betting;

use Log;
use TopBetta\Repositories\BetResultRepo;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Services\Betting\BetResults\BetResultService;
use TopBetta\Services\Betting\BetResults\TournamentBetResultService;

class EventBetResultingQueueService {

    /**
     * @var BetResultRepo
     */
    private $betResultRepo;
    /**
     * @var TournamentBetResultService
     */
    private $tournamentBetResultService;
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepositoryInterface;
    /**
     * @var BetResultService
     */
    private $betResultService;
    /**
     * @var BetProductRepositoryInterface
     */
    private $betProductRepository;
    /**
     * @var EventService
     */
    private $eventService;

    public function __construct(EventRepositoryInterface $eventRepositoryInterface, TournamentBetResultService $tournamentBetResultService,  BetResultService $betResultService, BetProductRepositoryInterface $betProductRepository, EventService $eventService)
    {
        $this->tournamentBetResultService = $tournamentBetResultService;
        $this->eventRepositoryInterface = $eventRepositoryInterface;
        $this->betResultService = $betResultService;
        $this->betProductRepository = $betProductRepository;
        $this->eventService = $eventService;
    }

    public function fire($job, $data)
    {
        if( (! $eventId = array_get($data, 'event_id', null)) || ! ($productId = array_get($data, 'product_id')) ) {
            Log::error("Either event or product id was not specified");
            return false;
        }

        $event = $this->eventRepositoryInterface->find($eventId);
        $product = $this->betProductRepository->find($productId);

        $result = $this->betResultService->resultBetsForEvent($event, $product);

        $tournamentResult = $this->tournamentBetResultService->resultAllBetsForEvent($event);

        $this->eventService->checkAndSetPaidStatus($event);

        return $job->delete();
    }

    public function failed($data)
    {
        \Log::error("BET RESULTING FAILED " . print_r($data,true));
    }
}