<?php
namespace TopBetta;

class TopBettaUser extends \Eloquent {
    
	protected $table = 'tbdb_topbetta_user';	
		
    protected $guarded = array();

    public static $rules = array();
    
    static public function getTopBettaUserDetails($userID){
    	return TopBettaUser::where('user_id', '=', $userID)->get();
    }
    
}