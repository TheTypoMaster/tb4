<?php

namespace TopBetta\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Description of BetResultServiceProvider
 *
 * @author mic
 */
class BetResultRepoServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('betresultrepo', 'TopBetta\Repositories\BetResultRepo');
    }

}
