<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/08/2015
 * Time: 10:43 AM
 */

namespace TopBetta\Services\Validation;


class TournamentUserValidator extends Validator {

    public $rules = array(
        'username' => 'alphadash|min:6|unique:tbdb_users|regex:(.*[a-zA-Z].*)',
        'external_user_id' => 'alphadash|min:1',
        'affiliate_id' => 'numeric',
    );

    public $createRules = array(
        'username' => 'required',
        'external_user_id' => 'required',
        'affiliate_id' => 'required',
    );

    public $updateRules = array();

}