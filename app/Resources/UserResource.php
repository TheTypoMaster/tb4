<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/08/2015
 * Time: 10:14 AM
 */

namespace TopBetta\Resources;


class UserResource extends AbstractEloquentResource
{

    protected $attributes = array(
        "id"                => "id",
        "username"          => "username",
        "email"             => "email",
        "isTopBetta"        => "isTopBetta",
        "activated"         => "activated_flag",
        "block"             => "block",
        "name"              => "name",
        "firstName"         => "topbettauser.first_name",
        "lastName"          => "topbettauser.last_name",
        "street"            => "topbettauser.street",
        "city"              => "topbettauser.city",
        "state"             => "topbettauser.state",
        "postcode"          => "topbettauser.postcode",
        "country"           => "topbettauser.country",
        "dob"               => "dob",
        "msisdn"            => "topbettauser.msisdn",
        "verified"          => "topbettauser.identity_verified_flag",
        "betLimit"          => "bet_limit",
        "accountBalance"    => "accountBalance",
        "freeCreditBalance" => "freeCreditBalance",
        "balanceToTurnover" => "balance_to_turnover",
    );

    protected $types = array(
        "id"                => "int",
        "activated"         => "bool",
        "isTopBetta"        => "bool",
        "verified"          => "bool",
        "block"             => "bool",
        "freeCreditBalance" => "int",
        "accountBalance"    => "int",
        "balanceToTurnover" => "int",
    );

    private $accountBalance = null;

    private $freeCreditBalance = null;

    public function getDob()
    {
        return $this->model->topbettauser->dob_year . '-' .
            $this->model->topbettauser->dob_month . '-' .
            $this->model->topbettauser->dob_day;
    }

    public function getAccountBalance()
    {
        if (is_null($this->accountBalance)) {
            $this->accountBalance = $this->model->accountBalance();
        }
        
        return $this->accountBalance;
    }

    public function getFreeCreditBalance()
    {
        if (is_null($this->freeCreditBalance)) {
            $this->freeCreditBalance = $this->model->freeCreditBalance();
        }

        return $this->freeCreditBalance;
    }

}