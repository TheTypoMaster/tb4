<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 25/03/2015
 * Time: 3:00 PM
 */

namespace TopBetta\Services\DashboardNotification;


use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;

class UserDashboardNotificationService extends AbstractTransactionDashboardNotificationService {

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var AccountTransactionRepositoryInterface
     */
    private $transactionRepository;

    public function __construct(UserRepositoryInterface $userRepository, AccountTransactionRepositoryInterface $transactionRepository)
    {
        $this->userRepository = $userRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function getEndpoint()
    {
        return "users";
    }

    public function getHttpMethod()
    {
        return "POST";
    }

    public function formatPayload($data)
    {
        //check the id exists
        if( ! $data['id'] ) {
            \Log::error("No user id specidfied in UserDashboardNotificationService");
            return false;
        }

        //get the user
        $user = $this->userRepository->find($data['id']);

        $payload = $this->formatUser($user);

        //add any account transactions
        $payload['transactions'] = array();

        if( $transactions = array_get($data, 'transactions', false) ) {
            foreach($transactions as $transactionId) {
                //get the transaction
                $transaction = $this->transactionRepository->getTransactionWithUsers($transactionId);

                $transactionPayload = $this->formatTransaction($transaction);

                $transactionPayload['users'] = count(array_get($transaction, 'giver', array())) ? array($this->formatUser(array_get($transaction, 'giver', array()))) : array();

                //add the transaction
                $payload['transactions'][] = $transactionPayload;
            }

        }

        return $payload;
    }

    private function formatUser($user) {

        if( ! count($user) ) {
            return array();
        }

        //get the users name
        if( array_get($user, 'topbettauser', null) ) {
            $firstName = array_get($user, 'topbettauser.first_name', null);
            $lastName = array_get($user, 'topbettauser.last_name', null);;
        } else {
            $names = explode(' ', array_get($user, 'name', null));
            $firstName = array_get($names, 0, "");
            $lastName = array_get($names, 1, "");
        }

        //create payload
        return array(
            "user_username"         => array_get($user, 'username', null),
            "user_first_name"       => $firstName,
            "user_last_name"        => $lastName,
            "user_email"            => array_get($user, 'email', null),
            "user_street"           => array_get($user, 'topbettauser.street', null),
            "user_city"             => array_get($user, 'topbettauser.city', null),
            "user_state"            => array_get($user, 'topbettauser.state', null),
            "user_postcode"         => array_get($user, 'topbettauser.postcode', null),
            "user_country"          => array_get($user, 'topbettauser.country', null),
            "user_dob"              => array_get($user, 'topbettauser.dob_year', null) . "-" . array_get($user, 'topbettauser.dob_month', null) . "-" . array_get($user, 'topbettauser.dob_day', null),
            "user_marketing_opt_in" => (bool) array_get($user, 'topbettauser.marketing_opt_in_flag', null),
            "user_source"           => array_get($user, 'topbettauser.source', null),
            "user_is_corporate"     => (bool) array_get($user, 'isCorporate', null),
            "user_is_topbetta"      => (bool) array_get($user, 'isTopBetta', null),
            "user_block"            => (bool) array_get($user, 'block', null),
            "user_activated"        => (bool) array_get($user, 'activated_flag'),
            "user_btag"             => array_get($user, 'topbettauser.btag', null),
        );
    }
}