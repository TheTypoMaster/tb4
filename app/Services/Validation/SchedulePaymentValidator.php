<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/06/2015
 * Time: 10:22 AM
 */

namespace TopBetta\Services\Validation;


class SchedulePaymentValidator extends Validator {

    public $rules = array();

    protected $pcreateRules = array(
        'amount' => 'required|Integer|Min:1000',
        'recurring_period' => 'in:weekly,fortnightly,daily,monthly'
    );
}