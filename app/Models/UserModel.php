<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:06
 * Project: tb4
 */

use Eloquent;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;


class UserModel extends Eloquent implements AuthenticatableContract, CanResetPasswordContract{

	use Authenticatable, CanResetPassword;

    protected $table = 'tbdb_users';
    protected $guarded = array();
    protected $hidden = array('password', 'remember_token');

	/*
	 * Relationships
	 */

    public function topbettauser() {
        return $this->hasOne('TopBetta\Models\TopBettaUserModel', 'user_id');
    }

    public function accountTransactions() {
        return $this->hasMany('TopBetta\Models\AccountTransactionModel', 'recipient_id');
    }

	public function affiliate()
	{
		return $this->belongsTo('TopBetta\Models\AffiliatesModel', 'user_affiliate_id', 'affiliate_id');
	}

	public function campaigns()
	{
		return $this->belongsToMany('TopBetta\Models\CampaignModel', 'tb_campaign_users', 'campaign_id', 'user_id');
	}

	public function promotions()
	{
		return $this->belongsToMany('TopBetta\Models\PromotionsModel', 'tb_promotios_users', 'user_id', 'promotion_id');
	}

	public function accountBalance()
	{
		return $this->hasMany('TopBetta\Models\AccountTransactionModel', 'recipient_id')->sum('amount');
	}

    public function depositLimit() {
        return $this->hasOne('TopBetta\Models\UserDepositLimitModel', 'user_id');
    }

	/**
	 * A User can have many tickets for tournaments
	 * @return mixed
	 */
	public function tournamentTickets() {
		return $this->hasMany('\TopBetta\Models\TournamentTicket', 'user_id');
	}

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    // --- Accessors for urlencoded ' in user's names ---
    public function getNameAttribute($value) {
        return str_replace("\\", "", urldecode($value));
    }

    public function freeCreditTransactions()
    {
        return $this->hasMany('TopBetta\Models\FreeCreditTransactionModel', 'recipient_id');
    }
    
	/**
	 * @param $relationship
	 * @param $closure
	 * @return mixed
	 */
    public function whereNotInRelationship($relationship, $closure)
    {
        $relationship = $this->$relationship();

        if(method_exists($relationship, 'getOtherKey')) {
            $localField = $relationship->getForeignKey();
            $relationshipField = $relationship->getQualifiedOtherKeyName();
        } else {
            $localField = $relationship->getQualifiedParentKeyName();
            $relationshipField = $relationship->getForeignKey();
        }

        return $this->whereNotIn($localField, function ($q) use ($relationship, $relationshipField, $closure) {

            $q  ->select($relationshipField)
                ->from($relationship->getModel()->getTable())
                ->where($closure);
        });
    }


}