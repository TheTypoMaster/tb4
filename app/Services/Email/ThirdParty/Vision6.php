<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 5/03/2015
 * Time: 9:58 AM
 */

namespace TopBetta\Services\Email\ThirdParty;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use TopBetta\Services\Email\Exceptions\EmailRequestException;

class Vision6 extends AbstractThirdPartyEmailService {

    const API_URL = 'http://www.vision6.com.au/api/jsonrpcserver.php?version=3.0';

    private $client;

    private $list = null;

    public function __construct($data)
    {
        parent::__construct($data);

        $this->client = new Client(array(
            "defaults" => array(
                "headers" => array(
                    "User-Agent" => "JSON-RPC PHP WRAPPER",
                ),
            ),
        ));

    }

    public function addAndUpdateContacts($contacts)
    {
        if( ! $this->list ) {
            if( $listId = array_get($this->data, 'list_id', false) ) {
                $this->list = $this->getListById($listId);
            } else {
                $this->list = $this->getListByName($this->data['list_name']);
            }
        }

        $response = $this->sendRequest('addContacts', array(
            $this->list['id'],
            $contacts,
            true
        ));

        return $response['result'];
    }

    public function editContacts($contacts)
    {
        $response = $this->sendRequest('editContacts', array(
            $this->getListid(),
            $contacts
        ));

        return $response['id'];
    }

    public function getList(){return $this->list;}

    public function getListId()
    {
        if( ! $this->list ) {
            if( $listId = array_get($this->data, 'list_id', false) ) {
                return $listId;
            } else {
                $this->list = $this->getListByName($this->data['list_name']);
            }
        }

        return $this->list['id'];
    }

    public function getContactsByEmail($contactEmails)
    {
        $searchCriteria = array(
            array("Email", "in", implode(",", $contactEmails))
        );

        return $this->searchContacts($this->getListId(), $searchCriteria);
    }

    public function getListByName($name)
    {
        $searchCriteria = array(
            array("name", "exactly", $name),
        );

        return $this->searchLists($searchCriteria)[0];
    }

    public function getListById($id)
    {
        return $this->sendRequest('getListById', array($id))['result'][0];
    }

    public function getMessageByName($name)
    {
        $searchCriteria = array(
            array("name", "exactly", $name)
        );

        return $this->searchMessages($searchCriteria)[0];
    }

    public function searchContacts($listId, $searchCriteria = array())
    {
        $response = $this->sendRequest("searchContacts", array($listId, $searchCriteria));

        return $response['result'];
    }

    public function searchLists($searchCriteria = array())
    {
        $response = $this->sendRequest("searchLists", array($searchCriteria));

        return $response['result'];
    }

    public function searchMessages($searchCriteria = array())
    {
        $response = $this->sendRequest("searchMessages", array($searchCriteria));

        return $response['result'];
    }

    public function sendRequest($method, $params)
    {
        //add the api key
        $params = array_merge(array($this->data['api_key']), $params);

        $payload = array(
            "method" => $method,
            "params" => $params,
        );

        try {
            $response = $this->client->post(self::API_URL, array(
                "json" => $payload,
                "connect_timeout" => \Config::get('vision6.connection_timeout'),
            ));
        } catch(RequestException $e) {
            throw new EmailRequestException($e->getMessage());
        }

        $response = $response->json();

        if($response['error']) {
            throw new EmailRequestException($response['error']['code'] . ": " . $response['error']['message']);
        }

        return $response;
    }

    public function sendMessage($contacts, $messageRef)
    {
        if( ! $this->list ) {
            if( $listId = array_get($this->data, 'list_id', false) ) {
                $this->list = $this->getListById($listId);
            } else {
                $this->list = $this->getListByName($this->data['list_name']);
            }
        }

        return $this->sendMessageToContacts($messageRef, $contacts);
    }

    private function sendMessageToContacts($messageId, $contactIds)
    {
        //create the batch
        $response = $this->sendRequest(
            "addBatch",
            array(
                $messageId,
                array(
                    array(
                        "list_id"      => $this->list['id'],
                        "type"         => "contacts",
                        "contact_list" => $contactIds,
                        'time'         => 'NOW'
                    )
                )
            )
        );

        $queueId = $response['result'];

        $messageId = $this->sendRequest('getBatchIdByQueueId', array($queueId))['result'];

        //poll for the batch id
        while ( ! $messageId ) {

            sleep(1);

            $messageId = $this->sendRequest('getBatchIdByQueueId', array($queueId));

            $messageId = $messageId['result'];
        }

        //get the batch information and wait for it to send
        $time = time();
        $batch = $this->sendRequest('getBatchById', array($messageId));

        while ($batch['result'][0]['send_status'] != 'completed' && time() - $time < $this->data['poll_time']) {
            sleep(5);

            $batch = $this->sendRequest('getBatchById', array($messageId));
        }

        if($batch['result'][0]['send_status'] != 'completed') {
            throw new EmailRequestException("Email not finished sending. Status: " . $batch['result']['send_status']);
        }

        return $batch['result'];
    }
}