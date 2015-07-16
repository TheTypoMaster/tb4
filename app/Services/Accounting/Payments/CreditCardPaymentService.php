<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/06/2015
 * Time: 4:27 PM
 */

namespace TopBetta\Services\Accounting\Payments;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\CreditCard;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Accounting\Payments\Exceptions\PaymentException;

abstract class CreditCardPaymentService {

    const DEPOSIT_CURRENCY = 'AUD';
    /**
     * @var AbstractGateway
     */
    protected $gateway;
    /**
     * @var AccountTransactionService
     */
    protected $accountTransactionService;

    public function __construct(AccountTransactionService $accountTransactionService)
    {
        $this->accountTransactionService = $accountTransactionService;
    }

    /**
     * Perform deposit with gateway
     * @param $user
     * @param $amount
     * @param $card
     * @param bool $recurring
     * @return mixed
     * @throws PaymentException
     */
    public function deposit($user, $amount, $card, $recurring = false)
    {
        $request = $this->gateway->purchase($this->getDepositPayload($amount, $card, $recurring));

        $response = $request->send();

        if ( ! $response->isSuccessful() ) {
            throw new PaymentException($response->getMessage());
        }

        return $response;
    }

    /**
     * Format payload for transaction
     * @param $amount
     * @param $card
     * @param bool $recurring
     * @return array
     */
    public function getDepositPayload($amount, $card, $recurring = false)
    {
        return array(
            "amount" => (float) $amount,
            "currency" => self::DEPOSIT_CURRENCY,
            "transactionType" => "Purchase",
            "card" => new CreditCard($card),
        );
    }

    /**
     * Get card validation rules
     * @param $user
     * @return mixed
     */
    abstract public function getCardValidationRules($user);
}