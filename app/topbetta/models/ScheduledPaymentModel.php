<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 11:05 AM
 */

namespace TopBetta\Models;

use Eloquent;

class ScheduledPaymentModel extends Eloquent {

    protected $guarded = array();

    protected $table = 'tb_scheduled_payments';

    public function paymentToken()
    {
        return $this->morphTo();
    }

    public function source()
    {
        return $this->belongsTo('TopBetta\Models\BetSourceModel', 'source_id');
    }

    public function user()
    {
        return $this->belongsTo('TopBetta\Models\UserModel', 'user_id');
    }
}