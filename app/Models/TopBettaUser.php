<?php
namespace TopBetta\Models;

class TopBettaUser extends \Eloquent {
    
	protected $table = 'tbdb_topbetta_user';	
		
    protected $guarded = array();

    public static $rules = array();

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