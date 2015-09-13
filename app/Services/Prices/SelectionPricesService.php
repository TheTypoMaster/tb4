<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/08/2015
 * Time: 2:48 PM
 */

namespace TopBetta\Services\Prices;


use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionPriceRepositoryInterface;

class SelectionPricesService {

    /**
     * @var SelectionPriceRepositoryInterface
     */
    private $priceRepository;
    /**
     * @var BetProductRepositoryInterface
     */
    private $betProductRepository;

    public function __construct(SelectionPriceRepositoryInterface $priceRepository, BetProductRepositoryInterface $betProductRepository)
    {
        $this->priceRepository = $priceRepository;
        $this->betProductRepository = $betProductRepository;
    }

    public function overridePrice($selectionId, $productCode, $amount, $betType, $manual = false)
    {
        $product = $this->betProductRepository->getProductByCode($productCode);

        $price = $this->priceRepository->getPriceForSelectionByProduct($selectionId, $product->id);

        if ($betType == BetTypeRepositoryInterface::TYPE_WIN) {
            return $this->overrideWinPrice($price, $amount, $manual);
        } else if ($betType == BetTypeRepositoryInterface::TYPE_PLACE) {
            return $this->overridePlacePrice($price, $amount, $manual);
        }

        throw new \InvalidArgumentException("Unkown bet type " . $betType);
    }

    public function overrideWinPrice($price, $amount, $manual = false)
    {
        return $this->priceRepository->updateWithId($price->id, array(
            "override_odds" => $amount,
            "override_type" => $manual ? SelectionPriceRepositoryInterface::OVERRIDE_TYPE_PROMO : SelectionPriceRepositoryInterface::OVERRIDE_TYPE_PRICE,
        ));
    }

    public function overridePlacePrice($price, $amount, $manual = false)
    {
        return $this->priceRepository->updateWithId($price->id, array(
            "override_place_odds" => $amount,
            "override_place_type" => $manual ? SelectionPriceRepositoryInterface::OVERRIDE_TYPE_PROMO : SelectionPriceRepositoryInterface::OVERRIDE_TYPE_PRICE,
        ));
    }
}