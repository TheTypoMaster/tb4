<?php

namespace TopBetta\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Description of BetResultServiceProvider
 *
 * @author mic
 */
class BetResultServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('betresult', 'TopBetta\Repositories\BetResult');
    }

}
