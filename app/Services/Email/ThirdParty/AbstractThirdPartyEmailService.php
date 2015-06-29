<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/03/2015
 * Time: 9:57 AM
 */

namespace TopBetta\Services\Email\ThirdParty;


abstract class AbstractThirdPartyEmailService {

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    abstract public function addAndUpdateContacts($contacts);

    abstract public function sendMessage($contactEmails, $message);

}