<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/08/2015
 * Time: 9:39 AM
 */

namespace TopBetta\Services\Affiliates\Messaging;


interface AffiliateMessenger {

    public function send($affiliateEndpoint, $message, $method = "POST");

}