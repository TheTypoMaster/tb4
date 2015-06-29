<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 6/02/2015
 * Time: 12:46 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Repositories\Contracts\PoliTransactionRepositoryInterface;
use TopBetta\Models\PoliTransactionModel;

class DbPoliTransactionRepository extends BaseEloquentRepository implements PoliTransactionRepositoryInterface{

    public function __construct(PoliTransactionModel $poliTransaction) {
        $this->model= $poliTransaction;
    }

    /**
     * Finds the transaction by the transaction reference no. provided by Poli
     * @param $refNo
     * @return mixed
     */
    public function findByRefNo($refNo)
    {
        return $this->model->where('poli_ref_no', $refNo)->first();
    }

    /**
     * Sets the poli token if it is not already set
     * @param $poliTransaction
     * @param $token
     * @return mixed
     */
    public function setToken($poliTransaction, $token)
    {
        if( ! $poliTransaction->token ) {
            $poliTransaction->poli_token = $token;
            $poliTransaction->save();
        }

        return $poliTransaction;
    }

    /**
     * Updates the transaction status and sets any error codes
     * @param $poliTransaction
     * @param $transactionStatus
     * @param int $errorCode
     * @return mixed
     */
    public function updateStatus($poliTransaction, $transactionStatus, $errorCode = 0){

        $poliTransaction->status = $transactionStatus;

        if( $errorCode ) {
            $poliTransaction->poli_error_code = $errorCode;
        }

        $poliTransaction->save();

        return $poliTransaction;
    }


    /**
     * Sets the transactions status to Initiated
     * @param $id
     * @param $refNo
     */
    public function initialize($id, $refNo) {
        $this->updateWithId($id, array(
            "status"        => PoliTransactionModel::STATUS_INITIATED,
            "poli_ref_no"    => $refNo,
        ));
    }

    /**
     * Set the transaction status to Initiation failed.
     * @param $id
     * @param $errorCode
     */
    public function initializationFailed($id, $errorCode){
        $this->updateWithId($id, array(
            "status"        => PoliTransactionModel::STATUS_FAILED_INITIATED,
            "poli_error_code"    => $errorCode,
        ));
    }
}