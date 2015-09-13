<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 15/07/2015
 * Time: 2:16 PM
 */

namespace TopBetta\Repositories;

use DB;
use TopBetta\Models\MarketModel;
use TopBetta\Repositories\Contracts\MarketModelRepositoryInterface;
use TopBetta\Repositories\Traits\SportsResourceRepositoryTrait;

class DbMarketModelRepository extends BaseEloquentRepository implements MarketModelRepositoryInterface
{
    use SportsResourceRepositoryTrait;


    public function __construct(MarketModel $model)
    {
        $this->model = $model;
    }

    public function getMarketsForCompetition($competition, $types = null)
    {
        $builder = $this->getVisibleSportsEventBuilder()
            ->where('eg.id', $competition)
            ->groupBy('m.id');

        if( $types ) {
            $builder->whereIn('m.market_type_id', $types)
                ->orderBy(DB::raw("FIELD(m.market_type_id," . implode(",", $types) . ")"));
        }

        return $this->model->hydrate($builder->get(array('m.*')))->load(array('marketType', 'selections', 'selections.price'));
    }

    public function getMarketsForEvents($events, $types = null)
    {
        $builder = $this->getVisibleSportsEventBuilder()
            ->whereIn('e.id', $events)
            ->groupBy('m.id');

        if( $types ) {
            $builder->whereIn('m.market_type_id', $types)
                ->orderBy(DB::raw("FIELD(m.market_type_id," . implode(",", $types) . ")"));
        }

        return $this->model->hydrate($builder->get(array('m.*')))->load(array('marketType', 'selections'));
    }

    public function getMarketsForEvent($event)
    {
        $builder = $this->getVisibleSportsEventBuilder()
            ->where('e.id', $event)
            ->groupBy('m.id');

        return $this->model->hydrate($builder->get(array('m.*')))->load(array('marketType', 'selections'));
    }

    public function getVisibleMarketsWithSelections()
    {
        $builder = $this->getVisibleSportsEventBuilder()
            ->groupBy('s.id')
            ->select(array('m.*', 's.id as selection_id'));

        return $this->model->hydrate($builder->get())->load(array('markettype'));
    }

    public function getMarketByExternalIds($externalMarketId, $externalEventId)
    {
        return $this->model
            ->where('external_market_id', $externalMarketId)
            ->where('external_event_id', $externalEventId)
            ->first()->toArray();
    }

    public function getMarketModelByExternalIds($externalMarketId, $externalEventId)
    {
        return $this->model
            ->where('external_market_id', $externalMarketId)
            ->where('external_event_id', $externalEventId)
            ->first();
    }
}