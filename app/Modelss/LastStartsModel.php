<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 5/04/15
 * File creation time: 23:58
 * Project: tb4
 */

use Eloquent;

class LastStartsModel extends Eloquent{
	protected $guarded = array();
	public static $rules = array();
	protected $table = 'tb_data_risa_runner_form_last_starts';

}