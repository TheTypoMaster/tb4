<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 2:58 PM
 */

namespace TopBetta\Jobs\Pusher\Racing;


use TopBetta\Resources\PriceResource;
use TopBetta\Resources\SelectionResource;

class PriceSocketUpdate extends RaceSocketUpdate {

    protected $event = 'odds_update';

    public $queue = 'racing-price-socket';

}