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

    /**
     * Get the eway endpoint for card details
     * @return string
     */
    public function getEndPoint()
    {
        return $this->getEndpointBase() . '/Customer/' . $this->getCardReference();
    }

    /**
     * @inherit
     */
    public function getData()
    {
        return $this->getBaseData();
    }

    /**
     * Send data to eway and return response
     * @param mixed $data
     * @return RapidResponse
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->get($this->getEndPoint())
            ->setAuth($this->getApiKey(), $this->getPassword())
            ->send();

        return $this->response = new RapidResponse($this, $httpResponse->json());
    }
}