<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 21/09/2015
 * Time: 12:39 PM
 */

namespace TopBetta\Providers;

use Pusher;
use Config;
use Illuminate\Support\ServiceProvider;

class PusherServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('Pusher', function() {
            return new Pusher(Config::get('pusher.app_key'), Config::get('pusher.app_secret'), Config::get('pusher.app_id'));
        });
    }
}