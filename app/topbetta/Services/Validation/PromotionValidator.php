<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/03/2015
 * Time: 11:57 AM
 */

namespace TopBetta\Services\Validation;


class PromotionValidator extends Validator {

    /**
     * Base rules
     *
     * @var array
     */

    public $rules = array(
        'pro_value'         => 'integer',
        'pro_description'   => 'alphanum',
        'pro_use_once_flag' => 'in:0,1',
        'pro_start_date'    => 'date',
        'pro_end_date'      => 'date|after:pro_start_date',
        'pro_status'        => 'in:0,1',
    );

    protected $createRules = array(
        'pro_code'         => 'alphanum|unique:tbdb_promotions',
        "pro_entered_by"   => 'exists:tbdb_users,id',
        "pro_entered_date" => 'date',
    );

    protected $updateRules = array(
        'pro_code'          => 'alphanum',
    );

}