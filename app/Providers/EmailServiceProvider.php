<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 26/06/2015
 * Time: 12:30 PM
 */

namespace TopBetta\Providers;


use Illuminate\Support\ServiceProvider;

class EmailServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind(
            'TopBetta\Services\Email\ThirdPartyEmailServiceInterface',
            'TopBetta\Services\Email\Vision6EmailService'
        );
    }
}