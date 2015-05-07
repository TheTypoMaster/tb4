<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/01/15
 * File creation time: 21:15
 * Project: tb4
 */

use Eloquent;


class TopBettaUserModel extends Eloquent {

    protected $table = 'tbdb_topbetta_user';
    protected $guarded = array();


    // --- Accessors for urlencoded ' in user's names amd remove \ ---
    public function getFirstNameAttribute($value) {
        return str_replace("\\", "", urldecode($value));
    }

    public function getLastNameAttribute($value) {
        return str_replace("\\", "", urldecode($value));
    }

    static public function getTopBettaUserDetails($userID){
        return TopBettaUser::where('user_id', '=', $userID)->get();
    }

}