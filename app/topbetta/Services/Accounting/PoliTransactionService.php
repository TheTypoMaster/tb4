<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/02/2015
 * Time: 10:57 AM
 */

namespace TopBetta\Services\Accounting;

use Config;
use Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use TopBetta\Repositories\Contracts\PoliTransactionRepositoryInterface;

class PoliTransactionService
{

    /**
     * @var PoliTransactionRepositoryInterface
     */
    private $poliTransactionRepository;

    public function __construct(PoliTransactionRepositoryInterface $poliTransactionRepository)
    {
        $this->poliTransactionRepository = $poliTransactionRepository;
    }

    /**
     * Calls initializeTransaction in the Poli API
     * @param $data
     * @return mixed
     */
    public function initiateTransaction(array $data)
    {
        //get the user
        $user = Auth::user();

        //create the transaction in the db
        $poliTransaction = $this->poliTransactionRepository->create(array(
            "user_id"       => $user->id,
            "status"        => "Not Initialized",
            "amount"        => $data['Amount'],
            "currency_code" => $data['CurrencyCode'],
        ));

        //Add Merchant data to payload for API
        $data['MerchantReference'] = "TB_" . $poliTransaction['id'] . "_" . Auth::user()->id;
        $data['MerchantData'] = array(
            "user"          => Auth::user()->id,
            "transaction"   => $poliTransaction['id'],
        );

        //Send the request to the Poli API
        $client = $this->createClient();

        try {
            $response = $client->post(Config::get("poli.apiEndPoints.initiateTransaction"), array("body" => $data));
        } catch (RequestException $e) {
            $transactionData = array("status" => "Initialization failed");
            $this->poliTransactionRepository->updateWithId($poliTransaction['id'], $transactionData);
            dd($e->getRequest());
        }

        //check the response and update the transaction accordingly
        if ($response['Success']) {
            $transactionData = array(
                "status" => "Initialized",
                "poli_token" => $response['TransactionRefNo'],
            );
        }

        $this->poliTransactionRepository->updateWithId($poliTransaction['id'], $transactionData);

        return $response;
    }

    private function createClient()
    {
        return new Client(array(
            "auth"  => array(
                "username"  => Config::get("poli.merchantId"),
                "password"  => Config::get("poli.password"),
            ),
        ));
    }
}

