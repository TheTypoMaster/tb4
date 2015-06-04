<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 2:24 PM
 */

namespace TopBetta\Services\Accounting\Gateways\Message;


use Omnipay\Eway\Message\RapidDirectAbstractRequest;
use Omnipay\Eway\Message\RapidResponse;

class RapidDirectGetCardRequest extends RapidDirectAbstractRequest {

    public function getEndPoint()
    {
        return $this->getEndpointBase() . '/Customer/' . $this->getCardReference();
    }

    public function getData()
    {
        return $this->getBaseData();
    }

    public function sendData($data)
    {
        $httpResponse = $this->httpClient->get($this->getEndPoint())
            ->setAuth($this->getApiKey(), $this->getPassword())
            ->send();

        return $this->response = new RapidResponse($this, $httpResponse->json());
    }
}