<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/06/2015
 * Time: 4:43 PM
 */

namespace TopBetta\Services\Accounting\Payments;

use Config;
use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;
use TopBetta\Models\PaymentEwayTokens;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Accounting\Gateways\EwayRapidDirectGateway;
use TopBetta\Services\Accounting\Payments\Exceptions\PaymentException;

class EwayPaymentService extends CreditCardPaymentService {

    public function __construct(AccountTransactionService $accountTransactionService)
    {
        parent::__construct($accountTransactionService);
        $this->gateway = new EwayRapidDirectGateway();

        $this->gateway->initialize(array(
            "apiKey" => Config::get('ewayrapid.api_key'),
            "password" => Config::get('ewayrapid.api_password'),
            "testMode" => Config::get('ewayrapid.test_mode'),
        ));
    }

    /**
     * Make a deposit with eway. Creates a credit card token first.
     * @param $user
     * @param $amount
     * @param $card
     * @param bool $recurring
     * @return mixed
     * @throws PaymentException
     */
    public function deposit($user, $amount, $card, $recurring = false)
    {

        if( ! $token = array_get($card, 'cc_token', null) ) {
            $token = $this->createCard($user, $card)->cc_token;
        }

        $response = parent::deposit($user, $amount/100, array('card' => array_only($card, 'cvv'), 'cardReference' => $token), $recurring);

        return $response;
    }

    /**
     * Format eway transaction payload
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
            "transactionType" => $recurring ? "Recurring" : "Purchase",
            "cardReference" => $card['cardReference'],
            "card" => new CreditCard($card['card']),
        );
    }

    /**
     * Get card validation rules
     * @param $user
     * @return array
     */
    public function getCardValidationRules($user)
    {
        return array(
            "number" => "required_without:cc_token",
            "cc_token" => "required_without:number|exists:tb_payment_eway_tokens,cc_token,user_id,".$user->id,
            "cvv" => "required",
            "firstName" => "required_with:number",
            "lastName" => "required_with:number",
            "expiryMonth" => "required_with:number",
            "expiryYear" => "required_with:number",
            "billingCountry" => "required_with:number",
        );
    }

    /**
     * Create a card token
     * @param $user
     * @param $card
     * @return PaymentEwayTokens
     * @throws PaymentException
     */
    public function createCard($user, $card)
    {
        //create the request
        $request = $this->gateway->createCard(array(
            'card' => new CreditCard($card)
        ));

        $response = $request->send();

        if ( ! $reference = $response->getCardReference() ) {
            throw new PaymentException($response->getMessage());
        }

        //save the token if created correctly
        $token = new PaymentEwayTokens(array("user_id" => $user->id, "cc_token" => $reference));
        $token->save();

        return $token;
    }

    /**
     * Gets card details from token
     * @param $token
     * @return mixed
     * @throws PaymentException
     */
    public function getCard($token)
    {
        //build request
        $request = $this->gateway->getCardDetails(array("cardReference" => $token));

        //send
        $response = $request->send();

        //check response
        if( ! $response ) {
            throw new PaymentException($response->getMessage());
        }

        return $response->getData();
    }
}