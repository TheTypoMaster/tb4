<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 10:25
 * Project: tb4
 */

use Eloquent;
use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;

class BetModel extends Eloquent {

    protected $table = 'tbdb_bet';
    protected $guarded = array();


    /*
     * Relationships
     */

    public function betselection()
    {
        return $this->hasMany('TopBetta\Models\BetSelectionModel', 'bet_id', 'id');
    }

    public function selection()
    {
        return $this->belongsToMany('TopBetta\Models\SelectionModel', 'tbdb_bet_selection', 'bet_id', 'selection_id');
    }

    public function user()
    {
        return $this->belongsTo('TopBetta\Models\UserModel', 'user_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo('TopBetta\Models\BetTypeModel', 'bet_type_id');
    }

    public function status()
    {
        return $this->belongsTo('TopBetta\Models\BetResultStatusModel', 'bet_result_status_id');
    }

    public function source()
    {
        return $this->belongsTo('TopBetta\Models\BetSourceModel', 'bet_source_id');
    }

    public function product()
    {
        return $this->belongsTo('TopBetta\Models\BetProductModel', 'bet_product_id');
    }

    public function result()
    {
        return $this->belongsTo('TopBetta\Models\AccountTransactionModel', 'result_transaction_id');
    }

    public function betTransaction()
    {
        return $this->belongsTo('TopBetta\Models\AccountTransactionModel', 'bet_transaction_id');
    }

    public function refundTransaction()
	{
        return $this->belongsTo('TopBetta\Models\AccountTransactionModel', 'refund_transaction_id');
    }
    
    public function refund()
    {
        return $this->belongsTo('TopBetta\Models\AccountTransactionModel', 'refund_transaction_id');
    }

    public function event()
    {
        return $this->belongsTo('TopBetta\Models\EventModel', 'event_id');
    }



}