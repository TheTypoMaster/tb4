<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/02/2015
 * Time: 12:46 PM
 */

namespace TopBetta\Repositories\Contracts;


interface PoliTransactionRepositoryInterface {

    public function findByRefNo($refNo);

    public function setToken($poliTransaction, $token);

    public function updateStatusAndAmount($poliTransaction, $transactionStatus, $amount, $errorCode = 0);

    public function initialize($id, $refNo);

    public function initializationFailed($id, $errorCode);
}