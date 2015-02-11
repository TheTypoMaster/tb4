<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 11/02/2015
 * Time: 10:06 AM
 */

namespace TopBetta\Repositories;

use DB;
use Carbon\Carbon;
use TopBetta\models\FreeCreditTransactionModel;
use TopBetta\Repositories\Contracts\FreeCreditTransactionRepository;

class DbFreeCreditTransactionRepository extends BaseEloquentRepository implements FreeCreditTransactionRepository {

    public function __construct(FreeCreditTransactionModel $freeCreditTransaction)
    {
        $this->model = $freeCreditTransaction;
    }

    public function getNonZeroBalancesForInactiveUsers($days)
    {
        $lastActivityDateThreshold = Carbon::now()->subDays($days);


        $model = $this -> model
                    //-> join('tbdb_users', 'tbdb_tournament_transaction.recipient_id', '=', 'tbdb_users.id')
                    //-> where('tbdb_users.lastvisitDate', '<', $lastActivityDateThreshold->format('Y-m-d H:i:s'))
                    -> groupBy('recipient_id')
                    -> having('balance', '>', '0')
                    ->forPage($page = 1, $count = 100)
                    -> get( array(
                        DB::raw("sum(amount) as balance"),
                        //"tbdb_users.id",
                    ));
                //->having("balance", ">", "0");


        dd($model);

    }

    public function bulkInsert(array $data)
    {
        // TODO: Implement bulkInsert() method.
    }


}