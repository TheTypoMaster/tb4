<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/08/2015
 * Time: 9:57 AM
 */

namespace TopBetta\Services\Affiliates\Exceptions;


class AffiliateResponseException extends \Exception {

    private $response;

    public function __construct($response, $message)
    {
        parent::__construct($message);
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }
}