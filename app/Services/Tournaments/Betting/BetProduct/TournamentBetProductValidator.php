<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 31/08/2015
 * Time: 10:58 AM
 */

namespace TopBetta\Services\Tournaments\Betting\BetProduct;

use App;
use TopBetta\Services\Betting\BetProduct\BetProductValidator;

class TournamentBetProductValidator extends BetProductValidator {

    public static function make($competition)
    {
        $validator = App::make('TopBetta\Services\Tournaments\Betting\BetProduct\TournamentBetProductValidator');

        $validator->loadProducts($competition);

        return $validator;
    }

    public function loadProducts($competition)
    {
        $this->competition = $competition;

        $this->userProducts = $this->competition->products;

        return $this;
    }
}