<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/03/2015
 * Time: 3:00 PM
 */

namespace TopBetta\Services\DashboardNotification;


use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;

class UserDashboardNotificationService extends AbstractDashboardNotificationService {

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var AccountTransactionService
     */
    private $accountTransactionService;

    public function __construct(UserRepositoryInterface $userRepository, AccountTransactionService $accountTransactionService)
    {
        $this->userRepository = $userRepository;
        $this->accountTransactionService = $accountTransactionService;
    }

    public function getEndpoint()
    {
        return "";
    }

    public function getHttpMethod()
    {
        return "POST";
    }

    public function formatPayload($data)
    {
        //get the user
        $user = $this->userRepository->find($data['id']);

        //get the users name
        if( $user->topbettauser ) {
            $firstName = $user->topbettauser->first_name;
            $lastName = $user->topbettauser->last_name;
        } else {
            $names = explode(' ', $user->name);
            $firstName = $names[0];
            $lastName = array_get($names, 1, "");
        }

        //create payload
        $payload = array(
            "user_username"         => $user->username,
            "user_first_name"       => $firstName,
            "user_last_name"        => $lastName,
            "user_email"            => $user->email,
            "user_street"           => $user->topbettauser->street,
            "user_city"             => $user->topbettauser->city,
            "user_state"            => $user->topbettauser->state,
            "user_postcode"         => $user->topbettauser->postcode,
            "user_country"          => $user->topbettauser->country,
            "user_dob"              => $user->topbettauser->dob_year . "-" . $user->topbettauser->dob_month . "-" . $user->topbettauser->dob_day,
            "user_marketing_opt_in" => (bool)$user->topbettauser->marketing_opt_in_flag,
            "user_source"           => $user->topbettauser->source,
            "user_is_corporate"     => (bool)$user->isCorporate,
            "user_is_topbetta"      => (bool)$user->isTopBetta,
            "user_block"            => (bool)$user->block,
            "user_activation"       => $user->activation,
            "user_btag"             => $user->topbettauser->btag,
        );

        return $payload;
    }
}