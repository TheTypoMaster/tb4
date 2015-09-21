<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/08/2015
 * Time: 10:14 AM
 */

namespace TopBetta\Resources;


use Illuminate\Contracts\Auth\Authenticatable;

class UserResource extends AbstractEloquentResource implements Authenticatable
{
    protected static $modelClass = 'TopBetta\Models\UserModel';

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

    public static function createResourceFromArray($array, $resource = null)
    {
        $resource = parent::createResourceFromArray($array, $resource);

        if ($balance = $resource->getModel()->account_balance) {
            $resource->setAccountBalance($balance);
        }

        if ($balance = $resource->getModel()->free_credit_balance) {
            $resource->setFreeCreditBalance($balance);
        }

        return $resource;
    }

    public function getDob()
    {
        if ($this->model->dob) {
            return $this->model->dob;
        }

        if (!$this->model->topbettauser) {
            return null;
        }

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

    public function addAccountBalance($amount)
    {
        $this->accountBalance = $this->getAccountBalance() + $amount;
        return $this;
    }

    public function addFreeCreditBalance($amount)
    {
        $this->freeCreditBalance = $this->getFreeCreditBalance() + $amount;
        return $this;
    }

    /**
     * @param null $accountBalance
     * @return $this
     */
    public function setAccountBalance($accountBalance)
    {
        $this->accountBalance = $accountBalance;
        return $this;
    }

    /**
     * @param null $freeCreditBalance
     * @return $this
     */
    public function setFreeCreditBalance($freeCreditBalance)
    {
        $this->freeCreditBalance = $freeCreditBalance;
        return $this;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->model->getAuthIdentifier();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->model->getAuthPassword();
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->model->getReminderEmail();
    }

    public function getRememberToken()
    {
        return $this->model->getRememberToken();
    }

    public function setRememberToken($value)
    {
        $this->model->setRememberToken($value);
    }

    public function getRememberTokenName()
    {
        return $this->model->getRememberTokenName();
    }

    public function topbettauser()
    {
        return $this->model->topbettauser;
    }
}