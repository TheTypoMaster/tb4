<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 2:22 PM
 */

namespace TopBetta\Services\Accounting\Gateways;


use Omnipay\Eway\RapidDirectGateway;

class EwayRapidDirectGateway extends RapidDirectGateway {

    /**
     * Get card details request
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function getCardDetails($parameters = array())
    {
        return $this->createRequest('TopBetta\Services\Accounting\Gateways\Message\RapidDirectGetCardRequest', $parameters);
    }
}