<?php
namespace TopBetta;

class PaymentEwayTokens extends \Eloquent {
    protected $guarded = array();
    protected $table = 'tb_payment_eway_tokens';
    public static $rules = array();
    
    static public function getEwayTokens($userID){
    	return PaymentEwayTokens::where('user_id', '=', $userID)->get();
    }
    
}