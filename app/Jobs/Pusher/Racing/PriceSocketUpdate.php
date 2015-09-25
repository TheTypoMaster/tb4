<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 2:58 PM
 */

namespace TopBetta\Jobs\Pusher\Racing;

use Log;

use TopBetta\Resources\PriceResource;
use TopBetta\Resources\SelectionResource;

class PriceSocketUpdate extends RaceSocketUpdate {

    const TUBE_PREFIX = 'racing-price-socket-';

    protected $event = 'odds_update';

    public $queue = '';

    public function __construct($data)
    {
        // Log::debug('### '. print_r($data,true));
        $this->queue = self::TUBE_PREFIX . $data['product'];
        parent::__construct($data);
    }

}