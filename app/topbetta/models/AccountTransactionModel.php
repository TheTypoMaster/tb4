<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 5/01/15
 * File creation time: 10:24
 * Project: tb4
 */

use Eloquent;

class AccountTransactionModel extends Eloquent {

    protected $table = 'tbdb_account_transaction';
    protected $guarded = array();

    /*
    * relationships
    */

    public function transactionType() {
        return $this->belongsTo('TopBetta\AccountTransactionTypes', 'account_transaction_type_id');
    }

}