<?php

namespace TopBetta\Models;

use Eloquent;

class BetLimitUserModel extends Eloquent {

    protected $table = 'tbdb_bet_limit_users';

	protected $guarded = array();

	public static $rules = array();

    public function limitType()
    {
        return $this->belongsTo('TopBetta\Models\BetLimitTypeModel', 'bet_limit_type_id');
    }

    public function user()
    {
        return $this->belongsTo('TopBetta\Models\UserModel', 'user_id');
    }
}
