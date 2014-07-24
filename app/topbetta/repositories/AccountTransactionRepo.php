<?php

namespace TopBetta\Repositories;

use TopBetta\AccountBalance;

/**
 * Description of AccountTransactionRepo
 *
 * @author mic
 */
class AccountTransactionRepo
{

	/**
	 * @var AccountBalance
	 */
	private $accountTransactions;

	public function __construct(AccountBalance $accountTransactions)
	{
		
		$this->accountTransactions = $accountTransactions;
	}
	
	public function allTransactions() {
		return $this->accountTransactions
				->with('transactionType', 'giver', 'recipient')
				->orderBy('created_date', 'DESC')
				->paginate();
	}
	
	public function userTransactions($userId) {
		return $this->accountTransactions
				->where('recipient_id', $userId)
				->with('transactionType', 'giver', 'recipient')
				->orderBy('created_date', 'DESC')
				->paginate();
	}	
}
