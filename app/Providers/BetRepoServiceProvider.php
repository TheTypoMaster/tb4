<?php

namespace TopBetta\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Description of BetResultServiceProvider
 *
 * @author mic
 */
class BetRepoServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('betrepo', 'TopBetta\Repositories\BetRepo');
    }

}
