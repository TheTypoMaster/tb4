<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 26/06/2015
 * Time: 11:08 AM
 */

namespace TopBetta\Services\Email;

use Config;
use TopBetta\Services\Email\Factories\ThirdPartyEmailServiceFactory;

class Vision6EmailService implements ThirdPartyEmailServiceInterface {

    const THIRD_PARTY_MAILER = 'vision6';

    /**
     * @var \TopBetta\Services\Email\ThirdParty\AbstractThirdPartyEmailService
     */
    private $mailer;

    public function __construct()
    {
        $this->mailer = ThirdPartyEmailServiceFactory::make(self::THIRD_PARTY_MAILER);
    }

    public function addUserToContacts($user)
    {
        $contact = $this->formatUserAsContact($user);

        return $this->mailer->addAndUpdateContacts(array($contact));
    }

    private function formatUserAsContact($user)
    {
        $fields = Config::get(self::THIRD_PARTY_MAILER . '.data.fields');

        return array(
            $fields['email'] => $user->email,
            $fields['first_name'] => $user->topbettauser->first_name,
            $fields['last_name'] => $user->topbettauser->last_name,
            $fields['mobile'] => $user->topbettauser->msisdn,
        );
    }
}