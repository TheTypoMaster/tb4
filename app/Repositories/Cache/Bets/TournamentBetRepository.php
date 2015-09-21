<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/09/2015
 * Time: 9:09 AM
 */

namespace TopBetta\Repositories\Cache\Bets;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use TopBetta\Models\TournamentBetModel;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Cache\RacingSelectionPriceRepository;
use TopBetta\Repositories\Cache\Tournaments\TournamentTicketRepository;
use TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\ResultPricesRepositoryInterface;
use TopBetta\Repositories\Contracts\TournamentBetRepositoryInterface;
use TopBetta\Repositories\DbEventRepository;
use TopBetta\Repositories\DbTournamentBetRepository;
use TopBetta\Resources\EloquentResourceCollection;

class TournamentBetRepository extends CachedResourceRepository implements TournamentBetRepositoryInterface
{

    const CACHE_KEY_PREFIX = 'tournament_bets_';

    protected $resourceClass = 'TopBetta\Resources\Tournaments\TournamentBetResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $storeIndividualResource = false;

    protected $tags = array("users", "tournament_bets");

    /**
     * @var RacingSelectionPriceRepository
     */
    private $racingSelectionPriceRepository;
    /**
     * @var ResultPricesRepositoryInterface
     */
    private $resultPricesRepository;
    /**
     * @var DbEventRepository
     */
    private $eventRepository;
    /**
     * @var TournamentTicketRepository
     */
    private $ticketRepository;

    public function __construct(DbTournamentBetRepository $repository, RacingSelectionPriceRepository $racingSelectionPriceRepository, ResultPricesRepositoryInterface $resultPricesRepository, DbEventRepository $eventRepository, TournamentTicketRepository $ticketRepository)
    {
        $this->repository = $repository;
        $this->racingSelectionPriceRepository = $racingSelectionPriceRepository;
        $this->resultPricesRepository = $resultPricesRepository;
        $this->eventRepository = $eventRepository;
        $this->ticketRepository = $ticketRepository;
    }

    public function create($data)
    {
        $model = $this->repository->createAndReturnModel($data);

        $this->ticketRepository->addAvailableCurrency($model->ticket->user_id, $model->ticket->tournament, -$model->bet_amount);

        return $model;
    }

    public function getTournamentBetsArray($user, $tournament)
    {
        return \Cache::tags($this->tags)->get($this->cachePrefix . $user . '_' . $tournament);
    }

    public function getTournamentBets($user, $tournament)
    {
        $bets = $this->getCollection($this->cachePrefix . $user . '_' . $tournament);

        if ($bets) {
            $bets = $bets->map(function ($v) {
                return $this->attachOdds($v);
            });
        }

        return $bets;
    }

    public function getTournamentBetsByEventStatuses($user, $tournament, $eventStatuses)
    {
        $bets = $this->getCollection($this->cachePrefix . $user . '_' . $tournament);

        if ($bets && $bets->count()) {

            $events = $this->eventRepository->getEventsWithStatusIn($bets->unique('event_id')->lists('eventId')->all(), $eventStatuses)->lists('id')->all();

            $bets = $bets->filter(function($v) use ($events) {
                return in_array($v->eventId, $events);
            });

            $bets = $bets->map(function ($v) {
                return $this->attachOdds($v);
            });
        }

        return $bets;
    }

    public function makeCacheResource($model)
    {
        if ($model->ticket->tournament->end_date >= Carbon::now()->subDays(2)) {
            $resource = $this->buildResourceArrayFromModel($model);
            $resource = $this->createResource(new TournamentBetModel($resource));
            $resource = $this->attachOdds($resource);

            $this->addBetToTournamentCollection($model->ticket->user_id, $model->ticket->tournament, $resource->getModel());
        }

        return $model;
    }

    public function makeAndReturnBetResource($model)
    {
        if ($model->ticket->tournament->end_date >= Carbon::now()->subDays(2)) {
            $resource = $this->buildResourceArrayFromModel($model);
            $resource = $this->createResource(new TournamentBetModel($resource));
            $resource = $this->attachOdds($resource);

            $this->addBetToTournamentCollection($model->ticket->user_id, $model->ticket->tournament, $resource->getModel());

            return $resource;
        }

        return null;
    }

    public function addBetToTournamentCollection($userId, $tournament, $resource)
    {
        $bets = $this->getTournamentBetsArray($userId, $tournament->id);

        if (!$bets) {
            $bets = $this->getBetsForUserTournament($userId, $tournament->id)->toArray();
        }

        if (($existingBet = array_get($bets, $resource->id)) && $existingBet['status'] == BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED &&
            $resource->status != BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED && $resource->paid > 0) {
            $this->ticketRepository->addAvailableCurrency($userId, $tournament, $resource->paid);
        }

        $bets[$resource->id] = $resource->toArray();

        $this->put($this->cachePrefix . $userId . '_' . $tournament->id, $bets, Carbon::createFromFormat('Y-m-d H:i:s', $tournament->end_date)->addDays(2)->diffInMinutes());
    }

    public function storeTournamentBets($user, $tournament, $bets)
    {
        $this->put(
            $this->cachePrefix . $user . '_' . $tournament,
            $bets->toArray(),
            Carbon::createFromFormat('Y-m-d H:i:s', $bets->first()->ticket->tournament->end_date)->addDays(2)->diffInMinutes()
        );
    }

    public function getResultedUserBetsInTournament($ticketId)
    {
        return $this->repository->getResultedUserBetsInTournament($ticketId);
    }

    public function getBetsForUserInTournamentWhereEventStatusIn($user, $tournament, $eventStatuses)
    {
        return $this->repository->getBetsForUserInTournamentWhereEventStatusIn($user, $tournament, $eventStatuses);
    }

    public function getBetResourcesForUserInTournamentWhereEventStatusIn($user, $tournament, $statuses)
    {
        $bets = $this->getTournamentBetsByEventStatuses($user, $tournament, $statuses);

        if ($bets) {
            return $bets;
        }

        $bets = $this->repository->getBetsForUserInTournamentWhereEventStatusIn($user, $tournament, $statuses);

        //set bets to be an emoty collection for this user
        $this->storeTournamentBets($user, $tournament, new EloquentResourceCollection(new Collection(), $this->resourceClass));

        return $bets;
    }

    public function getBetsForEventByStatusIn($eventId, $status, $product, $betType = null)
    {
        return $this->repository->getBetsForEventByStatusIn($eventId, $status, $product, $betType);
    }

    public function getBetsForSelection($selectionId)
    {
        return $this->repository->getBetsForSelection($selectionId);
    }

    public function getBetsForMarket($marketId)
    {
        return $this->repository->getBetsForMarket($marketId);
    }

    public function getBetsOnEventForTicket($ticket, $event)
    {
        return $this->repository->getBetsOnEventForTicket($ticket, $event);
    }

    public function getBetsForUserTournament($user, $tournament)
    {
        $bets = $this->getTournamentBets($user, $tournament);

        if ($bets) {
            return $bets;
        }

        $bets = $this->repository->getBetsForUserTournament($user, $tournament);

        $this->storeTournamentBets($user, $tournament, $bets);

        return $bets;
    }

    public function getBetsForEventByStatus($eventId, $status, $betType = null)
    {
        return $this->repository->getBetsForEventByStatus($eventId, $status, $betType = null);
    }

    public function buildResourceArrayFromModel($bet)
    {
        $array = array(
            'id'               => $bet->id,
            'amount'           => $bet->bet_amount,
            'freeCreditAmount' => $bet->bet_freebet_amount,
            'selection_id'      => $bet->selection->first()->id,
            'selection_name'    => $bet->selection->first()->name,
            'selectionString'  => $bet->selection_string,
            'selection_number'  => $bet->selection->first()->number,
            'market_name'       => $bet->selection->first()->market->name,
            'marketId'         => $bet->selection->first()->market->id,
            'eventId'          => $bet->selection->first()->market->event->id,
            'eventName'        => $bet->selection->first()->market->event->name,
            'competitionId'    => $bet->selection->first()->market->event->competition->first()->id,
            'competitionName'  => $bet->selection->first()->market->event->competition->first()->name,
            'betType'          => $bet->type->name,
            'status'           => $bet->status->name,
            'paid'             => $bet->win_amount,
            'date'             => $bet->selection->first()->market->event->start_date,
            'eventType'        => $bet->selection->first()->market->event->competition->first()->type_code,
            'percentage'       => $bet->percentage,
            'boxedFlag'        => $bet->boxed_flag,
            'fixed'            => ($bet->product ? $bet->product->is_fixed_odds : false) || $bet->type->name == BetTypeRepositoryInterface::TYPE_SPORT,
            "fixed_odds"       => $bet->fixed_odds,
            'productId'        => $bet->product ? $bet->product->id : null,
            'productCode'      => $bet->product ? $bet->product->productProviderMatch ? $bet->product->productProviderMatch->id : null : null,
            'event_status_id'    => $bet->selection->first()->market->event->event_status_id,
            'tournament_ticket_id' => $bet->tournament_ticket_id,
        );

        if (($bet->type->name == BetTypeRepositoryInterface::TYPE_WIN || $bet->type->name == BetTypeRepositoryInterface::TYPE_PLACE) && $bet->selection->first()->result) {
            if ($bet->type->name == BetTypeRepositoryInterface::TYPE_WIN) {
                $array['win_dividend'] = $this->resultPricesRepository->getPriceForResultByProductAndBetType($bet->selection->first()->result->id, $bet->bet_product_id, $bet->type->name);
            } else if ($bet->type->name == BetTypeRepositoryInterface::TYPE_PLACE) {
                $array['place_dividend'] = $this->resultPricesRepository->getPriceForResultByProductAndBetType($bet->selection->first()->result->id, $bet->bet_product_id, $bet->type->name);
            }
        }

        return $array;
    }

    public function attachOdds($resource)
    {
        if ($resource->status == BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED && !$resource->isExotic() && !$resource->isFixed()) {
            if ($resource->betType == BetTypeRepositoryInterface::TYPE_WIN) {
                $price = $this->racingSelectionPriceRepository->getPriceForSelectionByProduct($resource->selection_id, $resource->product_id);
                if ($price) {
                    $resource->win_odds = $price->win_odds;
                }
            } else if ($resource->betType == BetTypeRepositoryInterface::TYPE_PLACE) {
                $price = $this->racingSelectionPriceRepository->getPriceForSelectionByProduct($resource->selection_id, $resource->product_id);
                if ($price) {
                    $resource->place_odds = $price->place_odds;
                }
            }
        }

        return $resource;
    }
}