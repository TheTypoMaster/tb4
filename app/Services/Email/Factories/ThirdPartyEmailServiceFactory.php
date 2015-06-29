<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/03/2015
 * Time: 12:46 PM
 */

namespace TopBetta\Services\Email\Factories;

use Config;
use Toptippa\Services\Email\AbstractThirdPartyEmailService;

class ThirdPartyEmailServiceFactory {

    /**
     * Creates the third party email service given the name of a mail service config
     * @param $mailService
     * @return AbstractThirdPartyEmailService
     */
    public static function make($mailService)
    {
        $config = Config::get($mailService);

        $class = $config['class'];

        return new $class($config['data']);
    }
}