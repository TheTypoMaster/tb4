<?php

namespace TopBetta\Models;

use Eloquent;

class SelectionStatusModel extends Eloquent {

	protected $table = "tbdb_selection_status";

	protected $guarded = array();

	public static $rules = array();
}
