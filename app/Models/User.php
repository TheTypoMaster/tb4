<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Cartalyst\Sentry\Users\Eloquent\User implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tbdb_users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');
	
	/**
	 * Link our topbetta user to the standard user object :)
	 * 
	 * @return type
	 */
	public function topbettaUser() {
		return $this->hasOne('TopBetta\Models\TopBettaUser', 'user_id');
	}
	
	public function bets() {
		return $this->hasMany('TopBetta\Models\Bet');
	}
	
	public function accountTransactions() {
		return $this->hasMany('TopBetta\AccountBalance', 'recipient_id');
	}
	public function freeCreditTransactions() {
		return $this->hasMany('TopBetta\FreeCreditBalance', 'recipient_id');
	}

    public function accountBalance()
    {
        return $this->accountTransactions()->sum('amount');
    }

    public function depositLimit()
    {
        return $this->hasOne('TopBetta\Models\UserDepositLimitModel', 'user_id');
    }
	
	/**
	 * A User can have many tickets for tournaments
	 * @return mixed
	 */
	public function tournamentTickets()
	{
		return $this->hasMany('\TopBetta\Models\TournamentTicket', 'user_id');
	}

    /**
     * Returns the relationship between users and groups.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany('TopBetta\Models\AdminGroupModel', 'tb_admin_users_groups', 'group_id', 'user_id');
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

}