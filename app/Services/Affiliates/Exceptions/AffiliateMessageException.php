<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 7/08/2015
 * Time: 9:55 AM
 */

namespace TopBetta\Services\Affiliates\Exceptions;


class AffiliateMessageException extends \Exception {

    /**
     * @var string
     */
    private $affiliateMessage;
    /**
     * @var int
     */
    private $response;

    /**
     * @param string $affiliateMessage
     * @param string $response
     * @param string $message
     */
    public function __construct($affiliateMessage, $response, $message)
    {
        parent::__construct($message);
        $this->affiliateMessage = $affiliateMessage;
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getAffiliateMessage()
    {
        return $this->affiliateMessage;
    }

    /**
     * @return int
     */
    public function getResponse()
    {
        return $this->response;
    }
}