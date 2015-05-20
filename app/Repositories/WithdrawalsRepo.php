<?php

namespace TopBetta\Repositories;

use TopBetta\Models\WithdrawalRequest;

/**
 * Description of WithdrawalsRepo
 *
 * @author mic
 */
class WithdrawalsRepo
{

	/**
	 * @var WithdrawalRequest
	 */
	private $withdrawalRequest;

	public function __construct(WithdrawalRequest $withdrawalRequest)
	{

		$this->withdrawalRequest = $withdrawalRequest;
	}

    public function find($id)
    {
        return $this->withdrawalRequest->find($id);
    }

    public function updateWithId($id, $data)
    {
        $withdrawal = $this->find($id);

        return $withdrawal->update($data);
    }

	public function search($search)
	{
		return $this->withdrawalRequest
						->orderBy('requested_date', 'desc')
						->where('withdrawal_type_id', 'LIKE', "%$search%")
						->with('user', 'type', 'paypal', 'moneybookers')
						->paginate();
	}

	public function allWithdrawals()
	{
		return $this->withdrawalRequest
						->orderBy('requested_date', 'desc')
						->with('user', 'type', 'paypal', 'moneybookers')
						->paginate();
	}

    public function allPendingWithdrawals()
    {
        return $this->withdrawalRequest->whereNull('approved_flag')
            ->orderBy('requested_date', 'desc')
            ->with('user', 'type', 'paypal', 'moneybookers')
            ->paginate();
    }

	public function getUserWithdrawals($userId)
	{
		return $this->withdrawalRequest
						->orderBy('requested_date', 'desc')
						->where('requester_id', $userId)
						->with('user', 'type', 'paypal', 'moneybookers')
						->paginate();
	}

}
