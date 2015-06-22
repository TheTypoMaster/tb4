<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 4/06/2015
 * Time: 11:28 AM
 */

namespace TopBetta\Repositories;

use Config;
use TopBetta\Models\ScheduledPaymentModel;
use TopBetta\Repositories\Contracts\ScheduledPaymentRepositoryInterface;

class DbScheduledPaymentRepository extends BaseEloquentRepository implements ScheduledPaymentRepositoryInterface
{

    public function __construct(ScheduledPaymentModel $model)
    {
        $this->model = $model;
    }

    public function getPaymentsDueAfterDate($date)
    {
        return $this->model
            ->where('active', true)
            ->where('retries', '<', Config::get('ewayrapid.max_retries'))
            ->where('next_payment', '<=', $date)
            ->get();
    }

    public function getActivePaymentsForUser($userId, $source = null)
    {
        $model = $this->model
            ->where('active', true)
            ->where('user_id', $userId)
            ->with('paymentToken');

        if( $source ) {
            $model->where("source_id", $source);
        }

        return $model->get();
    }
}