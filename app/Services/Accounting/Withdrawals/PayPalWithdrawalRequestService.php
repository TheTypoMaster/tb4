<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 12:11 PM
 */

namespace TopBetta\Services\Accounting\Withdrawals;


use TopBetta\Repositories\Contracts\WithdrawalPayPalRepositoryInterface;
use TopBetta\Repositories\Contracts\WithdrawalRequestRepositoryInterface;
use TopBetta\Repositories\Contracts\WithdrawalTypeRepositoryInterface;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Validation\WithdrawalValidator;

class PayPalWithdrawalRequestService extends AbstractWithdrawalRequestService implements WithdrawalRequestServiceInterface {

    /**
     * @var WithdrawalPayPalRepositoryInterface
     */
    private $payPalRepository;

    public function __construct(WithdrawalValidator $validator, WithdrawalRequestRepositoryInterface $requestRepository, AccountTransactionService $accountTransactionService, WithdrawalTypeRepositoryInterface $withdrawalTypeRepository, WithdrawalPayPalRepositoryInterface $payPalRepository)
    {
        $this->payPalRepository = $payPalRepository;
        parent::__construct($validator, $requestRepository, $accountTransactionService, $withdrawalTypeRepository);
    }

    public function getType()
    {
        return WithdrawalTypeRepositoryInterface::WITHDRAWAL_TYPE_PAYPAL;
    }

    public function validateRequest($user, $request)
    {
        $this->validator->rules = $this->validator->mergeRules($this->validator->rules, array(
            "email" => 'required|email'
        ));

        parent::validateRequest($user, $request);
    }

    public function processRequest($user, $request)
    {
        $withdrawal = parent::processRequest($user, $request);

        $this->payPalRepository->create(array(
            'withdrawal_request_id' => $withdrawal['id'],
            'paypal_id' => $request['email']
        ));

        return $withdrawal;
    }

}