<?php

namespace TopBetta\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Description of BetLimitRepo
 *
 * @author mic
 */
class BetLimitRepo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'betlimitrepo';
    }
}
