<?php namespace TopBetta\Models;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 19:16
 * Project: tb4
 */

use Eloquent;

class SelectionResultModel extends Eloquent {

    protected $table = 'tbdb_selection_result';
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