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

class DbMarketModelRepository implements MarketModelRepositoryInterface
{
    use SportsResourceRepositoryTrait;
    /**
     * @var MarketModel
     */
    private $model;

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
}