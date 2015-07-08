<?php

namespace TopBetta\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Description of BetResult
 *
 * @author mic
 */
class BetRepo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'betrepo';
    }
}
