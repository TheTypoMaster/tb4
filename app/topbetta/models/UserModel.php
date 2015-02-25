<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 23/12/14
 * File creation time: 17:06
 * Project: tb4
 */

use Eloquent;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserModel extends Eloquent implements UserInterface, RemindableInterface {

    protected $table = 'tbdb_users';
    protected $guarded = array();
    protected $hidden = array('password', 'remember_token');

    public function topbettauser() {
        return $this->hasOne('TopBetta\Models\TopBettaUserModel', 'user_id');
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