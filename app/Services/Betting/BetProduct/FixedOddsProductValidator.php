<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/10/2015
 * Time: 9:47 AM
 */

namespace TopBetta\Services\Betting\BetProduct;

use TopBetta\Services\Betting\BetProduct\Exceptions\ProductNotAvailableException;

class FixedOddsProductValidator extends BetProductValidator {

    private $race;

    public static function make($competition)
    {
        $validator = \App::make('TopBetta\Services\Betting\BetProduct\FixedOddsProductValidator');

        $validator->loadProducts($competition);

        return $validator;
    }

    /**
     * @return mixed
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * @param mixed $race
     * @return $this
     */
    public function setRace($race)
    {
        $this->race = $race;
        return $this;
    }


    public function validateProduct($product, $betType)
    {
        parent::validateProduct($product, $betType);

        if (!$this->competition->fixed_odds_enabled && $product->is_fixed_odds) {
            throw new ProductNotAvailableException($this->competition, "Fixed odds not available on this meeting");
        }

        if (!$this->getRace()->fixed_odds_enabled && $product->is_fixed_odds) {
            throw new ProductNotAvailableException($this->competition, "Fixed odds not available on this race");
        }
    }
}