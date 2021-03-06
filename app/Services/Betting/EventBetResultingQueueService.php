<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 12/05/2015
 * Time: 1:54 PM
 */

namespace TopBetta\Services\Betting;

use Log;
use Pheanstalk\Exception;
use TopBetta\Repositories\BetResultRepo;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Services\Betting\BetResults\BetResultService;
use TopBetta\Services\Betting\BetResults\TournamentBetResultService;
use TopBetta\Services\Tournaments\Exceptions\TournamentResultedException;
use TopBetta\Services\Tournaments\Resulting\TournamentResulter;

use TopBetta\Helpers\RiskManagerAPI;

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
    /**
     * @var TournamentRepositoryInterface
     */
    private $tournamentRepository;
    /**
     * @var TournamentResulter
     */
    private $tournamentResulter;

    /**
     * @var CompetitionRepositoryInterface
     */
    private $competition;

    /**
     * @var RiskManagerAPI
     */
    private $riskapi;

    public function __construct(EventRepositoryInterface $eventRepositoryInterface, 
                                TournamentBetResultService $tournamentBetResultService,  
                                BetResultService $betResultService, 
                                BetProductRepositoryInterface $betProductRepository, 
                                EventService $eventService, 
                                TournamentRepositoryInterface $tournamentRepository,
                                TournamentResulter $tournamentResulter,
                                CompetitionRepositoryInterface $competition,
                                RiskManagerAPI $riskapi)
    {
        $this->tournamentBetResultService = $tournamentBetResultService;
        $this->eventRepositoryInterface = $eventRepositoryInterface;
        $this->betResultService = $betResultService;
        $this->betProductRepository = $betProductRepository;
        $this->eventService = $eventService;
        $this->tournamentRepository = $tournamentRepository;
        $this->tournamentResulter = $tournamentResulter;
        $this->competition = $competition;
        $this->riskapi = $riskapi;
    }

    public function fire($job, $data)
    {
        if( (! $eventId = array_get($data, 'event_id', null)) ) {
            Log::error("Event id was not specified");
            return false;
        }

        $event = $this->eventRepositoryInterface->find($eventId)->load('resultPrices.betType');

        $productId = null;
        if ($event->competition->first()->sport_id <= 3 && ! ($productId = array_get($data, 'product_id'))) {
            Log::error("Product id was not specified");
            return false;
        }

        $product = null;
        if ($productId) {
            $product = $this->betProductRepository->find($productId);
        }

        Log::info("RESULTING BETS FOR EVENT " . $event->id . " PRODUCT " . ($product ? $product->id : 0));
        $result = $this->betResultService->resultBetsForEvent($event, $product);

        $tournamentResult = $this->tournamentBetResultService->resultAllBetsForEvent($event, $product);

        if ($event->resultPrices->filter(function ($v) { return $v->betType->name == BetTypeRepositoryInterface::TYPE_WIN;})->count() &&
            $event->resultPrices->filter(function ($v) { return $v->betType->name == BetTypeRepositoryInterface::TYPE_PLACE;})->count()
        ) {
            //result fixed odds products
            $fixedProducts = $this->betProductRepository->getFixedOddsProducts();

            foreach ($fixedProducts as $fixedProduct) {
                Log::info("RESULTING BETS FOR EVENT " . $event->id . " PRODUCT " . $fixedProduct->id);
                $result = $this->betResultService->resultBetsForEvent($event, $fixedProduct);
                $tournamentResult = $this->tournamentBetResultService->resultAllBetsForEvent($event, $fixedProduct);
            }
        }

        $eventPaid = $this->eventService->checkAndSetPaidStatus($event);

        $tournaments = $this->tournamentRepository->getFinishedUnresultedTournaments();

        foreach ($tournaments as $tournament) {
            try {
                $this->tournamentResulter->resultTournament($tournament);
            } catch (TournamentResultedException $e) {
                Log::error("Tournament " . $tournament->id . " is already resulted");
            }
        }

        // Push paid status to RISK if all bets have been paid out and the event status is paid!
        if ($eventPaid){
            // push result status update to Risk
            $riskPayload = array('MeetingId' => str_replace('_'.$event->number, '', $event->external_event_id),
                            'RaceNo' => $event->number,
                            'status_id' => 4);
            Log::debug('EventBetResultingQueueService (fire): Pushing PAID status to Risk - Events Status : '. $event->event_status_id, $riskPayload );
            try{
                $this->riskapi->sendRaceStatus(array('RaceStatusUpdate' => $riskPayload));
            }catch (\Exception $e ){
                Log::error('EventBetResultingQueueService (fire): Failed to push PAID status to risk', $riskPayload);
            }
        }


        return $job->delete();
    }

    public function failed($data)
    {
        \Log::error("BET RESULTING FAILED " . print_r($data,true));
    }
}