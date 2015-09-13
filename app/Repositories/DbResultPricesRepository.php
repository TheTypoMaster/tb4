<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 26/08/2015
 * Time: 2:50 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\ResultPriceModel;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\ResultPricesRepositoryInterface;

class DbResultPricesRepository extends BaseEloquentRepository implements ResultPricesRepositoryInterface
{

    public function __construct(ResultPriceModel $model)
    {
        $this->model = $model;
    }

    public function deletePricesForResults($resultIds)
    {
        return $this->model->whereIn('selection_result_id', $resultIds)->delete();
    }

    public function getByResultProductAndBetType($result, $product, $betType)
    {
        return $this->model
            ->where('selection_result_id', $result)
            ->where('product_id', $product)
            ->where('bet_type_id', $betType)
            ->first();
    }

    public function deleteExoticPricesForEventAndProduct($event, $product)
    {
        return $this->model
            ->join('tbdb_bet_type', 'tbdb_bet_type.id', '=', 'tb_result_prices.bet_type_id')
            ->whereIn('tbdb_bet_type.name', array(BetTypeRepositoryInterface::TYPE_QUINELLA, BetTypeRepositoryInterface::TYPE_EXACTA, BetTypeRepositoryInterface::TYPE_TRIFECTA, BetTypeRepositoryInterface::TYPE_FIRSTFOUR))
            ->where('product_id', $product)
            ->where('event_id', $event)
            ->delete();
    }

    public function getPriceForResultByProductAndBetType($result, $product, $betType)
    {
        return $this->model
            ->where('selection_result_id', $result)
            ->where('product_id', $product)
            ->where('bet_type_id', $betType)
            ->first();
    }

    public function getPricesByProductAndBetType($product, $betType)
    {
        return $this->model
            ->where('product_id', $product)
            ->where('bet_type_id', $betType)
            ->get();
    }

    public function getResultsForEvent($event)
    {
        return $this->model
            ->leftJoin('tbdb_selection_result', 'tbdb_selection_result.id', '=', 'tb_result_prices.selection_result_id')
            ->leftJoin('tbdb_selection', 'tbdb_selection.id', '=', 'tbdb_selection_result.selection_id')
            ->join('tbdb_bet_type', 'tbdb_bet_type.id', '=', 'tb_result_prices.bet_type_id')
            ->where('event_id', $event)
            ->get(array('tb_result_prices.*', 'tbdb_selection.*', 'tbdb_bet_type.name as bet_type', 'tbdb_selection_result.position', 'tbdb_selection.id as selection_id'));
    }
}