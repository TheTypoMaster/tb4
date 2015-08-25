<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 24/08/2015
 * Time: 1:54 PM
 */

namespace TopBetta\Services\Betting\BetProduct;

use App;
use TopBetta\Services\Betting\BetProduct\Exceptions\ProductNotAvailableException;
use TopBetta\Services\Products\ProductService;

class BetProductValidator {

    /**
     * @var ProductService
     */
    private $productService;

    private $userProducts;

    private $competition;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public static function make($competition)
    {
        $validator = App::make('TopBetta\Services\Betting\BetProduct\BetProductValidator');

        $validator->loadAuthUserCompetitionProducts($competition);

        return $validator;
    }

    public function loadAuthUserCompetitionProducts($competition)
    {
        $this->competition = $competition;

        $this->userProducts = $this->productService->getAuthUserProductsForCompetition($competition);

        return $this;
    }

    public function validateProduct($product, $betType)
    {
        if (! $this->userProducts->where('id', $product->id)->where('bet_type', $betType)->count()) {
            throw new ProductNotAvailableException($this->competition, "Product " . $product->name . " not available");
        }
    }
}