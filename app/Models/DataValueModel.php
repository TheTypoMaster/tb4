<?php namespace TopBetta\Models; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 3/04/15
 * File creation time: 19:23
 * Project: tb4
 */

use Eloquent;

class DataValueModel extends Eloquent{

	protected $table = 'tb_data_values';
	protected $guarded = array();
	public static $rules = array();

}