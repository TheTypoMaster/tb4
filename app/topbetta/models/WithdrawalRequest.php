<?php
namespace TopBetta;

class WithdrawalRequest extends \Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_withdrawal_request';

	public function user() {
		return $this->belongsTo('User', 'requester_id');
	}
	
	public function type() {
		return $this->belongsTo('TopBetta\WithdrawalType', 'withdrawal_type_id');
	}
	
	public function paypal() {
		return $this->hasOne('TopBetta\WithdrawalPaypal', 'withdrawal_request_id');
	}	

	public function moneybookers() {
		return $this->hasOne('TopBetta\WithdrawalMoneybookers', 'withdrawal_request_id');
	}	
}
