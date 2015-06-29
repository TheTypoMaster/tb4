<?php namespace TopBetta\Services\Validation;
/**
 * Coded by Oliver Shanahan
 * File creation date: 31/12/14
 * File creation time: 19:42
 * Project: tb4
 */


class UserFullValidator extends Validator {


    /**
     * Base rules
     *
     * @var array
     */

    public $rules = array(
        'user_id' => 'numeric|unique:tbdb_topbetta_user',
        'title' => 'in:Mr,Mrs,Ms,Miss,Dr,Prof',
        'first_name' => 'alphanum|min:3',
        'last_name' => 'alphanum|min:3',
        'street' => 'max:100',
        'city' => 'max:50',
        'state' => 'max:50',
        'postcode' => 'max:6',
        'country' => 'alpha|max:50',
        'dob_day' => 'max:2',
        'dob_month' => 'max:2',
        'dob_year' => 'max:4',
        'msisdn' => 'numeric|max:15',
        'phone_number' => 'numeric|max:15',
        'promo_code' => 'alphadash|max:100',
        'heard_about' => 'alphadash|max:200',
        'heard_about_info' => 'alphadash|max:200',
        'marketing_opt_in_flag' => 'in:0,1',
        'identity_verified_flag' => 'in:0,1',
        'identity_doc' => 'alphanum|max:100',
        'identity_doc_id' => 'alphanum|max:100',
        'bsb_number' => 'alphadash|max:50',
        'account_name' => 'alphadash|max:100',
        'bank_name' => 'alphadash|max:100',
        'email_jackpot_reminder' => 'numeric',
        'source' => 'alphadash|max:100',
        'self_exclusion_date' => 'datetime',
        'bet_limit' => 'numeric',
        'requested_bet_limit' => 'numeric',
        'btag' => 'alpha_dash|max:100'
    );


    /**
     * Create rules
     *
     * @var array
     */
    protected $createRules = array(
        'user_id' => 'required',
        'first_name' => 'required',
        'last_name' => 'required',
        'source' => 'required',
        'title' => 'required',
        'dob_day' => 'required',
        'dob_month' => 'required',
        'dob_year' => 'required',
        'postcode' => 'required',
        'street' => 'required',
        'city' => 'required',
        'state' => 'required',
        'country' => 'required',
        'marketing_opt_in_flag' => 'required'
    );

    /**
     * Rules for updating a user
     *
     * @var array
     */
    protected $updateRules = array(
        'user_id' => 'required'

    );

}