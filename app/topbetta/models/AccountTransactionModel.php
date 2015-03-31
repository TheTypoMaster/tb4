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

    // --- Bets assosciated with the transaction if they exist ---

    public function bet()
    {
        return $this->hasOne('TopBetta\\Models\\BetModel', 'bet_transaction_id');
    }

    public function betWin()
    {
        return $this->hasOne('TopBetta\\Models\\BetModel', 'result_transaction_id');
    }

    public function betRefund()
    {
        return $this->hasOne('TopBetta\\Models\\BetModel', 'refund_transaction_id');
    }

    public function giver()
    {
        return $this->belongsTo('TopBetta\Models\UserModel', 'giver_id');
    }

    public function recipient()
    {
        return $this->belongsTo('TopBetta\Models\UserModel', 'recipient_id');
    }

}