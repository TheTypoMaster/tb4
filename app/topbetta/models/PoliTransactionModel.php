<?php

namespace TopBetta\Models;

use Eloquent;
class PoliTransactionModel extends Eloquent {
	const 	STATUS_NOT_INTIALIZED 		= "Not Initialized",
			STATUS_INITIALIZED			= "Initialized",
			STATUS_FAILED_INITIALIZE 	= "Failed Initialization";

	protected $guarded = array();

	protected $table = "tb_poli_transaction";

	public static $rules = array();
}
