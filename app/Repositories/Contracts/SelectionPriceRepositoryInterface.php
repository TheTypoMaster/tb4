<?php namespace TopBetta\Repositories\Contracts;
/**
 * Coded by Oliver Shanahan
 * File creation date: 7/4/15
 * File creation time: 13:35
 * Project: tb4
 */

interface SelectionPriceRepositoryInterface {

    /**
     * Create or update price based on selection and bet product
     * @param array $priceData
     * @return mixed
     */
    public function updateOrCreatePrice(array $priceData);

    /**
     * Gets price record by selection id and bet product id
     * @param $selection
     * @param $product
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function getPriceForSelectionByProduct($selection, $product);
}