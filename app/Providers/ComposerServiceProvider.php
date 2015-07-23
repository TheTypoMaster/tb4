<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 29/06/2015
 * Time: 12:05 PM
 */

namespace TopBetta\Providers;

use View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider {

    public function boot()
    {
        View::composer('admin.*', 'TopBetta\Services\Composers\NavigationComposer');
    }

    public function register(){}
}