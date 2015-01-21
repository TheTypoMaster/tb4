<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 20/01/15
 * File creation time: 10:25
 * Project: tb4
 */

use Eloquent;

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

//    public function selection()
//    {
//        return $this->hasManyThrough('TopBetta\Models\SelectionModel', 'TopBetta\Models\BetSelectionModel', 'selection_id', 'id');
//    }

    public function user() {
        return $this->belongsTo('TopBetta\Models\UserModel', 'user_id', 'id');
    }

    public function bettype() {
        return $this->belongsTo('TopBetta\Models\BetTypeModel', 'bet_type_id');
    }

    public function status() {
        return $this->belongsTo('TopBetta\Models\BetResultStatusModel', 'bet_result_status_id');
    }

}