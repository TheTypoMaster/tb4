<?php

namespace TopBetta\Models;

use Eloquent;
class PoliTransactionModel extends Eloquent {

	const 	STATUS_NOT_INTIATED						= "Not Initiated",
			STATUS_INITIATED						= "Initiated",
			STATUS_FAILED_INITIATED 				= "Failed Initialization",
			STATUS_FINANCIAL_INSTITUTION_SELECETED 	= "FinancialInstitutionSelected",
			STATUS_EULA_ACCEPTED					= "EULAAccepted",
			STATUS_IN_PROCESS						= "InProcess",
			STATUS_UNKNOWN							= "Unknown",
			STATUS_RECEIPT_UNVERIFIED				= "ReceiptUnverified",
			STATUS_COMPLETED						= "Completed",
			STATUS_INCOMPATIBLE						= "Incompatible",
			STATUS_REJECTED							= "Rejected",
			STATUS_FAILED							= "Failed",
			STATUS_CANCELLED						= "Cancelled",
			STATUS_TIMED_OUT						= "TimedOut";

	protected $guarded = array();

	protected $table = "tb_poli_transaction";

	public static $rules = array();

	public function isCompleted()
	{
		return $this->status == self::STATUS_COMPLETED;
	}
}
