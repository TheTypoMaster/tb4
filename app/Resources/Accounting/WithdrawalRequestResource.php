<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 1:08 PM
 */

namespace TopBetta\Resources\Accounting;


use TopBetta\Resources\AbstractEloquentResource;

class WithdrawalRequestResource extends AbstractEloquentResource {

    protected $attributes = array(
        'id' => 'id',
        'amount' => 'amount',
        'withdrawalType' => 'type.name',
        'date' => 'requested_date',
        'paypalEmail' => 'paypalEmail'
    );

    protected $types = array(
        "id" => "int",
        "amount" => "int",
    );

    public function getPaypalEmail()
    {
        if( $this->model->paypal ) {
            return $this->model->paypal->paypal_id;
        }

        return null;
    }
}