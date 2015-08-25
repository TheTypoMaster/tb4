<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 11:57 AM
 */

namespace TopBetta\Services\Accounting\Withdrawals;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\WithdrawalRequestRepositoryInterface;
use TopBetta\Repositories\Contracts\WithdrawalTypeRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use TopBetta\Services\Validation\WithdrawalValidator;

abstract class AbstractWithdrawalRequestService implements WithdrawalRequestServiceInterface
{

    /**
     * @var WithdrawalValidator
     */
    protected $validator;

    /**
     * @var WithdrawalRequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * @var AccountTransactionService
     */
    protected $accountTransactionService;
    /**
     * @var WithdrawalTypeRepositoryInterface
     */
    private $withdrawalTypeRepository;

    public function __construct(WithdrawalValidator $validator, WithdrawalRequestRepositoryInterface $requestRepository, AccountTransactionService $accountTransactionService, WithdrawalTypeRepositoryInterface $withdrawalTypeRepository)
    {
        $this->validator                 = $validator;
        $this->requestRepository         = $requestRepository;
        $this->accountTransactionService = $accountTransactionService;
        $this->withdrawalTypeRepository  = $withdrawalTypeRepository;
    }

    public function validateRequest($user, $request)
    {
        $this->validator->validateForCreation($request);

        if ($request['amount'] < ($min = \Config::get('withdrawal.minimum.' . $this->getType()))) {
            throw new ValidationException("Validation Failed", array("Minimum withdrawal is: " . $min));
        }

        if (($balance = $this->accountTransactionService->getAvailableWithdrawalBalance($user->id)) < $request['amount']) {
            throw new ValidationException("Validation Failed", array("Available withdrawal balance is $" . number_format($balance / 100, 2)));
        }
    }

    public function processRequest($user, $request)
    {
        $this->validateRequest($user, $request);

        return $this->requestRepository->create(array(
            "requester_id"       => $user->id,
            "withdrawal_type_id" => $this->withdrawalTypeRepository->getByName($this->getType())->id,
            "amount"             => $request['amount'],
            "requested_date"     => Carbon::now()
        ));
    }

    abstract public function getType();
}