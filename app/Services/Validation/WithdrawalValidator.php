<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 11:58 AM
 */

namespace TopBetta\Services\Validation;


class WithdrawalValidator extends Validator {

    public $rules = array(
        'amount' => 'required|numeric'
    );

    public $createRules = array();

    public $updateRules = array();
}