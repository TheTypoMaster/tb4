<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 26/08/2015
 * Time: 2:51 PM
 */
namespace TopBetta\Repositories\Contracts;

interface ResultPricesRepositoryInterface
{
    public function deletePricesForResults($resultIds);

    public function deleteExoticPricesForEventAndProduct($event, $product);

    public function getPriceForResultByProductAndBetType($result, $product, $betType);

    public function getPricesByProductAndBetType($product, $betType);

    public function getResultsForEvent($event);

    public function getPricesByProductEventAndBetType($product, $event, $betType);

    public function deletePricesForEventBetTypeAndProduct($event, $betType, $product);
}