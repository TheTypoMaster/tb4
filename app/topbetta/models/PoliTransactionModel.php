<?php

namespace TopBetta\Models;

use Eloquent;
class PoliTransactionModel extends Eloquent {
	protected $guarded = array();

	protected $table = "tb_poli_transaction";

	public static $rules = array();
}
