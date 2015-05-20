<?php

namespace TopBetta\Repositories;

use TopBetta\FreeCreditBalance;

/**
 * Description of FreeCreditTransactionRepo
 *
 * @author mic
 */
class FreeCreditTransactionRepo
{

	/**
	 * @var FreeCreditBalance
	 */
	private $freeCreditTransactions;

	public function __construct(FreeCreditBalance $freeCreditTransactions)
	{
		
		$this->freeCreditTransactions = $freeCreditTransactions;
	}
	
	public function allTransactions() {
		return $this->freeCreditTransactions
				->with('transactionType', 'giver', 'recipient')
				->orderBy('created_date', 'DESC')
				->paginate();
	}
	
	public function userTransactions($userId) {
		return $this->freeCreditTransactions
				->where('recipient_id', $userId)
				->with('transactionType', 'giver', 'recipient')
				->orderBy('created_date', 'DESC')
				->paginate();
	}
}
