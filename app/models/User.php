<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

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
		return $this->hasOne('TopBetta\TopBettaUser', 'user_id');
	}
	
	public function bets() {
		return $this->hasMany('TopBetta\Bet');
	}
	
	public function accountTransactions() {
		return $this->hasMany('TopBetta\AccountBalance', 'recipient_id');
	}
	public function freeCreditTransactions() {
		return $this->hasMany('TopBetta\FreeCreditBalance', 'recipient_id');
	}
	
	/**
	 * A User can have many tickets for tournaments
	 * @return mixed
	 */
	public function tournamentTickets() {
		return $this->hasMany('\TopBetta\TournamentTicket', 'user_id');
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

}