<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/09/2015
 * Time: 7:27 PM
 */

namespace TopBetta\Repositories\Cache\Bets;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use TopBetta\Models\BetModel;
use TopBetta\Repositories\Cache\CachedResourceRepository;
use TopBetta\Repositories\Cache\RacingSelectionPriceRepository;
use TopBetta\Repositories\Cache\Sports\SelectionPriceRepository;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\BetResultStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\ResultPricesRepositoryInterface;
use TopBetta\Repositories\DbBetRepository;
use TopBetta\Resources\EloquentResourceCollection;

class BetRepository extends CachedResourceRepository implements BetRepositoryInterface {

    const CACHE_KEY_PREFIX = 'bets_';

    protected $resourceClass = 'TopBetta\Resources\Betting\BetResource';

    protected $cachePrefix = self::CACHE_KEY_PREFIX;

    protected $storeIndividualResource = false;

    protected $tags = array("users", "bets");
    /**
     * @var SelectionPriceRepository
     */
    private $priceRepositoy;
    /**
     * @var ResultPricesRepositoryInterface
     */
    private $resultPricesRepository;
    /**
     * @var RacingSelectionPriceRepository
     */
    private $racingSelectionPriceRepository;

    public function __construct(DbBetRepository $repository, SelectionPriceRepository $priceRepositoy, RacingSelectionPriceRepository $racingSelectionPriceRepository, ResultPricesRepositoryInterface $resultPricesRepository)
    {
        $this->repository = $repository;
        $this->priceRepositoy = $priceRepositoy;
        $this->resultPricesRepository = $resultPricesRepository;
        $this->racingSelectionPriceRepository = $racingSelectionPriceRepository;
    }

    public function getDateBetsArray($user, $date)
    {
        return \Cache::tags($this->tags)->get($this->cachePrefix . $user . '_' . $date);
    }

    public function getActiveBetsArray($user)
    {
        return \Cache::tags($this->tags)->get($this->cachePrefix . $user . '_active');
    }

    public function getEventBetsArray($event, $user)
    {
        return \Cache::tags($this->tags)->get($this->cachePrefix . $user . '_event_' . $event);
    }

    public function getActiveBets($user)
    {
        $resources = $this->getCollection($this->cachePrefix . $user . '_active');

        if ($resources) {
            //add odds to bets
            $resources = $resources->map(function ($v) {
                return $this->attachOdds($v);
            });
        }

        return $resources;
    }

    public function getDateBets($user, $date)
    {
        return $this->getCollection($this->cachePrefix . $user . '_' . $date->toDateString());
    }

    public function getEventBets($event, $user)
    {
        return$this->getCollection($this->cachePrefix . $user . '_event_' . $event);
    }

    public function create($data)
    {
        return $this->repository->createAndReturnModel($data);
    }

    public function makeCacheResource($model)
    {
        if ( $model->event->start_date >= Carbon::now()->startOfDay()) {
            $resource = $this->createResource(new BetModel($this->buildResourceArrayFromModel($model)));

            $this->updateActiveBets($resource->getModel(), $model->user_id);

            $this->addToDateBets($resource->getModel(), $model->user_id);

            $this->addToEventBets($resource->event_id, $model->user_id,  $resource->getModel());
        }

        return $model;
    }

    public function addToDateBets($resource, $user)
    {
        $bets = $this->getDateBetsArray($user, $resource->startDate);

        if (!$bets) {
            $bets = array();
        }

        $bets[$resource->id] = $resource->toArray();

        $date = Carbon::createFromFormat('Y-m-d H:i:s', $resource->date);

        $this->put($this->cachePrefix . $user . '_' . $date->toDateString(), $bets, Carbon::createFromFormat('Y-m-d H:i:s', $resource->date)->endOfDay()->diffInMinutes());
    }

    public function addToActiveBets($resource, $user)
    {
        $bets = $this->getActiveBetsArray($user);

        if (!$bets) {
            $bets = array();
        }

        $bets[$resource->id] = $resource->toArray();

        $this->put($this->cachePrefix . $user . '_active', $bets, Carbon::now()->addMonth()->diffInMinutes());
    }

    public function addToEventBets($event, $user, $resource)
    {
        if ($resource->date >= Carbon::now()->startOfDay()->subDays(2)) {
            $bets = $this->getEventBetsArray($event, $user);

            if (!$bets) {
                $bets = array();
            }

            $bets[$resource->id] = $resource->toArray();

            $this->put($this->cachePrefix . $user . '_event_' . $event, $bets, Carbon::createFromFormat('Y-m-d H:i:s', $resource->date)->addDays(2)->diffInMinutes());
        }
    }

    public function updateActiveBets($resource, $user)
    {
        $bets = $this->getActiveBetsArray($user);

        if ($bet = array_get($bets, $resource->id)) {
            if ($resource->status->keyword != BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED) {
                unset($bets[$resource->id]);
            } else {
                $bets[$resource->id] = $resource->toArray();
            }

            $this->put($this->cachePrefix . $user . '_active', $bets, Carbon::now()->addMonth()->diffInMinutes());
        }
    }

    public function makeAndGetBetResource($model)
    {
        if ( $model->event->start_date >= Carbon::now()->startOfDay()) {

            $resource = $this->createResource(new BetModel($this->buildResourceArrayFromModel($model)));
            $resource = $this->attachOdds($resource);

            if ($model->status->name == BetResultStatusRepositoryInterface::RESULT_STATUS_UNRESULTED) {
                $this->addToActiveBets($resource->getModel(), $model->user_id);
            }

            $this->addToDateBets($resource->getModel(), $model->user_id);

            $this->addToEventBets($resource->event_id, $model->user_id,  $resource->getModel());

            return $resource;
        }

        return null;
    }

    public function storeActiveBetsCollection($user, $bets)
    {
        $this->put($this->cachePrefix . $user . '_active', $bets->toArray(), Carbon::now()->addMonth()->diffInMinutes());
    }

    public function storeBetsCollectionForDate($user, $date, $bets)
    {
        $this->put($this->cachePrefix . $user . '_' . $date->toDateString(), $bets->toArray(), Carbon::now()->addMonth()->diffInMinutes());
    }

    public function storeEventBets($event, $user, $bets)
    {
        $this->put($this->cachePrefix . $user . '_event_' . $event, $bets->toArray(), Carbon::now()->addDays(2)->diffInMinutes());
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
            'bet_result_status_id' => $bet->bet_result_status_id,
            'paid'             => $bet->result ? $bet->result->amount : 0,
            'date'             => $bet->selection->first()->market->event->start_date,
            'eventType'        => $bet->selection->first()->market->event->competition->first()->type_code,
            'percentage'       => $bet->percentage,
            'boxedFlag'        => $bet->boxed_flag,
            'fixed'            => $bet->product->is_fixed_odds || $bet->type->name == BetTypeRepositoryInterface::TYPE_SPORT,
            "fixed_odds"       => $bet->betselection->first()->fixed_odds,
            'productId'        => $bet->product->id,
            'productCode'      => $bet->product->productProviderMatch ? $bet->product->productProviderMatch->provider_product_name : null,
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
        if (!$resource->isExotic() && !$resource->isFixed()) {
            if ($resource->betType == BetTypeRepositoryInterface::TYPE_WIN) {
                $price = $this->racingSelectionPriceRepository->getPriceForSelectionByProduct($resource->selection_id, $resource->productId);
                if ($price) {
                    $resource->win_odds = $price->win_odds;
                }
            } else if ($resource->betType == BetTypeRepositoryInterface::TYPE_PLACE) {
                $price = $this->racingSelectionPriceRepository->getPriceForSelectionByProduct($resource->selection_id, $resource->productId);
                if ($price) {
                    $resource->place_odds = $price->place_odds;
                }
            }
        }

        return $resource;
    }

    public function getBetsForSelectionsByBetType($user, $selections, $betType)
    {
        return $this->repository->getBetsForSelectionsByBetType($user, $selections, $betType);
    }

    public function getBetsForUserByMarket($user, $market, $type = null)
    {
        return $this->repository->getBetsForUserByMarket($user, $market, $type);
    }

    public function getBetsForUserByEvent($userId, $eventId, $type = null)
    {
        if($type) {
            return $this->repository->getBetsForUserByEvent($userId, $eventId, $type);
        }

        $bets = $this->getEventBets($eventId, $userId);

        if ($bets) {
            return $bets;
        }

        $bets = $this->repository->getBetsForUserByEvent($userId, $eventId, $type);

        $this->storeEventBets($eventId, $userId, $bets);

        return $bets;
    }

    public function getBetsForUserBySelection($userId, $selection, $type = null)
    {
        return $this->repository->getBetsForUserBySelection($userId, $selection, $type);
    }

    public function getAllBetsForUser($user)
    {
        return $this->repository->getAllBetsForUser($user);
    }

    public function getWinningBetsForUser($user)
    {
        return $this->repository->getWinningBetsForUser($user);
    }

    public function getLosingBetsForUser($user)
    {
        return $this->repository->getLosingBetsForUser($user);
    }

    public function getRefundedBetsForUser($user)
    {
        return $this->repository->getRefundedBetsForUser($user);
    }

    public function getUnresultedBetsForUser($user, $page = true)
    {
        if (!$page) {
            $bets = $this->getActiveBets($user);

            if ($bets) {
                return $bets;
            }
        }

        //load bets for user
        $bets = $this->repository->getUnresultedBetsForUser($user, $page);

        $this->storeActiveBetsCollection($user, $bets);

        return $bets;
    }

    public function getBetsOnDateForUser($user, Carbon $date, $resulted = null)
    {
        $bets = $this->getDateBets($user, $date);

        if ($bets) {
            return $bets;
        }

        $bets = $this->repository->getBetsOnDateForUser($user, $date, $resulted);

        $this->storeBetsCollectionForDate($user, $date, $bets);

        return $bets;
    }

    public function getBetsForEventGroup($user, $eventGroup)
    {
        return $this->repository->getBetsForEventGroup($user, $eventGroup);
    }

    public function getByResultTransaction($transaction)
    {
        return $this->repository->getByResultTransaction($transaction);
    }

    public function getByRefundTransaction($transaction)
    {
        return $this->repository->getByRefundTransaction($transaction);
    }

    public function getByEntryTransaction($transaction)
    {
        return $this->repository->getByEntryTransaction($transaction);
    }

    public function findBets($bets)
    {
        return $this->repository->findBets($bets);
    }

    public function getBetsForEventByStatus($event, $status, $type = null)
    {
        return $this->repository->getBetsForEventByStatus($event, $status, $type);
    }

    public function getBetsForEventByStatusAndProduct($event, $status, $product, $type = null)
    {
        return $this->repository->getBetsForEventByStatusAndProduct($event, $status, $product, $type);
    }

    /**
     * @param $key
     * @return EloquentResourceCollection
     */
    public function getCollection($key, $resource = null)
    {
        $collection = \Cache::tags($this->tags)->get($key);

        if ($collection) {
            return $this->createCollectionFromArray($collection, $resource);
        }

        return null;
    }
}