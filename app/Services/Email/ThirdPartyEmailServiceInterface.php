<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 26/06/2015
 * Time: 11:11 AM
 */

namespace TopBetta\Services\Email;


interface ThirdPartyEmailServiceInterface {

    public function addUserToContacts($user);
}