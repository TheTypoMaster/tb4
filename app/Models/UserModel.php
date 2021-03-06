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
use Cartalyst\Sentry\Users\Eloquent\User as SentryUserModel;

class UserModel extends SentryUserModel implements AuthenticatableContract, CanResetPasswordContract{

	use Authenticatable, CanResetPassword;

    protected $table = 'tbdb_users';
    protected $guarded = array();
    protected $hidden = array('password', 'remember_token');

    protected $hashableAttributes = array();

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
		return $this->belongsTo('TopBetta\Models\AffiliatesModel', 'affiliate_id', 'affiliate_id');
	}

	public function campaigns()
	{
		return $this->belongsToMany('TopBetta\Models\CampaignModel', 'tb_campaign_users', 'campaign_id', 'user_id');
	}

	public function promotions()
	{
		return $this->belongsToMany('TopBetta\Models\PromotionsModel', 'tb_promotios_users', 'user_id', 'promotion_id');
	}

	public function accountBalance($transactionId = null)
	{
		$relation = $this->hasMany('TopBetta\Models\AccountTransactionModel', 'recipient_id');

        if ($transactionId) {
            $relation->where('id', '<=', $transactionId);
        }

        return $relation->sum('amount');
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
     * Returns the relationship between users and groups.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany('TopBetta\Models\AdminGroupModel', 'tb_admin_users_groups', 'user_id', 'group_id');
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

    public function freeCreditBalance()
    {
        return $this->hasMany('TopBetta\Models\FreeCreditTransactionModel', 'recipient_id')->sum('amount');
    }

    public function ewayTokens()
    {
        return $this->hasMany('TopBetta\Models\PaymentEwayTokens', 'user_id');
    }

    /**
     * Override Sentry Validation
     */
    public function validate()
    {}

    public function products()
    {
        return $this->belongsToMany('TopBetta\Models\BetProductModel', 'tb_user_products', 'user_id', 'bet_product_id');
    }


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