<?php namespace TopBetta\Models;

use Eloquent;

class TournamentTicketModel extends Eloquent {

    protected $guarded = array();
    public static $rules = array();
    protected $table = 'tbdb_tournament_ticket';

    public function user()
    {
        return $this->belongsTo('TopBetta\Models\UserModel', 'user_id');
    }

    public function buyinTransaction()
    {
        return $this->belongsTo('TopBetta\Models\AccountTransactionModel', 'buy_in_transaction_id')
            ->with('transactionType');
    }

    public function entryFeeTransaction()
    {
        return $this->belongsTo('TopBetta\Models\AccountTransactionModel', 'entry_fee_transaction_id')
            ->with('transactionType');
    }

    public function resultTransaction()
    {
        return $this->belongsTo('TopBetta\Models\ACcountTransactionModel', 'result_transaction_id')
            ->with('transactionType');
    }

}
