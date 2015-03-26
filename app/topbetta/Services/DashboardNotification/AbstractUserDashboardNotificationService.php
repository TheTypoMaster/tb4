<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 26/03/2015
 * Time: 11:24 AM
 */

namespace TopBetta\Services\DashboardNotification;


abstract class AbstractUserDashboardNotificationService extends AbstractDashboardNotificationService{

    public function formatPayload($user)
    {
        //get the users name
        if( array_get($user, 'topbettauser', null) ) {
            $firstName = array_get($user, 'topbettauser.first_name', null);
            $lastName = array_get($user, 'topbettauser.last_name', null);;
        } else {
            $names = explode(' ', array_get($user, 'name', null));
            $firstName = array_get($names, 0, "");
            $lastName = array_get($names, 1, "");
        }

        $payload = array(
            "user_username"         => array_get($user, 'username', null),
            "user_first_name"       => $firstName,
            "user_last_name"        => $lastName,
            "user_email"            => array_get($user, 'email', null),
            "transactions"          => array(),
        );

        return $payload;
    }
}