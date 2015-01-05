<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/01/15
 * File creation time: 21:15
 * Project: tb4
 */

use Eloquent;


class TopbettaUserModel extends Eloquent {

    protected $table = 'tbdb_topbetta_user';
    protected $guarded = array();


    static public function getTopBettaUserDetails($userID){
        return TopBettaUser::where('user_id', '=', $userID)->get();
    }

}