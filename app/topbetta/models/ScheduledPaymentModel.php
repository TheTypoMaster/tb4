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
}