<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/08/2015
 * Time: 2:48 PM
 */

namespace TopBetta\Services\Prices;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use TopBetta\Jobs\Pusher\Racing\PriceSocketUpdate;
use TopBetta\Repositories\Cache\RacingSelectionPriceRepository;
use TopBetta\Repositories\Cache\RacingSelectionRepository;
use TopBetta\Repositories\Contracts\BetProductRepositoryInterface;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionPriceRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Resources\PriceResource;

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

    public function __construct(RacingSelectionRepository $selectionRepository, RacingSelectionPriceRepository $priceRepository, BetProductRepositoryInterface $betProductRepository)
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
            $price = $this->overrideWinPrice($price, $amount, $manual);
        } else if ($betType == BetTypeRepositoryInterface::TYPE_PLACE) {
            $price = $this->overridePlacePrice($price, $amount, $manual);
        }

        $this->selectionRepository->updatePricesForSelectionInRace($selection->id, $selection->market->event, $price);

        \Bus::dispatch(new PriceSocketUpdate(array("id" => $selection->market->event->id, "selections" => array("id" => $selection->id, "prices" => (new PriceResource($price))->toArray()))));

        return $price;
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