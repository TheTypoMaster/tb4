<?php

namespace TopBetta\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Description of BetLimitRepoServiceProvider
 *
 * @author mic
 */
class BetLimitRepoServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('betlimitrepo', 'TopBetta\Repositories\BetLimitRepo');
    }

}
