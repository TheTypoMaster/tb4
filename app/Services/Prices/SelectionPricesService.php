<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/08/2015
 * Time: 2:48 PM
 */

namespace TopBetta\Services\Prices;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionPriceRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;

class SelectionPricesService {

    /**
     * @var SelectionPriceRepositoryInterface
     */
    private $priceRepository;
    /**
     * @var BetProductRepositoryInterface
     */
    private $betProductRepository;
    /**
     * @var SelectionRepositoryInterface
     */
    private $selectionRepository;

    public function __construct(SelectionRepositoryInterface $selectionRepository, SelectionPriceRepositoryInterface $priceRepository, BetProductRepositoryInterface $betProductRepository)
    {
        $this->priceRepository = $priceRepository;
        $this->betProductRepository = $betProductRepository;
        $this->selectionRepository = $selectionRepository;
    }

    public function overridePrice($selectionId, $productCode, $amount, $betType, $manual = false)
    {
        $selection = $this->selectionRepository->getByExternalId($selectionId);

        $product = $this->betProductRepository->getProductByCode($productCode);

        $price = $this->priceRepository->getPriceForSelectionByProduct($selection->id, $product->id);

        if (!$price) {
            throw new ModelNotFoundException("Price not found");
        }

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