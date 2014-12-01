<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 21/11/14
 * File creation time: 11:09
 * Project: tb4
 */

use Eloquent;

class SelectionPricesModel extends Eloquent{

    protected $table = 'tbdb_selection_price';
    protected $guarded = array();
    public static $rules = array();

    /*
     * Relationships
     */
    public function selection()
    {
        return $this->belongsTo('TopBetta\Models\SelectionsModel', 'selection_id', 'id');
    }

}