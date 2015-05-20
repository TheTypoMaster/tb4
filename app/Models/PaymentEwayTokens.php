<?php
namespace TopBetta\Models;

class PaymentEwayTokens extends \Eloquent {
    protected $guarded = array();
    protected $table = 'tb_payment_eway_tokens';
    public static $rules = array();
    
    static public function getEwayTokens($userID){
    	return PaymentEwayTokens::where('user_id', '=', $userID)->get();
    }
    
    static public function checkTokenExists($userID, $managedId){
    	return PaymentEwayTokens::where('user_id', '=', $userID)
    							->where('cc_token' , '=', $managedId)->pluck('id');
    }
    
    
}