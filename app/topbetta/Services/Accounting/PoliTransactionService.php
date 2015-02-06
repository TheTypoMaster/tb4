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
        $data['MerchantReference'] = "TB_" . $poliTransaction['id'] . "_" . $user->id;
        $data['MerchantData'] = array(
            "user"          => $user->id,
            "transaction"   => $poliTransaction['id'],
        );
        $data['NotificationUrl'] = route('api.v1.users.poli-deposit.store');

        //Send the request to the Poli API
        $client = $this->createClient();

        try {
            $response = $client->post(Config::get("poli.apiEndPoints.initiateTransaction"), array("body" => $data));
        }

        catch (RequestException $e) {
            //error so mark record as failed
            $errorCode = $e->getResponse() ? $e->getResponse()->json()['ErrorCode'] : 0;
            $this->poliTransactionRepository->initializationFailed($poliTransaction['id'], $errorCode);
            return $e->getResponse();
        }

        //everything worked so init
        $this->poliTransactionRepository->initialize($poliTransaction['id'], $response->json()['TransactionRefNo']);

        return $response;
    }

    public function getTransactionDetails($token) {

        $client = $this->createClient();

        try {
            $response = $client->get(Config::get("poli.apiEndPoints.getTransactionDetails"), array("query" => array("token" => $token)));
        } catch (RequestException $e) {

            return $e->getResponse();
        }

        return $response;
    }

    private function createClient()
    {
        return new Client(array(
            "defaults" => array(
                "auth"  => array(
                    Config::get("poli.merchantId"),
                    Config::get("poli.merchantPassword"),
                ),
            ),
        ));
    }
}

