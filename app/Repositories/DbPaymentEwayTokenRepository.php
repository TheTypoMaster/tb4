<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 12:58 PM
 */

namespace TopBetta\Repositories;


use TopBetta\Models\PaymentEwayTokens;
use TopBetta\Repositories\Contracts\PaymentEwayTokenRepositoryInterface;

class DbPaymentEwayTokenRepository extends BaseEloquentRepository implements PaymentEwayTokenRepositoryInterface
{

    public function __construct(PaymentEwayTokens $model)
    {
        $this->model = $model;
    }

    public function getByToken($token)
    {
        return $this->model->where('cc_token',  $token)->first();
    }

}