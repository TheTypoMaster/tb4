<?php

namespace TopBetta\Models;

use Eloquent;

class UserDepositLimitModel extends Eloquent {

    protected $table = 'tb_user_deposit_limit';

	protected $guarded = array();

	public static $rules = array();

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value*100;
    }

    public function getAmountAttribute($value)
    {
        return $value/100;
    }
}
