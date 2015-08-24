<?php namespace TopBetta\Repositories\Contracts;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 22:30
 * Project: tb4
 */


interface BetProductRepositoryInterface {

    public function getProductByCode($productCode);

    public function getProductsByCodes($productCodes);

    public function getProductsForUser($user, $venue);
}