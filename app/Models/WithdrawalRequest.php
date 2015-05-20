<?php
namespace TopBetta\Models;

class WithdrawalRequest extends \Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_withdrawal_request';
    public $timestamps = false;

	public function user() {
		return $this->belongsTo('TopBetta\Models\UserModel', 'requester_id');
	}
	
	public function type() {
		return $this->belongsTo('TopBetta\Models\WithdrawalType', 'withdrawal_type_id');
	}
	
	public function paypal() {
		return $this->hasOne('TopBetta\Models\WithdrawalPaypal', 'withdrawal_request_id');
	}	

	public function moneybookers() {
		return $this->hasOne('TopBetta\Models\WithdrawalMoneybookers', 'withdrawal_request_id');
	}	
}
