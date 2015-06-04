<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 10:32 AM
 */

namespace TopBetta\Services\Validation;


class DepositValidator extends Validator {

    public $rules = array(
        'amount' => 'required|Integer|Min:1000'
    );
}